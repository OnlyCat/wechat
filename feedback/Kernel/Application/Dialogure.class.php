<?php
	require_once("RedisExtend.class.php");
	require_once('MediaStrong.class.php');

	require_once("Bill.class.php");
    require_once('Music.class.php');

    require_once('BaiduVoice.model.php');
    include_once('CurlExtend.class.php');
	class Dialogure
	{

		//redis状态
		private $redis = null;

		public function __construct()
        {
			$this->redis = new RedisExtend();
            MediaStrong::getToken();
		}
		
		private  function getLastMsg($userId)
        {
			return $this->redis->get($userId);;
		}
		
		//设置回复消息到缓存
		private function setReplyMsg($wxid, /*  上次类型 */ $lastType,/*  上次消息 */ $lastMsg, /*  期望得到的类型 */ $wantType, /* 会话深度 */ $depth = 0, /* 处理模块 */  $modular = "", /* 参考数据 */ $consult = "")
        {
            $ret = $this->getLastMsg($wxid);
            if($ret){
                $data['sid'] = $ret['sid'];
            } else {
                $data['sid'] = md5(base64_encode(time()));
            }
			$data['uid'] = $wxid;
            $data['depth'] = $depth;
			$data['lastType'] = $lastType;
			$data['lastmsg'] = $lastMsg;
			$data['wanttype'] = $wantType;
			$data['modular'] = $modular;
			$data['consult'] = $consult;
			$this->redis->set($wxid, $data);
		}
		
		
		public function getReplyMsg($fromUser, $toUser, $type, $msg)
        {
            $lastMsg = $this->getLastMsg($fromUser);
			$result = array();
            $consult = "";
			switch($type)
			{
					case 'subscribe':
                         $result['type'] = 'text';
                        $remsg = file_get_contents("http://123.206.96.205//index.php/Interface/subscribe?wxid=" . $fromUser);
                        $result['msg'] = $remsg;
						break;
					case 'unsubscribe':
                        file_get_contents("http://123.206.96.205//index.php/Interface/unsubscribe?wxid=" . $fromUser);
						break;
					case 'text':
                       if(!$lastMsg){
                                   $this->setReplyMsg($fromUser, $type, $msg, $type);
                       }
                       $this->setChatMsg($fromUser, $fromUser, 1, $type, $msg);
                       if($lastMsg['consult'] && $lastMsg['wanttype'] == 'text') {
                           switch($lastMsg['modular']) {
                               case 'music':
                                       switch($lastMsg['depth']){
                                           case 0:
                                               $result['type'] = 'text';
                                               $result['msg'] = "你说话好没有层次！";
                                               break;
                                           case 1:
                                               if(array_key_exists($msg,$lastMsg['consult'])){
                                                   $music = new Music();
                                                   $musicUrl = $music->getMusicUrl($lastMsg['consult'][$msg]['mid']);
                                                   $result['type'] = 'text';
                                                   $result['msg'] = $musicUrl;
                                                   $result['type'] = 'music';
                                                   $result['msg'] = array("title"=>$lastMsg['consult'][$msg]['name'], "description" => $lastMsg['consult'][$msg]['artist'], "musicurl" => $musicUrl, "hqmusicurl" => $musicUrl, "thumbmediaid" => "0");
                                               } else {
                                                   $this->setReplyMsg($fromUser, $type, $msg, "text");
                                                   $result['type'] = 'text';
                                                   $result['msg'] = "你要找的歌我实在是找不到啊！";
                                               }
                                               break;
                                           default:
                                               $result['type'] = 'text';
                                               $result['msg'] = "出错了哦！";
                                               break;
                                       }
                                       break;
                               default:
                                   $result['type'] = 'text';
                                   $result['msg'] = "出错了哦！";
                                   break;
                           }
                       } else {
                           $remsg = $this->semanticsAnalysis($msg, $consult);
                           if(!$remsg){
                               $msgObj = file_get_contents('http://api.qingyunke.com/api.php?key=free&appid=0&msg=' . $msg);
                               $msgObj = json_decode($msgObj, true);
                               $remsg =  $msgObj['content'];
                               $this->setReplyMsg($fromUser, $type, $msg, "text");
                           } else {
                               $this->setReplyMsg($fromUser, $type, $msg, "text", 1, "music", $consult);
                           }
                           $result['type'] = 'text';
                           $result['msg']  = $remsg;
                           $this->setChatMsg($fromUser, $toUser, 0, $result['type'], $result['msg']);
                      
                            
                            $result['type'] = 'news';
                            $result['msg'] = '111';
                      }
                        break;
					case 'voice':
                        if(!$lastMsg){
                            $this->setReplyMsg($fromUser, $type, $msg['msgStr'], $type);
                        }
                        $this->setChatMsg($fromUser, $fromUser, 1, $type, $msg['mediaId']);
                        $remsg = $this->semanticsAnalysis($msg['msgStr'], $consult);
                        if(!$remsg){
                            $result['type'] = 'voice';
                            $remsg = file_get_contents('http://api.qingyunke.com/api.php?key=free&appid=0&msg=' . $msg['msgStr']);
                            $remsg = json_decode($remsg, true);
                            $reMedia = $remsg['content'];
                            $result['msg'] =  array('media_id' => $this->getVoice($reMedia));
                            $this->setChatMsg($fromUser, $toUser, 0, $result['type'], $this->getVoice($reMedia));
                        } else {
                            $result['type'] = 'text';
                            $result['msg']  = $remsg;
                            $this->setReplyMsg($fromUser, $type, $msg, "text", 1, "music", $consult);
                        }
						break;

                    case 'image':

                        $this->setReplyMsg($fromUser, $type, $msg['picUrl'], $type, 0);
                        $this->setChatMsg($fromUser, $fromUser, 1, $type, $msg['mediaId']);

                        $result['type'] = 'text';
                        $result['msg'] = "行了，行了，别发图了，我又不认识图片，PS：管理员可是能看到的呦！" ;

                        $this->setChatMsg($fromUser, $toUser, 0, $result['type'], $result['msg']);
                        break;

                    case 'video':
                        $this->setReplyMsg($fromUser, $type, "", $type, 0);
                        $this->setChatMsg($fromUser, $fromUser, 1, $type, $msg['mediaId']);
                        $result['type'] = 'text';
                        $result['msg'] = "你的视频真好看，如果合适的话我想把它分享给其他人看看" ;
                        $this->setChatMsg($fromUser, $toUser, 0, $result['type'], $result['msg']);
                        break;

                case "location":
                    $result['type'] = 'text';

                    $result['msg'] = "你的位置在" . $msg['label'] ."位于X：" . $msg['latitude']  . " , Y: " .  $msg['longitude'];
                    break;
                default:
						return array('type' => 'text', 'msg' => '这些知识我还没有学会呢！');
					
			}

			return $result;
		}

		//取得语音数据
		public function getVoice($msg)
        {
		    $baiduvoice = new BaiduVoice();
		    $mediasorong = new MediaStrong();
            $tmepfile = "/tmp/".  time() . ".mp3";
		    file_put_contents($tmepfile, $baiduvoice->getVoice($msg));
            $mediaid = $mediasorong->uploadTempVoice($tmepfile);
            unlink($tmepfile);
            return $mediaid;
        }

        //取得图像数据
        private function getImageMsg($picUrl)
        {


        }

        //保存聊天数据
        private function setChatMsg($wxid, $launch, $receive, $type, $msg)
        {
            $ret = $this->getLastMsg($wxid);
            $data['sid'] = $ret['sid'];
            $data['uid'] = $launch;
            $data['receive'] = $receive;
            $data['time'] = time();
            $data['type'] = $type;
            $data['msg'] = $msg;
            $data['createtime'] = time();
          //  $params = http_build_query($data);
        //    return   file_get_contents("http://123.206.96.205//index.php/Interface/setchatmsg?" . $params);
        }



        private function semanticsAnalysis($msg, &$result){

            $msg = str_replace(" ", "", $msg);
            $sign = array(
                'music' => array('我想听', "想听", '听', "播放", "唱", "唱一个", "来一个", "小曲", "搜歌"),
                );
            $tag = "";
            $keyWord = "";
            $reveal = "";
            foreach($sign as $key => $value){
                foreach ($value as $ikey => $ivalue){
                    $signLength = strlen($ivalue);
                    if($ivalue === substr($msg, 0, $signLength)){
                        $keyWord = substr($msg, $signLength);
                        $tag = $key;
                    }
                }
            }

           switch($tag){
               case 'music':
                   $music = new Music();
                   $result = $music->getMusicList($keyWord);
                   $reveal = "已为您找到《" . $keyWord . "》" . "请问您想听哪一个？" . "\n---------------------------\n";
                   foreach ($result as $r_key => $r_value){
                       $reveal .= "【" .(strtoupper($r_value['id']) ."】" .  str_replace("&nbsp;", "", strip_tags($r_value['name'])) ."-" . str_replace("&nbsp;", "", strip_tags($r_value['artist'])) . "\n");
                   }
                   $reveal .=  ("\n---------------------------\n" . "回复编号直接试听,点击歌曲还可下载呦！");
                   break;

           }
           return $reveal;
        }
	}
