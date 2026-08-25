<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ChangePasswordForm $model */

$this->title = 'Change Password';
?>

<div class="container mt-4">

    <h2 class="mb-4">Change Password</h2>

    <div class="card">
        <div class="card-body">

            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field(
                $model,
                'current_password'
            )->passwordInput() ?>

            <?= $form->field(
                $model,
                'new_password'
            )->passwordInput() ?>

            <?= $form->field(
                $model,
                'confirm_password'
            )->passwordInput() ?>

            <div class="mt-4">

                <?= Html::submitButton(
                    'Change Password',
                    ['class' => 'btn btn-warning me-2']
                ) ?>

                <?= Html::a(
                    'Cancel',
                    ['index'],
                    ['class' => 'btn btn-secondary']
                ) ?>

            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>

</div>