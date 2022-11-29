<?php

use yii\web\Response;

return [
    'formats' => [
        'application/json' => Response::FORMAT_JSON
    ],
    'user.passwordResetTokenExpire' => 3600,
    'user.passwordMinLength' => 8,
    'pattern' => [
        'letter' => '/[a-zA-Z]/',
        'digit' => '/[0-9]/',
        'specialChar' => '/[^A-Za-z0-9\s]/'
    ],
    'resourceManager' => [
        's3.path.prefix' => 'local',
        's3.file.prefix' => '',
        'image.thumb.path' => 'thumbs/', //relative from path prefix
        's3.expire.time' => '+20 minutes',
        'image.thumb.expire' => '+20 minutes'
    ],
];
