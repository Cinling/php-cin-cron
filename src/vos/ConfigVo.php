<?php
namespace cin\cron\vo;


use cin\cron\Cin;
use cin\cron\exceptions\CinCornException;

/**
 * Class ConfigVo
 * @package cin\cron
 */
class ConfigVo {
    /**
     * @var string log path
     */
    public $savePath = "./cin-cron-runtime";
    /**
     * @var TaskVo[] task list
     * it's suggested to add tasks using [addTask()]
     * @see ConfigVo::addTask()
     */
    public $taskVoList = [];

    /**
     * validate config values
     * @throws CinCornException
     */
    public function validate() {
        if (empty($this->savePath)) {
            throw new CinCornException("no save path");
        }
    }

    /**
     * add one task
     * @example $this->addTask(1, "test task", "* * * * *", "php /path/to/testTask.php");
     * @param $id
     * @param $name
     * @param $cronTime
     * @param $command
     */
    public function addTask($id, $name, $cronTime, $command) {
        $taskVo = new TaskVo();
        $taskVo->id = $id;
        $taskVo->name = $name;
        $taskVo->cronTime = $cronTime;
        $taskVo->command = $command;
        $taskVo->active = Cin::TASK_ACTIVE_YES;
        $taskVo->status = Cin::TASK_STATUS_WAIT;
        $this->taskVoList[] = $taskVo;
    }
}