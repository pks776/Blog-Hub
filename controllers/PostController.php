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
                'reject' => ['POST'],
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

        $model->author_id = Yii::$app->user->id;
        $model->status = Post::STATUS_PENDING;
        $model->slug = Inflector::slug($model->title);

        if ($model->save()) {
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

    // Blogger can edit only their own blogs
    if ($role == 'blogger') {

        if ($model->author_id != Yii::$app->user->id) {
            throw new ForbiddenHttpException('You cannot edit this blog.');
        }
    }

    // Admin & Moderator can edit every blog

    if ($model->load(Yii::$app->request->post())) {

        // If blogger edits, send it for approval again
        if ($role == 'blogger') {
            $model->status = Post::STATUS_PENDING;
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

    if ($post->save(false)) {
        Yii::$app->session->setFlash(
            'success',
            'Blog approved successfully.'
        );
    }

    return $this->redirect(['index']);
}

//reject post

public function actionReject($id)
{
    if (
        Yii::$app->user->identity->role != 'admin' &&
        Yii::$app->user->identity->role != 'moderator'
    ) {
        throw new ForbiddenHttpException('Access Denied');
    }

    $post = $this->findModel($id);

    $post->status = Post::STATUS_REJECTED;

    if ($post->save(false)) {
        Yii::$app->session->setFlash(
            'success',
            'Blog rejected successfully.'
        );
    }

    return $this->redirect(['index']);
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