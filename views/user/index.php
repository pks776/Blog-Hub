<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        'id',
        'name',
        'email:email',
        'role',
        [
            'attribute' => 'status',
            'value' => function ($model) {
                return $model->status ? 'Active' : 'Inactive';
            },
        ],
        [
    'class' => ActionColumn::class,

    'template' => '{view} {update} {delete} {makeModerator} {removeModerator}',

    'urlCreator' => function ($action, User $model, $key, $index, $column) {
        return Url::toRoute([$action, 'id' => $model->id]);
    },

    'buttons' => [

        'makeModerator' => function ($url, User $model) {

            if (
                Yii::$app->user->identity->role === 'admin' &&
                $model->role === 'blogger' &&
                $model->id != Yii::$app->user->id
            ) {

                return Html::a(
                    'Make Moderator',
                    ['make-moderator', 'id' => $model->id],
                    [
                        'class' => 'btn btn-sm btn-primary',
                        'title' => 'Make Moderator',
                        'data' => [
                            'confirm' => 'Promote this user to Moderator?',
                            'method' => 'post',
                        ],
                    ]
                );
            }

            return '';
        },

        'removeModerator' => function ($url, User $model) {

            if (
                Yii::$app->user->identity->role === 'admin' &&
                $model->role === 'moderator' &&
                $model->id != Yii::$app->user->id
            ) {

                return Html::a(
                    'Remove Moderator',
                    ['remove-moderator', 'id' => $model->id],
                    [
                        'class' => 'btn btn-sm btn-warning',
                        'title' => 'Remove Moderator',
                        'data' => [
                            'confirm' => 'Remove Moderator role?',
                            'method' => 'post',
                        ],
                    ]
                );
            }

            return '';
        },

    ],
],
    ],
]); ?>