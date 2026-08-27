<?php

namespace app\controllers;

use Yii;
use app\models\AuditLog;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class AuditLogController extends Controller
{
    /**
     * Display audit history.
     *
     * Only Admin and Moderator can view history.
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException(
                'Please login to access history.'
            );
        }

        $role = Yii::$app->user->identity->role;

        if (!in_array($role, ['admin', 'moderator'])) {
            throw new ForbiddenHttpException(
                'Access Denied'
            );
        }

        $logs = AuditLog::find()
            ->with('user')
            ->orderBy([
                'created_at' => SORT_DESC,
            ])
            ->all();

        return $this->render('index', [
            'logs' => $logs,
        ]);
    }
}