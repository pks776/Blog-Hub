<?php

namespace app\models;

use Yii;
use yii\base\Model;

class SignupForm extends Model
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $confirmPassword = '';

    public function rules(): array
    {
        return [
            [['name', 'email', 'password', 'confirmPassword'], 'required'],

            ['email', 'email'],

            ['email', 'unique',
                'targetClass' => User::class,
                'targetAttribute' => 'email',
                'message' => 'This email is already registered.',
            ],

            ['password', 'string', 'min' => 6],

            ['confirmPassword', 'compare',
                'compareAttribute' => 'password',
                'message' => 'Passwords do not match.',
            ],
        ];
    }

    public function signup(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $user = new User();

        $user->name = trim($this->name);
        $user->email = trim($this->email);
        $user->password_hash = Yii::$app->security->generatePasswordHash($this->password);

        $user->role = 'blogger';
        $user->status = 1;
        $user->created_at = date('Y-m-d H:i:s');
        $user->updated_at = date('Y-m-d H:i:s');

        return $user->save();
    }
}