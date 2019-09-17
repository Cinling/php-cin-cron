<?php
namespace cin\cron\component;

use cin\cron\Cin;
use cin\cron\utils\ConsoleUtil;
use cin\cron\utils\JsonUtil;
use cin\cron\vo\ConfigVo;
use cin\cron\vo\TaskVo;
use Exception;

/**
 * Class Manager
 * 任务管理器
 * @package cin\cron
 */
class TaskManager {
    /**
     * 保存路径
     * @var string
     */
    private $savePath = "";
    /**
     * @var TaskVo[]
     */
    private $taskVoDict = [];

    /**
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
     * 初始化需要运行的任务
     * @throws Exception
     */
    public function init() {
        // 暂停所有定时任务
        $oldTaskVoDict = $this->readTaskVoDict();
        foreach ($oldTaskVoDict as $taskVo) {
            $taskVo->active = Cin::TASK_ACTIVE_FALSE;
        }
        $this->saveTaskVoDict($oldTaskVoDict);

        // 重新写入的任务数据
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
                $tmpTaskVo->active = Cin::TASK_ACTIVE_TRUE;
                $oldTaskVoDict[$taskId] = $tmpTaskVo;
            } else {
                $oldTaskVoDict[$taskId] = $taskVo;
            }
        }
        $this->saveTaskVoDict($oldTaskVoDict);
    }

    /**
     * 运行任务（一般是定时任务，每分钟运行一次）
     * @throws Exception
     */
    public function run() {
        $now = time();
        $runTaskVoDict = $this->getRunTaskVoDict();

        foreach ($runTaskVoDict as $taskVo) {
           $taskVo->status = Cin::TASK_STATUS_RUN;
        }
        $this->saveTaskVoDict($runTaskVoDict, false);

        $procTaskIdDict = []; // 进程池
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
     * 保存当前的任务列表
     * @param TaskVo[] $taskVoDict
     * @param bool $overwrite 是否覆盖。如果不覆盖则值替换其中部分数据
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
     * 从文件中读取任务列表数据
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
     * 获取可以运行的任务字典
     * @return TaskVo[]
     */
    private function getRunTaskVoDict() {
        $taskVoDict = $this->readTaskVoDict();
        $runTaskVoDict = [];
        $now = time();
        foreach ($taskVoDict as $taskVo) {
            if (
                $taskVo->status === Cin::TASK_STATUS_RUN
                || $taskVo->active === Cin::TASK_ACTIVE_FALSE
                || $taskVo->nextRunTime > $now
            ) {
                continue;
            }
            $runTaskVoDict[$taskVo->id] = $taskVo;
        }
        return $runTaskVoDict;
    }

    /**
     * 获取任务列表保存的文件路径
     * @return string
     */
    private function getTaskListFilename() {
        return  $this->savePath . "/task-list.json";
    }
}