<?php


namespace cin\cron\vos;


use cin\cron\Cin;
use cin\cron\vo\BaseVo;

/**
 * Class FileConfigVo
 * @package cin\cron\vos
 */
class FileConfigVo extends BaseVo {
    /**
     * @var int     record file max size.
     *      If the file size is greater than $recordFileMaxSize, the file is renamed and a new file is created
     */
    public $recordFileMaxSize = Cin::MB;
}