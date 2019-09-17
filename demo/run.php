<?php
require "./../vendor/autoload.php";

use CinCron\Cin;
use CinCron\exceptions\CinCornException;
use CinCron\vo\ConfigVo;

try {
    init();
} catch (CinCornException $e) {
    echo "异常";
}


/**
 * @return ConfigVo
 */
function getConfig() {
    $configVo = new ConfigVo();
    $configVo->addTask(1, "测试1", "* * * * *", "php 1");
    return $configVo;
}

/**
 * 初始化
 * @throws CinCornException
 */
function init() {
    $taskManager = Cin::getTaskManager(getConfig());
    $taskManager->init();

    echo "init finished";
}

/**
 * 运行
 * @throws CinCornException
 */
function run() {
    $taskManager = Cin::getTaskManager();
    $taskManager->init();

    echo "run finished";
}