<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var int $totalPosts
 * @var int $publishedPosts
 * @var int $pendingPosts
 * @var int $rejectedPosts
 */

$this->title = 'BlogHub';
?>

<style>
    .blogger-dashboard {
        padding: 30px 10px;
    }

    .dashboard-header {
        margin-bottom: 35px;
    }

    .dashboard-label {
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: #8b8fa3;
        margin-bottom: 8px;
    }

    .dashboard-title {
        font-size: 38px;
        font-weight: 700;
        margin: 0;
        color: #f5f5f7;
    }

    .dashboard-subtitle {
        margin-top: 8px;
        color: #a7aab7;
        font-size: 16px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }

    .stat-card {
        position: relative;
        min-height: 190px;
        padding: 25px;
        border-radius: 20px;
        background: #24272c;
        border: 1px solid rgba(255, 255, 255, 0.06);
        overflow: hidden;
        transition: all 0.25s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        border-color: rgba(255, 255, 255, 0.14);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
    }

    .stat-icon {
        position: absolute;
        top: 22px;
        right: 22px;

        width: 48px;
        height: 48px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 14px;
        font-size: 24px;
    }

    .stat-title {
        margin-top: 5px;
        color: #b5b8c5;
        font-size: 15px;
        font-weight: 600;
    }

    .stat-number {
        margin-top: 18px;
        font-size: 45px;
        line-height: 1;
        font-weight: 700;
    }

    .stat-description {
        margin-top: 15px;
        color: #8f93a1;
        font-size: 14px;
    }

    .stat-line {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 4px;
        width: 100%;
    }

    /* My Posts */
    .stat-posts .stat-icon {
        background: rgba(139, 92, 246, 0.15);
    }

    .stat-posts .stat-number {
        color: #a78bfa;
    }

    .stat-posts .stat-line {
        background: #8b5cf6;
    }

    /* Published */
    .stat-published .stat-icon {
        background: rgba(34, 197, 94, 0.15);
    }

    .stat-published .stat-number {
        color: #22c55e;
    }

    .stat-published .stat-line {
        background: #22c55e;
    }

    /* Pending */
    .stat-pending .stat-icon {
        background: rgba(245, 158, 11, 0.15);
    }

    .stat-pending .stat-number {
        color: #f59e0b;
    }

    .stat-pending .stat-line {
        background: #f59e0b;
    }

    /* Rejected */
    .stat-rejected .stat-icon {
        background: rgba(239, 68, 68, 0.15);
    }

    .stat-rejected .stat-number {
        color: #ef4444;
    }

    .stat-rejected .stat-line {
        background: #ef4444;
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

        .dashboard-title {
            font-size: 30px;
        }
    }
</style>


<div class="blogger-dashboard">

    <div class="dashboard-header">

        <h1 class="dashboard-title">
            Here is your dashboard!!, <?= Html::encode(Yii::$app->user->identity->name) ?> 👋
        </h1>

        <p class="dashboard-subtitle">
            Here's a quick overview of your blogging activity.
        </p>

    </div>


    <div class="stats-grid">

        <!-- My Posts -->
        <div class="stat-card stat-posts">

            <div class="stat-icon">
                ✍️
            </div>

            <div class="stat-title">
                My Posts
            </div>

            <div class="stat-number">
                <?= $totalPosts ?>
            </div>

            <div class="stat-description">
                Total blogs created by you
            </div>

            <div class="stat-line"></div>

        </div>


        <!-- Published -->
        <div class="stat-card stat-published">

            <div class="stat-icon">
                ✓
            </div>

            <div class="stat-title">
                Published
            </div>

            <div class="stat-number">
                <?= $publishedPosts ?>
            </div>

            <div class="stat-description">
                Blogs currently live
            </div>

            <div class="stat-line"></div>

        </div>


        <!-- Pending -->
        <div class="stat-card stat-pending">

            <div class="stat-icon">
                ⏳
            </div>

            <div class="stat-title">
                Pending
            </div>

            <div class="stat-number">
                <?= $pendingPosts ?>
            </div>

            <div class="stat-description">
                Waiting for approval
            </div>

            <div class="stat-line"></div>

        </div>


        <!-- Rejected -->
        <div class="stat-card stat-rejected">

            <div class="stat-icon">
                ⚠
            </div>

            <div class="stat-title">
                Rejected
            </div>

            <div class="stat-number">
                <?= $rejectedPosts ?>
            </div>

            <div class="stat-description">
                Blogs needing revision
            </div>

            <div class="stat-line"></div>

        </div>

    </div>

</div>