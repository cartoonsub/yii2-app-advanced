<?php

namespace app\models;

use Yii;
use common\models\User;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;
use backend\Enums\StatusEnum;

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

    public function requestProcessing(array $data): array
    {
        $result = [
            'result' => false,
        ];

        if ($this->checkUser($data['user_id']) === false) {
            Yii::$app->response->statusCode = 400;
            $result['message'] = 'User not found';
            return $result;
        }

        if ($this->checkAproved($data['user_id']) === false) {
            Yii::$app->response->statusCode = 400;
            $result['message'] = 'User has approved request';
            return $result;
        }

        if ($this->checkDuplicate($data) === false) {
            Yii::$app->response->statusCode = 400;
            $result['message'] = 'Duplicate request';
            return $result;
        }

        $LoanAplication = new LoanAplication();
        $LoanAplication->user_id = $data['user_id'];
        $LoanAplication->amount = $data['amount'];
        $LoanAplication->term = $data['term'];
        $LoanAplication->status = StatusEnum::NEW->value;

        if ($LoanAplication->save()) {
            $result['result'] = true;
            $result['id'] = $LoanAplication->id;
        }
        
        return $result;
    }

    private function checkUser(int $user_id): bool
    {
        $user = User::findOne($user_id);
        if (empty($user)) {
            // todo создать seed для User
            // $newUser = new User();
            // $newUser->username = 'other User';
            // $newUser->email = 'adminO@admin.com';
            // $newUser->auth_key = 'auth_keyO';
            // $newUser->password_hash = 'password_hashO';
            // $newUser->password_reset_token = 'password_reset_tokenO';
            // $newUser->save();

            return false;
        }

        return true;
    }

    private function checkAproved(int $user_id): bool
    {
        $approved = LoanAplication::find()
            ->where([
                'user_id' => $user_id,
                'status' => StatusEnum::APPROVED->value,
            ])
            ->one();

        if (empty($approved)) {
            return true;
        }

        return false;
    }

    private function checkDuplicate(array $data): bool
    {
        $duplicate = LoanAplication::find()
            ->where([
                'user_id' => $data['user_id'],
                'amount'  => $data['amount'],
                'term'    => $data['term'],
            ])
            ->one();

        if (empty($duplicate)) {
            return true;
        }

        return false;
    }
}
