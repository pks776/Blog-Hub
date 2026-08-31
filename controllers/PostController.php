<?php

namespace app\controllers;

use app\models\Post;
use app\models\PostSearch;
use app\models\AuditLog;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Inflector;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;
use app\models\PostVersion;

class PostController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'approve-version' => ['POST'],
                    'reject-version' => ['GET', 'POST'],
                    'unpublish' => ['POST'],
                    'publish' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * All Posts
     */
    public function actionIndex()
{
    if (
        Yii::$app->user->identity->role != 'admin' &&
        Yii::$app->user->identity->role != 'moderator'
    ) {
        throw new ForbiddenHttpException('Access Denied');
    }

    $searchModel = new PostSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]);
}
   /**
     * My Posts
     */
public function actionMyPosts()
{
    if (Yii::$app->user->identity->role != 'blogger') {
        throw new ForbiddenHttpException('Access Denied');
    }

    $searchModel = new PostSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    $dataProvider->query->andWhere([
        'author_id' => Yii::$app->user->id,
    ]);

    // Get pending edited versions
    $pendingVersions = PostVersion::find()
        ->innerJoinWith('post')
        ->where([
            'posts.author_id' => Yii::$app->user->id,
            'post_versions.status' => PostVersion::STATUS_PENDING,
        ])
        ->orderBy(['post_versions.created_at' => SORT_DESC])
        ->all();

    return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'pendingVersions' => $pendingVersions,
    ]);
}
    /**
     * View Post
     */
   public function actionView($id)
{
    $model = $this->findModel($id);

    if ($model->status == Post::STATUS_PUBLISHED) {
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    if (Yii::$app->user->isGuest) {
        throw new ForbiddenHttpException('Access Denied');
    }

    $role = Yii::$app->user->identity->role;

    if (in_array($role, ['admin', 'moderator'])) {
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    if (
        $role == 'blogger' &&
        $model->author_id == Yii::$app->user->id
    ) {
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    throw new ForbiddenHttpException('Access Denied');
}
/**
     * ActionRejectVersion
     */
public function actionRejectVersion($id)
{
    if (
        Yii::$app->user->identity->role != 'admin' &&
        Yii::$app->user->identity->role != 'moderator'
    ) {
        throw new ForbiddenHttpException('Access Denied');
    }

    $version = PostVersion::findOne($id);

    if ($version === null) {
        throw new NotFoundHttpException('Version not found.');
    }

    // Only pending versions can be rejected
    if ($version->status !== PostVersion::STATUS_PENDING) {
        throw new BadRequestHttpException(
            'This version is not pending. Current status: ' . $version->status
        );
    }

    // Show rejection form
    if (!Yii::$app->request->isPost) {
        return $this->render('reject-version', [
            'version' => $version,
        ]);
    }

    // Get rejection reason
    $reason = trim(
        Yii::$app->request->post('rejection_reason', '')
    );

    // Reason is required
    if ($reason === '') {

        Yii::$app->session->setFlash(
            'error',
            'Please enter a rejection reason.'
        );

        return $this->render('reject-version', [
            'version' => $version,
        ]);
    }

    $transaction = Yii::$app->db->beginTransaction();

    try {

        // Find the main post
        $post = Post::findOne($version->post_id);

        if ($post === null) {
            throw new NotFoundHttpException('Post not found.');
        }

        // Reject the version
        $version->status = PostVersion::STATUS_REJECTED;
        $version->rejection_reason = $reason;
        $version->reviewed_by = Yii::$app->user->id;
        $version->updated_at = date('Y-m-d H:i:s');

        if (!$version->save(false)) {
            throw new \Exception('Unable to reject the blog version.');
        }
        AuditLog::record(
    'rejected',
    'Post',
    $post->id,
    'Blog "' . $post->title . '" Version ' .
    $version->version . ' was rejected. Reason: ' .
    $reason
);

        // Update the main post
        $post->status = Post::STATUS_REJECTED;
        $post->rejection_reason = $reason;
        $post->updated_at = date('Y-m-d H:i:s');

        if (!$post->save(false)) {
            throw new \Exception('Unable to update the blog status.');
        }

        $transaction->commit();

        Yii::$app->session->setFlash(
            'success',
            'Blog rejected successfully.'
        );

        return $this->redirect(['pending']);

    } catch (\Throwable $e) {

        $transaction->rollBack();

        Yii::error(
            'Reject Version Error: ' . $e->getMessage(),
            __METHOD__
        );

        throw $e;
    }
}
    /**
     * Create Post
     */
public function actionCreate()
{
    if (Yii::$app->user->identity->role != 'blogger') {
        throw new ForbiddenHttpException('Access Denied');
    }

    $model = new Post();

    if ($model->load(Yii::$app->request->post())) {

        $model->imageFile = UploadedFile::getInstance(
            $model,
            'imageFile'
        );

        $model->author_id = Yii::$app->user->id;
        $model->status = Post::STATUS_PENDING;

        // Generate slug temporarily from title
        $model->slug = Inflector::slug($model->title);

        // Validate form data
        if ($model->validate()) {

            $transaction = Yii::$app->db->beginTransaction();

            try {

                // ==========================================
                // Upload image
                // ==========================================

                $image = null;

                if ($model->imageFile) {

                    $fileName = uniqid() . '.' .
                        $model->imageFile->extension;

                    $uploadPath = Yii::getAlias(
                        '@webroot/uploads/posts/'
                    );

                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }

                    if (!$model->imageFile->saveAs(
                        $uploadPath . $fileName
                    )) {
                        throw new \Exception(
                            'Unable to upload image.'
                        );
                    }

                    $image = $fileName;
                }

                // ==========================================
                // Save basic post information
                // ==========================================

                // Only fields that actually exist in posts
                $model->image = null;
                $model->slug = null;
                $model->rejection_reason = null;

                if (!$model->save(false)) {
                    throw new \Exception(
                        'Unable to save post.'
                    );
                }

                // ==========================================
                // Create Version 1
                // ==========================================

                $version = new PostVersion();

                $version->post_id = $model->id;
                $version->version = 1;

                $version->title = $model->title;
                $version->content = $model->content;
                $version->image = $image;

                $version->status =
                    PostVersion::STATUS_PENDING;

                $version->created_by =
                    Yii::$app->user->id;

                $version->created_at =
                    date('Y-m-d H:i:s');

                $version->updated_at =
                    date('Y-m-d H:i:s');

                if (!$version->save()) {
                    throw new \Exception(
                        'Unable to save post version: ' .
                        json_encode($version->errors)
                    );
                }

                // ==========================================
                // Update current version number
                // ==========================================
$model->created_at = date('Y-m-d H:i:s');
$model->updated_at = date('Y-m-d H:i:s');
                $model->version = 1;

                if (!$model->save(false)) {
                    throw new \Exception(
                        'Unable to update post version number.'
                    );
                }

                // ==========================================
                // Audit log
                // ==========================================

                AuditLog::record(
                    'created',
                    'Post',
                    $model->id,
                    'Blog "' . $model->title .
                    '" was created and Version 1 was submitted for approval.'
                );

                $transaction->commit();

                Yii::$app->session->setFlash(
                    'success',
                    'Your blog has been submitted for Admin approval.'
                );

                return $this->redirect([
                    'my-posts'
                ]);

            } catch (\Throwable $e) {

                $transaction->rollBack();

                Yii::error(
                    'Create Post Error: ' .
                    $e->getMessage(),
                    __METHOD__
                );

                throw $e;
            }
        }
    }

    return $this->render('create', [
        'model' => $model,
    ]);
}
    /**
     * Update Post
     */
public function actionUpdate($id)
{
    $model = $this->findModel($id);
    $role = Yii::$app->user->identity->role;

    // Only bloggers can edit blogs
    if ($role != 'blogger') {
        throw new ForbiddenHttpException(
            'Only bloggers can edit blogs.'
        );
    }

    // Blogger can edit only their own blogs
    if ($model->author_id != Yii::$app->user->id) {
        throw new ForbiddenHttpException(
            'You cannot edit this blog.'
        );
    }

    /*
     * Get the current version of the blog.
     *
     * posts.version tells us which version is currently
     * associated with the post.
     */
    $currentVersion = PostVersion::find()
        ->where([
            'post_id' => $model->id,
            'version' => $model->version,
        ])
        ->one();

    /*
     * If the exact version is not found, use the latest
     * version as a fallback.
     */
    if ($currentVersion === null) {
        $currentVersion = PostVersion::find()
            ->where([
                'post_id' => $model->id,
            ])
            ->orderBy([
                'version' => SORT_DESC,
            ])
            ->one();
    }

    /*
     * Load existing version data into the virtual
     * Post model properties.
     *
     * This makes the existing content appear in the
     * update form.
     */
    if ($currentVersion !== null) {
        $model->content = $currentVersion->content;
        $model->image = $currentVersion->image;
    }

    if ($model->load(Yii::$app->request->post())) {

        /*
         * Get uploaded image.
         */
        $imageFile = UploadedFile::getInstance(
            $model,
            'imageFile'
        );

        /*
         * Keep the current image unless a new image
         * is uploaded.
         */
        $image = $model->image;

        /*
         * Upload new image if provided.
         */
        if ($imageFile) {

            $fileName = time() . '_' .
                $imageFile->baseName . '.' .
                $imageFile->extension;

            $uploadPath = Yii::getAlias(
                '@webroot/uploads/posts/'
            );

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            if (!$imageFile->saveAs(
                $uploadPath . $fileName
            )) {

                Yii::$app->session->setFlash(
                    'error',
                    'Unable to upload image.'
                );

                return $this->render('update', [
                    'model' => $model,
                ]);
            }

            $image = $fileName;
        }

        /*
         * Get latest version number.
         */
        $latestVersion = PostVersion::find()
            ->where([
                'post_id' => $model->id,
            ])
            ->max('version');

        $nextVersion = $latestVersion
            ? $latestVersion + 1
            : 1;

        /*
         * Create a new version.
         */
        $version = new PostVersion();

        $version->post_id = $model->id;
        $version->version = $nextVersion;
        $version->title = $model->title;
        $version->content = $model->content;
        $version->image = $image;
        $version->status = PostVersion::STATUS_PENDING;
        $version->created_by = Yii::$app->user->id;
        $version->created_at = date('Y-m-d H:i:s');
        $version->updated_at = date('Y-m-d H:i:s');

        if ($version->save()) {

            /*
             * Audit log.
             */
            AuditLog::record(
                'updated',
                'Post',
                $model->id,
                'Blog "' . $model->title .
                '" was updated and Version ' .
                $nextVersion .
                ' was submitted for approval.'
            );

            Yii::$app->session->setFlash(
                'success',
                'Blog updated and sent for approval.'
            );

            return $this->redirect([
                'my-posts'
            ]);
        }

        Yii::$app->session->setFlash(
            'error',
            'Unable to create new version: ' .
            json_encode($version->errors)
        );
    }

    return $this->render('update', [
        'model' => $model,
    ]);
}
/**
     * ActionViewVersion
     */

public function actionViewVersion($id)
{
    if (
        Yii::$app->user->identity->role != 'admin' &&
        Yii::$app->user->identity->role != 'moderator'
    ) {
        throw new ForbiddenHttpException('Access Denied');
    }

    $version = PostVersion::findOne($id);

    if ($version === null) {
        throw new NotFoundHttpException('Version not found.');
    }

    return $this->render('view-version', [
        'version' => $version,
    ]);
}
//delete

public function actionDelete($id)
{
    if (
        Yii::$app->user->identity->role != 'admin' &&
        Yii::$app->user->identity->role != 'moderator' &&
        Yii::$app->user->identity->role != 'blogger'
    ) {
        throw new ForbiddenHttpException('Access Denied');
    }

    $post = $this->findModel($id);

    $role = Yii::$app->user->identity->role;

    // Blogger can delete only their own post
    if (
        $role == 'blogger' &&
        $post->author_id != Yii::$app->user->id
    ) {
        throw new ForbiddenHttpException('Access Denied');
    }

    // Published posts cannot be deleted by blogger
    if (
        $role == 'blogger' &&
        $post->status == Post::STATUS_PUBLISHED
    ) {
        throw new ForbiddenHttpException(
            'Published blogs cannot be deleted.'
        );
    }

    // Soft delete instead of physically deleting the post
    $post->status = Post::STATUS_DELETED;
    $post->rejection_reason = null;

    if ($post->save(false)) {
        AuditLog::record(
    'deleted',
    'Post',
    $post->id,
    'Blog "' . $post->title . '" was deleted.'
);
        Yii::$app->session->setFlash(
            'success',
            'Blog deleted successfully.'
        );

    } else {

        Yii::$app->session->setFlash(
            'error',
            'Unable to delete the blog.'
        );
    }

    return $this->redirect(['index']);
}


// approve post

public function actionApprove($id)
{
    if (
        Yii::$app->user->identity->role != 'admin' &&
        Yii::$app->user->identity->role != 'moderator'
    ) {
        throw new ForbiddenHttpException('Access Denied');
    }

    $post = $this->findModel($id);

    $transaction = Yii::$app->db->beginTransaction();

    try {

        // Publish the main post
        $post->status = Post::STATUS_PUBLISHED;
        $post->rejection_reason = null;

        if (!$post->save(false)) {
            throw new \Exception('Unable to publish post.');
        }

        // Find the pending version
        $version = PostVersion::find()
            ->where([
                'post_id' => $post->id,
                'status' => PostVersion::STATUS_PENDING,
            ])
            ->orderBy(['version' => SORT_DESC])
            ->one();

        if ($version) {

            // Make any previously published version outdated
            PostVersion::updateAll(
                ['status' => PostVersion::STATUS_OUTDATED],
                [
                    'post_id' => $post->id,
                    'status' => PostVersion::STATUS_PUBLISHED,
                ]
            );

            // Publish the new version
            $version->status = PostVersion::STATUS_PUBLISHED;
            $version->reviewed_by = Yii::$app->user->id;
            $version->updated_at = date('Y-m-d H:i:s');

            if (!$version->save(false)) {
                throw new \Exception('Unable to publish post version.');
            }
        }

        $transaction->commit();

        Yii::$app->session->setFlash(
            'success',
            'Blog approved successfully.'
        );

    } catch (\Throwable $e) {

        $transaction->rollBack();

        Yii::error($e->getMessage());

        throw $e;
    }

    return $this->redirect(['index']);
}
//action unpublish

public function actionPublish($id)
{
    // Only Admin and Moderator can publish
    if (
        Yii::$app->user->identity->role != 'admin' &&
        Yii::$app->user->identity->role != 'moderator'
    ) {
        throw new ForbiddenHttpException('Access Denied');
    }

    $post = $this->findModel($id);

    // Only unpublished posts can be published from here
    if ($post->status !== Post::STATUS_UNPUBLISHED) {
        throw new BadRequestHttpException(
            'Only unpublished blogs can be published.'
        );
    }

    $transaction = Yii::$app->db->beginTransaction();

    try {

        /*
         * Get the most recent version of the blog.
         *
         * Publishing an unpublished blog does NOT create
         * a new content version.
         */
        $version = PostVersion::find()
            ->where([
                'post_id' => $post->id,
            ])
            ->orderBy([
                'version' => SORT_DESC,
            ])
            ->one();

        if ($version === null) {
            throw new NotFoundHttpException(
                'No version found for this blog.'
            );
        }

        /*
         * Update the main post.
         */
        $post->status = Post::STATUS_PUBLISHED;
        $post->rejection_reason = null;
        $post->updated_at = date('Y-m-d H:i:s');

        if (!$post->save(false)) {
            throw new \Exception(
                'Unable to publish blog.'
            );
        }

        /*
         * Audit log.
         */
        AuditLog::record(
            'published',
            'Post',
            $post->id,
            'Blog "' . $post->title .
            '" was published again.'
        );

        $transaction->commit();

        Yii::$app->session->setFlash(
            'success',
            'Blog published successfully.'
        );

    } catch (\Throwable $e) {

        $transaction->rollBack();

        Yii::error(
            'Publish Error: ' . $e->getMessage(),
            __METHOD__
        );

        throw $e;
    }

    return $this->redirect(['index']);
}

public function actionUnpublish($id)
{
    if (
        Yii::$app->user->identity->role != 'admin' &&
        Yii::$app->user->identity->role != 'moderator'
    ) {
        throw new ForbiddenHttpException('Access Denied');
    }

    $post = $this->findModel($id);

    $transaction = Yii::$app->db->beginTransaction();

    try {

        // Change the main post to unpublished
        $post->status = Post::STATUS_UNPUBLISHED;
        $post->rejection_reason = null;

        if (!$post->save(false)) {
            throw new \Exception('Unable to unpublish post.');
        }

        // Record the action in audit history
        AuditLog::record(
            'unpublished',
            'Post',
            $post->id,
            'Blog "' . $post->title . '" was unpublished.'
        );

        $transaction->commit();

        Yii::$app->session->setFlash(
            'success',
            'Blog unpublished successfully.'
        );

    } catch (\Throwable $e) {

        $transaction->rollBack();

        Yii::error(
            'Unpublish Error: ' . $e->getMessage(),
            __METHOD__
        );

        throw $e;
    }

    return $this->redirect(['index']);
}
/**
     * ApproveVersion
     */

public function actionApproveVersion($id)
{
    if (
        Yii::$app->user->identity->role != 'admin' &&
        Yii::$app->user->identity->role != 'moderator'
    ) {
        throw new ForbiddenHttpException('Access Denied');
    }

    $version = PostVersion::findOne($id);

    if ($version === null) {
        throw new NotFoundHttpException('Version not found.');
    }

    // Make sure only pending versions can be approved
    if ($version->status != PostVersion::STATUS_PENDING) {
        throw new \yii\web\BadRequestHttpException(
            'This version is not pending approval.'
        );
    }

    // Find the original post
    $post = Post::findOne($version->post_id);

    if ($post === null) {
        throw new NotFoundHttpException('Post not found.');
    }

    $transaction = Yii::$app->db->beginTransaction();

    try {

        // Make only the currently published version outdated
        PostVersion::updateAll(
            ['status' => PostVersion::STATUS_OUTDATED],
            [
                'post_id' => $post->id,
                'status' => PostVersion::STATUS_PUBLISHED,
            ]
        );

        // Update the main post with the approved version
        $post->title = $version->title;
        $post->content = $version->content;
        $post->image = $version->image;
        $post->status = Post::STATUS_PUBLISHED;
        $post->rejection_reason = null;

        if (!$post->save(false)) {
            throw new \Exception('Unable to update post.');
        }

        // Mark this version as published
        $version->status = PostVersion::STATUS_PUBLISHED;
        $version->reviewed_by = Yii::$app->user->id;
        $version->updated_at = date('Y-m-d H:i:s');

        if (!$version->save(false)) {
            throw new \Exception('Unable to update version.');
        }
        AuditLog::record(
    'approved',
    'Post',
    $post->id,
    'Blog "' . $post->title . '" Version ' .
    $version->version . ' was approved and published.'
);

        $transaction->commit();

        Yii::$app->session->setFlash(
            'success',
            'Blog version approved successfully.'
        );

    } catch (\Throwable $e) {

        $transaction->rollBack();

        Yii::error($e->getMessage());

        throw $e;
    }

    return $this->redirect(['pending']);
}
    /**
     * Pending Posts
     */
    
public function actionPending()
{
    if (
        Yii::$app->user->identity->role != 'moderator' &&
        Yii::$app->user->identity->role != 'admin'
    ) {
        throw new ForbiddenHttpException('Access Denied');
    }

    $versions = PostVersion::find()
        ->where(['status' => PostVersion::STATUS_PENDING])
        ->orderBy(['created_at' => SORT_DESC])
        ->all();

    return $this->render('pending', [
        'versions' => $versions,
    ]);
}
    /**
     * Find Model
     */
    protected function findModel($id)
    {
        if (($model = Post::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}