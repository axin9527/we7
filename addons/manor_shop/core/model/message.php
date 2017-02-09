<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
class Ewei_DShop_Message{
    public function sendTplNotice($dephp_0, $dephp_1, $dephp_2, $dephp_3 = '', $dephp_4 = null){
        if (!$dephp_4){
            $dephp_4 = m('common') -> getAccount();
        }
        if (!$dephp_4){
            return;
        }
        return $dephp_4 -> sendTplNotice($dephp_0, $dephp_1, $dephp_2, $dephp_3);
    }
    public function sendCustomNotice($dephp_5, $dephp_6, $dephp_3 = '', $dephp_4 = null){{
            if (!$dephp_4){
                $dephp_4 = m('common') -> getAccount();
            }
            if (!$dephp_4){
                return;
            }
            $dephp_7 = "";
            if(is_array($dephp_6)){
                foreach ($dephp_6 as $dephp_8 => $dephp_9){
                    if (!empty($dephp_9['title'])){
                        $dephp_7 .= $dephp_9['title'] . ':' . $dephp_9['value'] . '
';
                    }else{
                        $dephp_7 .= $dephp_9['value'] . '
';
                        if ($dephp_8 == 0){
                            $dephp_7 .= '
';
                        }
                    }
                }
            }else{
                $dephp_7 = $dephp_6;
            }
            if (!empty($dephp_3)){
                $dephp_7 .= "<a href='{$dephp_3}'>点击查看详情</a>";
            }
            return $dephp_4 -> sendCustomNotice(array('touser' => $dephp_5, 'msgtype' => 'text', 'text' => array('content' => urlencode($dephp_7))));
        }
    }
    public function sendImage($dephp_5, $dephp_10){
        $dephp_4 = m('common') -> getAccount();
        return $dephp_4 -> sendCustomNotice(array('touser' => $dephp_5, 'msgtype' => 'image', 'image' => array('media_id' => $dephp_10)));
    }
    public function sendNews($dephp_5, $dephp_11, $dephp_4 = null){
        if(!$dephp_4){
            $dephp_4 = m('common') -> getAccount();
        }
        return $dephp_4 -> sendCustomNotice(array('touser' => $dephp_5, 'msgtype' => 'news', 'news' => array('articles' => $dephp_11)));
    }
}
