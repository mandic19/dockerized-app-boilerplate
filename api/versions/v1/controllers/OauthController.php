<?php

namespace api\versions\v1\controllers;

use OAuth2\Response;
use yii\helpers\ArrayHelper;
use filsh\yii2\oauth2server\controllers\RestController;
use yii;
use yii\filters\Cors;

class OauthController extends RestController
{
    public function init(){
        parent::init();
        $this->module = Yii::$app->getModule('oauth2');
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'corsFilter' => [
                'class' => Cors::class
            ],
        ]);
    }

    public function actionOptions()
    {
        Yii::$app->getResponse()->getHeaders()->set('Allow', implode(', ', ['OPTIONS', 'POST']));
    }

    public function actionToken()
    {
        /** @var $response Response */
        $response = $this->module->getServer()->handleTokenRequest();
        return $response->getParameters();
    }

    public function actionRevoke()
    {
        /** @var $response Response */
        $response = $this->module->getServer()->handleRevokeRequest();
        return $response->getParameters();
    }
}
