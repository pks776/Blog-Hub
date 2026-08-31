<?php

namespace app\models;

use app\models\User;
use app\models\PostVersion;

class Post extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 'draft';
const STATUS_PENDING = 'pending';
const STATUS_PUBLISHED = 'published';
const STATUS_REJECTED = 'rejected';
const STATUS_UNPUBLISHED = 'unpublished';
const STATUS_DELETED = 'deleted';


    // Virtual attribute for file upload
    public $imageFile;
    public $content;
    public $image;
    public $slug;
    public $rejection_reason;

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

            [['content', 'status', 'rejection_reason'], 'string'],

            [['author_id'], 'integer'],

            [['created_at', 'updated_at'], 'safe'],

            [['title', 'slug', 'image'], 'string', 'max' => 255],

            [
                'status',
                'in',
                'range' => array_keys(self::optsStatus())
            ],

            [
                ['author_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['author_id' => 'id'],
            ],

            [
                ['imageFile'],
                'file',
                'extensions' => [
                    'jpg',
                    'jpeg',
                    'png',
                    'webp'
                ],
                'checkExtensionByMimeType' => false,
                'skipOnEmpty' => true,
                'maxSize' => 5 * 1024 * 1024,
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
            'imageFile' => 'Upload Image',
            'status' => 'Status',
            'author_id' => 'Author',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'rejection_reason' => 'Moderator Comment',
        ];
    }

    /**
     * Relation with User
     */
    public function getAuthor()
    {
        return $this->hasOne(
            User::class,
            ['id' => 'author_id']
        );
    }

    /**
     * Relation with PostVersion
     */
    public function getVersions()
    {
        return $this->hasMany(
            PostVersion::class,
            ['post_id' => 'id']
        )->orderBy([
            'version' => SORT_DESC
        ]);
    }

    /**
     * Status options
     */
    public static function optsStatus()
{
    return [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_PENDING => 'Pending',
        self::STATUS_PUBLISHED => 'Published',
        self::STATUS_REJECTED => 'Rejected',
        self::STATUS_DELETED => 'Deleted',
    ];
}

    /**
     * Display status
     */
    public function displayStatus()
    {
        return self::optsStatus()[$this->status] ?? '';
    }

    /**
     * Draft
     */
    public function isStatusDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function setStatusToDraft()
    {
        $this->status = self::STATUS_DRAFT;
    }

    /**
     * Pending
     */
    public function isStatusPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function setStatusToPending()
    {
        $this->status = self::STATUS_PENDING;
    }

    /**
     * Published
     */
    public function isStatusPublished()
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function setStatusToPublished()
    {
        $this->status = self::STATUS_PUBLISHED;
    }

    /**
     * Rejected
     */
    public function isStatusRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function setStatusToRejected()
    {
        $this->status = self::STATUS_REJECTED;
    }

    public function isStatusUnpublished()
{
    return $this->status === self::STATUS_UNPUBLISHED;
}

public function setStatusToUnpublished()
{
    $this->status = self::STATUS_UNPUBLISHED;
}
    /**
     * Deleted
     */
    public function isStatusDeleted()
    {
        return $this->status === self::STATUS_DELETED;
    }

    public function setStatusToDeleted()
    {
        $this->status = self::STATUS_DELETED;
    }
}