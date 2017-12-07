 <?php
	Class Text
	{
		public static function getFeedbackMsg($str, $user, $sid = "")
		{
			$str = file_get_contents("http://api.tufaqixiang.cn/index.php/bill/index?msg=" . $str);
			$str = var_export(json_decode($str, true), TRUE);
			$result = array("type" => "text", "content" => $str);
			return $result;
		}
	}