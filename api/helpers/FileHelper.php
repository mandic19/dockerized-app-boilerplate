<?php

namespace api\helpers;

class FileHelper extends \yii\helpers\FileHelper
{
    public static function getIsImageByMimeType($mimeType)
    {
        return strpos($mimeType, 'image') !== false;
    }
}
