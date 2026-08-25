<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use app\models\ChangePasswordForm;

class ProfileController extends Controller
{
    /**
     * Display logged-in user's profile.
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException(
                'Please login to access your profile.'
            );
        }

        $user = User::findOne(Yii::$app->user->id);

        if ($user === null) {
            throw new NotFoundHttpException(
                'User not found.'
            );
        }

        return $this->render('index', [
            'user' => $user,
        ]);
    }

    /**
     * Edit logged-in user's profile.
     */
    public function actionUpdate()
    {
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException(
                'Please login to access your profile.'
            );
        }

        $user = User::findOne(Yii::$app->user->id);

        if ($user === null) {
            throw new NotFoundHttpException(
                'User not found.'
            );
        }

        if ($user->load(Yii::$app->request->post())) {

            // Role is controlled by Admin/Moderator.
            // Never allow the user to change it.
            $user->role = $user->getOldAttribute('role');

            if ($user->save()) {

                Yii::$app->session->setFlash(
                    'success',
                    'Profile updated successfully.'
                );

                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash(
                'error',
                'Unable to update profile.'
            );
        }

        return $this->render('update', [
            'user' => $user,
        ]);
    }
    public function actionChangePassword()
{
    if (Yii::$app->user->isGuest) {
        throw new ForbiddenHttpException(
            'Please login to access your profile.'
        );
    }

    $user = User::findOne(Yii::$app->user->id);

    if ($user === null) {
        throw new NotFoundHttpException(
            'User not found.'
        );
    }

    $model = new ChangePasswordForm($user);

    if ($model->load(Yii::$app->request->post())) {

        if ($model->validate()) {

            if ($model->changePassword()) {

                Yii::$app->session->setFlash(
                    'success',
                    'Password changed successfully.'
                );

                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash(
                'error',
                'Unable to change password.'
            );
        }
    }

    return $this->render('change-password', [
        'model' => $model,
    ]);
}
}