<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Html;

$items = [
    [
        'label' => 'Home',
        'url' => ['/site/index'],
    ],

    [
        'label' => 'Manage',
        'url' => Yii::$app->user->isGuest
            ? ['/site/login']
            : (
                Yii::$app->user->identity->role == 'admin'
                    ? ['/site/dashboard']
                    : (
                        Yii::$app->user->identity->role == 'moderator'
                            ? ['/post/pending']
                            : ['/post/my-posts']
                    )
            ),
        'visible' => !Yii::$app->user->isGuest,
    ],

    [
        'label' => 'Login',
        'url' => ['/site/login'],
        'visible' => Yii::$app->user->isGuest,
    ],

    [
        'label' => 'Logout (' . Html::encode(Yii::$app->user->identity?->name ?? '') . ')',
        'url' => ['/site/logout'],
        'linkOptions' => [
            'data-method' => 'post',
            'class' => 'nav-link logout',
        ],
        'visible' => !Yii::$app->user->isGuest,
    ],
];

?>
<header id="header">
    <?php NavBar::begin(
        [
            'brandLabel' => Yii::$app->name,
            'brandUrl' => Yii::$app->homeUrl,
            'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top']
        ],
    ) ?>
    <?= Nav::widget(
        [
            'options' => ['class' => 'navbar-nav me-auto'],
            'encodeLabels' => false,
            'items' => $items,
        ],
    ) ?>
    <?= Html::button(
        '&#127769;',
        [
            'id' => 'theme-toggle',
            'class' => 'btn btn-link nav-link fs-5',
            'aria-label' => 'Switch to dark mode',
        ],
    ) ?>
    <?php NavBar::end() ?>
</header>
