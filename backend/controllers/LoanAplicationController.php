<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use yii\web\Controller;
use yii\data\Pagination;
use yii\helpers\VarDumper;
use backend\Enums\StatusEnum;
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
        $Model = new LoanAplication();
        $Model->load(Yii::$app->request->post(), '');
        if ($Model->validate() === false) {
            Yii::$app->response->statusCode = 400;
            return [
                'result' => false,
                'errors' => $Model->errors,
            ];
        }

        $request = (array)Yii::$app->request->post();
        return $this->requestProcessing($request);
    }
}
