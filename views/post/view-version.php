<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\PostVersion $version */

$this->title = 'Review Version';
?>

<div class="container mt-4">

    <h2><?= Html::encode($version->title) ?></h2>

    <hr>

    <p>
        <strong>Version:</strong>
        <?= Html::encode($version->version) ?>
    </p>

    <p>
        <strong>Status:</strong>

        <span class="badge bg-warning text-dark">
            <?= Html::encode(ucfirst($version->status)) ?>
        </span>
    </p>

    <p>
        <strong>Created:</strong>
        <?= Yii::$app->formatter->asDatetime($version->created_at) ?>
    </p>

    <?php if (!empty($version->image)): ?>

        <div class="mb-4">

            <img
                src="<?= Yii::getAlias('@web/uploads/posts/') . $version->image ?>"
                alt="<?= Html::encode($version->title) ?>"
                style="max-width: 500px; max-height: 350px; object-fit: cover;"
                class="img-thumbnail"
            >

        </div>

    <?php endif; ?>

    <div class="mb-4">

        <h4>Content</h4>

        <div class="border rounded p-3">
            <?= nl2br(Html::encode($version->content)) ?>
        </div>

    </div>

    <?= Html::a(
        '← Back to Pending Blogs',
        ['pending'],
        ['class' => 'btn btn-secondary']
    ) ?>

</div>