<?php
namespace cin\cron\vo;


use cin\cron\exceptions\CinCornException;
use cin\cron\utils\CronParseUtil;
use Exception;

/**
 * Class Task 单个任务的数据封装
 * @author Cinling
 * @package cin\cron\vo
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
    public $lastRunAt;
    /**
     * @var int 下一次运行时间（时间戳）
     */
    public $nextRunAt;
    /**
     * @var bool 是否激活
     */
    public $active;
    /**
     * @var int 修改任务时间（时间戳）
     */
    public $changeAt;
    /**
     * @var int 任务创建时间（时间戳）
     */
    public $createAt;

    /**
     * 初始化数据
     */
    public function init() {
        parent::init();
        $this->lastRunAt = 0;
        $this->createAt = time();
    }

    /**
     * 获取下一次运行时间
     * @return int 时间
     * @throws CinCornException
     */
    public function getNextRunAt()
    {
        $array = CronParseUtil::formatToDate($this->cronTime);
        return strtotime($array[0]);
    }
}