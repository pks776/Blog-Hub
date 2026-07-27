<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class AdminController extends Controller
{
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        if (!Yii::$app->user->can('manageUsers')) {
            throw new ForbiddenHttpException('You are not authorized to access this page.');
        }

        return parent::beforeAction($action);
    }

    public function actionDashboard()
    {
        return $this->render('dashboard');
    }
}