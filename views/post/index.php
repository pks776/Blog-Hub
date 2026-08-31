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
        !Yii::$app->user->isGuest &&
        Yii::$app->user->identity->role === 'blogger'
    ): ?>

        <p>
            <?= Html::a(
                'Create Post',
                ['create'],
                [
                    'class' => 'btn btn-success',
                ]
            ) ?>
        </p>

    <?php endif; ?>


    <?= GridView::widget([

        'dataProvider' => $dataProvider,

        'filterModel' => $searchModel,

        'tableOptions' => [
            'class' =>
                'table table-bordered table-striped table-hover align-middle',
        ],

        'columns' => [

            // ==========================================
            // ID
            // ==========================================

            [
                'attribute' => 'id',
                'label' => 'ID',
                'headerOptions' => [
                    'style' => 'width:70px;',
                ],
            ],


            // ==========================================
            // TITLE
            // ==========================================

            [
                'attribute' => 'title',
                'label' => 'Title',
            ],


            // ==========================================
            // AUTHOR
            // ==========================================

            [
                'attribute' => 'author_id',
                'label' => 'Author',

                'value' => function ($model) {

                    return $model->author
                        ? $model->author->name
                        : 'N/A';
                },
            ],


            // ==========================================
            // VERSION
            // ==========================================

            [
                'label' => 'Version',

                'value' => function ($model) {

                    $version = PostVersion::find()
                        ->where([
                            'post_id' => $model->id,
                        ])
                        ->orderBy([
                            'version' => SORT_DESC,
                        ])
                        ->one();

                    return $version
                        ? 'V' . $version->version
                        : '—';
                },

                'headerOptions' => [
                    'style' => 'width:100px;',
                ],
            ],


            // ==========================================
            // STATUS
            // ==========================================

            [
                'attribute' => 'status',
                'label' => 'Status',
                'format' => 'raw',

                'value' => function ($model) {

                    switch ($model->status) {

                        case Post::STATUS_PENDING:

                            return '<span class="badge bg-warning text-dark px-3 py-2">
                                        Pending
                                    </span>';

                        case Post::STATUS_PUBLISHED:

                            return '<span class="badge bg-success px-3 py-2">
                                        Published
                                    </span>';

                        case Post::STATUS_REJECTED:

                            return '<span class="badge bg-danger px-3 py-2">
                                        Rejected
                                    </span>';

                        case Post::STATUS_UNPUBLISHED:

                            return '<span class="badge bg-secondary px-3 py-2">
                                        Unpublished
                                    </span>';

                        case Post::STATUS_DRAFT:

                            return '<span class="badge bg-light text-dark px-3 py-2">
                                        Draft
                                    </span>';

                        case Post::STATUS_DELETED:

                            return '<span class="badge bg-dark px-3 py-2">
                                        Deleted
                                    </span>';

                        default:

                            return '<span class="badge bg-secondary px-3 py-2">'
                                . Html::encode($model->status)
                                . '</span>';
                    }
                },

                'headerOptions' => [
                    'style' => 'width:150px;',
                ],
            ],


            // ==========================================
            // ACTIONS
            // ==========================================

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

                        $pendingVersion = PostVersion::find()
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
                                'id' => $pendingVersion->id,
                            ]);
                        }

                        return '#';
                    }

                    /*
                     * All other actions
                     * use Post ID.
                     */
                    return Url::toRoute([
                        $action,
                        'id' => $model->id,
                    ]);
                },


                /*
                 * Keep all existing actions.
                 */
                'template' =>
                    '{view} {update} {delete} {approve} {reject} {unpublish} {publish}',


                // ==========================================
                // BUTTON VISIBILITY
                // ==========================================

                'visibleButtons' => [

                    /*
                     * View
                     */
                    'view' => function ($model) {

                        return true;
                    },


                    /*
                     * Update
                     */
                    'update' => function ($model) {

                        return
                            Yii::$app->user->identity->role === 'blogger'
                            &&
                            $model->author_id == Yii::$app->user->id;
                    },


                    /*
                     * Delete
                     */
                    'delete' => function ($model) {

                        $role =
                            Yii::$app->user->identity->role;

                        /*
                         * Admin and Moderator
                         */
                        if (
                            in_array(
                                $role,
                                ['admin', 'moderator']
                            )
                        ) {
                            return true;
                        }

                        /*
                         * Blogger
                         */
                        return
                            $role === 'blogger'
                            &&
                            $model->author_id == Yii::$app->user->id
                            &&
                            $model->status !== Post::STATUS_PUBLISHED;
                    },


                    /*
                     * Publish
                     */
                    'publish' => function ($model) {

                        return
                            in_array(
                                Yii::$app->user->identity->role,
                                ['admin', 'moderator']
                            )
                            &&
                            $model->status === Post::STATUS_UNPUBLISHED;
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
                            $model->status === Post::STATUS_PENDING;
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
                         * a pending version exists.
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
                            $model->status === Post::STATUS_PUBLISHED;
                    },
                ],


                // ==========================================
                // BUTTONS
                // ==========================================

                'buttons' => [

                    /*
                     * View
                     */
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


                    /*
                     * Edit
                     */
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


                    /*
                     * Delete
                     */
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


                    /*
                     * Approve
                     */
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


                    /*
                     * Reject
                     */
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


                    /*
                     * Unpublish
                     */
                    'unpublish' => function ($url) {

                        return Html::a(
                            'Unpublish',
                            $url,
                            [
                                'class' =>
                                    'btn btn-secondary btn-sm me-1',

                                'data' => [
                                    'method' => 'post',
                                    'confirm' =>
                                        'Unpublish this blog?',
                                ],
                            ]
                        );
                    },


                    /*
                     * Publish
                     */
                    'publish' => function ($url) {

                        return Html::a(
                            'Publish',
                            $url,
                            [
                                'class' =>
                                    'btn btn-success btn-sm me-1',

                                'data' => [
                                    'method' => 'post',
                                    'confirm' =>
                                        'Publish this blog again?',
                                ],
                            ]
                        );
                    },
                ],
            ],

        ],

    ]) ?>

</div>