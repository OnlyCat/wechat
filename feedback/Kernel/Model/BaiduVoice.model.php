<?php
/**
 * Created by PhpStorm.
 * User: onlycat
 * Date: 17-5-30
 * Time: 下午4:08
 */
require_once('CurlExtend.class.php');
class BaiduVoice
{
    private function getToken()
    {
        $url = 'https://openapi.baidu.com/oauth/2.0/token';
        $params['grant_type'] = 'client_credentials';
        $params['client_id'] = 'iGBZY2uOAGqatkiT9qGQ6SXjGtXyRk9C';
        $params['client_secret'] = 'V4FUlcxP5uN2Hyny2TN4YMvj7k963As0';
        $data = CurlExtend::getWebData($url, $params, 1);
        $data = json_decode($data, true);
        return $data;
    }

    public function getVoice($text, $sex = 4, $speed = 5 )
    {
        $token = $this->getToken();
        $url = 'http://tsn.baidu.com/text2audio';
        $params['tex'] = $text;
        $params['lan'] = 'zh';
        $params['tok'] =$token['access_token'] ;
        $params['ctp'] = '1';
        $params['spd'] = $speed;
        $params['per'] = $sex;
        $params['cuid'] = 'MR:CA:T0:00:53:01';
        $data = CurlExtend::getWebData($url, $params);
        return $data;
    }
}