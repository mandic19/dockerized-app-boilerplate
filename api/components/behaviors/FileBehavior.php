<?php

namespace api\components\behaviors;

use api\components\orm\ActiveRecord;
use api\helpers\EmailHelper;
use api\helpers\FileHelper;
use api\models\Image;
use api\components\FileSystemResourceManager;
use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;
use yii\web\HttpException;
use yii\web\UploadedFile;

/**
 * Class FileBehavior
 * @package api\components\behaviors
 *
 * @property string $nameAttribute
 * @property string $storageKeyAttribute
 * @property string $mimeTypeAttribute
 * @property string $thumbImageIdAttribute
 * @property string $sizeAttribute
 * @property string $fileAttribute
 * @property string $storageTypeAttribute
 * @property string $_tmpFile
 * @property string $_tmpStorageLocation
 *
 */
class FileBehavior extends Behavior
{
    const STORAGE_TYPE_S3 = 'S3';
    /**
     * @var string model's attribute name that will be associated with original file name
     */
    public $nameAttribute = 'original_name';

    /**
     * @var string model's attribute name that will be associated with storage key
     */
    public $storageKeyAttribute = 'storage_key';

    /**
     * @var string Mime type attribute
     */
    public $mimeTypeAttribute = 'mime_type';

    /**
     * @var string $thumbImageIdAttribute thumb image id attribute
     */
    public $thumbImageIdAttribute = null;

    /**
     * @var string Mime type attribute
     */
    public $sizeAttribute = 'size';

    /**
     * @var string file attribute
     */
    public $fileAttribute = 'file';

    /**
     * @var string file attribute
     */
    public $storageTypeAttribute = 'storage_type';


    /**
     * @var string tmp file holder
     */
    private $_tmpFile = null;

    /**
     * @var string location for tmp file
     */
    private $_tmpStorageLocation = '@app/runtime/';

