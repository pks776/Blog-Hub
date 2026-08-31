<?php

use app\models\PostVersion;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $posts app\models\Post[] */

$this->title = 'BlogHub';
?>

<div class="container mt-5">

    <h1 class="text-center mb-5">Latest Blogs</h1>

    <?php if (empty($posts)): ?>

        <div class="alert alert-info text-center">
            No published blogs available.
        </div>

    <?php else: ?>

        <div class="row">

            <?php foreach ($posts as $post): ?>

                <?php
                // Get the currently published version of this blog
                $version = PostVersion::find()
                    ->where([
                        'post_id' => $post->id,
                        'status' => PostVersion::STATUS_PUBLISHED,
                    ])
                    ->orderBy([
                        'version' => SORT_DESC,
                    ])
                    ->one();
                ?>

                <?php if ($version === null): ?>
                    <?php continue; ?>
                <?php endif; ?>

                <div class="col-lg-4 col-md-6 mb-4">

                    <div class="card shadow-sm h-100">

                        <?php if (!empty($version->image)): ?>

                            <?= Html::img(
                                Yii::getAlias('@web') . '/uploads/posts/' . $version->image,
                                [
                                    'class' => 'card-img-top',
                                    'alt' => Html::encode($version->title),
                                    'style' => 'height:220px; width:100%; object-fit:cover;',
                                ]
                            ) ?>

                        <?php else: ?>

                            <div
                                class="d-flex align-items-center justify-content-center bg-light"
                                style="height:220px;"
                            >
                                <span class="text-muted">
                                    No Image Available
                                </span>
                            </div>

                        <?php endif; ?>


                        <div class="card-body d-flex flex-column">

                            <h4 class="card-title">
                                <?= Html::encode($version->title) ?>
                            </h4>


                            <p class="text-muted mb-2">

                                By

                                <strong>
                                    <?= Html::encode(
                                        $post->author
                                            ? $post->author->name
                                            : 'Unknown Author'
                                    ) ?>
                                </strong>

                                <br>

                               <?= Yii::$app->formatter->asDate($version->created_at) ?>

                            </p>


                            <p class="flex-grow-1">

                                <?= Html::encode(
                                    mb_substr(
                                        strip_tags($version->content),
                                        0,
                                        120
                                    )
                                ) ?>...

                            </p>


                            <?= Html::a(
                                'Read More',
                                [
                                    'post/view',
                                    'id' => $post->id,
                                ],
                                [
                                    'class' => 'btn btn-primary w-100',
                                ]
                            ) ?>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>