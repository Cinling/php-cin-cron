<?php
namespace CinCron\utils;

use CinCron\vo\BaseVo;

/**
 * Class JsonUtil json工具
 * @package common\utils
 */
class JsonUtil {
    /**
     * @param $value
     * @param int $options
     * @return string
     */
    public static function encode($value, $options = 320) {
        return json_encode($value, $options);
    }

    /**
     * @param $jsonStr
     * @return array
     */
    public static function decode($jsonStr) {
        return json_decode($jsonStr, true);
    }
}