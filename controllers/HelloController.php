<?php

declare(strict_types=1);

namespace app\controllers;

use yii\web\Controller;

class HelloController extends Controller
{
    public function actionIndex(): string
    {
        return "Hello Controller Working";
    }
}