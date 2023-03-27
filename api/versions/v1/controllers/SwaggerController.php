<?php

namespace api\versions\v1\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

/**
 * @SWG\Swagger(
 *     swagger="2.0",
 *     basePath="/v1",
 *     produces={"application/json"},
 *     consumes={"application/json"},
 *     @SWG\Info(
 *      version="1.0",
 *      title="Swagger - Developer API Documentation"),
 *     )
 * )
 *
 * @SWG\SecurityScheme(
 *    securityDefinition="Bearer",
 *    type="apiKey",
 *    in="header",
 *    name="Authorization"
 * )
 */

class SwaggerController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        /* @var $response yii\web\Response */
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_HTML;

        return [
            'docs' => [
                'class' => 'yii2mod\swagger\SwaggerUIRenderer',
                'restUrl' => Url::to(['swagger/json-schema']),
            ],
            'json-schema' => [
                'class' => 'yii2mod\swagger\OpenAPIRenderer',
                // Тhe list of directories that contains the swagger annotations.
                'scanDir' => [
                    Yii::getAlias('@api/versions/v1')
                ],
            ],
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
}
