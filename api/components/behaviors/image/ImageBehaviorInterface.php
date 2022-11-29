<?php

namespace api\components\behaviors\image;

interface ImageBehaviorInterface
{
    /**
     * Resize image
     * @param $width
     * @param $height
     * @param bool $outbound
     */
    public function resize($width, $height, $outbound = false);
}
