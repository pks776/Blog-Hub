<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var int $totalUsers */
/** @var int $totalPosts */
/** @var int $publishedPosts */
/** @var int $pendingPosts */
/** @var int $rejectedPosts */

$this->title = 'Dashboard';

?>

<style>

.dashboard-wrapper {
    padding: 35px 10px;
}

/* ==========================
   HEADER
========================== */

.dashboard-header {
    margin-bottom: 35px;
}

.dashboard-header h1 {
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 8px;
}

.dashboard-header p {
    color: #9ca3af;
    font-size: 16px;
}


/* ==========================
   STATISTICS
========================== */

.stats-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
    margin-bottom: 35px;
}

.stat-card {
    position: relative;
    padding: 28px;
    border-radius: 18px;
    background: #252a2f;
    border: 1px solid #343a40;
    overflow: hidden;
    transition: all 0.25s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    border-color: #555;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 14px;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 24px;
    margin-bottom: 18px;
}

.stat-title {
    color: #aeb6c1;
    font-size: 15px;
    margin-bottom: 5px;
}

.stat-number {
    font-size: 36px;
    font-weight: 700;
    color: #fff;
}

.stat-description {
    color: #8c96a3;
    font-size: 13px;
    margin-top: 8px;
}


/* Bottom accent line */

.stat-line {
    position: absolute;
    bottom: 0;
    left: 0;

    width: 100%;
    height: 4px;
}


/* ==========================
   CARD COLORS
========================== */

.users .stat-icon {
    background: rgba(59, 130, 246, 0.15);
}

.users .stat-line {
    background: #3b82f6;
}


.posts .stat-icon {
    background: rgba(168, 85, 247, 0.15);
}

.posts .stat-line {
    background: #a855f7;
}


.published .stat-icon {
    background: rgba(34, 197, 94, 0.15);
}

.published .stat-line {
    background: #22c55e;
}


.pending .stat-icon {
    background: rgba(245, 158, 11, 0.15);
}

.pending .stat-line {
    background: #f59e0b;
}


.rejected .stat-icon {
    background: rgba(239, 68, 68, 0.15);
}

.rejected .stat-line {
    background: #ef4444;
}


/* ==========================
   RESPONSIVE
========================== */

@media (max-width: 1100px) {

    .stats-grid {
        grid-template-columns: repeat(3, 1fr);
    }

}

@media (max-width: 700px) {

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }

}

@media (max-width: 450px) {

    .stats-grid {
        grid-template-columns: 1fr;
    }

}

</style>


<div class="dashboard-wrapper">


    <!-- ==========================
         HEADER
    ========================== -->

    <div class="dashboard-header">

        <h1>
            📊 BlogHub Dashboard
        </h1>

        <p>
            Monitor your blogging platform and manage content from one place.
        </p>

    </div>


    <!-- ==========================
         STATISTICS
    ========================== -->

    <div class="stats-grid">


        <!-- TOTAL USERS -->

        <div class="stat-card users">

            <div class="stat-icon">
                👥
            </div>

            <div class="stat-title">
                Total Users
            </div>

            <div class="stat-number">
                <?= $totalUsers ?>
            </div>

            <div class="stat-description">
                Registered users
            </div>

            <div class="stat-line"></div>

        </div>


        <!-- TOTAL POSTS -->

        <div class="stat-card posts">

            <div class="stat-icon">
                📝
            </div>

            <div class="stat-title">
                Total Posts
            </div>

            <div class="stat-number">
                <?= $totalPosts ?>
            </div>

            <div class="stat-description">
                Blogs created
            </div>

            <div class="stat-line"></div>

        </div>


        <!-- PUBLISHED POSTS -->

        <div class="stat-card published">

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


        <!-- PENDING POSTS -->

        <div class="stat-card pending">

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


        <!-- REJECTED POSTS -->

        <div class="stat-card rejected">

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
                Blogs requiring attention
            </div>

            <div class="stat-line"></div>

        </div>


    </div>


</div>