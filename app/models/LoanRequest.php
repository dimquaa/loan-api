<?php

namespace app\models;

use yii\db\ActiveRecord;

class LoanRequest extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%loan_requests}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'amount', 'term'], 'required'],
            [['user_id', 'amount', 'term'], 'integer'],
            ['amount', 'integer', 'min' => 1],
            ['term', 'integer', 'min' => 1],
        ];
    }
}
