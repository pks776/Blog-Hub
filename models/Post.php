<?php

namespace app\models;

use Yii;
use app\models\User;

/**
 * This is the model class for table "posts".
 *
 * @property int $id
 * @property string $title
 * @property string|null $slug
 * @property string $content
 * @property string|null $image
 * @property string|null $status
 * @property int $author_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $author
 */
class Post extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_PUBLISHED = 'published';
    const STATUS_REJECTED = 'rejected';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'posts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['slug', 'image'], 'default', 'value' => null],
            [['status'], 'default', 'value' => self::STATUS_DRAFT],

            [['title', 'content', 'author_id'], 'required'],

            [['content', 'status'], 'string'],
            [['author_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],

            [['title', 'slug', 'image'], 'string', 'max' => 255],

            ['status', 'in', 'range' => array_keys(self::optsStatus())],

            [['slug'], 'unique'],

            [
                ['author_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['author_id' => 'id'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'slug' => 'Slug',
            'content' => 'Content',
            'image' => 'Image',
            'status' => 'Status',
            'author_id' => 'Author',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets the author.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }

    /**
     * Status options.
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    public function displayStatus()
    {
        return self::optsStatus()[$this->status] ?? '';
    }

    public function isStatusDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function setStatusToDraft()
    {
        $this->status = self::STATUS_DRAFT;
    }

    public function isStatusPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function setStatusToPending()
    {
        $this->status = self::STATUS_PENDING;
    }

    public function isStatusPublished()
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function setStatusToPublished()
    {
        $this->status = self::STATUS_PUBLISHED;
    }

    public function isStatusRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function setStatusToRejected()
    {
        $this->status = self::STATUS_REJECTED;
    }
}