<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var app\models\PostVersion $version
 */

$this->title = 'Reject Blog Version';
?>

<div class="container mt-4">

    <h2>Reject Blog Version</h2>

    <hr>

    <div class="mb-3">
        <strong>Post ID:</strong>
        <?= Html::encode($version->post_id) ?>
    </div>

    <div class="mb-3">
        <strong>Version:</strong>
        <?= Html::encode($version->version) ?>
    </div>

    <div class="mb-3">
        <strong>Title:</strong>
        <?= Html::encode($version->title) ?>
    </div>

    <form
        method="post"
        action="<?= Html::encode(
            \yii\helpers\Url::to([
                'reject-version',
                'id' => $version->id,
            ])
        ) ?>"
    >

        <?= Html::hiddenInput(
            Yii::$app->request->csrfParam,
            Yii::$app->request->csrfToken
        ) ?>

        <div class="mb-3">

            <label
                for="rejection_reason"
                class="form-label"
            >
                Rejection Reason
            </label>

            <textarea
                id="rejection_reason"
                name="rejection_reason"
                class="form-control"
                rows="5"
                placeholder="Enter the reason for rejecting this blog..."
                required
            ></textarea>

        </div>

        <button
            type="submit"
            class="btn btn-danger"
        >
            Reject Version
        </button>

        <?= Html::a(
            'Cancel',
            ['pending'],
            [
                'class' =>
                    'btn btn-secondary ms-2',
            ]
        ) ?>

    </form>

</div>