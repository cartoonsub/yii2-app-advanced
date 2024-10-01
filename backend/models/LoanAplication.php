<?php

namespace app\models;

use backend\Enums\StatusEnum;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

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

    public function aplicationProcess(int $delay)
    {
        $excldedUsers = $this->getUsersWithAprrovedLoan();
        $aplications = LoanAplication::find()
                     ->where(['not in', 'user_id', $excldedUsers])
                     ->all();
        
        if (empty($aplications)) {
            return false;
        }

        $aplicationsByUser = $this->groupByUser($aplications);
        foreach ($aplicationsByUser as $userId => $aplications) {
            $flag = false;

            foreach ($aplications as $aplication) {
                sleep($delay);

                $aplication->status = StatusEnum::DECLINED->value;
                if ($flag === true) {
                    $aplication->save();
                    continue;
                }

                if ($this->aproveAplication() === true) {
                    $flag = true;
                    $aplication->status = StatusEnum::APPROVED->value;
                }

                $aplication->save();
            }
        }

        return true;
    }

    private function getUsersWithAprrovedLoan(): array
    {
        $users = LoanAplication::find()
            ->select('user_id')
            ->where(['status' => StatusEnum::APPROVED->value])
            ->column();

        return $users;
    }

    private function groupByUser(array $aplications): array
    {
        $result = [];
        foreach ($aplications as $aplication) {
            $userId = $aplication->user_id;
            $result[$userId][] = $aplication;
        }

        return $result;
    }
    
    private function aproveAplication(): bool
    {
        return random_int(1, 10) === 1;
    }
}
