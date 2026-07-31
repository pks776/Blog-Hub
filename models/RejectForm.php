<?php

namespace app\models;

use yii\base\Model;

class RejectForm extends Model
{
    public $reason;

    public function rules()
    {
        return [
            [['reason'], 'required'],
            [['reason'], 'string', 'max' => 1000],
        ];
    }

    public function attributeLabels()
    {
        return [
            'reason' => 'Moderator Comment',
        ];
    }
}