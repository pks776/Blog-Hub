<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\AuditLog[] $logs */

$this->title = 'History';
?>

<style>
    .history-wrapper {
        padding: 30px 10px;
    }

    .history-header {
        margin-bottom: 30px;
    }

    .history-header h1 {
        font-size: 34px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .history-header p {
        color: #9ca3af;
    }

    .history-card {
        background: #252a2f;
        border: 1px solid #343a40;
        border-radius: 18px;
        overflow: hidden;
    }

    .history-table-wrapper {
        overflow-x: auto;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }

    .history-table th {
        background: #202428;
        color: #fff;
        padding: 15px;
        text-align: left;
        white-space: nowrap;
    }

    .history-table td {
        padding: 15px;
        border-top: 1px solid #343a40;
        color: #e5e7eb;
        vertical-align: top;
    }

    .history-table tr:hover {
        background: #2b3035;
    }

    .action-badge {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }

    .action-created {
        background: rgba(59, 130, 246, 0.15);
        color: #60a5fa;
    }

    .action-updated {
        background: rgba(168, 85, 247, 0.15);
        color: #c084fc;
    }

    .action-approved {
        background: rgba(34, 197, 94, 0.15);
        color: #4ade80;
    }

    .action-rejected {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
    }

    .action-unpublished,
    .action-unpublish_requested {
        background: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
    }

    .action-published {
        background: rgba(20, 184, 166, 0.15);
        color: #2dd4bf;
    }

    .description {
        max-width: 450px;
        color: #b8c0ca;
    }

    .empty-history {
        padding: 50px;
        text-align: center;
        color: #9ca3af;
    }
</style>


<div class="history-wrapper">

    <div class="history-header">

        <h1>
            📜 History
        </h1>

        <p>
            View all important activities performed in BlogHub.
        </p>

    </div>


    <div class="history-card">

        <?php if (empty($logs)): ?>

            <div class="empty-history">
                No history records found.
            </div>

        <?php else: ?>

            <div class="history-table-wrapper">

                <table class="history-table">

                    <thead>

                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Entity</th>
                            <th>ID</th>
                            <th>Description</th>
                            <th>Date & Time</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach ($logs as $log): ?>

                        <?php
                        $actionClass = 'action-' .
                            strtolower(
                                str_replace(
                                    ' ',
                                    '_',
                                    $log->action
                                )
                            );
                        ?>

                        <tr>

                            <td>
                                <?= Html::encode($log->id) ?>
                            </td>

                            <td>
                                <?= Html::encode(
                                    $log->user->name ?? 'System'
                                ) ?>
                            </td>

                            <td>

                                <span class="action-badge <?= Html::encode($actionClass) ?>">
                                    <?= Html::encode(
                                        ucwords(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $log->action
                                            )
                                        )
                                    ) ?>
                                </span>

                            </td>

                            <td>
                                <?= Html::encode(
                                    $log->entity_type ?? '-'
                                ) ?>
                            </td>

                            <td>
                                <?= Html::encode(
                                    $log->entity_id ?? '-'
                                ) ?>
                            </td>

                            <td class="description">
                                <?= Html::encode(
                                    $log->description ?? '-'
                                ) ?>
                            </td>

                            <td>
                                <?= Yii::$app->formatter->asDatetime(
                                    $log->created_at
                                ) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>

</div>