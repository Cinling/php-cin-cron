<?php
namespace CinCron\vo;

use CinCron\utils\JsonUtil;
use ReflectionObject;

/**
 * Class BaseVo 所有vo（ValueObject）的基类
 * @package CinCron\vo
 */
class BaseVo {
    /**
     * 构造方法
     * BaseVo constructor.
     */
    public function __construct() {
        $this->init();
    }

    /**
     * 初始化方法
     */
    public function init() {

    }

    /**
     * 使用数组初始化对象
     * @param $values
     */
    public function initByArray($values) {
        foreach ($values as $prop => $value) {
            if (property_exists($this, $prop)) {
                $this->$prop = $value;
            }
        }
    }

    /**
     * 使用json字符串初始一个实例列表
     * @param $jsonStr
     * @return static[]
     */
    public static function initListByJsonStr($jsonStr) {
        $voList = [];
        $jsonArr = JsonUtil::decode($jsonStr);
        foreach ($jsonArr as $row) {
            $vo = new static();
            $vo->initByArray($row);
            $voList[] = $vo;
        }
        return $voList;
    }
}