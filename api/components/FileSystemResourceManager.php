<?php

namespace api\components;

use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class FileSystemResourceManager extends \dosamigos\resourcemanager\FileSystemResourceManager
{
    /**
     * Saves a file
     * @param UploadedFile $file the file uploaded
     * @param string $name the name of the file. If empty, it will be set to the name of the uploaded file
     * @param array $options to save the file. The options can be any of the following:
     *  - `folder` : whether we should create a subfolder where to save the file
     * @return boolean
     */
    public function save($file, $name, $options = [])
    {
        $folder = ArrayHelper::getValue($options, 'folder');
        $path = $folder
            ? $this->getBasePath() . DIRECTORY_SEPARATOR . $folder . ltrim($name, DIRECTORY_SEPARATOR)
            : $this->getBasePath() . DIRECTORY_SEPARATOR . ltrim($name, DIRECTORY_SEPARATOR);
        @mkdir(dirname($path), 0777, true);

        return $file->saveAs($path, false);
    }
}
