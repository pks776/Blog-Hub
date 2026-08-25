<?php

namespace app\models;

use Yii;
use yii\base\Model;

class ChangePasswordForm extends Model
{
    public $current_password;
    public $new_password;
    public $confirm_password;

    private $_user;

    public function __construct(User $user, $config = [])
    {
        $this->_user = $user;

        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [
                [
                    'current_password',
                    'new_password',
                    'confirm_password'
                ],
                'required'
            ],

            [
                'current_password',
                'validateCurrentPassword'
            ],

            [
                'new_password',
                'string',
                'min' => 6,
                'max' => 255
            ],

            [
                'confirm_password',
                'compare',
                'compareAttribute' => 'new_password',
                'message' => 'Passwords do not match.'
            ],
        ];
    }

    public function validateCurrentPassword($attribute)
    {
        if ($this->hasErrors()) {
            return;
        }

        if (!$this->_user->validatePassword($this->current_password)) {
            $this->addError(
                $attribute,
                'Current password is incorrect.'
            );
        }
    }

    public function changePassword()
    {
        $this->_user->password_hash =
            Yii::$app->security->generatePasswordHash(
                $this->new_password
            );

        return $this->_user->save(false);
    }
}