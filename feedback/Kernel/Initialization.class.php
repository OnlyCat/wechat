<?php
	include_once("MainDialogure.class.php");
	class Initialization
	{
		private $signature;
		private $timestamp;
		private $nonce;
		private $echostr;
		
		/*
		参数说明：
			signature	微信加密签名，signature结合了开发者填写的token参数和请求中的timestamp参数、nonce参数。
			timestamp	时间戳
			nonce		随机数
			echostr		随机字符串	
		*/
		function __construct($signature, $timestamp, $nonce, $echostr)
		{
			$this->signature = $signature;
			$this->timestamp = $timestamp;
			$this->nonce = $nonce;
			$this->echostr = $echostr;
		}

		//检测链接是否正常
		public function isValid()
		{
			$echostr=$this->echostr;
			if ($this->checkSignature()) {
        		echo $echostr;
			}
       	}	
		
		//校验签名
		private function checkSignature()
		{
			if(!defined("TOKEN")) {
			    throw new Exception('TOKEN is not defined!');
        	}
			$token = TOKEN;
       		$signature = $this->signature;
        	$timestamp = $this->timestamp;
        	$nonce = $this->nonce;
			$receiveMsgObj = array($token, $timestamp, $nonce);
			
			//use SORT_STRING rule
			sort($receiveMsgObj, SORT_STRING);
			$receiveMsgStr = implode($receiveMsgObj);
			$receiveMsgStr = sha1($receiveMsgStr);
	
			if( $receiveMsgStr == $this->signature ) {
				return true;
			} else {
				return false;
			}
		}

		//数据打包发送
		private function sendDataPack($msgType, $fromUser, $toUser, $msgObj)
		{
			$createTime = time();	
			switch ($msgType) {
				case 'text':
				    $textTpl = file_get_contents('./Kernel/Template/TextTpl.xml');
				    $resultStr = sprintf($textTpl, $fromUser, $toUser, $createTime, $msgType, $msgObj);
				    return  $resultStr;
                case 'voice':
                    $voiceTpl = file_get_contents('./Kernel/Template/VoiceTpl.xml');
                    $resultStr = sprintf($voiceTpl, $fromUser, $toUser, $createTime, $msgType, $msgObj['media_id']);
                    return  $resultStr;
                case 'music':
                    $voiceTpl = file_get_contents('./Kernel/Template/MusicTpl.xml');
                    $resultStr = sprintf($voiceTpl, $fromUser, $toUser, $createTime, $msgType, $msgObj['title'], $msgObj['description'], $msgObj['musicurl'], $msgObj['hqmusicurl']);
                    return  $resultStr;
                case 'image':
                    $voiceTpl = file_get_contents('./Kernel/Template/ImageTpl.xml');
                    $resultStr = sprintf($voiceTpl, $fromUser, $toUser, $createTime, $msgType, $msgObj['media_id']);
                    return  $resultStr;
                case 'news':
                    $voiceTpl = file_get_contents('./Kernel/Template/NewsTpl.xml');
                    $resultStr = sprintf($voiceTpl, $fromUser, $toUser, $createTime, $msgType, $msgObj['title'], $msgObj['description'], $msgObj['picurl'], $msgObj['url']);
                    return  $resultStr;
			}
		}
	
		//接收反馈并处理
		public function responseMsg()
		{
		//	$PostStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		
			$postStr = file_get_contents("php://input");	

			//收到的数据类型
			$takeType = "";
			//收到的消息
			$takeMsg = "";
			
			if (!empty($postStr)) {
				
				//解析xml对象
            	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
          
				//发送方用户表示
				$fromUser = $postObj->FromUserName;
				
				//接收方用户表示
          		$toUser = $postObj->ToUserName;

				//接受的消息类型
				$msgType = $postObj->MsgType;
				$msgObj=array();
				switch ($msgType) {
					case 'event':
						switch($postObj->Event) {
							//关注事件
							case 'subscribe':
								$takeType = "subscribe";
								break;	
							//取消关注事件
							case 'unsubscribe':
								$takeType = "unsubscribe";
								break;
						}
						break;
					//地理位置上传事件
					case 'location':
						//经度
						$arr['latitude'] = $postObj->Location_X;
						//纬度
						$arr['longitude'] = $postObj->Location_Y;
						//标记
						$arr['label'] = $postObj->Label;
						$takeType = 'location';
						$takeMsg = $arr;
						break;
						
					//文本消息处理
					case 'text':
						//接受到的文本消息内容
						$textStr = trim($postObj->Content);
						$takeType = 'text';
						$takeMsg = $textStr ;
						break;	
						
					//语音消息处理
					case 'voice':
						//接受到的语音转文本消息内容
						$voiceToStr = trim($postObj->Recognition);
                        $mediaId = trim($postObj->MediaId);
						$takeType = 'voice';
						$takeMsg = array('msgStr' => $voiceToStr, 'mediaId' => $mediaId) ;
						break;

                    //图像消息处理
                    case 'image':
                        $picUrl = trim($postObj->PicUrl);
                        $mediaId = trim($postObj->MediaId);
                        $takeType = 'image';
                        $takeMsg = array('picUrl' => $picUrl, 'mediaId' => $mediaId);
                        break;

                    //视频逻辑处理
                    case 'video':
                        $mediaId = trim($postObj->MediaId);
                        $takeType = 'video';
                        $takeMsg = array('mediaId' => $mediaId);
                        break;
                }

				//会话处理逻辑
                $replyMsg = (new MainDialogure())->getReplyMsg((string)$fromUser, (string)$toUser, $takeType, $takeMsg);
                echo $this->sendDataPack($replyMsg['type'], $fromUser, $toUser, $replyMsg['content']);;
			}
		}
	}
