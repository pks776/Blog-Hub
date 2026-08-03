<?php

namespace app\controllers;

use app\models\Post;
use app\models\PostSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Inflector;
use Yii;
use yii\web\ForbiddenHttpException;
use app\models\RejectForm;
use yii\web\UploadedFile;
class PostController extends Controller
{
    public function behaviors()
{
    return [
        'verbs' => [
            'class' => VerbFilter::class,
            'actions' => [
                'delete' => ['POST'],
                'approve' => ['POST'],
                'reject' => ['GET', 'POST'],
                'unpublish' => ['POST'],
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

    return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
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
     * Create Post
     */
    public function actionCreate()
    {
        if (Yii::$app->user->identity->role != 'blogger') {
            throw new ForbiddenHttpException('Access Denied');
        }

        $model = new Post();

        if ($model->load(Yii::$app->request->post())) {

            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');

            $model->author_id = Yii::$app->user->id;
            $model->status = Post::STATUS_PENDING;
            $model->slug = Inflector::slug($model->title);

            // Validate FIRST
            if ($model->validate()) {

                if ($model->imageFile) {

                    $fileName = uniqid() . '.' . $model->imageFile->extension;

                    $model->imageFile->saveAs(
                        Yii::getAlias('@webroot/uploads/posts/') . $fileName
                    );

                    $model->image = $fileName;
                }

                // Save without validating again
                $model->save(false);

                Yii::$app->session->setFlash(
                    'success',
                    'Your blog has been submitted for Admin approval.'
                );

                return $this->redirect(['my-posts']);
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

    // Store old image
    $oldImage = $model->image;

    // Blogger can edit only their own blogs
    if ($role == 'blogger') {

        if ($model->author_id != Yii::$app->user->id) {
            throw new ForbiddenHttpException('You cannot edit this blog.');
        }
    }

    // Admin & Moderator can edit every blog

    if ($model->load(Yii::$app->request->post())) {

        // Get uploaded image
        $model->imageFile = UploadedFile::getInstance($model, 'imageFile');

        // If a new image is uploaded
        if ($model->imageFile) {

            // Delete old image if it exists
            if (
                $oldImage &&
                file_exists(Yii::getAlias('@webroot/uploads/posts/') . $oldImage)
            ) {
                unlink(Yii::getAlias('@webroot/uploads/posts/') . $oldImage);
            }

            // Generate unique filename
            $fileName = time() . '_' .
                $model->imageFile->baseName . '.' .
                $model->imageFile->extension;

            // Save image
            $model->imageFile->saveAs(
                Yii::getAlias('@webroot/uploads/posts/') . $fileName
            );

            // Save filename in database
            $model->image = $fileName;

        } else {

            // Keep existing image
            $model->image = $oldImage;
        }

        // Blogger edits go back for approval
        if ($role == 'blogger') {

            $model->status = Post::STATUS_PENDING;

            // Clear previous rejection comment
            $model->rejection_reason = null;
        }

        if ($model->save()) {

            Yii::$app->session->setFlash(
                'success',
                ($role == 'blogger')
                    ? 'Blog updated and sent for approval.'
                    : 'Blog updated successfully.'
            );

            return ($role == 'blogger')
                ? $this->redirect(['my-posts'])
                : $this->redirect(['index']);
        }
    }

    return $this->render('update', [
        'model' => $model,
    ]);
}
//delete

public function actionDelete($id)
{
    $model = $this->findModel($id);
    $role = Yii::$app->user->identity->role;

    if ($role == 'blogger') {

        if (
            $model->author_id != Yii::$app->user->id ||
            $model->status == Post::STATUS_PUBLISHED
        ) {
            throw new ForbiddenHttpException('You cannot delete this blog.');
        }
    }

    if (
    $model->image &&
    file_exists(Yii::getAlias('@webroot/uploads/posts/') . $model->image)
) {
    unlink(Yii::getAlias('@webroot/uploads/posts/') . $model->image);
}
    $model->delete();

    Yii::$app->session->setFlash(
        'success',
        'Blog deleted successfully.'
    );

    return ($role == 'blogger')
        ? $this->redirect(['my-posts'])
        : $this->redirect(['index']);
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

   $post->status = Post::STATUS_PUBLISHED;
$post->rejection_reason = null;

    if ($post->save(false)) {
        Yii::$app->session->setFlash(
            'success',
            'Blog approved successfully.'
        );
    }

    return $this->redirect(['index']);
}
//action unpublish

public function actionUnpublish($id)
{
    if (
        Yii::$app->user->identity->role != 'admin' &&
        Yii::$app->user->identity->role != 'moderator'
    ) {
        throw new ForbiddenHttpException('Access Denied');
    }

    $post = $this->findModel($id);

    $post->status = Post::STATUS_PENDING;
    $post->rejection_reason = null;

    if ($post->save(false)) {

        Yii::$app->session->setFlash(
            'success',
            'Blog unpublished successfully.'
        );
    }

    return $this->redirect(['index']);
}
//reject post

public function actionReject($id)
{
    if (!in_array(Yii::$app->user->identity->role, ['admin', 'moderator'])) {
        throw new ForbiddenHttpException('Access Denied');
    }

    $post = $this->findModel($id);
    $rejectForm = new RejectForm();

    if ($rejectForm->load(Yii::$app->request->post()) && $rejectForm->validate()) {

        $post->status = Post::STATUS_REJECTED;
        $post->rejection_reason = $rejectForm->reason;

        if ($post->save(false)) {

            Yii::$app->session->setFlash(
                'success',
                'Blog rejected successfully.'
            );

            return $this->redirect(['index']);
        }
    }

    return $this->render('reject', [
        'model' => $post,
        'rejectForm' => $rejectForm,
    ]);
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

    $posts = Post::find()
        ->where(['status' => Post::STATUS_PENDING])
        ->orderBy(['created_at' => SORT_DESC])
        ->all();

    return $this->render('pending', [
        'posts' => $posts,
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