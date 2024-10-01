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

    // http://localhost:21080/index.php/loan-aplication/processor?delay=5
    public function actionProcessor(): array
    {
        $delay = Yii::$app->request->get('delay', 5);

        $Model = new LoanAplication();
        $result = $Model->aplicationProcess($delay);
        if ($result === false) {
            Yii::$app->response->statusCode = 502;
        }

        return $result;
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
        $result = $Model->requestProcessing($request);
        if ($result === false) {
            Yii::$app->response->statusCode = 400;
        }

        return $result;
    }
}
