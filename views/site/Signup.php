<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\SignupForm $model */

$this->title = 'Register';
?>

<div class="site-signup">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>Create your BlogHub account.</p>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput() ?>

    <?= $form->field($model, 'confirmPassword')->passwordInput() ?>

    <div class="form-group mt-3">
        <?= Html::submitButton('Register', [
            'class' => 'btn btn-success'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
