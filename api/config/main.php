<?php

use api\components\WebUser;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\web\Response;

$db = require(__DIR__ . '/db.php');

$params = array_merge(
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

$routes = require __DIR__ . '/routes.php';

return [
    'id' => 'api-template',
    'name' => 'Api Template',
    'version' => "1.0.0",
    'basePath' => dirname(__DIR__),
    'vendorPath' => dirname(__DIR__) . '/vendor',
    'bootstrap' => ['log', [
        'class' => ContentNegotiator::class,
        'formats' => [
            'application/json' => Response::FORMAT_JSON,
            'application/xml' => Response::FORMAT_XML,
        ],
    ]],
    'controllerNamespace' => 'api\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'modules' => [
        'oauth2' => [
            'class' => 'filsh\yii2\oauth2server\Module',
            'tokenParamName' => 'access_token',
            'tokenAccessLifetime' => 864000 * 3 * 12 * 5, // 10 days
            'storageMap' => [
                'user_credentials' => 'api\models\OauthUser',
            ],
            'grantTypes' => [
                'user_credentials' => [
                    'class' => 'OAuth2\GrantType\UserCredentials',
                ],
                'client_credentials' => [
                    'class' => 'OAuth2\GrantType\ClientCredentials'
                ],
                'refresh_token' => [
                    'class' => 'OAuth2\GrantType\RefreshToken',
                    'always_issue_new_refresh_token' => true,
                    'refresh_token_lifetime' => '100800',
                ]
            ]
        ],
        'v1' => [
            'basePath' => '@api/versions/v1',
            'class' => 'api\versions\v1\Module'
        ],
    ],
    'components' => [
        'db' => $db,
        'user' => [
            'class' => WebUser::class,
            'identityClass' => 'api\models\OauthUser',
            'enableAutoLogin' => false,
            'loginUrl' => null,
            'enableSession' => false
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'showScriptName' => false,
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'rules' => $routes,
        ],
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser'
            ]
        ],
        'resourceManager' => [
            'class' => 'api\components\FileSystemResourceManager',
            'basePath' => Yii::getAlias('@api/web/storage'),
            'directory' => 'storage'
        ],
    ],
    'params' => $params,
];
