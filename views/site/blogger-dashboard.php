<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var int $myPosts */
/** @var int $publishedPosts */
/** @var int $pendingPosts */
/** @var int $rejectedPosts */

$this->title = 'My Dashboard';

?>

<style>
    .blogger-dashboard {
        padding: 30px 10px;
    }

    .dashboard-header {
        margin-bottom: 35px;
    }

    .dashboard-header .eyebrow {
        font-size: 15px;
        letter-spacing: 1px;
        color: #9ca3af;
        margin-bottom: 8px;
    }

    .dashboard-header h1 {
        font-size: 40px;
        font-weight: 700;
        margin: 0 0 8px;
    }

    .dashboard-header p {
        color: #9ca3af;
        font-size: 17px;
        margin: 0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        position: relative;
        min-height: 190px;
        padding: 25px;
        background: #212529;
        border: 1px solid #343a40;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.25s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        border-color: #555;
    }

    .stat-card::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
        height: 5px;
    }

    .stat-card.posts::after {
        background: #8b5cf6;
    }

    .stat-card.published::after {
        background: #22c55e;
    }

    .stat-card.pending::after {
        background: #f59e0b;
    }

    .stat-card.rejected::after {
        background: #ef4444;
    }

    .stat-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stat-title {
        font-size: 18px;
        color: #b8c0cc;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 14px;
        font-size: 24px;
    }

    .posts .stat-icon {
        background: rgba(139, 92, 246, 0.15);
    }

    .published .stat-icon {
        background: rgba(34, 197, 94, 0.15);
    }

    .pending .stat-icon {
        background: rgba(245, 158, 11, 0.15);
    }

    .rejected .stat-icon {
        background: rgba(239, 68, 68, 0.15);
    }

    .stat-number {
        font-size: 44px;
        font-weight: 700;
        margin-top: 20px;
    }

    .posts .stat-number {
        color: #a78bfa;
    }

    .published .stat-number {
        color: #22c55e;
    }

    .pending .stat-number {
        color: #f59e0b;
    }

    .rejected .stat-number {
        color: #ef4444;
    }

    .stat-description {
        color: #9ca3af;
        margin-top: 5px;
    }

    .dashboard-actions {
        padding: 25px;
        background: #212529;
        border: 1px solid #343a40;
        border-radius: 20px;
    }

    .dashboard-actions h3 {
        font-size: 23px;
        margin-bottom: 8px;
    }

    .dashboard-actions p {
        color: #9ca3af;
        margin-bottom: 20px;
    }

    @media (max-width: 992px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .dashboard-header h1 {
            font-size: 30px;
        }
    }
</style>


<div class="blogger-dashboard">

    <!-- Header -->

    <div class="dashboard-header">

        <div class="eyebrow">
            BLOGHUB CREATOR SPACE
        </div>

        <h1>
            Welcome to the Dashboard,
            <?= Html::encode(Yii::$app->user->identity->name) ?> 👋
        </h1>

        <p>
            Create, manage and track your blogs from one place.
        </p>

    </div>


    <!-- Statistics -->

    <div class="stats-grid">


        <!-- My Posts -->

        <div class="stat-card posts">

            <div class="stat-top">

                <div class="stat-title">
                    My Posts
                </div>

                <div class="stat-icon">
                    ✍️
                </div>

            </div>

            <div class="stat-number">
                <?= $myPosts ?>
            </div>

            <div class="stat-description">
                Total blogs created by you
            </div>

        </div>


        <!-- Published -->

        <div class="stat-card published">

            <div class="stat-top">

                <div class="stat-title">
                    Published
                </div>

                <div class="stat-icon">
                    ✅
                </div>

            </div>

            <div class="stat-number">
                <?= $publishedPosts ?>
            </div>

            <div class="stat-description">
                Blogs currently live
            </div>

        </div>


        <!-- Pending -->

        <div class="stat-card pending">

            <div class="stat-top">

                <div class="stat-title">
                    Pending
                </div>

                <div class="stat-icon">
                    ⏳
                </div>

            </div>

            <div class="stat-number">
                <?= $pendingPosts ?>
            </div>

            <div class="stat-description">
                Waiting for approval
            </div>

        </div>


        <!-- Rejected -->

        <div class="stat-card rejected">

            <div class="stat-top">

                <div class="stat-title">
                    Rejected
                </div>

                <div class="stat-icon">
                    ⚠️
                </div>

            </div>

            <div class="stat-number">
                <?= $rejectedPosts ?>
            </div>

            <div class="stat-description">
                Blogs needing revision
            </div>

        </div>

    </div>


    <!-- Quick Actions -->

    
</div>