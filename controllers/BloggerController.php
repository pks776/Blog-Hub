<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class BloggerController extends Controller
{
   public function beforeAction($action)
{
    if (Yii::$app->user->isGuest) {
        return $this->redirect(['site/login']);
    }

    if (!Yii::$app->user->can('createPost')) {
        throw new \yii\web\ForbiddenHttpException('Access Denied');
    }

    return parent::beforeAction($action);
}
}