    /**
     * @inheritdoc
     */
    public function __destruct()
    {
        @unlink($this->_tmpFile);
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'processUploadedFile',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'processUploadedFile'
        ];
    }

    /**
     * @return UploadedFile|null
     */
    public function getUploadedFile()
    {
        if ($this->owner->hasProperty($this->fileAttribute)) {

            if ($this->owner->{$this->fileAttribute} instanceof UploadedFile) {
                return $this->owner->{$this->fileAttribute};
            }

            return UploadedFile::getInstance($this->owner, $this->fileAttribute);
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function processUploadedFile()
    {
        $file = $this->getUploadedFile();

        if (empty($file)) {
            return;
        }

        $this->_tmpFile = $file->tempName;

        $owner = $this->owner;

        $storageKey = $this->getGeneratedStorageKey($file->getExtension());

        $owner->{$this->fileAttribute} = $file; //just for validation purpose of owner object
        $owner->{$this->nameAttribute} = $file->name;
        $owner->{$this->sizeAttribute} = $file->size;
        $owner->{$this->mimeTypeAttribute} = $file->type;
        $owner->{$this->storageKeyAttribute} = $storageKey;
        $owner->{$this->storageTypeAttribute} = static::STORAGE_TYPE_S3;

        if (!$this->saveOnStorageAs($storageKey)) {
            throw new HttpException(500, 'Sorry, we are unable to upload your file.');
        }

        if ($this->getIsImage()) {
            $this->createImageRecord();
        }

    }

    public function getIsImage()
    {
        return FileHelper::getIsImageByMimeType($this->owner->{$this->mimeTypeAttribute});
    }

    public function createImageRecord()
    {
        if (empty($this->thumbImageIdAttribute)) {
            return;
        }

        $image = Image::findOne($this->owner->{$this->thumbImageIdAttribute});

        if (empty($image)) {
            $image = new Image();
        }

        $imageSize = getimagesize($this->getLocalFilePath());
        $image->width = $imageSize[0];
        $image->height = $imageSize[1];
        $image->size = $this->owner->{$this->sizeAttribute};
        $image->mime_type = $this->owner->{$this->mimeTypeAttribute};
        $image->original_name = $this->owner->{$this->nameAttribute};
        $image->storage_key = $this->owner->{$this->storageKeyAttribute};
        $image->storage_type = $this->owner->{$this->storageTypeAttribute};

        if (!$image->save()) {
            throw new HttpException(500, "Sorry, we weren't able to create thumb image for this file.");
        }

        $this->owner->{$this->thumbImageIdAttribute} = $image->id;
    }

    /**
     * Get Image storage key
     * @return string
     */
    public function getStorageKey()
    {
        return $this->owner->{$this->storageKeyAttribute};
    }

    /**
     * Regenerate image storage key, using this will put out of use all thumbs already created
     * Use example: when image is transformed
     */
    public function regenerateStorageKey()
    {
        $fileExtension = pathinfo($this->getStorageKey(), PATHINFO_EXTENSION);
        $this->owner->{$this->storageKeyAttribute} = $this->getGeneratedStorageKey($fileExtension);
    }

    /**
     * Generate unique storage_key
     * @param string $fileExtension
     * @return string
     */
    public function getGeneratedStorageKey($fileExtension = '')
    {
        return $this->getPathPrefix() . $this->getEnvPrefix() .
            md5(microtime(true) . $this->owner->{$this->nameAttribute}.mt_rand(0, 5000)) .
            "_" . mt_rand(0, 5000) . ".{$fileExtension}";
    }

    /**
     * Get thumb path
     * @return string
     */
    public function getPathPrefix()
    {
        $path = trim(Yii::$app->params['resourceManager']['s3.path.prefix'], '/');

        return $path ? $path . '/' : $path;
    }

    /**
     * Get environment prefix
     * @return string
     */
    public function getEnvPrefix()
    {
        return trim(Yii::$app->params['resourceManager']['s3.file.prefix']);
    }

    /**
     * Get local file instance
     * @return string|bool The function returns the read data or false on failure.
     */
    public function getLocalFile()
    {
        $filePath = $this->getLocalFilePath();

        if (!file_exists($filePath)) {
            return false;
        }

        return file_get_contents($filePath);
    }

    /**
     * Get file filepath on local storage
     * @return string
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function getLocalFilePath()
    {
        if (!empty($this->_tmpFile)) {
            return $this->_tmpFile;
        }

        $dirPath = Yii::getAlias($this->_tmpStorageLocation);
        $fileStorageKey = $this->getStorageKey();
        $fileExtension = '.' . pathinfo($fileStorageKey, PATHINFO_EXTENSION);
        $filePath = tempnam($dirPath, md5($fileStorageKey) . '-') . $fileExtension;
        if(!is_file($filePath)){
            $fileData = $this->readFileData($fileStorageKey);
            file_put_contents($filePath, $fileData);
        }

        return $this->_tmpFile = $filePath;
    }

    public function saveFromPath($path, $overwrite = true, $tryCreatingImageRecord = true)
    {
        $this->_tmpFile = $path;

        /* @var ActiveRecord $owner */
        $owner = $this->owner;

        $owner->{$this->nameAttribute} = basename($path);
        $owner->{$this->sizeAttribute} = @filesize($path);
        $owner->{$this->mimeTypeAttribute} = mime_content_type($path);
        $owner->{$this->storageTypeAttribute} = static::STORAGE_TYPE_S3;
        $owner->{$this->storageKeyAttribute} = $overwrite ?
            $owner->{$this->storageKeyAttribute} :
            $this->getGeneratedStorageKey(pathinfo($path, PATHINFO_EXTENSION));

        if ($overwrite && !$this->deleteFromStorage()) {
            throw new HttpException(500, 'Sorry, we are unable to overwrite uploaded file.');
        }

        if (!$this->saveOnStorage()) {
            throw new HttpException(500, 'Sorry, we are unable to upload your file.');
        }

        if ($tryCreatingImageRecord && $this->getIsImage()) {
            $this->createImageRecord();
        }

        return $owner->save();
    }

    /**
     * Save to path
     *
     * @param $path
     * @return string
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function saveToLocalPath($path, $expireAt = null)
    {
        $expireAt = $expireAt ?: strtotime('+1 hour');
        $fileStorageKey = $this->getStorageKey();
        $fileData = $this->readFileData($fileStorageKey);
        $fileExtension = '.' . pathinfo($fileStorageKey, PATHINFO_EXTENSION);
        $filePath = Yii::getAlias($path) . $fileStorageKey . '-' . $expireAt . $fileExtension;

        file_put_contents($filePath, $fileData);

        return $filePath;
    }

    /**
     * Save image on storage using object storage key
     * @return bool
     */
    public function saveOnStorage()
    {
        return $this->saveOnStorageAs($this->getStorageKey());
    }

    /**
     * Save image on storage as different file name
     * @param string $storageKey
     * @param array $options
     * @return bool
     */
    public function saveOnStorageAs($storageKey, $options = [])
    {
        // TODO: Remove FileSystemResourceManagement code when you switch to AWS S3

        if($this->getResourceManager() instanceof FileSystemResourceManager) {
            if($this->owner->{$this->fileAttribute} instanceof UploadedFile) {
                $file = $this->owner->{$this->fileAttribute};
            } else {
                $file = new UploadedFile([
                    'tempName' => $this->getLocalFilePath()
                ]);
            }
            return $this->getResourceManager()->save($file, $storageKey, $options);
        }

        if ($this->getResourceManager()->has($storageKey)) {
            EmailHelper::sendMessage(['html' => 'message'], Yii::$app->params['admin.email'], 'Image exists on storaget: ' . $storageKey, [
                'message' => print_r([
                    Yii::$app->request->getUrl()
                ], true)
            ]);
            $this->getResourceManager()->delete($storageKey);
        }
        return $this->getResourceManager()->write($storageKey, file_get_contents($this->getLocalFilePath()), $options);
    }

    /**
     * Delete Image from storage
     * @return bool
     */
    public function deleteFromStorage()
    {
        $filename = $this->getStorageKey();
        if ($this->getResourceManager()->has($filename)) {
            return $this->getResourceManager()->delete($filename);
        } else {
            return true;
        }
    }

    /**
     * Get file url
     * @return string url
     */
    public function getUnsignedUrl()
    {
        if($this->getResourceManager() instanceof FileSystemResourceManager) {
            return $this->getResourceManager()->getUrl($this->owner->{$this->storageKeyAttribute});
        }

        $client = $this->getResourceManager()->getAdapter()->getClient();

        return (string)$client->getObjectUrl($this->getResourceManager()->bucket,  $this->owner->{$this->storageKeyAttribute});
    }

    /**
     * Get file url
     * @return string url
     */
    public function getUrl()
    {
        if($this->getResourceManager() instanceof FileSystemResourceManager) {
            return $this->getResourceManager()->getUrl($this->owner->{$this->storageKeyAttribute});
        }

        $expire = Yii::$app->params['resourceManager']['s3.expire.time'];
        $client = $this->getResourceManager()->getAdapter()->getClient();

        $command = $client->getCommand('GetObject', [
            'Bucket' => $this->getResourceManager()->bucket,
            'Key' => $this->owner->{$this->storageKeyAttribute}
        ]);

        return (string)$client->createPresignedRequest($command, $expire)->getUri();
    }

    public function getFileContent()
    {
        return $this->getResourceManager()->read($this->getStorageKey());
    }

    /**
     * @return \League\Flysystem\Filesystem
     */
    protected function getResourceManager()
    {
        return Yii::$app->resourceManager;
    }


    protected function readFileData($storageKey) {
        $resourceManager = $this->getResourceManager();

        if($resourceManager instanceof FileSystemResourceManager) {
            $path = $resourceManager->getBasePath() . DIRECTORY_SEPARATOR . $storageKey;
            return file_get_contents($path);
        }

       return $resourceManager->read($storageKey);
    }
}
