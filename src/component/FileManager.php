<?php


namespace cin\cron\component;


use cin\cron\exceptions\CinCornException;
use cin\cron\utils\JsonUtil;
use cin\cron\vo\TaskVo;

/**
 * Class FileManager
 * File implementation of BaseManager
 * @package cin\cron\component
 */
class FileManager extends BaseManager {
    /**
     * @var string
     */
    private $savePath = "";

    /**
     * FileManager constructor.
     * @param $savePath string File save path.
     * @throws CinCornException
     */
    public function __construct($savePath) {
        if (empty($savePath)) {
            throw new CinCornException('$savePath was empty');
        }
        $this->savePath = $savePath;
        if (!file_exists($this->savePath)) {
            mkdir($this->savePath, 0755, true);
        }
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
        // TODO: Implement recordTaskRuntimeLog() method.
    }

    /**
     * @return string
     */
    private function getTaskListFilename() {
        return $this->savePath . "/task-list.json";
    }
}