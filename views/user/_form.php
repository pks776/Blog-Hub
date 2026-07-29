<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\User $model */

?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

  <?= $form->field($model, 'password_hash')->passwordInput([
    'maxlength' => true,
    'value' => '',
    'placeholder' => 'Enter Password',
]) ?>

 <?= $form->field($model, 'role')->dropDownList([
    'admin' => 'Admin',
    'moderator' => 'Moderator',
    'blogger' => 'Blogger',
], [
    'prompt' => 'Select Role'
]) ?>

    <?= $form->field($model, 'status')->dropDownList([
        1 => 'Active',
        0 => 'Inactive',
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>      