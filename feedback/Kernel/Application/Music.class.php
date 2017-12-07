<?php
    require_once('KuwoMusic.model.php');
    class Music
    {
        private $music = "";
        public function __construct()
        {
            $this->music = new KuwoMusic();
        }

        function getMusicList($musicName = ""){
            $musicInfo = $this->music->getMusicInfo($musicName, 0, 10);
            if(!$musicInfo[0]['id']) {
               return false;
            }
            $result = array();
            $i = 0;
            foreach ($musicInfo  as $key => $value){
                    $result[] = array("id" => dechex($i), "mid" =>  $value['id'], "name" =>  $value['name'], "artist" => $value['artist']);
                    $i++;
            }
            return $result;
        }

        function getMusicUrl($musicId){
            return   $this->music->getMusicUrl($musicId);
        }
    }