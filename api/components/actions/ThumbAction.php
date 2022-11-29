<?php
/**
 * Created by Marko Mandić on Jan, 2022
 * Email: marko.mandic.engr@gmail.com
 */

namespace api\components\actions;

use api\components\behaviors\image\ImageSpecification;
use api\helpers\TimeHelper;
use api\models\Image;
use Yii;
use yii\rest\Action;
use yii\web\Response;

class ThumbAction extends Action
{
    public function run($id, $spec = ImageSpecification::THUMB_MEDIUM_SQUARED)
    {
        ob_clean();
        ini_set('memory_limit', '256M');

        /** @var Image $image */
        $image = $this->findModel($id);

        $imageSpec = new ImageSpecification($spec);
        $thumb = $image->getThumb($imageSpec->getKey());

        $file = $thumb->getLocalFilePath();

        $year = TimeHelper::YEAR_SECONDS * 365;

        \Yii::$app->response->format = Response::FORMAT_RAW;

        header('Pragma: public');
        header('Content-Type: ' . mime_content_type($file));
        header("Cache-Control: max-age={$year}");
        header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + $year));
        header('Content-Length: ' . filesize($file));
        header("Access-Control-Allow-Origin: *");

        readfile($file);
        exit;
    }

    protected function renderImageResponse($eTag, $lastModified, $mimeType, $storageUrl)
    {
        $expireTime = TimeHelper::YEAR_SECONDS;
        $this->setResponseHeaderCache($expireTime, $eTag, $lastModified);

        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', $mimeType);

        return $this->controller->redirect($storageUrl);
    }

    protected function setResponseHeaderCache($expireTime, $eTag, $lastModified)
    {
        Yii::$app->response->headers->add('Expires', gmdate('D, j M Y H:i:s T', time() + $expireTime));
        Yii::$app->response->headers->add('ETag', $eTag);
        Yii::$app->response->headers->add('Cache-Control', "max-age={$expireTime}, must-revalidate");
        Yii::$app->response->headers->add('Last-Modified', gmdate('D, j M Y H:i:s', $lastModified) . ' GMT');
    }
}
