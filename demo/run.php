<?php
require "./../vendor/autoload.php";

use CinCron\Cin;
use CinCron\exceptions\CinCornException;
use CinCron\utils\ConsoleUtil;
use CinCron\vo\ConfigVo;

try {
    init();
    run();
} catch (CinCornException $e) {
    ConsoleUtil::output($e->getMessage());
} catch (Exception $e) {
    ConsoleUtil::output($e->getMessage());
}


/**
 * @return ConfigVo
 */
function getConfig() {
    $configVo = new ConfigVo();
    $configVo->addTask(1, "测试1", "* * * * *", "php task_1.php");
    $configVo->addTask(2, "测试2", "* * * * *", "php task_2.php");
    return $configVo;
}

/**
 * 初始化
 * @throws CinCornException
 */
function init() {
    $taskManager = Cin::getTaskManager(getConfig());
    $taskManager->init();

    ConsoleUtil::output("init finished");
}

/**
 * 运行
 * @throws CinCornException
 * @throws Exception
 */
function run() {
    $taskManager = Cin::getTaskManager();
    $taskManager->run();

    ConsoleUtil::output("run finished");
}