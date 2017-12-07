<?php   
    $filepath = '/var/www/wechat/config';

    $access_config = file_get_contents($filepath . "/service.conf");
    $access_config = json_decode($access_config, true);
    $accessData = getAccessToken($access_config);
    $access_config['accesstoken'] = $accessData['access_token'];
    $access_config['requesttime'] = time();
    $access_config['invalidseconds'] = $accessData['expires_in'];
    file_put_contents($filepath . "/service.conf", json_encode($access_config));



    $webapp_config = file_get_contents($filepath . "/webapp.conf");
    $webapp_config = json_decode($webapp_config, true);	
    unset($webapp_config['signature']);
    $webapp_config['jsapi_ticket'] = getSignature($access_config['accesstoken']);
    $webapp_config['timestamp'] = $access_config['requesttime'];
    $webapp_config['signature'] = sha1(urldecode(http_build_query($webapp_config))); 
    file_put_contents($filepath . "/webapp.conf", json_encode($webapp_config));


    function getAccessToken($config) {

        $data['grant_type'] = 'client_credential';
	$data['appid'] = $config['appid'];
	$data['secret'] = $config['appsecret'];
	$tokenURL = "https://api.weixin.qq.com/cgi-bin/token";	
	$tokenJson=getWebData($tokenURL, $data, 0, 1);
	$tokenData = json_decode($tokenJson, true);
	return  $tokenData;
     }
     


     function getSignature($access_token){
       $data['type'] = 'jsapi';
       $data['access_token'] = $access_token;
       $signatureURL = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket';
       $signatureJson = getWebData($signatureURL, $data, 0, 1);
       $signatureData = json_decode($signatureJson, true);
       return  $signatureData['ticket'];
     }


     function getWebData($url, $params = null, $ispost = 0, $https = 0, $postfields = null){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
        }
        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
            if($postfields) {
                curl_setopt($ch,CURLOPT_SAFE_UPLOAD, true );
            }
        } else {
               if ($params) {
                  if (is_array($params)) {
                       $params = http_build_query($params);
                  }
                   curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
               } else {
                   curl_setopt($ch, CURLOPT_URL, $url);
               }
        }
        $response = curl_exec($ch);
        if($response === FALSE) { 
               return false;
        }
        curl_close($ch);
        return $response;
    }

   

  
    


    





