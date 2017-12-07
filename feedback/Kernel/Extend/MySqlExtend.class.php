<?php
/**
 * Created by PhpStorm.
 * User: yangyaozhong
 * Date: 2017/10/12
 * Time: 9:45
 */

    class MySqlExtend {
        private $host = '127.0.0.1';
        private $user = 'root';
        private $password = 'yang@db';
        private $dbname = 'wechat';

        private  static $_MYSQL = null;
        private static $Unique = null;

        public static function Initialization(){
            if(!self::$Unique){
               self::$Unique = new self();
            }
            return self::$Unique;
        }
        private function __construct(){
            self::$_MYSQL = new mysqli($this->host, $this->user, $this->password, $this->dbname);
            if(self::$_MYSQL->connect_errno){
                die("Connect failed: " . self::$_MYSQL->connect_error);
            }
        }

        public function insert($table , $item){
            $keys = array_keys($item);
            $values = array_values($item);
            $sql = "insert into " . $table . " (" . implode($keys, ",") .  ") values ('" . implode($values, "','") ."');";
            return self::$_MYSQL->query($sql);
        }
    }