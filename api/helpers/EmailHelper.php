<?php

namespace api\helpers;

use Yii;

class EmailHelper
{
    public static function sendMessage($view, $to, $subject, $params = [])
    {
        if(YII_ENV != 'prod'){
            $subject = YII_ENV .' - '. $subject;
        }

        return Yii::$app->mailer->compose($view, $params)
            ->setFrom([Yii::$app->params['support.email'] => Yii::$app->name])
            ->setTo($to)
            ->setSubject($subject)
            ->send();
    }
}
