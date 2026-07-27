<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class ModeratorController extends Controller
{
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        if (
            !Yii::$app->user->can('approvePost') &&
            !Yii::$app->user->can('rejectPost')
        ) {
            throw new ForbiddenHttpException('Access Denied');
        }

        return parent::beforeAction($action);
    }

    public function actionDashboard()
    {
        return $this->render('dashboard');
    }
}   