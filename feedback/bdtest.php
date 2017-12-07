<?php
set_include_path(
    dirname(__FILE__) . "/Kernel" . PATH_SEPARATOR .
    dirname(__FILE__) . "/Kernel/Application" . PATH_SEPARATOR .
    dirname(__FILE__) . "/Kernel/Extend" . PATH_SEPARATOR .
    dirname(__FILE__) . "/Kernel/Model" . PATH_SEPARATOR .
    dirname(__FILE__) . "/Kernel/Library" . PATH_SEPARATOR .
    dirname(__FILE__) . "/Kernel/Template" . PATH_SEPARATOR .
    get_include_path());


header('Content-Type:audio/mp3');

include_once ('BaiduVoice.model.php');
require_once('CurlExtend.class.php');

$bd = new BaiduVoice();


echo $bd ->getVoice('好吧，我承认是在下输了', 4);

/*
define('APPID', 'wx1221623409f55df0');
define('SECRET', '0894da86e2a73872b6a47d22ab130963');

include_once ('MediaStrong.class.php');
$t = new MediaStrong();
var_dump($t->getToken());
*/