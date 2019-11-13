<?php


namespace cin\cron\component;


use cin\cron\Cin;
use cin\cron\vo\ConfigVo;
use cin\cron\vo\TaskVo;

abstract class BaseManager {
    /**
     * @var TaskVo[]
     */
    protected $taskVoList;

    /**
     * @param ConfigVo $configVo
     * @return void
     */
    public function initByConfigVo(ConfigVo $configVo) {
        $this->taskVoList;
    }

    /**
     * get list of all tasks
     * @return TaskVo[]
     */
    abstract public function getTaskVoList();

    /**
     * get list of all ready to run task
     * @return TaskVo[]
     */
    public function getReadyTaskVoList() {
        $taskVoList = $this->getTaskVoList();
        $now = time();
        $readyTaskVoList = [];
        foreach ($taskVoList as $taskVo) {
            if ($taskVo->active === Cin::TASK_ACTIVE_NO || $taskVo->status === Cin::TASK_STATUS_RUN || $taskVo->nextRunTime > $now) {
                continue;
            }
            $readyTaskVoList[] = $taskVo;
        }
        return $readyTaskVoList;
    }

    /**
     * save list of all tasks
     * @param TaskVo[] $taskVoList
     */
    public function saveTaskVoList(array $taskVoList) {
    }

    /**
     * add a task's running record.
     * @param TaskVo $taskVo task instance
     * @param int $status running status
     * @param int $runningMS How many milliseconds does it take to run
     */
    protected function addRunRecord(TaskVo $taskVo, $status, $runningMS) {
    }
}