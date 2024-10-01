<?php

namespace frontend\controllers;

use Yii;
use yii\rest\ActiveController;
use app\models\Loan;
use yii\web\Controller;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use common\models\LoginForm;
use yii\filters\AccessControl;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use frontend\models\VerifyEmailForm;
use yii\web\BadRequestHttpException;
use frontend\models\ResetPasswordForm;
use yii\base\InvalidArgumentException;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResendVerificationEmailForm;

/**
 * Site controller
 */
class LoanController extends ActiveController
{
    public $modelClass = 'app\models\Loan';

    public function actionIndex()
    {
        $Loand = new Loan();
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

    public function actionProcessor()
    {
        if (Yii::$app->request->isGet) {
            // Логика для обработки заявок
            // Получите данные заявок из базы данных или выполните другие действия
            return ['status' => 'success', 'message' => 'Заявки обработаны'];
        }

        return ['status' => 'error', 'message' => 'Неверный метод запроса'];
    }
}
