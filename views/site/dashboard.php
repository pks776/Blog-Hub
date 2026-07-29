<?php

use yii\helpers\Html;

$this->title = 'Dashboard';

$user = Yii::$app->user->identity;
?>

<h1>Welcome, <?= Html::encode($user->name) ?> 👋</h1>

<p>You are successfully logged in.</p>

<p>
    Your Role:
    <strong><?= ucfirst($user->role) ?></strong>
</p>

<hr>

<?php if ($user->role == 'blogger'): ?>

    <p>
        <?= Html::a('Create Blog', ['post/create'], ['class' => 'btn btn-success']) ?>
    </p>

    <p>
        <?= Html::a('My Blogs', ['post/my-posts'], ['class' => 'btn btn-primary']) ?>
    </p>

<?php elseif ($user->role == 'moderator'): ?>

    <p>
        <?= Html::a('All Blogs', ['post/index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <p>
        <?= Html::a('Pending Blogs', ['post/pending'], ['class' => 'btn btn-warning']) ?>
    </p>

<?php elseif ($user->role == 'admin'): ?>

    <p>
        <?= Html::a('Manage Users', ['user/index'], ['class' => 'btn btn-danger']) ?>
    </p>

    <p>
        <?= Html::a('All Blogs', ['post/index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <p>
        <?= Html::a('Pending Blogs', ['post/pending'], ['class' => 'btn btn-warning']) ?>
    </p>

<?php endif; ?>

<hr>

<p>
    <?= Html::a('Logout', ['site/logout'], [
        'class' => 'btn btn-secondary',
        'data-method' => 'post',
    ]) ?>
</p>