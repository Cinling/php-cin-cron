<?php
namespace CinCron\component;

use CinCron\Cin;
use CinCron\utils\ConsoleUtil;
use CinCron\utils\JsonUtil;
use CinCron\vo\ConfigVo;
use CinCron\vo\TaskVo;

/**
 * Class Manager
 * 任务管理器
 * @package CinCron
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
     */
    public function init() {
        $filename = $this->getTaskListFilename();

        // 暂停所有定时任务
        $oldTaskVoDict = $this->readTaskVoDict();
        foreach ($oldTaskVoDict as $taskVo) {
            $taskVo->active = Cin::TASK_ACTIVE_FALSE;
        }
        $this->saveTaskVoDict($oldTaskVoDict);
        ConsoleUtil::output("close all tasks");

        // 重新写入的任务数据
        $taskVoDict = $oldTaskVoDict;


        $this->saveTaskVoDict($taskVoDict);
    }

    public function run() {

    }

    public function runByTask(TaskVo $taskVo) {

    }

    /**
     * 保存当前的任务列表
     * @param $taskVoDict
     */
    private function saveTaskVoDict($taskVoDict) {
        $content = JsonUtil::encode(array_values($taskVoDict));
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
     * 获取任务列表保存的文件路径
     * @return string
     */
    private function getTaskListFilename() {
        return  $this->savePath . "/task-list.json";
    }
}