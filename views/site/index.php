<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\web\View $this */
/** @var int $totalUsers */
/** @var int $totalPosts */
/** @var int $publishedPosts */
/** @var int $draftPosts */

$this->title = 'Dashboard';
?>

<div class="site-index">

    <h1 class="mb-4">📊 BlogHub Dashboard</h1>

    <div class="row">

        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5>Total Users</h5>
                    <h2><?= $totalUsers ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5>Total Posts</h5>
                    <h2><?= $totalPosts ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5>Published Posts</h5>
                    <h2><?= $publishedPosts ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5>Draft Posts</h5>
                    <h2><?= $draftPosts ?></h2>
                </div>
            </div>
        </div>

    </div>

</div>