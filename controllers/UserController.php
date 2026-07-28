<?php

namespace app\controllers;

use app\models\User;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use Yii;

class UserController extends Controller
{
    public function behaviors()
{
    return [
        'access' => [
            'class' => \yii\filters\AccessControl::class,
            'only' => ['index', 'view', 'create', 'update', 'delete'],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['manageUsers'],
                ],
            ],
        ],
        'verbs' => [
            'class' => \yii\filters\VerbFilter::class,
            'actions' => [
                'delete' => ['POST'],
            ],
        ],
    ];
}

    /**
     * Lists all User models.
     */
    public function actionIndex()
    
    {
        if (!Yii::$app->user->can('manageUsers')) {
    throw new \yii\web\ForbiddenHttpException('Access Denied');
}
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('manageUsers')) {
    throw new \yii\web\ForbiddenHttpException('Access Denied');
}
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     */
public function actionCreate()
{
    if (!Yii::$app->user->can('manageUsers')) {
        throw new \yii\web\ForbiddenHttpException('Access Denied');
    }

    $model = new User();

    if ($this->request->isPost && $model->load($this->request->post())) {

        // Hash the password before saving
        $model->password_hash = Yii::$app->security
            ->generatePasswordHash($model->password_hash);

        if ($model->save()) {

            Yii::$app->session->setFlash(
                'success',
                'User created successfully.'
            );

            return $this->redirect([
                'view',
                'id' => $model->id
            ]);
        }

    } else {
        $model->loadDefaultValues();
    }

    return $this->render('create', [
        'model' => $model,
    ]);
}
 public function actionUpdate($id)
{
    if (!Yii::$app->user->can('manageUsers')) {
        throw new \yii\web\ForbiddenHttpException('Access Denied');
    }

    $model = $this->findModel($id);

    // Store the existing hashed password
    $oldPassword = $model->password_hash;

    if ($this->request->isPost && $model->load($this->request->post())) {

        // If a new password is entered, hash it
        if (!empty($model->password_hash)) {
            $model->password_hash = Yii::$app->security
                ->generatePasswordHash($model->password_hash);
        } else {
            // Keep the old hashed password
            $model->password_hash = $oldPassword;
        }

        if ($model->save()) {

            Yii::$app->session->setFlash(
                'success',
                'User updated successfully.'
            );

            return $this->redirect([
                'view',
                'id' => $model->id,
            ]);
        }
    }

    // Clear the password field so the hash isn't shown in the form
    $model->password_hash = '';

    return $this->render('update', [
        'model' => $model,
    ]);
}
    /**
     * Deletes an existing User model.
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('manageUsers')) {
    throw new \yii\web\ForbiddenHttpException('Access Denied');
}
     $model = $this->findModel($id);

if ($model->id == Yii::$app->user->id) {
    Yii::$app->session->setFlash(
        'error',
        'You cannot delete your own account.'
    );

    return $this->redirect(['index']);
}

$model->delete();

Yii::$app->session->setFlash(
    'success',
    'User deleted successfully.'
);

return $this->redirect(['index']);
    }
    /**
     * Finds the User model.
     */
    protected function findModel($id)
    {

        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}