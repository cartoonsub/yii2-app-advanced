<?php

namespace backend\controllers;

use Yii;
use yii\console\Controller;
use app\models\User;

class UserController extends Controller
{
    public $modelClass = 'app\models\User';

    // public function actionCreate($username, $password, $email)
    // {
    //     $user = new User();
    //     $user->username = $username;
    //     $user->email = $email;
    //     $user->setPassword($password);
    //     $user->generateAuthKey();
    //     if ($user->save()) {
    //         echo "User created successfully.\n";
    //     } else {
    //         echo "Error creating user.\n";
    //         print_r($user->errors);
    //     }
    // }
}