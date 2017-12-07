<?php
    class KuwoMusic
    {
        function getMusicInfo($name, $page = 0, $num = 1)
        {
            $url = "http://search.kuwo.cn/r.s?all=" . urlencode($name) . "&ft=music&itemset=web_2013&client=kt&pn=" . $page . "&rn=" . $num . "&rformat=json&encoding=utf8";
            $result = file_get_contents($url);
            $result = str_replace("'", '"', $result);
            $result = json_decode($result, true);
            for($i = 0; $i < $num; $i++) {
                $data[$i]['id'] = $result['abslist'][$i]['MUSICRID'];
                $data[$i]['name'] = $result['abslist'][$i]['SONGNAME'];
                $data[$i]['artist'] = $result['abslist'][$i]['ARTIST'];
            }
            return  $data;
        }

        function getMusicUrl($mid)
        {
           $url = "http://antiserver.kuwo.cn/anti.s?type=convert_url&rid=" . $mid . "&format=aac|mp3&response=url";
           $result = file_get_contents($url);
           return $result;
        }
    }
