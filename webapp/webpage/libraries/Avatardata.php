<?php
/**
 * Created by PhpStorm.
 * User: yangyaozhong
 * Date: 2017/9/29
 * Time: 16:58
 */
     defined('BASEPATH') OR exit('No direct script access allowed');

    /*
    *  接口地址 :http://avatardata.cn
    *  用户名: mrcatv5
    *  密  码: Only@avatardata
    */
    class Avatardata
    {
        private $url = "http://api.avatardata.cn";

        function __construct(){

        }

        function  getRregionNews($region = '内蒙古'){
             $appKey  = "e63964b8a60c46679416bf784d3d4b0a";
             $uri = $this->url . "/ActNews/Query?key=" . $appKey . "&keyword=" . urlencode($region);
             //暂时使用file_get_contents
              $resStr =  file_get_contents($uri);
              $ret =  json_decode($resStr, true);
              return $ret['result'];
        }
        function  getPositionCity($ip){
            $appKey = "df361b78065f48159d4e3e7d43cfc00a";
            $uri = $this->url . "/IpLookUp/LookUp?key=" . $appKey ."&ip=" . $ip;
            //暂时使用file_get_contents
            $resStr =  file_get_contents($uri);
            $ret =  json_decode($resStr, true);
            return preg_replace('/自治区|省|市|县/','',$ret['result']['area']);
        }
    }