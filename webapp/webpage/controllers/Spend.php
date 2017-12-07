<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Spend extends CI_Controller {

	public function index()
	{
        $this->load->helper('url');
        $this->load->view('Spend/index');
	}

	public function weekSpendTotal(){
        $userid =  $userid = $this->input->get('auth');
        $weekDate = null;
	    $result = $this->spend_model->getWeekTotalDateRange($userid, null, $weekDate);
	    foreach ($result as $key => $value){
            $weekList[$key]['iscurrent'] = $value['date'] == date('Y-m-d');
            $weekList[$key]['money'] = $value['data']->money ?? 0;
        }
        sort($weekList);
        $weekData['spenddate'] = $weekDate;
        $weekData['spendlist'] = $weekList;
        echo json_encode($weekData);
    }

    public  function  weekSpendClass(){
        $userid =  $userid = $this->input->get('auth');
        $weekDate = null;
        $result = $this->spend_model->getWeekClassDateRange($userid, null, $weekDate);
        echo json_encode($result);
    }

    public function daySpend(){
        $userid =  $userid = $this->input->get('auth');
        $nowdata = date('Y-m-d');
        $daySpend = $this->spend_model->getDaySpendRecord($userid, $nowdata);
        echo json_encode($daySpend);
    }

    public function  getClassList(){
        $classify = file_get_contents("http://api.onlycat.xyz:81/index.php/inward/index");
        echo $classify;
    }

    public function  updateSpend(){
        $data['id'] = $_POST['id'];
        $data['userid'] = $_POST['uid'];
        $data['classify'] = $_POST['classify'];
        $data['childr'] = $_POST['childer'];
        $data['money'] = $_POST['money'];
        $data['spendtime'] = date('Y-m-d') . " " . $_POST['spendtime'];
        $data['descripe'] = $_POST['remark'];
        $ret = $this->spend_model->updateSpend($data);
       if($ret){
           echo  json_encode(array("success" => true));
       } else {
           echo  json_encode(array("success" => false));
       }
    }

}
