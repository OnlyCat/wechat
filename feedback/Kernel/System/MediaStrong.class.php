<?php
include_once('CurlExtend.class.php');
class MediaStrong
{
   public static  function getToken(){
        $url = 'https://api.weixin.qq.com/cgi-bin/token';
        $data = file_get_contents(__CONFIG_PATH__ . "service.conf");
        $data = json_decode($data, true);
        $params['grant_type'] = 'client_credential';
        $params['appid'] = $data['appid'];
        $params['secret'] = $data['appsecret'];
        if($data['requesttime'] + $data['invalidseconds'] < time()){
            $wxconf = CurlExtend::getWebData($url, $params);
            $wxconf = json_decode($wxconf, true);
            $data['requesttime'] = time();
            $data['accesstoken'] = $wxconf['access_token'];
            $data['invalidseconds'] = $wxconf['expires_in'];
            file_put_contents((__CONFIG_PATH__  . "service.conf"), json_encode($data));
        }
       return $data['accesstoken'];
   }

   public function uploadTempVoice($filePath){
        $url = 'https://api.weixin.qq.com/cgi-bin/media/upload';
        $params['access_token'] = self::getToken();
        $params['type'] = 'voice';
        if (class_exists('\CURLFile')) {
            $data['media'] = new \CURLFile(realpath($filePath));
        } else {
            $data['media'] = '@'.realpath($filePath);
        }
        $params = http_build_query($params);
        $url = $url .'?' . $params;
        $ret =  CurlExtend::getWebData($url, $data ,1, 1 ,  1);
        $ret = json_decode($ret, true);
        return $ret['media_id'];
       }
}
