<?php

use yii\helpers\Html;

/** @var app\models\Post $model */

$this->title = $model->title;
?>

<h1><?= Html::encode($model->title) ?></h1>

<hr>

<p>
    <strong>Author:</strong>
    <?= Html::encode($model->author->name ?? 'Unknown') ?>
</p>

<p>
    <strong>Published:</strong>
    <?= Yii::$app->formatter->asDate($model->created_at) ?>
</p>

<hr>

<div style="font-size:18px; line-height:1.8;">
    <?= nl2br(Html::encode($model->content)) ?>
</div>

<br>

<?= Html::a(
    '← Back',
    ['site/index'],
    ['class' => 'btn btn-secondary']
) ?>