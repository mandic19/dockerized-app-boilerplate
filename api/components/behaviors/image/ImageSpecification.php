<?php
/**
 * Created by PhpStorm.
 * User: Nikola Radovic <nikola@2amigos.us>
 * Date: 12/1/2015
 * Time: 01:38 AM
 */

namespace api\components\behaviors\image;

use yii\base\Exception;

class ImageSpecification
{
    private $width;
    private $height;
    private $forceSize = false;

    const PARAM_WIDTH = 'w';
    const PARAM_HEIGHT = 'h';
    const PARAM_FORCE_SIZE = 'fs';

    const MAX_WIDTH = 'w999999';

    const THUMB_EXTRA_SMALL = 'h25';
    const THUMB_SMALL = 'w50_h50';
    const THUMB_MEDIUM = 'w100_h100';
    const THUMB_LARGE = 'w200_h200';
    const THUMB_EXTRA_LARGE = 'w1200_h1200';

    const THUMB_EXTRA_SMALL_SQUARED = 'w35_h35_fs1';
    const THUMB_SMALL_SQUARED = 'w50_h50_fs1';
    const THUMB_MEDIUM_SQUARED = 'w120_h120_fs1';
    const THUMB_LARGE_SQUARED = 'w200_h200_fs1';
    const THUMB_EXTRA_LARGE_SQUARED = 'w1200_h1200_fs1';


    public function __construct($spec = null)
    {
        if (!empty($spec)) {
            $this->setSpec($spec);
        }
    }

    public function setSpec($spec)
    {
        if (is_array($spec)) {
            $this->initFromArray($spec);
        } else if (is_string($spec)) {
            $this->tryParsingSpecKey($spec);
        } else if ($spec instanceof self) {
            $this->initFromOtherSpecObject($spec);
        } else {
            throw new ImageSpecificationException();
        }
    }


    private function initFromOtherSpecObject(ImageSpecification $spec)
    {
        $this->width = $spec->width;
        $this->height = $spec->height;

        $this->forceSize = $spec->forceSize;
    }

    private function initFromArray(array $spec)
    {
        if (empty($spec['width']) && empty($spec['height'])) {
            throw new ImageSpecificationParsingException();
        }

        if (!empty($spec['width'])) {
            $this->width = (int)$spec['width'];
        }

        if (!empty($spec['height'])) {
            $this->height = (int)$spec['height'];
        }

        if (!empty($spec['forceSize'])) {
            $this->forceSize = (bool)$spec['forceSize'];
        }
    }

    private function tryParsingSpecKey($spec)
    {
        $params = explode('_', $spec);
        $this->trySettingWidth($params);
        $this->trySettingHeight($params);
        $this->trySettingForceSizeParams($params);
    }

    private function trySettingWidth($params)
    {
        foreach ($params as $p) {
            if ($this->paramStartsWith($p, self::PARAM_WIDTH)) {
                $this->width = (int)substr($p, strlen(self::PARAM_WIDTH));
                return;
            }
        }
    }

    private function trySettingHeight($params)
    {
        foreach ($params as $p) {
            if ($this->paramStartsWith($p, self::PARAM_HEIGHT)) {
                $this->height = (int)substr($p, strlen(self::PARAM_HEIGHT));
                return;
            }
        }
    }

    private function trySettingForceSizeParams($params)
    {
        foreach ($params as $p) {
            if ($this->paramStartsWith($p, self::PARAM_FORCE_SIZE)) {
                $this->forceSize = (bool)substr($p, strlen(self::PARAM_FORCE_SIZE));
                return;
            }
        }
    }

    public function getKey()
    {
        $specs = array();

        if (!empty($this->width)) {
            $specs[] = self::PARAM_WIDTH . $this->width;
        }
        if (!empty($this->height)) {
            $specs[] = self::PARAM_HEIGHT . $this->height;
        }

        if ($this->forceSize) {
            $specs[] = self::PARAM_FORCE_SIZE . ((int)$this->forceSize);
        }

        return implode('_', $specs);
    }

    public function hasResizeParams()
    {
        return ($this->width !== null) || ($this->height !== null);
    }

    public function getShouldForceSize()
    {
        return $this->forceSize;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }


    private function paramStartsWith($haystack, $needle)
    {
        return !strncmp($haystack, $needle, strlen($needle));
    }
}

class ImageSpecificationException extends Exception
{

}

class ImageSpecificationParsingException extends ImageSpecificationException
{

}
