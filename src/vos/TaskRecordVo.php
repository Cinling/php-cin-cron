<?php


namespace cin\cron\vos;


use cin\cron\vo\BaseVo;

/**
 * Class TaskRecordVo 任务运行记录
 * @package cin\cron\vos
 */
class TaskRecordVo extends BaseVo {
    /**
     * @var int 任务运行id
     */
    public $taskId;
    /**
     * @var int
     */
    public $exitCode;
}