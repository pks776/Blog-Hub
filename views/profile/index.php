<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\User $user */

$this->title = 'My Profile';
?>

<div class="container mt-4">

    <h2 class="mb-4">My Profile</h2>

    <div class="card">

        <div class="card-body">

            <div class="mb-3">
                <strong>Name</strong>
                <div>
                    <?= Html::encode($user->name) ?>
                </div>
            </div>

            <div class="mb-3">
                <strong>Email</strong>
                <div>
                    <?= Html::encode($user->email) ?>
                </div>
            </div>

            <div class="mb-3">
                <strong>Role</strong>
                <div>
                    <?= Html::encode(ucfirst($user->role)) ?>
                </div>
            </div>

            <div class="mt-4">

                <?= Html::a(
                    'Edit Profile',
                    ['update'],
                    ['class' => 'btn btn-primary me-2']
                ) ?>

                <?= Html::a(
                    'Change Password',
                    ['change-password'],
                    ['class' => 'btn btn-warning']
                ) ?>

            </div>

        </div>

    </div>

</div>