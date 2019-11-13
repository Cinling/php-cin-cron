<?php


namespace cin\cron;

use cin\cron\component\FileManager;
use cin\cron\vo\ConfigVo;
use cin\cron\exceptions\CinCornException;

defined('STDOUT') or define('STDOUT', fopen('php://stdout', 'w'));

/**
 * Class Cin cin-cron global entry, static class
 * @package cin\cron
 */
class Cin {
    /**
     * task status: running
     */
    const TASK_STATUS_RUN = 1;
    /**
     * task status: waiting to run
     */
    const TASK_STATUS_WAIT = 2;

    /**
     * task active：yes
     */
    const TASK_ACTIVE_YES = 1;
    /**
     * task active：no
     */
    const TASK_ACTIVE_NO = 0;

    /**
     * @var null|FileManager single instance of task manager
     */
    private static $taskManager = null;

    /**
     * get task manager single instance
     * @param ConfigVo|null $configVo
     * @return FileManager
     * @throws CinCornException
     */
    public static function getTaskManager($configVo = null) {
        if (Cin::$taskManager === null) {
            if ($configVo === null) {
                $configVo = Cin::getDefaultConfigVo();
            }
            $configVo->validate();
            Cin::$taskManager = new FileManager();
            Cin::$taskManager->initByConfigVo($configVo);
        }
        return Cin::$taskManager;
    }

    /**
     * get default config
     * @return ConfigVo
     */
    private static function getDefaultConfigVo() {
        $config = new ConfigVo();
        return $config;
    }
}