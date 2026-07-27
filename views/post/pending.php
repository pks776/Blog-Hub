<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $posts app\models\Post[] */

$this->title = 'Pending Blogs';
?>

<div class="container mt-4">

    <h2 class="mb-4">Pending Blogs</h2>

    <?php if (empty($posts)): ?>

        <div class="alert alert-info">
            No pending blogs found.
        </div>

    <?php else: ?>

        <table class="table table-bordered table-striped">

            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th width="220">Actions</th>
                </tr>
            </thead>

            <tbody>

            <?php foreach ($posts as $post): ?>

                <tr>

                    <td><?= $post->id ?></td>

                    <td><?= Html::encode($post->title) ?></td>

                    <td><?= Html::encode($post->author->name) ?></td>

                    <td>
                        <span class="badge bg-warning text-dark">
                            <?= ucfirst($post->status) ?>
                        </span>
                    </td>

                    <td>
                        <?= Yii::$app->formatter->asDate($post->created_at) ?>
                    </td>

                    <td>

                        <?= Html::a(
                            'View',
                            ['view', 'id' => $post->id],
                            ['class' => 'btn btn-info btn-sm']
                        ) ?>

                        <?= Html::a(
                            'Approve',
                            ['approve', 'id' => $post->id],
                            [
                                'class' => 'btn btn-success btn-sm',
                                'data' => [
                                    'confirm' => 'Approve this blog?',
                                    'method' => 'post',
                                ],
                            ]
                        ) ?>

                        <?= Html::a(
                            'Reject',
                            ['reject', 'id' => $post->id],
                            [
                                'class' => 'btn btn-danger btn-sm',
                                'data' => [
                                    'confirm' => 'Reject this blog?',
                                    'method' => 'post',
                                ],
                            ]
                        ) ?>

                    </td>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    <?php endif; ?>

</div>