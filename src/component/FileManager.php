<?php


namespace cin\cron\component;


use cin\cron\exceptions\CinCornException;
use cin\cron\utils\JsonUtil;
use cin\cron\vo\ConfigVo;
use cin\cron\vo\TaskVo;
use cin\cron\vos\FileConfigVo;

/**
 * Class FileManager
 * File implementation of BaseManager
 * @package cin\cron\component
 */
class FileManager extends BaseManager {
    /**
     * @var FileConfigVo
     */
    private $fileConfigVo;

    /**
     * load config
     * @param ConfigVo $configVo
     */
    public function load(ConfigVo $configVo) {
        parent::load($configVo);
        $this->fileConfigVo = $configVo->file;
    }

    /**
     * get file save path
     * @return string
     */
    public function getSavePath() {
        $savePath = $this->getRuntimeDir() . "/file-manager";
        if (!file_exists($savePath)) {
            mkdir($savePath, 0755, true);
        }
        return $savePath;
    }

    /**
     * read task list
     * @return TaskVo[]
     */
    protected function readTaskVoList() {
        $content = file_get_contents($this->getTaskListFilename());
        if (empty($content)) {
            return [];
        }
        return TaskVo::initListByJsonStr($content);
    }

    /**
     * save task list.
     * @param TaskVo[] $taskVoList
     * @param $overwrite bool is overwrite all task.
     *      true:   delete all task, and write by $taskVoList.
     *      false:  read all task, and change task by name. new if the task doesn't exists.
     * @return bool save done.
     */
    protected function saveTaskVoList(array $taskVoList, $overwrite = true) {
        if (!$overwrite) {
            $list = $this->readTaskVoList();
            $dict = [];
            foreach ($list as $vo) {
                $dict[$vo->id] = $vo;
            }
            foreach ($taskVoList as $vo) {
                $list[$vo->id] = $vo;
            }
            $content = JsonUtil::encode(array_values($list));
        } else {
            $content = JsonUtil::encode(array_values($taskVoList));
        }
        return boolval(file_put_contents($this->getTaskListFilename(), $content));
    }

    /**
     * record task runtime log
     * @param TaskVo $taskVo task
     * @param $exitCode int process exit code.
     * @return bool
     * @throws CinCornException
     */
    protected function recordTaskRuntimeLog(TaskVo $taskVo, $exitCode) {
        // TODO
        return true;
    }

    /**
     * @return string
     */
    private function getTaskListFilename() {
        return $this->getSavePath() . "/task-list.json";
    }

    /**
     * @return string
     */
    private function getTaskRecordFilename() {
        return $this->getSavePath() . "/task-record.json";
    }
}