<?php

namespace app\models;

use Yii;

use yii\db\ActiveRecord;

class AuditLog extends ActiveRecord
{
    public static function tableName()
    {
        return 'audit_logs';
    }

    /**
     * Create an audit log.
     */
    public static function record(
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?string $description = null
    ): bool {
        $log = new self();

        $log->user_id = Yii::$app->user->isGuest
            ? null
            : Yii::$app->user->id;

        $log->action = $action;
        $log->entity_type = $entityType;
        $log->entity_id = $entityId;
        $log->description = $description;

        return $log->save();
    }

    public function rules()
    {
        return [
            [['user_id', 'entity_id'], 'integer'],

            [['description'], 'string'],

            [['created_at'], 'safe'],

            [['action'], 'string', 'max' => 100],

            [['entity_type'], 'string', 'max' => 50],
        ];
    }

    /**
     * User who performed the action.
     */
    public function getUser()
    {
        return $this->hasOne(
            User::class,
            ['id' => 'user_id']
        );
    }
}