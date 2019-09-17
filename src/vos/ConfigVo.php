<?php
namespace CinCron\vo;


use CinCron\exceptions\CinCornException;

/**
 * Class Config 配置类
 * @package CinCron
 */
class ConfigVo {
    /**
     * @var string 日志目录
     */
    public $savePath = "./cin-cron-runtime";
    /**
     * @var TaskVo[] 任务列表
     */
    public $taskVoList = [];

    /**
     * 验证配置是否合法
     * @return bool
     * @throws CinCornException
     */
    public function validate() {
        if (empty($this->savePath)) {
            throw new CinCornException("数据保存路径不能为空");
        }
        return true;
    }

    /**
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
        $this->taskVoList[] = $taskVo;
    }
}