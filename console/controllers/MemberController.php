<?php

namespace app\controllers;

class MemberController extends \yii\rest\ActiveController
{
    public $modelClass = 'app\models\User';

    public function actionIndex()
    {
        return $this->render('index');
    }

}
