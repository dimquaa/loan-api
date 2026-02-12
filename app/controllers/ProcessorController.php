<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use app\models\LoanRequest;

class ProcessorController extends Controller
{
    private const DEFAULT_DELAY = 5;
    private const APPROVE_CHANCE_PERCENT = 10;
    private const STATUS_APPROVED = 'approved';
    private const STATUS_DECLINED = 'declined';
    private const STATUS_PENDING = 'pending';

    public function actionIndex($delay = self::DEFAULT_DELAY)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $delay = (int)$delay;
        if ($delay < 0) {
            $delay = self::DEFAULT_DELAY;
        }

        $requests = LoanRequest::find()->where(['status' => self::STATUS_PENDING])->all();

        foreach ($requests as $request) {

            Yii::$app->db->transaction(function ($db) use ($request, $delay) {

                Yii::$app->db->createCommand(
                    'SELECT id FROM loan_requests WHERE id=:id FOR UPDATE'
                )->bindValue(':id', $request->id)->queryOne();

                $request = LoanRequest::findOne($request->id);
                if (!$request || $request->status !== self::STATUS_PENDING) {
                    return;
                }

                $hasApproved = LoanRequest::find()
                    ->where(['user_id' => $request->user_id, 'status' => self::STATUS_APPROVED])
                    ->exists();

                sleep($delay);

                $request->status = $hasApproved
                    ? self::STATUS_DECLINED
                    : (mt_rand(1, 100) <= self::APPROVE_CHANCE_PERCENT
                        ? self::STATUS_APPROVED
                        : self::STATUS_DECLINED);

                $request->save(false);
            });
        }

        return ['result' => true];
    }
}
