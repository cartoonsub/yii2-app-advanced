<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use yii\web\Controller;
use yii\data\Pagination;
use yii\helpers\VarDumper;
use app\models\LoanAplication;
use yii\rest\ActiveController;

class LoanAplicationController extends ActiveController
{
    public $modelClass = 'app\models\LoanAplication';

    public function actions()
    {
        $actions = parent::actions();

        // оставить действия "update" и "create"
        // unset($actions['index'], $actions['view'], $actions['delete'], $actions['options']);

        return $actions;
    }

    public function actionProcessor($delay = 5): array
    {
        $delay = Yii::$app->request->get('delay', 5);
        return LoanAplication::find()->all();

        $Model = new LoanAplication();
        if ($Model->aplicationProcess($delay) === false) {
            Yii::$app->response->statusCode = 502;
            return ['result' => false];
        }

        return ['result' => true];
    }

    // http://localhost:21080/index.php/loan-aplication/requests?user_id=1&amount=3000&term=30
    public function actionRequests()
    {
        $request = Yii::$app->request->post();
        if (empty($request)) {
            Yii::$app->response->statusCode = 400;
            return [
                'result' => false,
            ];
        }

        $loanRequest = new LoanAplication();
        $loanRequest->user_id = $request['user_id'];
        $loanRequest->amount = $request['amount'];
        $loanRequest->term = $request['term'];
        $loanRequest->status = 1;

        if ($this->checkUser($loanRequest->user_id) === false) {
            Yii::$app->response->statusCode = 400;
            return [
                'allUsers' => User::find()->all(),
                'message' => 'User not found',
                'result' => false,
            ];
        }

        if ($this->checkAproved($loanRequest) === false) {
            Yii::$app->response->statusCode = 400;
            return [
                'message' => 'User has approved request',
                'result' => false,
            ];
        }

        if ($this->checkDuplicate($loanRequest) === false) {
            Yii::$app->response->statusCode = 400;
            return [
                'message' => 'Duplicate request',
                'result' => false,
            ];
        }

        if ($loanRequest->save()) {
            return [
                'result' => true,
                'id' => $loanRequest->id,
            ];
        } else {
            return [
                'result' => false,
            ];
        }
    }

    private function checkUser(int $user_id): bool
    {
        $user = User::findOne($user_id);
        if (empty($user)) {
            $newUser = new User();
            $newUser->username = 'other User';
            $newUser->email = 'adminO@admin.com';
            $newUser->auth_key = 'auth_keyO';
            $newUser->password_hash = 'password_hashO';
            $newUser->password_reset_token = 'password_reset_tokenO';
            $newUser->save();

            return false;
        }

        return true;
    }

    private function checkAproved(LoanAplication $loanRequest): bool
    {
        $approved = LoanAplication::find()
            ->where([
                'user_id' => $loanRequest->user_id,
                'status' => 1,
            ])
            ->one();

        if (empty($approved)) {
            return true;
        }

        return false;
    }

    private function checkDuplicate(LoanAplication $loanRequest): bool
    {
        $duplicate = LoanAplication::find()
            ->where([
                'user_id' => $loanRequest->user_id,
                'amount' => $loanRequest->amount,
                'term' => $loanRequest->term,
            ])
            ->one();

        if (empty($duplicate)) {
            return true;
        }

        return false;
    }
}
