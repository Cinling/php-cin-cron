<?php


namespace cin\cron\component;


use cin\cron\Cin;
use cin\cron\exceptions\CinCornException;
use cin\cron\vo\ConfigVo;
use cin\cron\vo\TaskVo;

/**
 * Class BaseManager task manager abstract class
 * @package cin\cron\component
 */
abstract class BaseManager {
    /**
     * task list
     * @var ConfigVo
     */
    protected $configVo = null;
    /**
     * @var string
     */
    protected $runtimeDir = "";


    /**
     * read task list
     * @return mixed
     */
    protected abstract function readTaskVoList();

    /**
     * save task list.
     * @param TaskVo[] $taskVoList
     * @param $overwrite bool is overwrite all task.
     *      true:   delete all task, and write by $taskVoList.
     *      false:  read all task, and change task by name. new if the task doesn't exists.
     * @return bool save done.
     * @throws CinCornException
     */
    protected abstract function saveTaskVoList(array $taskVoList, $overwrite = true);

    /**
     * record task runtime log
     * @param TaskVo $taskVo task
     * @param $exitCode int process exit code.
     * @return bool
     * @throws CinCornException
     */
    protected abstract function recordTaskRuntimeLog(TaskVo $taskVo, $exitCode);


    /**
     * @param ConfigVo $configVo
     */
    public function load(ConfigVo $configVo) {
        $this->configVo = $configVo;
    }

    /**
     * save task list to local, file system or database, this depends on the implementation class
     * @throws CinCornException
     */
    public function init() {
        $this->saveTaskVoList($this->configVo->taskVoList, false);
    }

    /**
     * run the task list
     * @throws CinCornException
     */
    public function run() {
        $taskVoList = $this->getTaskVoList();
        foreach ($taskVoList as $taskVo) {
            $taskVo->status = Cin::TASK_STATUS_RUN;
        }
        $this->saveTaskVoList($taskVoList, false);

        $procTaskIdDict = []; // process pool
        $pipesDict = []; // pipes map
        foreach ($taskVoList as $taskId => $taskVo) {
            $pipes = [["pipe" => "w"]];
            $process = proc_open($taskVo->command, [],  $pipesDict[$taskId]);
            if (!is_resource($process)) {
                $this->recordTaskRuntimeLog($taskVo, "-1");
                continue;
            }

            $procTaskIdDict[$taskId] = $process;
            $pipesDict[$taskId] = $pipes;
        }
        $now = time();

        while (count($procTaskIdDict)) {
            foreach ($procTaskIdDict as $taskId => $result) {
                $status = proc_get_status($result);
                if ($status['running'] == FALSE) {

                    $exitCode = $status["exitcode"];
                    proc_close($result);
                    unset($procTaskIdDict[$taskId]);

                    $taskVo = $taskVoList[$taskId];
                    $taskVo->status = Cin::TASK_STATUS_WAIT;
                    $taskVo->lastRunAt = $now;
                    $taskVo->nextRunAt = $taskVo->getNextRunAt();
                    $taskVo->changeAt = time();
                    $this->saveTaskVoList([$taskVo], false);
                    $this->recordTaskRuntimeLog($taskVo, $exitCode);
                }
            }

            usleep(100);
        }
    }

    /**
     * get task list.
     * @param bool $reload reload or not
     *      true:   reload form source
     *      false:  load form object's property
     * @return TaskVo[]
     */
    protected function getTaskVoList($reload = false) {
        if (empty($this->taskVoList) || $reload) {
            $this->taskVoList = $this->readTaskVoList();
        }
        return $this->taskVoList;
    }

    /**
     * get runtime dir
     */
    protected function getRuntimeDir() {
        return $this->configVo->runtimeDir;
    }
}