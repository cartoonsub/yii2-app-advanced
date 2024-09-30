<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class LoanAplication extends ActiveRecord
{
    public $name;
    public $message;

    public static function tableName()
    {
        return 'loan_applications';
    }

    public function fields()
    {
        return [
            'id',
            'user_id',
            'amount',
            'term',
            'status',
        ];
    }

    public function makeAplication()
    {
        $tables = Yii::$app->db->schema->tableNames;
        foreach ($tables as $table) {
            echo $table . "<br>";
        }
    }

    public function rules()
    {
        return [
            [['user_id', 'amount', 'term'], 'required'],
            [['user_id', 'amount', 'term'], 'integer'],
        ];
    }

    public function aplicationProcess(int $delay): bool
    {
        
        return true;
    }
}
