<?php
namespace cin\cron\component;

use cin\cron\Cin;
use cin\cron\utils\JsonUtil;
use cin\cron\vo\ConfigVo;
use cin\cron\vo\TaskVo;
use Exception;

/**
 * Class TaskManager
 * task manager
 * @package cin\cron
 */
class TaskManager {
    /**
     * save path
     * @var string
     */
    private $savePath = "";
    /**
     * @var TaskVo[]
     */
    private $taskVoDict = [];

    /**
     * use ConfigVo init the class
     * @param ConfigVo $configVo
     */
    public function initByConfig(ConfigVo $configVo) {
        $this->savePath = $configVo->savePath;
        foreach ($configVo->taskVoList as $taskVo) {
            $this->taskVoDict[$taskVo->id] = $taskVo;
        }

        if (!file_exists($this->savePath)) {
            mkdir($this->savePath, 0744, true);
        }
    }

    /**
     * init task list
     * It is recommended to run by update project once
     * @throws Exception
     */
    public function init() {
        // pause all tasks
        $oldTaskVoDict = $this->readTaskVoDict();
        foreach ($oldTaskVoDict as $taskVo) {
            $taskVo->active = Cin::TASK_ACTIVE_NO;
        }
        $this->saveTaskVoDict($oldTaskVoDict);

        // rewrite task list
        foreach ($this->taskVoDict as $taskId => $taskVo) {
            if (isset($oldTaskVoDict[$taskId])) {
                $tmpTaskVo = $oldTaskVoDict[$taskId];
                $tmpTaskVo->name = $taskVo->name;
                $tmpTaskVo->cronTime = $taskVo->cronTime;
                $tmpTaskVo->command = $taskVo->command;
                $nextRunTime = $tmpTaskVo->getNextRunTime();
                if ($nextRunTime < $tmpTaskVo->nextRunTime) {
                    $tmpTaskVo->nextRunTime = $nextRunTime;
                }
                $tmpTaskVo->active = Cin::TASK_ACTIVE_YES;
                $oldTaskVoDict[$taskId] = $tmpTaskVo;
            } else {
                $oldTaskVoDict[$taskId] = $taskVo;
            }
        }
        $this->saveTaskVoDict($oldTaskVoDict);
    }

    /**
     * run task list
     * It is recommended to run every minute with crontab.
     * @throws Exception
     */
    public function run() {
        $now = time();
        $runTaskVoDict = $this->getRunTaskVoDict();

        foreach ($runTaskVoDict as $taskVo) {
           $taskVo->status = Cin::TASK_STATUS_RUN;
        }
        $this->saveTaskVoDict($runTaskVoDict, false);

        $procTaskIdDict = []; // process pool
        foreach ($runTaskVoDict as $taskId => $taskVo) {
            $procTaskIdDict[$taskId] = proc_open($taskVo->command, [], $pipe);
        }

        while (count($procTaskIdDict)) {
            foreach ($procTaskIdDict as $taskId => $result) {
                $status = proc_get_status($result);
                if ($status['running'] == FALSE) {
                    proc_close($result);
                    unset($procTaskIdDict[$taskId]);

                    $taskVo = $runTaskVoDict[$taskId];
                    $taskVo->status = Cin::TASK_STATUS_WAIT;
                    $taskVo->lastRunTime = $now;
                    $taskVo->nextRunTime = $taskVo->getNextRunTime();
                    $taskVo->changeTime = time();
                    $this->saveTaskVoDict([$taskId => $taskVo], false);
                }
            }

            usleep(100);
        }
    }

    /**
     * save current task list
     * @param TaskVo[] $taskVoDict
     * @param bool $overwrite is overwrite.
     *      true: overwrite by $taskVoDict.
     *      false: merge by $taskVoDict.
     */
    private function saveTaskVoDict($taskVoDict, $overwrite = true) {
        if (!$overwrite) {
            $fileTaskVoDict = $this->readTaskVoDict();
            foreach ($taskVoDict as $taskVo) {
                $fileTaskVoDict[$taskVo->id] = $taskVo;
            }
            $content = JsonUtil::encode(array_values($fileTaskVoDict));
        } else {
            $content = JsonUtil::encode(array_values($taskVoDict));
        }
        file_put_contents($this->getTaskListFilename(), $content);
    }

    /**
     * read task list form local file
     * @return TaskVo[]
     */
    private function readTaskVoDict() {
        $content = file_get_contents($this->getTaskListFilename());
        if (empty($content)) {
            return [];
        }
        /** @var TaskVo[] $taskVoList */
        $taskVoList = TaskVo::initListByJsonStr($content);
        $taskVoDict = [];
        foreach ($taskVoList as $taskVo) {
            $taskVoDict[$taskVo->id] = $taskVo;
        }
        return $taskVoDict;
    }

    /**
     * get task list that are ready to run.
     * @return TaskVo[]
     */
    private function getRunTaskVoDict() {
        $taskVoDict = $this->readTaskVoDict();
        $runTaskVoDict = [];
        $now = time();
        foreach ($taskVoDict as $taskVo) {
            if (
                $taskVo->status === Cin::TASK_STATUS_RUN
                || $taskVo->active === Cin::TASK_ACTIVE_NO
                || $taskVo->nextRunTime > $now
            ) {
                continue;
            }
            $runTaskVoDict[$taskVo->id] = $taskVo;
        }
        return $runTaskVoDict;
    }

    /**
     * get file path where the task list is save
     * @return string
     */
    private function getTaskListFilename() {
        return  $this->savePath . "/task-list.json";
    }
}