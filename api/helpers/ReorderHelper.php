<?php

namespace api\helpers;

class ReorderHelper
{
    public static function getParameters($oldValue, $newValue)
    {
        $newOrder = intval($newValue);
        $oldOrder = intval($oldValue);
        $inc = $oldOrder >= $newOrder ? 1 : -1;
        $bottomLimit = $inc > 0 ? $newOrder : ($oldOrder - $inc);
        $topLimit = $inc < 0 ? $newOrder : ($oldOrder - $inc);

        return [$inc, $bottomLimit, $topLimit];
    }
}
