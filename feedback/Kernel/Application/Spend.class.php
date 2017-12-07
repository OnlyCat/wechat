<?php
    require_once ("MySqlExtend.class.php");
	Class Spend
	{
		public static function getFeedbackMsg($str)
		{
            $result = file_get_contents("http://api.onlycat.xyz:81/index.php/bill/index?msg=" . $str);
			return json_decode($result, true);
		}

        public function saveSpend($appid, $spendtime = null, $classify = null, $childr = null, $money = null, $descripe = null){
            $mysql = MySqlExtend::Initialization();
            $data['userid'] = $appid;
            $data['spendtime'] = $spendtime;
            $data['classify'] = $classify;
            $data['childr'] = $childr;
            $data['money'] = $money;
            $data['descripe'] = $descripe;
            $ret = $mysql->insert('wx_spend',$data);
           if($ret){
               return true;
           }
           return false;
		}
	}