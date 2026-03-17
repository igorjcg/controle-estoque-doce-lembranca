<?php

namespace app\controllers;

use yii\web\Controller;
use yii\web\Response;

class BaseController extends Controller
{
    public function redirectAfterCreate(): Response
    {
        return $this->redirect(['index']);
    }
}