<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\User $user */

$this->title = 'Edit Profile';
?>

<div class="container mt-4">

    <h2 class="mb-4">Edit Profile</h2>

    <div class="card">
        <div class="card-body">

            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($user, 'name')->textInput([
                'maxlength' => true,
            ]) ?>

            <?= $form->field($user, 'email')->input('email', [
                'maxlength' => true,
            ]) ?>

            <div class="mb-3">
                <label class="form-label">Role</label>

                <input
                    type="text"
                    class="form-control"
                    value="<?= Html::encode(ucfirst($user->role)) ?>"
                    disabled
                >

                <small class="text-muted">
                    Role can only be changed by an Admin or Moderator.
                </small>
            </div>

            <div class="mt-4">

                <?= Html::submitButton(
                    'Save Changes',
                    ['class' => 'btn btn-success me-2']
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