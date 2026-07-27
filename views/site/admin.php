<?php

use yii\helpers\Html;

$this->title = 'Admin Dashboard';
?>

<h1>Admin Dashboard</h1>

<p>Welcome <?= Html::encode(Yii::$app->user->identity->name) ?>!</p>

<p>You are logged in as <strong>Admin</strong>.</p>