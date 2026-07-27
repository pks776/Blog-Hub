<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $post \app\models\Post|null */

$this->title = isset($post) ? $post->title : 'Blog';
?>

<div class="container mt-5">

    <?php if (!isset($post)): ?>

        <div class="alert alert-danger">
            Blog data not found.
        </div>

    <?php else: ?>

        <div class="card shadow-sm">

            <div class="card-body">

                <h1><?= Html::encode($post->title) ?></h1>

                <p class="text-muted">
                    By
                    <strong>
                        <?= Html::encode($post->author ? $post->author->name : 'Unknown Author') ?>
                    </strong>
                    |
                    <?= Yii::$app->formatter->asDate($post->created_at) ?>
                </p>

                <hr>

                <?php if (!empty($post->image)): ?>

                    <img src="<?= Yii::getAlias('@web/uploads/') . $post->image ?>"
                         class="img-fluid rounded mb-4"
                         alt="<?= Html::encode($post->title) ?>">

                <?php endif; ?>

                <div style="font-size:17px; line-height:1.8;">
                    <?= nl2br(Html::encode($post->content)) ?>
                </div>

            </div>

        </div>

    <?php endif; ?>

</div>