<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use app;

class Loan extends ActiveRecord
{
    public $name;
    public $message;

    public static function tableName()
    {
        return 'country';
    }

    public function makeAplication()
    {
        $tables = Yii::$app->db->schema->tableNames;
        foreach ($tables as $table) {
            echo $table . "<br>";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        
        return [
            [['name', 'message'], 'required'],
            ['message', 'string', 'max' => 255],
        ];
    }
}
