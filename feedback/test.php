<?php
/**
 * Created by PhpStorm.
 * User: onlycat
 * Date: 17-5-30
 * Time: 下午5:06
 */
set_include_path(
    dirname(__FILE__) . "/Kernel" . PATH_SEPARATOR .
    dirname(__FILE__) . "/Kernel/Application" . PATH_SEPARATOR .
    dirname(__FILE__) . "/Kernel/Extend" . PATH_SEPARATOR .
    dirname(__FILE__) . "/Kernel/Model" . PATH_SEPARATOR .
    dirname(__FILE__) . "/Kernel/Library" . PATH_SEPARATOR .
    dirname(__FILE__) . "/Kernel/System" . PATH_SEPARATOR .
    dirname(__FILE__) . "/Kernel/Template" . PATH_SEPARATOR .
    get_include_path());

include_once('MediaStrong.class.php');
define('APPID', 'wx1221623409f55df0');
define('SECRET', 'ad9b961b52762ff65fe3e8c12f03c3b1');

$t = new MediaStrong();
var_dump( $t->uploadTempVoice('/tmp/1496148683.mp3'));
