<?php


namespace cin\cron;

use cin\cron\component\BaseManager;
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
     * file size unit: KB
     */
    const KB = 1024;
    /**
     * file size unit: MB
     */
    const MB = 1048576;

    /**
     * @var ConfigVo
     */
    private static $configVo = null;

    /**
     * @var null|BaseManager single instance of task manager
     */
    private static $manager = null;

    /**
     * load config
     * @param ConfigVo $configVo
     * @throws CinCornException
     */
    public static function load(ConfigVo $configVo) {
        Cin::$configVo = $configVo;
        Cin::$manager = Cin::getManager();
    }

    /**
     * store task list
     * It is recommended to run once when the project is updated
     * @throws CinCornException
     */
    public static function init() {
        Cin::checkConfigVo();
        Cin::$manager->init();
    }

    /**
     * run task list by cron time
     * It is recommended that the system run once a minute
     * @throws CinCornException
     */
    public static function run() {
        Cin::checkConfigVo();
        Cin::$manager->run();
    }

    /**
     * Check CIn::$configVo
     * @throws CinCornException
     */
    private static function checkConfigVo() {
        if (false) {
            throw new CinCornException("not Implemented.");
        }
    }

    /**
     * init task manager
     * @throws CinCornException
     */
    private static function getManager() {
        Cin::checkConfigVo();
        $manager = null;
        if (Cin::$configVo->file !== null) {
            $manager = Cin::getFileManager();
        } else {
            throw new CinCornException("no manager config");
        }
        $manager->load(Cin::$configVo);
        return $manager;
    }


    /**
     * @return FileManager|BaseManager
     */
    private static function getFileManager() {
        if (Cin::$manager === null || !(Cin::$manager instanceof FileManager)) {
            Cin::$manager = new FileManager();
        }
        return Cin::$manager;
    }
}