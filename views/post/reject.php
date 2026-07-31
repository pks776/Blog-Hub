<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var app\models\Post $model */
/** @var app\models\RejectForm $rejectForm */

$this->title = 'Reject Blog';
?>

<div class="post-reject">

    <h2>Reject Blog</h2>

    <p><strong>Title:</strong> <?= Html::encode($model->title) ?></p>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($rejectForm, 'reason')->textarea([
        'rows' => 6,
        'placeholder' => 'Enter the reason for rejecting this blog...'
    ]) ?>

    <div class="form-group mt-3">

        <?= Html::submitButton(
            'Reject Blog',
            ['class' => 'btn btn-danger']
        ) ?>

        <?= Html::a(
            'Cancel',
            ['index'],
            ['class' => 'btn btn-secondary']
        ) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>