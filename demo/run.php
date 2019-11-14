<?php
require "./../vendor/autoload.php";

use cin\cron\Cin;
use cin\cron\exceptions\CinCornException;
use cin\cron\utils\ConsoleUtil;
use cin\cron\vo\ConfigVo;
use cin\cron\vos\FileConfigVo;


try {
    $configVo = new ConfigVo();
    $configVo->runtimeDir = __DIR__ . "/runtime";
    $taskPath = __DIR__;
    $configVo->addTask(1, "测试1", "* * * * *", "php {$taskPath}/task_1.php");
    $configVo->addTask(2, "测试2", "10 * * * *", "php {$taskPath}/task_2.php");
    $configVo->addTask(3, "测试3", "* 10 * * *", "php {$taskPath}/task_3.php");
    $configVo->addTask(4, "测试4", "* * 1 * *", "php {$taskPath}/task_4.php");
    $configVo->file = new FileConfigVo();
    Cin::load($configVo);

    Cin::init();
    ConsoleUtil::output("init finished");

    Cin::run();
    ConsoleUtil::output("run finished");
} catch (CinCornException $e) {
    ConsoleUtil::output($e->getMessage());
} catch (Exception $e) {
    ConsoleUtil::output($e->getMessage());
}