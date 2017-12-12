<?php
    require_once("RedisExtend.class.php");
    require_once('MediaStrong.class.php');

    require_once('BaiduVoice.model.php');
    include_once('CurlExtend.class.php');

    require_once("Spend.class.php");
    require_once('Music.class.php');
    class MainDialogure
    {
        //redis状态
        private $redis = null;

        public function __construct()
        {
            $this->redis = new RedisExtend();
            MediaStrong::getToken();
        }

        private function getLastMsg($userId)
        {
            return $this->redis->get($userId);
        }

        /**
         * @param $wxid     微信id
         * @param $lastType 上次发送类型
         * @param $lastMsg  上次发送的消息
         * @param $wantType 期望得到的类型
         * @param int $depth 会话深度
         * @param string $modular 处理模块
         * @param string $consult 参考数据
         */
        private function setReplyMsg($openid, /*  上次类型 */ $lastType,/*  上次消息 */ $lastMsg, /*  期望得到的类型 */ $wantType, /* 会话深度 */ $depth = 0, /* 处理模块 */ $modular = "", /* 参考数据 */ $consult = "")
        {
            $ret = $this->getLastMsg($openid);
            if ($ret) {
                $data['sid'] = $ret['sid'];
            } else {
                $data['sid'] = md5(base64_encode(time()));
            }
            $data['uid'] = $openid;
            $data['depth'] = $depth;
            $data['lastType'] = $lastType;
            $data['lastmsg'] = $lastMsg;
            $data['wanttype'] = $wantType;
            $data['modular'] = $modular;
            $data['consult'] = $consult;
            $this->redis->set($openid, $data);
        }

        public function getReplyMsg($fromUser, $toUser, $type, $information)
        {
            switch ($type) {
                //关注事件
                case 'subscribe':
                    $result['type'] = 'text';
                    $result['content'] = "你发现了一个有趣的公众号";
                    return $result;

                //取消关注事件
                case 'unsubscribe':
                    break;

                //文字处理逻辑
                case 'text':
                    $result = $this->session_Process($fromUser, $information);
                    if(!$result){
                        $result['type'] = 'text';
                        $result['content'] =  $this->getChatMsg($information);
                    }
                    return$result;
                    break;

                //音频处理逻辑
                case 'voice':
                    $result = $this->session_Process($fromUser, $information['msgStr']);
                    if(!$result){
                        $msg = $this->getChatMsg($information['msgStr']);
                        $result['type'] = 'voice';
                        $result['content'] = array('media_id' =>  $this->getVoiceId($msg));
                    }
                    return $result;
                //视频处理逻辑
                case 'video':
                    $result['type'] = 'text';
                    $result['content'] = "你的视频真好看，如果合适的话我想把它分享给其他人看看" ;
                    return $result;
                    break;

                //图像消息处理
                case 'image':
                    $result['type'] = 'text';
                    $result['content'] = "行了，行了，别发图了，我又不认识图片，PS：管理员可是能看到的呦！" ;
                    return $result;
                    break;

                //地理位置事件
                case 'location':
                    break;
            }

        }

        /**
         *  语音转换逻辑
         * @param $msg
         * @return mixed
         */
        private function getVoiceId($msg)
        {
            $baiduvoice = new BaiduVoice();
            $mediasorong = new MediaStrong();
            $tmepfile = "/tmp/".  time() . ".mp3";
            file_put_contents($tmepfile, $baiduvoice->getVoice($msg));
            $mediaid = $mediasorong->uploadTempVoice($tmepfile);
            unlink($tmepfile);
            return $mediaid;
        }

        /**
         * 聊天程序
         * @param $msg
         * @return mixed
         */
        private  function getChatMsg($msg){
            $qyk_Str = file_get_contents('http://api.qingyunke.com/api.php?key=free&appid=0&msg=' . $msg);
            $qyk_Str = json_decode($qyk_Str, true);
            return $qyk_Str['content'];
        }


        /**
         * 會話處理邏輯
         * @param $fromUser
         * @param $information
         * @return array|string
         */
        private function session_Process($userid, $information){
            $lastMsgInfo = $this->getLastMsg($userid);
            $result = "";
            //会话逻辑处理逻辑处理
            $moduleInfo =  $lastMsgInfo['modular'] ? $lastMsgInfo['modular']:$this->participle_Process($information);
            if(is_array($moduleInfo)){
                $information = $moduleInfo['words'];
                $moduleInfo = $moduleInfo['process'];
            }
            switch ($moduleInfo) {
                //歌曲模块处理
                case 'music':
                    $result = $this->music_Process($lastMsgInfo['depth'], $information, $lastMsgInfo['consult']);
                    $depth = $lastMsgInfo['depth'] + 1;
                    $this->setReplyMsg($userid, "text", $information, null, $depth, "music", $lastMsgInfo['consult']);
                    break;

                //账单模块处理
                case 'spend':
                   $result = $this->spend_Process($userid, $lastMsgInfo['depth'], $information, $lastMsgInfo['consult']);
                   $depth = $lastMsgInfo['depth'] + 1;
                   $this->setReplyMsg($userid, "text", $information, null, $depth, "spend", $lastMsgInfo['consult']);
                   break;

                //进入网站app
                case 'webapp':
                    $webapp['title'] =  "猫先生实验室";
                    $webapp['description'] = "猫先生实验室为提供多种功能";
                    $webapp['picurl'] = 'http://dl.pinyin.sogou.com/cache/skins/uploadImage/2015/02/08/14233751722161_former.jpg';
                    $webapp['url'] = "http://webapp.onlycat.xyz:81?auth=" . $userid;
                    return array('type' => 'news',  "content" => $webapp);
                    break;

                default :
                    return false;
            }
            if(!$result) {
                $this->setReplyMsg($userid, "text", $information, null, 0, null, null);
                return false;
            }
            return $result;
        }

        /**
         * 字典分词逻辑处理
         * @param $sentence   原语句
         */
        private function participle_Process($sentence)
        {
            //字典
            $sign = array(
                'music' => array('我想听', "想听", '听', "播放", "唱", "唱一个", "来一个", "小曲", "搜歌", "歌曲"),
                'spend' => array("账单", "记一笔", "记账本", "记账", "記一筆", "記賬本"),
                'webapp' => array("猫先生", '猫窝')
            );
            $sentence = str_replace(" ", "", $sentence);
            $result = array('process' => null, 'words' => $sentence);
            foreach($sign as $key => $value){
                foreach ($value as $ikey => $ivalue){
                    $signLength = strlen($ivalue);
                    if($ivalue === substr($sentence, 0, $signLength)){;
                        $result = array('process' => $key, 'words' =>  substr($sentence, $signLength));
                    }
                }
            }
            return $result;
        }

        /**
         * 音乐播放功能
         * @param $depth     会话深度
         * @param $keyWord   词条
         * @param $musicList 歌曲列表
         * @return string
         */
        private function music_Process($depth, $keyWord, &$musicList)
        {
            $music = new Music();
            switch ($depth) {
                //获取歌曲列表
                case 0:
                    $musicList = $music->getMusicList($keyWord);
                    $result = "已为您找到《" . $keyWord . "》" . "请问您想听哪一个？" . "\n----------------------------------------------\n";
                    foreach ($musicList as $key => $value) {
                        $result .= "【" . (strtoupper($value['id']) . "】" . str_replace("&nbsp;", "", strip_tags($value['name'])) . "-" . str_replace("&nbsp;", "", strip_tags($value['artist'])) . "\n");
                    }
                    $result .= ("\n----------------------------------------------\n" . "回复编号直接试听,点击歌曲还可下载呦！");
                    return array('type' => "text", "content" => $result);
                //获取歌曲
                case 1:
                    if(array_key_exists($keyWord, $musicList)){
                        $musicUrl = $music->getMusicUrl($musicList[$keyWord]['mid']);
                        $result =  array("title" => $musicList[$keyWord]['name'], "description" => $musicList[$keyWord]['artist'], "musicurl" => $musicUrl, "hqmusicurl" => $musicUrl, "thumbmediaid" => "0");
                        return array('type' => "music", "content" => $result);
                    }
                    return false;
                default:
                    return false;
            }
        }

        /**
         *  账单逻辑处理
         * @param $depth
         * @param $keyWord
         * @param $musicList
         * @return array
         */
        private  function spend_Process($userid, $depth, $keyWord, &$SpendList){

            $spend = new Spend();
            switch ($depth) {
                //显示账单列表
                case 0:
                    $result = "\t\t-☆-猫先生账单程序-☆-\n";
                    $result .= "----------------------------------------------\n";
                    $result .= "欢迎使用猫先生账单功能，请在30秒内，说出你的(账单语音和文字均可)(*╹▽╹*)\n";
                    $result .= "----------------------------------------------\n";
                    return array('type' => "text", "content"=> $result);

                //账单处理程序
                case '1':
//                    $ret = $this->strToNumber($keyWord);
//                    foreach($ret as $key => $value){
//                        $keyWord = str_replace($key, $value, $keyWord);
//                    }
                    $SpendList = $spend->getFeedbackMsg($keyWord);
                    $ret = $spend->saveSpend($userid,  $SpendList['ReportMsg']['date'],  $SpendList['ReportMsg']['type'], $SpendList['ReportMsg']['branch'],  $SpendList['ReportMsg']['money'], $SpendList['ReportMsg']['describe']);
                    if(!$ret){
                        return array('type' => "text", "content"=>  "阿欧，账单记录失败了，猫先生已通知程序猿GG，相信问题很快就会解决");
                    }
                    $result = "\t\t-☆-账单小票-☆-\n";
                    $result .= "----------------------------------------------\n";
                    $result .= "时间：" . $SpendList['ReportMsg']['date'] . "\n";
                    $result .= "类型：" . $SpendList['ReportMsg']['type'] . "\n";
                    $result .= "种类：" . $SpendList['ReportMsg']['branch'] . "\n";
                    $result .= "金额：" . $SpendList['ReportMsg']['money'] . "\n";
                    $result .= "备注：" . $SpendList['ReportMsg']['describe'] . "\n";
                    $result .= "----------------------------------------------\n";
                    $result .= '点击<a href="http://webapp.onlycat.xyz:81?auth=' . $userid .'&module=spend"> 查看详情 </a>进行更多账单查看';
                    return array('type' => "text", "content"=>  $result);
                case '2':
                    return false;
                default:
                    return false;
            }
        }

        /**
         *  字符串转数字转换函数
         * @param $str
         * @return array
         */
       private function  strToNumber($str){
            $value = $number = $integerIndex = 0;
            $floatIndex = $symbol = 1;
            $result = [];
            $key = "";
            $numStr = ['零', '一','二','三','四','五','六','七','八','九','点','块','十','百','千','万'];
            $strIndex = mb_strlen($str, "utf-8");
            for($i = 0; $i <$strIndex; $i++){
                $char =  mb_substr($str, $i , 1, "utf-8");
                if(($sign = array_search($char, $numStr)) || in_array($char, $numStr)){
                    if($sign > 11 ){
                        $integerIndex = pow(10, ($sign - 11));
                        $number += ($integerIndex * $value);
                    } else if($sign < 10){
                        if($symbol > 0 ){
                            if($integerIndex == 0){
                                $value = ($value * 10 + $sign);
                            } else {
                                $value = $sign;
                            }

                        } else {
                            $floatIndex *= 10;
                            $value += (($sign * 1.0) / $floatIndex);
                        }
                    } else if($sign == 10 || $sign == 11){
                        if(!$symbol){
                            break;
                        }
                        $symbol--;
                    }
                    $key .= $char;
                } else {
                    $result[$key] = ($number + $value);
                    $value = $number = $integerIndex = 0;
                    $floatIndex = $symbol = 1;
                    $key = "";
                }
            }
            $result[$key] = ($number + $value);
            return array_filter($result);
        }

    }
