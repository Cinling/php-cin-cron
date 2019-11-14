<?php
namespace cin\cron\vo;


use cin\cron\Cin;
use cin\cron\vos\FileConfigVo;

/**
 * Class ConfigVo
 * @package cin\cron
 */
class ConfigVo {
    /**
     * @var TaskVo[] task list
     * it's suggested to add tasks using [addTask()]
     * @see ConfigVo::addTask()
     */
    public $taskVoList = [];
    /**
     * @var string  runtime dir. Some files will be written here
     */
    public $runtimeDir = "";
    /**
     * @var null|FileConfigVo
     */
    public $file = null;

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