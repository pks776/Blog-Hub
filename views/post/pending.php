<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $versions app\models\PostVersion[] */

$this->title = 'Pending Blogs';
?>

<div class="container mt-4">

    <h2 class="mb-4">Pending Blogs</h2>

    <?php if (empty($versions)): ?>

        <div class="alert alert-info">
            No pending blogs found.
        </div>

    <?php else: ?>

        <table class="table table-bordered table-striped">

            <thead class="table-dark">
                <tr>
                    <th>Post ID</th>
                    <th>Version</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th width="220">Actions</th>
                </tr>
            </thead>

            <tbody>

            <?php foreach ($versions as $version): ?>

                <tr>

                    <td>
                        <?= Html::encode($version->post_id) ?>
                    </td>

                    <td>
                        <strong>
                            Version <?= Html::encode($version->version) ?>
                        </strong>
                    </td>

                    <td>
                        <?= Html::encode($version->title) ?>
                    </td>

                    <td>
                        <?= Html::encode(
                            $version->post->author->name ?? 'Unknown Author'
                        ) ?>
                    </td>

                    <td>
                        <span class="badge bg-warning text-dark">
                            <?= Html::encode(
                                ucfirst($version->status)
                            ) ?>
                        </span>
                    </td>

                    <td>
                        <?= Yii::$app->formatter->asDate(
                            $version->created_at
                        ) ?>
                    </td>

                    <td>

                        <!-- View Version -->
                        <?= Html::a(
                            'View',
                            [
                                '/post/view-version',
                                'id' => $version->id,
                            ],
                            [
                                'class' => 'btn btn-info btn-sm',
                            ]
                        ) ?>

                        <!-- Approve Version -->
                        <?= Html::a(
                            'Approve',
                            [
                                '/post/approve-version',
                                'id' => $version->id,
                            ],
                            [
                                'class' => 'btn btn-success btn-sm',
                                'data' => [
                                    'confirm' => 'Approve this version?',
                                    'method' => 'post',
                                ],
                            ]
                        ) ?>

                        <!-- Reject Version -->
                        <?= Html::a(
                            'Reject',
                            [
                                '/post/reject-version',
                                'id' => $version->id,
                            ],
                            [
                                'class' => 'btn btn-danger btn-sm',
                            ]
                        ) ?>

                    </td>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    <?php endif; ?>

</div>