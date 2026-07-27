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
    if (Yii::$app->user->identity->role != 'admin') {
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

    if (
        $model->status != Post::STATUS_PUBLISHED &&
        Yii::$app->user->identity->role != 'admin' &&
        Yii::$app->user->identity->role != 'moderator' &&
        $model->author_id != Yii::$app->user->id
    ) {
        throw new ForbiddenHttpException('Access Denied');
    }

    return $this->render('view', [
        'model' => $model,
    ]);
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
                'Your blog has been submitted for moderator approval.'
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

if (
    Yii::$app->user->identity->role == 'blogger' &&
    $model->author_id != Yii::$app->user->id
) {
    throw new ForbiddenHttpException('You cannot edit this post.');
}

if (
    Yii::$app->user->identity->role == 'blogger' &&
    $model->status == Post::STATUS_PUBLISHED
) {
    throw new ForbiddenHttpException('Published posts cannot be edited.');
}
    if ($model->load(Yii::$app->request->post()) && $model->save()) {

        Yii::$app->session->setFlash(
            'success',
            'Blog updated successfully.'
        );

        if (Yii::$app->user->identity->role == 'admin') {
    return $this->redirect(['index']);
}

return $this->redirect(['my-posts']);
    }

    return $this->render('update', [
        'model' => $model,
    ]);
}

    /**
     * Delete Post
     */
    public function actionDelete($id)
{
    $model = $this->findModel($id);

if (
    Yii::$app->user->identity->role == 'blogger' &&
    (
        $model->author_id != Yii::$app->user->id ||
        $model->status == Post::STATUS_PUBLISHED
    )
) {
    throw new ForbiddenHttpException('You cannot delete this post.');
}

    $model->delete();

    Yii::$app->session->setFlash(
        'success',
        'Blog deleted successfully.'
    );

   if (Yii::$app->user->identity->role == 'admin') {
    return $this->redirect(['index']);
}

return $this->redirect(['my-posts']);
}

    /**
     * Approve Post
     */
    public function actionApprove($id)
{
    if (
        Yii::$app->user->identity->role != 'moderator' &&
        Yii::$app->user->identity->role != 'admin'
    ) {
        throw new ForbiddenHttpException('Access Denied');
    }

    $post = $this->findModel($id);

    $post->status = Post::STATUS_PUBLISHED;
    $post->save(false);

    Yii::$app->session->setFlash('success', 'Blog approved successfully.');

    return $this->redirect(['pending']);
}

    /**
     * Reject Post
     */
    public function actionReject($id)
{
    if (
        Yii::$app->user->identity->role != 'moderator' &&
        Yii::$app->user->identity->role != 'admin'
    ) {
        throw new ForbiddenHttpException('Access Denied');
    }

    $post = $this->findModel($id);

    $post->status = Post::STATUS_REJECTED;
    $post->save(false);

    Yii::$app->session->setFlash('success', 'Blog rejected.');

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