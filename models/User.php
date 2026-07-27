<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    /**
     * Returns the table name.
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * Validation rules.
     */
    public function rules()
    {
        return [
            [['role'], 'default', 'value' => 'blogger'],
            [['status'], 'default', 'value' => 1],

            [['name', 'email', 'password_hash'], 'required'],

            [['status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],

            [['name'], 'string', 'max' => 100],
            [['email'], 'string', 'max' => 150],
            [['password_hash'], 'string', 'max' => 255],
            [['role'], 'string', 'max' => 20],

            [['email'], 'unique'],
        ];
    }

    /**
     * Attribute labels.
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'password_hash' => 'Password',
            'role' => 'Role',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Find user by email.
     */
    public static function findByUsername($username): ?self
    {
        return self::find()
            ->where(['email' => $username])
            ->one();
    }

    /**
     * Validate password.
     */
    public function validatePassword($password): bool
    {
        return Yii::$app->security->validatePassword(
            $password,
            $this->password_hash
        );
    }

    // ==========================
    // IdentityInterface Methods
    // ==========================

    public static function findIdentity($id): ?IdentityInterface
    {
        return self::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null): ?IdentityInterface
    {
        return null;
    }

    public function getId(): int
    {
        return (int)$this->id;
    }

    public function getAuthKey(): string
    {
        return '';
    }

    public function validateAuthKey($authKey): bool
    {
        return true;
    }

    /**
     * Relation: User has many Posts.
     */
    public function getPosts()
    {
        return $this->hasMany(Post::class, ['author_id' => 'id']);
    }
}