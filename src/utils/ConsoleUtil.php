<?php


namespace cin\cron\utils;


/**
 * Class ConsoleUtil command line utils
 * @copyright https://github.com/yiisoft/yii2/blob/2.0.26/framework/helpers/Console.php
 * @package cin\cron\utils
 */
class ConsoleUtil {
    /**
     * Prints text to STDOUT appended with a carriage return (PHP_EOL).
     *
     * @param string $string the text to print
     * @return int|bool number of bytes printed or false on error.
     */
    public static function output($string = null)
    {
        return static::stdout($string . PHP_EOL);
    }

    /**
     * Prints a string to STDOUT.
     *
     * @param string $string the string to print
     * @return int|bool Number of bytes printed or false on error
     */
    public static function stdout($string)
    {
        return fwrite(\STDOUT, $string);
    }
}