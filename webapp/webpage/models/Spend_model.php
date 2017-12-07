<?php
/**
 * Created by PhpStorm.
 * User: yangyaozhong
 * Date: 2017/10/12
 * Time: 14:53
 */
    class Spend_model extends CI_Model{
        public function __construct(){
           parent::__construct();
           $this->load->database();
        }

        public  function getNewSpendRecord($userid){
            $this->db->select("id,userid,spendtime,classify,childr,money,descripe,inserttime,isconfirm");
            $this->db->where('userid', $userid);
            $this->db->order_by('inserttime', 'DESC');
            $ret = $this->db->get('wx_spend', 1);
            $result = $ret->result();
            if($result){
                return $result[0];
            }
            return false;
        }


        public  function getDaySpendRecord($userid, $spendtime){
            $result =  $this->db->select("id,spendtime,classify,childr,money,descripe,inserttime,isconfirm")
                    ->from("wx_spend")
                    ->group_start()
                        ->where("userid", $userid)
                        ->where("spendtime >=", $spendtime)
                    ->group_end()
                    ->order_by('spendtime')
                ->get();
            $result = $result->result();
            if($result){
                return $result;
            }
            return false;
        }

        private function getWeekDateRange($date = null){
            $nodeDate = time();
            $timeAxix = [];
            $startWeek = date('w');
            if($startWeek == 0){
                $startWeek = 7;
            }
            if($date != null){
                $validDate = time() - ($startWeek  * 24 * 3600);
                if(strtotime($date) <= $validDate){
                    $startWeek = 7;
                    $nodeDate = $validDate;
                }
            }
            $weekData['interval'] =  $startWeek;
            $weekData['strardate'] = date('Y-m-d', $nodeDate - (($startWeek - 1) * 24 *3600));
            $weekData['stopdate'] =  date('Y-m-d', $nodeDate);
            return $weekData;
        }

        public function getWeekTotalDateRange($userid, $date = null ,&$weekData){
            $weekData = $this->getWeekDateRange($date);
            $nodeDate = strtotime($weekData['stopdate']);
            //计算一周时间轴
            while($weekData['interval']){
                $result =  $this->db->select_sum("money")->from("wx_spend")->group_start()->where("userid", $userid)->where("date(spendtime)", date('Y-m-d', $nodeDate) )->group_end()->get();
                $timeAxix[$weekData['interval']--] = array(
                    'date' => date('Y-m-d', $nodeDate),
                    "data" => $result->result()[0]
                );
                $nodeDate -= (3600 * 24);
            }
            return $timeAxix;
        }

        public  function getWeekClassDateRange($userid, $date = null, &$weekData){
            $weekData = $this->getWeekDateRange($date);
            $nodeDate = strtotime($weekData['stopdate']);
            //计算一周时间轴

              $result =  $this->db->select_sum("money")->select("childr")->from("wx_spend")->
                group_start()->
                where("userid", $userid)->
                where("date(spendtime) >=", $weekData['strardate'])->
                where("date(spendtime) <=", $weekData['stopdate'])->
                group_end()->group_by('childr')->get();

            return $result->result();
        }

        public function getClassify(){
            $result =  $this->db->select_sum("money")->from("wx_spend")->group_start()->where("userid", $userid)->where("date(spendtime)", date('Y-m-d', $nodeDate) )->group_end()->get();
        }

        public function updateSpend($data){
            $result = $this->db->where('id', $data['id'])->where('userid', $data['userid'])->update('wx_spend', $data);
            if($result > 0){
                return $result;
            } else {
                return false;
            }
        }
    }

    #select date_format(spendtime, '%Y-%m-%d')  spendtime_1 , childr, format(sum(money),2) from wx_spend where spendtime >'2017-11-21' group by  spendtime_1, childr;
