<?php

use app\models\PostVersion;
use yii\helpers\Html;

/** @var app\models\Post $model */

// Get the currently published version
$version = PostVersion::find()
    ->where([
        'post_id' => $model->id,
        'status' => PostVersion::STATUS_PUBLISHED,
    ])
    ->orderBy([
        'version' => SORT_DESC,
    ])
    ->one();

// Fallback to the latest version if no published version is found
if ($version === null) {

    $version = PostVersion::find()
        ->where([
            'post_id' => $model->id,
        ])
        ->orderBy([
            'version' => SORT_DESC,
        ])
        ->one();
}

$this->title = $version
    ? $version->title
    : $model->title;
?>

<?php if ($version && !empty($version->image)): ?>

    <div class="mb-3">

        <?= Html::img(
            Yii::getAlias('@web/uploads/posts/') . $version->image,
            [
                'class' => 'img-fluid rounded',
                'style' => 'max-width:500px',
            ]
        ) ?>

    </div>

<?php endif; ?>


<h1>
    <?= Html::encode(
        $version
            ? $version->title
            : $model->title
    ) ?>
</h1>

<hr>

<p>
    <strong>Author:</strong>
    <?= Html::encode(
        $model->author->name ?? 'Unknown'
    ) ?>
</p>

<p>
    <strong>Created On:</strong>

    <?php if ($version): ?>

        <?= Yii::$app->formatter->asDatetime(
            $version->created_at
        ) ?>

    <?php else: ?>

        Not available

    <?php endif; ?>

</p>

<hr>

<div style="font-size:18px; line-height:1.8;">

    <?php if ($version): ?>

        <?= nl2br(
            Html::encode($version->content)
        ) ?>

    <?php else: ?>

        Content not available.

    <?php endif; ?>

</div>

<br>

<?= Html::a(
    '← Back',
    ['site/index'],
    [
        'class' => 'btn btn-secondary',
    ]
) ?>