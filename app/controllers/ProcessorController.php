<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use app\models\LoanRequest;

class ProcessorController extends Controller
{
    public function actionIndex($delay = 5)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $delay = (int)$delay;
        if ($delay < 0) {
            $delay = 5;
        }

        $requests = LoanRequest::find()->where(['status' => 'pending'])->all();

        foreach ($requests as $request) {

            Yii::$app->db->transaction(function ($db) use ($request, $delay) {

                Yii::$app->db->createCommand(
                    'SELECT id FROM loan_requests WHERE id=:id FOR UPDATE'
                )->bindValue(':id', $request->id)->queryOne();

                $request = LoanRequest::findOne($request->id);
                if (!$request || $request->status !== 'pending') {
                    return;
                }

                $hasApproved = LoanRequest::find()
                    ->where(['user_id' => $request->user_id, 'status' => 'approved'])
                    ->exists();

                sleep($delay);

                if ($hasApproved) {
                    $request->status = 'declined';
                } else {
                    $request->status = (mt_rand(1, 100) <= 10) ? 'approved' : 'declined';
                }

                $request->save(false);
            });
        }

        return ['result' => true];
    }
}
