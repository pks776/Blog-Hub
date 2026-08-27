<?php

use app\models\Post;
use app\models\PostVersion;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\grid\ActionColumn;

/**
 * @var yii\web\View $this
 * @var app\models\PostSearch $searchModel
 * @var yii\data\ActiveDataProvider $dataProvider
 */

$this->title = 'Posts';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="post-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (
        Yii::$app->user->identity->role == 'blogger'
    ): ?>

        <p>
            <?= Html::a(
                'Create Post',
                ['create'],
                ['class' => 'btn btn-success']
            ) ?>
        </p>

    <?php endif; ?>


    <?= GridView::widget([

        'dataProvider' => $dataProvider,

        'filterModel' => $searchModel,

        'tableOptions' => [
            'class' =>
                'table table-bordered table-striped table-hover',
        ],

        'columns' => [

            'id',

            [
                'attribute' => 'title',
                'label' => 'Title',
            ],

            [
                'attribute' => 'content',
                'format' => 'ntext',
            ],

            [
                'attribute' => 'author_id',
                'label' => 'Author',

                'value' => function ($model) {

                    return $model->author
                        ? $model->author->name
                        : 'N/A';
                },
            ],

            /*
             * Status
             */
            [
                'attribute' => 'status',

                'format' => 'raw',

                'value' => function ($model) {

                    switch ($model->status) {

                        case Post::STATUS_PENDING:

                            return
                                '<span class="badge bg-warning text-dark">'
                                . 'Pending'
                                . '</span>';

                        case Post::STATUS_PUBLISHED:

                            return
                                '<span class="badge bg-success">'
                                . 'Published'
                                . '</span>';

                        case Post::STATUS_REJECTED:

                            return
                                '<span class="badge bg-danger">'
                                . 'Rejected'
                                . '</span>';

                        default:

                            return Html::encode(
                                $model->status
                            );
                    }
                },
            ],

            /*
             * Moderator Feedback
             */
            [
                'label' => 'Moderator Feedback',

                'value' => function ($model) {

                    $rejectedVersion =
                        PostVersion::find()
                            ->where([
                                'post_id' => $model->id,
                                'status' =>
                                    PostVersion::STATUS_REJECTED,
                            ])
                            ->orderBy([
                                'version' => SORT_DESC,
                            ])
                            ->one();

                    if (
                        $rejectedVersion &&
                        $rejectedVersion->rejection_reason
                    ) {
                        return
                            $rejectedVersion->rejection_reason;
                    }

                    return '-';
                },
            ],

            /*
             * Created Date
             */
            [
                'attribute' => 'created_at',

                'label' => 'Created On',

                'format' => [
                    'date',
                    'php:d M Y',
                ],
            ],

            /*
             * Actions
             */
            [
                'class' => ActionColumn::class,

                /*
                 * Generate URLs
                 */
                'urlCreator' => function (
                    $action,
                    Post $model,
                    $key,
                    $index,
                    $column
                ) {

                    /*
                     * Reject must use the
                     * PostVersion ID.
                     */
                    if ($action === 'reject') {

                        $pendingVersion =
                            PostVersion::find()
                                ->where([
                                    'post_id' => $model->id,
                                    'status' =>
                                        PostVersion::STATUS_PENDING,
                                ])
                                ->orderBy([
                                    'version' => SORT_DESC,
                                ])
                                ->one();

                        if ($pendingVersion) {

                            return Url::toRoute([
                                'reject-version',
                                'id' =>
                                    $pendingVersion->id,
                            ]);
                        }

                        return '#';
                    }

                    /*
                     * Other actions use Post ID
                     */
                    return Url::toRoute([
                        $action,
                        'id' => $model->id,
                    ]);
                },

                'template' => '{view} {update} {delete} {approve} {reject} {unpublish} {publish}',

                /*
                 * Which buttons are visible
                 */
                'visibleButtons' => [

                    'view' => function ($model) {
                        return true;
                    },

                    'update' => function ($model) {

                        return
                            Yii::$app->user->identity->role
                                == 'blogger'
                            &&
                            $model->author_id
                                == Yii::$app->user->id;
                    },

                    'delete' => function ($model) {

                        $role =
                            Yii::$app->user->identity->role;

                        if (
                            in_array(
                                $role,
                                ['admin', 'moderator']
                            )
                        ) {
                            return true;
                        }

                        return
                            $role == 'blogger'
                            &&
                            $model->author_id
                                == Yii::$app->user->id
                            &&
                            $model->status
                                != Post::STATUS_PUBLISHED;
                    },
                    'publish' => function ($model) {
    return in_array(
        Yii::$app->user->identity->role,
        ['admin', 'moderator']
    )
    && $model->status === Post::STATUS_UNPUBLISHED;
},

                    /*
                     * Approve
                     */
                    'approve' => function ($model) {

                        return
                            in_array(
                                Yii::$app->user->identity->role,
                                ['admin', 'moderator']
                            )
                            &&
                            $model->status
                                == Post::STATUS_PENDING;
                    },

                    /*
                     * Reject
                     */
                    'reject' => function ($model) {

                        if (
                            !in_array(
                                Yii::$app->user->identity->role,
                                ['admin', 'moderator']
                            )
                        ) {
                            return false;
                        }

                        /*
                         * Show Reject only when
                         * there is a pending version.
                         */
                        return PostVersion::find()
                            ->where([
                                'post_id' => $model->id,
                                'status' =>
                                    PostVersion::STATUS_PENDING,
                            ])
                            ->exists();
                    },

                    /*
                     * Unpublish
                     */
                    'unpublish' => function ($model) {

                        return
                            in_array(
                                Yii::$app->user->identity->role,
                                ['admin', 'moderator']
                            )
                            &&
                            $model->status
                                == Post::STATUS_PUBLISHED;
                    },
                ],

                /*
                 * Buttons
                 */
                'buttons' => [

                    'view' => function ($url) {

                        return Html::a(
                            'View',
                            $url,
                            [
                                'class' =>
                                    'btn btn-info btn-sm me-1',
                            ]
                        );
                    },

                    'publish' => function ($url) {
    return Html::a('Publish', $url, [
        'class' => 'btn btn-success btn-sm me-1',
        'data' => [
            'method' => 'post',
            'confirm' => 'Publish this blog again?',
        ],
    ]);
},

                    'update' => function ($url) {

                        return Html::a(
                            'Edit',
                            $url,
                            [
                                'class' =>
                                    'btn btn-primary btn-sm me-1',
                            ]
                        );
                    },

                    'delete' => function ($url) {

                        return Html::a(
                            'Delete',
                            $url,
                            [
                                'class' =>
                                    'btn btn-danger btn-sm me-1',

                                'data' => [
                                    'confirm' =>
                                        'Are you sure you want to delete this blog?',
                                    'method' => 'post',
                                ],
                            ]
                        );
                    },

                    'approve' => function ($url) {

                        return Html::a(
                            'Approve',
                            $url,
                            [
                                'class' =>
                                    'btn btn-success btn-sm me-1',

                                'data' => [
                                    'method' => 'post',
                                    'confirm' =>
                                        'Approve this blog?',
                                ],
                            ]
                        );
                    },

                    'reject' => function ($url) {

                        return Html::a(
                            'Reject',
                            $url,
                            [
                                'class' =>
                                    'btn btn-warning btn-sm me-1',
                            ]
                        );
                    },

                    'unpublish' => function ($url) {

                        return Html::a(
                            'Unpublish',
                            $url,
                            [
                                'class' =>
                                    'btn btn-secondary btn-sm',

                                'data' => [
                                    'method' => 'post',
                                    'confirm' =>
                                        'Unpublish this blog?',
                                ],
                            ]
                        );
                    },
                ],
            ],
        ],
    ]); ?>

</div>