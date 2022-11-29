<?php

namespace api\components\behaviors\image;

use ImageOptimizer\OptimizerFactory;
use Yii;
use yii\web\HttpException;
use api\components\behaviors\FileBehavior;
use api\models\ImageThumb;
use yii\imagine\Image as Imagine;
use Imagine\Image\Box;
use Imagine\Image\ManipulatorInterface;


class ImageBehavior extends FileBehavior implements ImageBehaviorInterface
{
    /**
     * @var string model's attribute name that will be associated with storage key
     */
    public $widthAttribute = 'width';

    /**
     * @var string Mime type attribute
     */
    public $heightAttribute = 'height';

    public $imageQuality = 75;


    /**
     * Apply transformation specification to an image
     *
     * @param $spec
     */
    public function applyTransformations($spec)
    {
        $specObj = new ImageSpecification($spec);

        if ($specObj->hasResizeParams()) {
            $this->resize($specObj->getWidth(), $specObj->getHeight(), $specObj->getShouldForceSize(), $specObj->getShouldForceSize());
        }
    }

    /**
     * Resize image
     * @param $width
     * @param $height
     * @param bool $outbound
     */
    public function resize($width, $height, $outbound = false, $forceSize = false)
    {
        $imagePath = $this->getLocalFilePath();

        if (!file_exists($imagePath)) {
            return;
        }

        $file = Imagine::getImagine()->open($imagePath);
        $fileSize = $file->getSize();
        $aspectRatio = $fileSize->getWidth() / $fileSize->getHeight();

        if ($height === null) {
            $height = ceil($width / $aspectRatio);
        }

        if ($width === null) {
            $width = ceil($height * $aspectRatio);
        }

        if ($forceSize) {
            if ($fileSize->getWidth() < $width) {
                $file->resize($fileSize->widen($width));
            }

            if ($fileSize->getHeight() < $height) {
                $file->resize($fileSize->heighten($height));
            }
        }

        if ($outbound) {
            $file->thumbnail(new Box($width, $height), ManipulatorInterface::THUMBNAIL_OUTBOUND)->save($imagePath, ['quality' => $this->imageQuality]);
        } else {
            $file->thumbnail(new Box($width, $height))->save($imagePath, [
                'quality' => $this->imageQuality,
                'jpeg_quality' => $this->imageQuality
            ]);
        }

        $this->optimizeImage($imagePath);
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
     * Generate thumb storage key
     * @param $spec
     * @return mixed
     */
    protected function generateThumbStorageKey($spec)
    {
        $specObj = new ImageSpecification($spec);
        $thumbPath = $this->getThumbPath() . $specObj->getKey();

        return $thumbPath . '/' . str_replace($this->getPathPrefix(), '', $this->getStorageKey());
    }

    /**
     * Get thumb path
     * @return string
     */
    protected function getThumbPath()
    {
        $thumbPath = trim(Yii::$app->params['resourceManager']['image.thumb.path'], '/') . '/';
        $basePath = trim(Yii::$app->params['resourceManager']['s3.path.prefix'], '/');

        return $basePath . '/' . $thumbPath;
    }

    /**
     * Set width and height file size from local file
     */
    public function setSizeFromLocalFile()
    {
        $file = $this->getLocalFile();
        if (!$file) {
            return;
        }

        $this->owner->{$this->widthAttribute} = $file->getSize()->getWidth();
        $this->owner->{$this->heightAttribute} = $file->getSize()->getHeight();
    }

    public function getOriginalWidth(){
        if(!empty($this->owner->{$this->widthAttribute})){
            return $this->owner->{$this->widthAttribute};
        }
        return $this->getLocalFile()->getSize()->getWidth();
    }

    public function getOriginalHeight(){
        if(!empty($this->owner->{$this->heightAttribute})){
            return $this->owner->{$this->heightAttribute};
        }
        return $this->getLocalFile()->getSize()->getHeight();
    }

    /**
     * Get local file instance
     * @return \Imagine\Image\ImageInterface
     */
    public function getLocalFile()
    {
        $imagePath = $this->getLocalFilePath();

        if (!file_exists($imagePath)) {
            return null;
        }

        return Imagine::getImagine()->open($imagePath);
    }

    /**
     * Get image url
     * @param string $spec
     * @return string url
     */
    public function getImageUrl($spec = ImageSpecification::THUMB_MEDIUM, $token = null)
    {
        $specObj = new ImageSpecification($spec, $this->getOriginalWidth(), $this->getOriginalHeight());

        return Yii::$app->urlManager->createUrl([
            '/image/view',
            'id' => $this->owner->id,
            'spec' => $specObj->getKey(),
            'token' => $token
        ]);
    }

    /**
     * Find appropriate image thumbAR object
     * @param $spec
     * @return mixed
     */
    public function findThumb($spec)
    {
        $specObj = new ImageSpecification($spec, $this->getOriginalWidth(), $this->getOriginalHeight());

        return ImageThumb::findOne([
            'spec_key' => $specObj->getKey(),
            'image_id' => $this->owner->id,
            'storage_key' => $this->generateThumbStorageKey($specObj->getKey())
        ]);
    }

    /**
     * Get image Thumb object
     * @param $spec
     * @return ImageThumb
     * @throws ImageSpecificationException
     */
    public function getThumb($spec)
    {
        $specObj = new ImageSpecification($spec);
        $thumb = $this->findThumb($specObj);

        if (empty($thumb)) {
            $thumb = $this->createThumb($specObj->getKey());
        }

        return $thumb;
    }

    /**
     * Create Image thumb object
     * @param $spec
     * @return ImageThumb
     * @throws HttpException
     */
    protected function createThumb($spec)
    {
        $specObj = new ImageSpecification($spec);

        $this->applyTransformations($specObj);

        $storageKey = $this->generateThumbStorageKey($specObj->getKey());

        if (!parent::saveOnStorageAs($storageKey, [
            'ACL' => 'public-read',
            'CacheControl' => 'max-age=31536000',
        ])) {
            throw new HttpException(500, 'Unable to save image thumb on storage.');
        }

        $thumb = new ImageThumb([
            'spec_key' => $specObj->getKey(),
            'image_id' => $this->owner->id,
            'storage_type' => $this->owner->storage_type,
            'storage_key' => $storageKey
        ]);

        if (!$thumb->save()) {
            throw new HttpException(500, 'Unable to save image thumb in DB.');
        }

        return $thumb;
    }

    public function optimizeImage($imagePath)
    {
        $factory = new OptimizerFactory();
        $optimizer = $factory->get();
        $optimizer->optimize($imagePath);
    }
}


