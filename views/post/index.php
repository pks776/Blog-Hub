<?php

use app\models\Post;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\PostSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Posts';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="post-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->user->identity->role == 'blogger'): ?>

        <p>
            <?= Html::a('Create Post', ['create'], ['class' => 'btn btn-success']) ?>
        </p>

    <?php endif; ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'table table-bordered table-striped table-hover',
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
                    return $model->author ? $model->author->name : 'N/A';
                },
            ],

            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model) {

                    switch ($model->status) {

                        case Post::STATUS_PENDING:
                            return '<span class="badge bg-warning text-dark">Pending</span>';

                        case Post::STATUS_PUBLISHED:
                            return '<span class="badge bg-success">Published</span>';

                        case Post::STATUS_REJECTED:
                            return '<span class="badge bg-danger">Rejected</span>';

                        default:
                            return $model->status;
                    }
                },
            ],

            [
                'attribute' => 'created_at',
                'label' => 'Created On',
                'format' => ['date', 'php:d M Y'],
            ],

            [
                'class' => ActionColumn::class,

                'urlCreator' => function ($action, Post $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },

                'template' => '{view} {update} {delete}',

                'visibleButtons' => [

                    'view' => function () {
                        return in_array(
                            Yii::$app->user->identity->role,
                            ['admin', 'moderator']
                        );
                    },

                    'update' => function () {
                        return in_array(
                            Yii::$app->user->identity->role,
                            ['admin', 'moderator']
                        );
                    },

                    'delete' => function () {
                        return in_array(
                            Yii::$app->user->identity->role,
                            ['admin', 'moderator']
                        );
                    },

                ],

                'buttons' => [

    'view' => function ($url) {
        return Html::a('View', $url, [
            'class' => 'btn btn-info btn-sm me-1',
        ]);
    },

    'update' => function ($url) {
        return Html::a('Edit', $url, [
            'class' => 'btn btn-primary btn-sm me-1',
        ]);
    },

    'delete' => function ($url) {
        return Html::a('Delete', $url, [
            'class' => 'btn btn-danger btn-sm',
            'data' => [
                'confirm' => 'Are you sure you want to delete this blog?',
                'method' => 'post',
            ],
        ]);
    },
],
            ],

        ],
    ]); ?>

</div>