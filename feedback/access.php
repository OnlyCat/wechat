<?php
    set_include_path(
	dirname(__FILE__) . "/Kernel" . PATH_SEPARATOR .
	dirname(__FILE__) . "/Kernel/Application" . PATH_SEPARATOR .
	dirname(__FILE__) . "/Kernel/Extend" . PATH_SEPARATOR . 
	dirname(__FILE__) . "/Kernel/Model" . PATH_SEPARATOR .
	dirname(__FILE__) . "/Kernel/Library" . PATH_SEPARATOR .
  	dirname(__FILE__) . "/Kernel/System" . PATH_SEPARATOR .
	dirname(__FILE__) . "/Kernel/Template" . PATH_SEPARATOR .
	get_include_path());
    include_once("Initialization.class.php");
	

    define('__CONFIG_PATH__', dirname(dirname(__FILE__)). "/config/");
    $config = file_get_contents(__CONFIG_PATH__ . "server.conf");
    $config = json_decode($config, true);
    define('TOKEN', $config['token']);
    define('ENCODINGAESKEY', $config['encodingaeskey']);
    define('PATTERN', $config['pattern']);


    date_default_timezone_set('Asia/Shanghai');
    $signature = $_GET["signature"] ?? "";
    $timestamp = $_GET["timestamp"] ?? "";
    $nonce = $_GET["nonce"] ?? "";
    $echostr=$_GET['echostr'] ?? "";
    
    $wx=new Initialization($signature, $timestamp, $nonce, $echostr); 
    $wx->isValid();
    $wx->responseMsg();

