<?php

namespace app\models;

use yii\db\ActiveRecord;

class PostVersion extends ActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_PUBLISHED = 'published';
    const STATUS_REJECTED = 'rejected';
    const STATUS_OUTDATED = 'outdated';
    const STATUS_UNPUBLISHED = 'unpublished';
    const STATUS_DELETED = 'deleted';

    public static function tableName()
    {
        return 'post_versions';
    }

    public function rules()
    {
        return [
            [['post_id', 'version', 'title', 'content', 'created_by'], 'required'],

            [['post_id', 'version', 'created_by', 'reviewed_by'], 'integer'],

            [['content', 'rejection_reason'], 'string'],

            [['created_at', 'updated_at'], 'safe'],

            [['title', 'image'], 'string', 'max' => 255],

            [
                'status',
                'in',
                'range' => [
                    self::STATUS_PENDING,
                    self::STATUS_PUBLISHED,
                    self::STATUS_REJECTED,
                    self::STATUS_OUTDATED,
                    self::STATUS_UNPUBLISHED,
                    self::STATUS_DELETED,
                ],
            ],
        ];
    }

    public function getPost()
    {
        return $this->hasOne(Post::class, ['id' => 'post_id']);
    }

    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getReviewer()
    {
        return $this->hasOne(User::class, ['id' => 'reviewed_by']);
    }
}