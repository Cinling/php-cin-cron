<?php
namespace CinCron\vo;


use CinCron\utils\CronParseUtil;
use Exception;

/**
 * Class Task 单个任务的数据封装
 * @author Cinling
 * @package CinCron\vo
 */
class TaskVo extends BaseVo {
    /**
     * @var int 任务id。用于标识任务
     */
    public $id;
    /**
     * @var string 任务名字
     */
    public $name;
    /**
     * @var string cron时间。和 linux crontab 一致
     */
    public $cronTime;
    /**
     * @var string 运行命令
     */
    public $command;
    /**
     * @var string 当前任务状态
     */
    public $status;
    /**
     * @var int 上次运行时间（时间戳）
     */
    public $lastRunTime;
    /**
     * @var int 下一次运行时间（时间戳）
     */
    public $nextRunTime;
    /**
     * @var bool 是否激活
     */
    public $active;
    /**
     * @var int 修改任务时间（时间戳）
     */
    public $changeTime;
    /**
     * @var int 任务创建时间（时间戳）
     */
    public $createTime;

    /**
     * 初始化数据
     */
    public function init() {
        parent::init();
        $this->lastRunTime = 0;
        $this->createTime = time();
    }

    /**
     * 获取下一次运行时间
     * @return string 时间
     * @throws Exception
     */
    public function getNextRunTime()
    {
        $array = CronParseUtil::formatToDate($this->cronTime);
        return strtotime($array[0]);
    }
}