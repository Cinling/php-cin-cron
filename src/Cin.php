<?php


namespace CinCron;

use CinCron\component\TaskManager;
use CinCron\vo\ConfigVo;
use CinCron\exceptions\CinCornException;

defined('STDOUT') or define('STDOUT', fopen('php://stdout', 'w'));

/**
 * Class Cin cin-cron 全局静态类、入口类
 * @package CinCron
 */
class Cin {
    /**
     * 任务状态：正在运行
     */
    const TASK_STATUS_RUN = 1;
    /**
     * 任务状态：等待运行
     */
    const TASK_STATUS_WAIT = 2;

    /**
     * 任务激活状态：激活中
     */
    const TASK_ACTIVE_TRUE = 1;
    /**
     * 任务激活状态：禁用
     */
    const TASK_ACTIVE_FALSE = 0;

    /**
     * @var null|TaskManager 任务管理器实例
     */
    private static $taskManager = null;

    /**
     * 获取任务管理器
     * @param ConfigVo|null $configVo
     * @return TaskManager
     * @throws CinCornException
     */
    public static function getTaskManager($configVo = null) {
        if (Cin::$taskManager === null) {
            if ($configVo === null) {
                $configVo = Cin::getDefaultConfigVo();
            }
            if (!$configVo->validate()) {
                throw new CinCornException("配置错误");
            }
            Cin::$taskManager = new TaskManager();
            Cin::$taskManager->initByConfig($configVo);
        }
        return Cin::$taskManager;
    }

    /**
     * 获取默认配置
     * @return ConfigVo
     */
    private static function getDefaultConfigVo() {
        $config = new ConfigVo();
        return $config;
    }
}