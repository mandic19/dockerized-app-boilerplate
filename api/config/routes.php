<?php

return [
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => 'v1/swagger',
        'pluralize' => false,
        'patterns' => [
            'GET' => 'docs',
            'GET index' => 'docs',
            'GET json-schema' => 'json-schema',
            'OPTIONS <action>' => 'options',
            '' => 'options',
        ],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => 'v1/oauth',
        'pluralize' => false,
        'patterns' => [
            'POST token' => 'token',
            'POST revoke' => 'revoke',
            'OPTIONS <action>' => 'options',
            '' => 'options',
        ],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => 'v1/image',
        'patterns' => [
            'GET <id>/thumb/<spec>' => 'thumb',
            'OPTIONS <id>/thumb/<spec>/<slug>' => 'options',
            'OPTIONS <action>' => 'options',
            '' => 'options',
        ],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => 'v1/user',
        'pluralize' => false,
        'patterns' => [
            'GET info' => 'info',
            'POST signup' => 'signup',
            'PUT update' => 'update',
            'OPTIONS <action>' => 'options',
            '' => 'options',
        ],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => 'v1/board',
        'patterns' => [
            'GET' => 'index',
            'POST' => 'create',
            'GET <id>' => 'view',
            'PUT <id>' => 'update',
            'DELETE <id>' => 'delete',
            'OPTIONS <action>' => 'options',
            '' => 'options',
        ],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => 'v1/section',
        'patterns' => [
            'GET' => 'index',
            'POST' => 'create',
            'GET <id>' => 'view',
            'PUT <id>' => 'update',
            'PUT <id>/reorder' => 'reorder',
            'DELETE <id>' => 'delete',
            'OPTIONS <action>' => 'options',
            'OPTIONS <id>/<action>' => 'options',
            '' => 'options',
        ],
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => 'v1/task',
        'patterns' => [
            'GET' => 'index',
            'POST' => 'create',
            'GET <id>' => 'view',
            'PUT <id>' => 'update',
            'PUT <id>/reorder' => 'reorder',
            'DELETE <id>' => 'delete',
            'OPTIONS <action>' => 'options',
            'OPTIONS <id>/<action>' => 'options',
            '' => 'options',
        ],
    ],
];
