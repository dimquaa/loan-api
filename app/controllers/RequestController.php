<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use app\models\LoanRequest;

class RequestController extends Controller
{
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new LoanRequest();
        $model->load(Yii::$app->request->bodyParams, '');

        if (!$model->validate()) {
            Yii::$app->response->statusCode = 400;
            return ['result' => false];
        }

        $existsApproved = LoanRequest::find()
            ->where([
                'user_id' => $model->user_id,
                'status' => 'approved'
            ])
            ->exists();

        if ($existsApproved) {
            Yii::$app->response->statusCode = 400;
            return ['result' => false];
        }

        $model->status = 'pending';
        $model->created_at = time();
        $model->save(false);

        Yii::$app->response->statusCode = 201;

        return [
            'result' => true,
            'id' => $model->id,
        ];
    }
}
