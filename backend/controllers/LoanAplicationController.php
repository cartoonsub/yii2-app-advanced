<?php

namespace backend\controllers;

use Yii;
use yii\rest\ActiveController;
use app\models\LoanAplication;
use yii\web\Controller;
use yii\data\Pagination;
use yii\helpers\VarDumper;

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

        $Model = new LoanAplication();
        if ($Model->aplicationProcess($delay) === false) {
            Yii::$app->response->statusCode = 502;
            return ['result' => false];
        }

        return ['result' => true];
    }

    // public function actionProcessor()
    // {
    //     if (Yii::$app->request->isGet) {
    //         // Логика для обработки заявок
    //         // Получите данные заявок из базы данных или выполните другие действия
    //         return ['status' => 'success', 'message' => 'Заявки обработаны'];
    //     }

    //     return ['status' => 'error', 'message' => 'Неверный метод запроса'];
    // }

    public function actionRequests()
    {
        $request = Yii::$app->request->post();
        $loanRequest = new LoanAplication();
        $loanRequest->user_id = $request['user_id'];
        $loanRequest->amount = $request['amount'];
        $loanRequest->term = $request['term'];
        $loanRequest->status = 'pending';

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

    public function actionIndex()
    {
        $Loand = new LoanAplication();
        $Loand->makeAplication();
        die;
        $query = Loan::find();

        $pagination = new Pagination([
            'defaultPageSize' => 5,
            'totalCount' => $query->count(),
        ]);

        $countries = $query->orderBy('name')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('index', [
            'countries' => $countries,
            'pagination' => $pagination,
        ]);
    }
}
