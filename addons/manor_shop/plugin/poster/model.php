<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
if (!class_exists('PosterModel')){
    class PosterModel extends PluginModel{
        public function checkScan(){
            global $_W, $_GPC;
            $dephp_0 = m('user') -> getOpenid();
            $dephp_1 = intval($_GPC['posterid']);
            if (empty($dephp_1)){
                return;
            }
            $dephp_2 = pdo_fetch('select id,times from ' . tablename('manor_shop_poster') . ' where id=:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $dephp_1));
            if (empty($dephp_2)){
                return;
            }
            $dephp_3 = intval($_GPC['mid']);
            if (empty($dephp_3)){
                return;
            }
            $dephp_4 = m('member') -> getMember($dephp_3);
            if (empty($dephp_4)){
                return;
            }
            $this -> scanTime($dephp_0, $dephp_4['openid'], $dephp_2);
        }
        public function scanTime($dephp_0, $dephp_5, $dephp_2){
            if ($dephp_0 == $dephp_5){
                return;
            }
            global $_W, $_GPC;
            $dephp_6 = pdo_fetchcolumn('select count(*) from ' . tablename('manor_shop_poster_scan') . ' where openid=:openid  and posterid=:posterid and uniacid=:uniacid limit 1', array(':openid' => $dephp_0, ':posterid' => $dephp_2['id'], ':uniacid' => $_W['uniacid']));
            if ($dephp_6 <= 0){
                $dephp_7 = array('uniacid' => $_W['uniacid'], 'posterid' => $dephp_2['id'], 'openid' => $dephp_0, 'from_openid' => $dephp_5, 'scantime' => time());
                pdo_insert('manor_shop_poster_scan', $dephp_7);
                pdo_update('manor_shop_poster', array('times' => $dephp_2['times'] + 1), array('id' => $dephp_2['id']));
            }
        }
        public function createCommissionPoster($dephp_0, $dephp_8 = 0){
            global $_W;
            $dephp_9 = 2;
            if (!empty($dephp_8)){
                $dephp_9 = 3;
            }
            $dephp_2 = pdo_fetch('select * from ' . tablename('manor_shop_poster') . ' where uniacid=:uniacid and type=:type and isdefault=1 limit 1', array(':uniacid' => $_W['uniacid'], ':type' => $dephp_9));
            if (empty($dephp_2)){
                return '';
            }
            $dephp_10 = m('member') -> getMember($dephp_0);
            if (empty($dephp_2)){
                return "";
            }
            $dephp_11 = $this -> getQR($dephp_2, $dephp_10, $dephp_8);
            if (empty($dephp_11)){
                return "";
            }
            return $this -> createPoster($dephp_2, $dephp_10, $dephp_11, false);
        }
        public function getFixedTicket($dephp_2, $dephp_10, $dephp_12){
            global $_W, $_GPC;
            $dephp_13 = md5("manor_shop_poster:{$_W['uniacid']}:{$dephp_10['openid']}:{$dephp_2['id']}");
            $dephp_14 = '{"action_info":{"scene":{"scene_str":"' . $dephp_13 . '"} },"action_name":"QR_LIMIT_STR_SCENE"}';
            $dephp_15 = $dephp_12 -> fetch_token();
            $dephp_16 = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $dephp_15;
            $dephp_17 = curl_init();
            curl_setopt($dephp_17, CURLOPT_URL, $dephp_16);
            curl_setopt($dephp_17, CURLOPT_POST, 1);
            curl_setopt($dephp_17, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($dephp_17, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($dephp_17, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($dephp_17, CURLOPT_POSTFIELDS, $dephp_14);
            $dephp_18 = curl_exec($dephp_17);
            $dephp_19 = @json_decode($dephp_18, true);
            if(!is_array($dephp_19)){
                return false;
            }
            if (!empty($dephp_19['errcode'])){
                return error(-1, $dephp_19['errmsg']);
            }
            $dephp_20 = $dephp_19['ticket'];
            return array('barcode' => json_decode($dephp_14, true), 'ticket' => $dephp_20);
        }
        public function getQR($dephp_2, $dephp_10, $dephp_8 = 0){
            global $_W, $_GPC;
            $dephp_21 = $_W['acid'];
            if ($dephp_2['type'] == 1){
                $dephp_22 = m('qrcode') -> createShopQrcode($dephp_10['id'], $dephp_2['id']);
                $dephp_11 = pdo_fetch('select * from ' . tablename('manor_shop_poster_qr') . ' where openid=:openid and acid=:acid and type=:type limit 1', array(':openid' => $dephp_10['openid'], ':acid' => $_W['acid'], ':type' => 1));
                if (empty($dephp_11)){
                    $dephp_11 = array('acid' => $dephp_21, 'openid' => $dephp_10['openid'], 'type' => 1, 'qrimg' => $dephp_22,);
                    pdo_insert('manor_shop_poster_qr', $dephp_11);
                    $dephp_11['id'] = pdo_insertid();
                }
                $dephp_11['current_qrimg'] = $dephp_22;
                return $dephp_11;
            }else if ($dephp_2['type'] == 2){
                $dephp_23 = p('commission');
                if ($dephp_23){
                    $dephp_22 = $dephp_23 -> createMyShopQrcode($dephp_10['id'], $dephp_2['id']);
                    $dephp_11 = pdo_fetch('select * from ' . tablename('manor_shop_poster_qr') . ' where openid=:openid and acid=:acid and type=:type limit 1', array(':openid' => $dephp_10['openid'], ':acid' => $_W['acid'], ':type' => 2));
                    if (empty($dephp_11)){
                        $dephp_11 = array('acid' => $dephp_21, 'openid' => $dephp_10['openid'], 'type' => 2, 'qrimg' => $dephp_22);
                        pdo_insert('manor_shop_poster_qr', $dephp_11);
                        $dephp_11['id'] = pdo_insertid();
                    }
                    $dephp_11['current_qrimg'] = $dephp_22;
                    return $dephp_11;
                }
            }else if ($dephp_2['type'] == 3){
                $dephp_22 = m('qrcode') -> createGoodsQrcode($dephp_10['id'], $dephp_8, $dephp_2['id']);
                $dephp_11 = pdo_fetch('select * from ' . tablename('manor_shop_poster_qr') . ' where openid=:openid and acid=:acid and type=:type and goodsid=:goodsid limit 1', array(':openid' => $dephp_10['openid'], ':acid' => $_W['acid'], ':type' => 3, ':goodsid' => $dephp_8));
                if (empty($dephp_11)){
                    $dephp_11 = array('acid' => $dephp_21, 'openid' => $dephp_10['openid'], 'type' => 3, 'goodsid' => $dephp_8, 'qrimg' => $dephp_22);
                    pdo_insert('manor_shop_poster_qr', $dephp_11);
                    $dephp_11['id'] = pdo_insertid();
                }
                $dephp_11['current_qrimg'] = $dephp_22;
                return $dephp_11;
            }else if ($dephp_2['type'] == 4){
                $dephp_12 = WeAccount :: create($dephp_21);
                $dephp_11 = pdo_fetch('select * from ' . tablename('manor_shop_poster_qr') . ' where openid=:openid and acid=:acid and type=4 limit 1', array(':openid' => $dephp_10['openid'], ':acid' => $dephp_21));
                if (empty($dephp_11)){
                    $dephp_19 = $this -> getFixedTicket($dephp_2, $dephp_10, $dephp_12);
                    if (is_error($dephp_19)){
                        return $dephp_19;
                    }
                    if (empty($dephp_19)){
                        return error(-1, '生成二维码失败');
                    }
                    $dephp_24 = $dephp_19['barcode'];
                    $dephp_20 = $dephp_19['ticket'];
                    $dephp_22 = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $dephp_20;
                    $dephp_25 = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'scene_str' => $dephp_24['action_info']['scene']['scene_str'], 'model' => 2, 'name' => 'manor_shop_POSTER_QRCODE', 'keyword' => 'manor_shop_POSTER', 'expire' => 0, 'createtime' => time(), 'status' => 1, 'url' => $dephp_19['url'], 'ticket' => $dephp_19['ticket']);
                    pdo_insert('qrcode', $dephp_25);
                    $dephp_11 = array('acid' => $dephp_21, 'openid' => $dephp_10['openid'], 'type' => 4, 'scenestr' => $dephp_24['action_info']['scene']['scene_str'], 'ticket' => $dephp_19['ticket'], 'qrimg' => $dephp_22, 'url' => $dephp_19['url']);
                    pdo_insert('manor_shop_poster_qr', $dephp_11);
                    $dephp_11['id'] = pdo_insertid();
                    $dephp_11['current_qrimg'] = $dephp_22;
                }else{
                    $dephp_11['current_qrimg'] = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $dephp_11['ticket'];
                }
                return $dephp_11;
            }
        }
        public function getRealData($dephp_26){
            $dephp_26['left'] = intval(str_replace('px', '', $dephp_26['left'])) * 2;
            $dephp_26['top'] = intval(str_replace('px', '', $dephp_26['top'])) * 2;
            $dephp_26['width'] = intval(str_replace('px', '', $dephp_26['width'])) * 2;
            $dephp_26['height'] = intval(str_replace('px', '', $dephp_26['height'])) * 2;
            $dephp_26['size'] = intval(str_replace('px', '', $dephp_26['size'])) * 2;
            $dephp_26['src'] = tomedia($dephp_26['src']);
            return $dephp_26;
        }
        public function createImage($dephp_27){
            load() -> func('communication');
            $dephp_28 = ihttp_request($dephp_27);
            return imagecreatefromstring($dephp_28['content']);
        }
        public function mergeImage($dephp_29, $dephp_26, $dephp_27){
            $dephp_30 = $this -> createImage($dephp_27);
            $dephp_31 = imagesx($dephp_30);
            $dephp_32 = imagesy($dephp_30);
            imagecopyresized($dephp_29, $dephp_30, $dephp_26['left'], $dephp_26['top'], 0, 0, $dephp_26['width'], $dephp_26['height'], $dephp_31, $dephp_32);
            imagedestroy($dephp_30);
            return $dephp_29;
        }
        public function mergeText($dephp_29, $dephp_26, $dephp_33){
            $dephp_34 = IA_ROOT . '/addons/manor_shop/static/fonts/msyh.ttf';
            $dephp_35 = $this -> hex2rgb($dephp_26['color']);
            $dephp_36 = imagecolorallocate($dephp_29, $dephp_35['red'], $dephp_35['green'], $dephp_35['blue']);
            imagettftext($dephp_29, $dephp_26['size'], 0, $dephp_26['left'], $dephp_26['top'] + $dephp_26['size'], $dephp_36, $dephp_34, $dephp_33);
            return $dephp_29;
        }
        function hex2rgb($dephp_37){
            if ($dephp_37[0] == '#'){
                $dephp_37 = substr($dephp_37, 1);
            }
            if (strlen($dephp_37) == 6){
                list($dephp_38, $dephp_39, $dephp_40) = array($dephp_37[0] . $dephp_37[1], $dephp_37[2] . $dephp_37[3], $dephp_37[4] . $dephp_37[5]);
            }elseif (strlen($dephp_37) == 3){
                list($dephp_38, $dephp_39, $dephp_40) = array($dephp_37[0] . $dephp_37[0], $dephp_37[1] . $dephp_37[1], $dephp_37[2] . $dephp_37[2]);
            }else{
                return false;
            }
            $dephp_38 = hexdec($dephp_38);
            $dephp_39 = hexdec($dephp_39);
            $dephp_40 = hexdec($dephp_40);
            return array('red' => $dephp_38, 'green' => $dephp_39, 'blue' => $dephp_40);
        }
        public function createPoster($dephp_2, $dephp_10, $dephp_11, $dephp_41 = true){
            global $_W;
            $dephp_42 = IA_ROOT . '/addons/manor_shop/data/poster/' . $_W['uniacid'] . '/';
            if (!is_dir($dephp_42)){
                load() -> func('file');
                mkdirs($dephp_42);
            }
            if (!empty($dephp_11['goodsid'])){
                $dephp_43 = pdo_fetch('select id,title,thumb,commission_thumb,marketprice,productprice from ' . tablename('manor_shop_goods') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $dephp_11['goodsid'], ':uniacid' => $_W['uniacid']));
                if (empty($dephp_43)){
                    m('message') -> sendCustomNotice($dephp_10['openid'], '未找到商品，无法生成海报');
                    exit;
                }
            }
            $dephp_44 = md5(json_encode(array('openid' => $dephp_10['openid'], 'goodsid' => $dephp_11['goodsid'], 'bg' => $dephp_2['bg'], 'data' => $dephp_2['data'], 'version' => 1)));
            $dephp_45 = $dephp_44 . '.png';
            if (!is_file($dephp_42 . $dephp_45) || $dephp_11['qrimg'] != $dephp_11['current_qrimg']){
                set_time_limit(0);
                @ini_set('memory_limit', '256M');
                $dephp_29 = imagecreatetruecolor(640, 1008);
                $dephp_46 = $this -> createImage(tomedia($dephp_2['bg']));
                imagecopy($dephp_29, $dephp_46, 0, 0, 0, 0, 640, 1008);
                imagedestroy($dephp_46);
                $dephp_26 = json_decode(str_replace('&quot;', '\'', $dephp_2['data']), true);
                foreach ($dephp_26 as $dephp_47){
                    $dephp_47 = $this -> getRealData($dephp_47);
                    if ($dephp_47['type'] == 'head'){
                        $dephp_48 = preg_replace('/\/0$/i', '/96', $dephp_10['avatar']);
                        $dephp_29 = $this -> mergeImage($dephp_29, $dephp_47, $dephp_48);
                    }else if ($dephp_47['type'] == 'img'){
                        $dephp_29 = $this -> mergeImage($dephp_29, $dephp_47, $dephp_47['src']);
                    }else if ($dephp_47['type'] == 'qr'){
                        $dephp_29 = $this -> mergeImage($dephp_29, $dephp_47, tomedia($dephp_11['current_qrimg']));
                    }else if ($dephp_47['type'] == 'nickname'){
                        $dephp_29 = $this -> mergeText($dephp_29, $dephp_47, $dephp_10['nickname']);
                    }else{
                        if (!empty($dephp_43)){
                            if ($dephp_47['type'] == 'title'){
                                $dephp_29 = $this -> mergeText($dephp_29, $dephp_47, $dephp_43['title']);
                            }else if ($dephp_47['type'] == 'thumb'){
                                $dephp_49 = !empty($dephp_43['commission_thumb']) ? tomedia($dephp_43['commission_thumb']) : tomedia($dephp_43['thumb']);
                                $dephp_29 = $this -> mergeImage($dephp_29, $dephp_47, $dephp_49);
                            }else if ($dephp_47['type'] == 'marketprice'){
                                $dephp_29 = $this -> mergeText($dephp_29, $dephp_47, $dephp_43['marketprice']);
                            }else if ($dephp_47['type'] == 'productprice'){
                                $dephp_29 = $this -> mergeText($dephp_29, $dephp_47, $dephp_43['productprice']);
                            }
                        }
                    }
                }
                imagepng($dephp_29, $dephp_42 . $dephp_45);
                imagedestroy($dephp_29);
                if ($dephp_11['qrimg'] != $dephp_11['current_qrimg']){
                    pdo_update('manor_shop_poster_qr', array('qrimg' => $dephp_11['current_qrimg']), array('id' => $dephp_11['id']));
                }
            }
            $dephp_30 = $_W['siteroot'] . 'addons/manor_shop/data/poster/' . $_W['uniacid'] . '/' . $dephp_45;
            if (!$dephp_41){
                return $dephp_30;
            }
            if ($dephp_11['qrimg'] != $dephp_11['current_qrimg'] || empty($dephp_11['mediaid']) || empty($dephp_11['createtime']) || $dephp_11['createtime'] + 3600 * 24 * 3 - 7200 < time()){
                $dephp_50 = $this -> uploadImage($dephp_42 . $dephp_45);
                $dephp_11['mediaid'] = $dephp_50;
                pdo_update('manor_shop_poster_qr', array('mediaid' => $dephp_50, 'createtime' => time()), array('id' => $dephp_11['id']));
            }
            return array('img' => $dephp_30, 'mediaid' => $dephp_11['mediaid']);
        }
        public function uploadImage($dephp_30){
            load() -> func('communication');
            $dephp_51 = m('common') -> getAccount();
            $dephp_52 = $dephp_51 -> fetch_token();
            $dephp_16 = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token={$dephp_52}&type=image";
            $dephp_17 = curl_init();
            $dephp_26 = array('media' => '@' . $dephp_30);
            if (version_compare(PHP_VERSION, '5.5.0', '>')){
                $dephp_26 = array('media' => curl_file_create($dephp_30));
            }
            curl_setopt($dephp_17, CURLOPT_URL, $dephp_16);
            curl_setopt($dephp_17, CURLOPT_POST, 1);
            curl_setopt($dephp_17, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($dephp_17, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($dephp_17, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($dephp_17, CURLOPT_POSTFIELDS, $dephp_26);
            $dephp_53 = @json_decode(curl_exec($dephp_17), true);
            if (!is_array($dephp_53)){
                $dephp_53 = array('media_id' => '');
            }
            curl_close($dephp_17);
            return $dephp_53['media_id'];
        }
        public function getQRByTicket($dephp_20 = ''){
            global $_W;
            if (empty($dephp_20)){
                return false;
            }
            $dephp_54 = pdo_fetchall('select * from ' . tablename('manor_shop_poster_qr') . ' where ticket=:ticket and acid=:acid and type=4 limit 1', array(':ticket' => $dephp_20, ':acid' => $_W['acid']));
            $dephp_55 = count($dephp_54);
            if ($dephp_55 <= 0){
                return false;
            }
            if ($dephp_55 == 1){
                return $dephp_54[0];
            }
            return false;
        }
        public function checkMember($dephp_0 = ''){
            global $_W;
            $dephp_56 = WeiXinAccount :: create($_W['acid']);
            $dephp_57 = $dephp_56 -> fansQueryInfo($dephp_0);
            $dephp_57['avatar'] = $dephp_57['headimgurl'];
            load() -> model('mc');
            $dephp_58 = mc_openid2uid($dephp_0);
            if (!empty($dephp_58)){
                pdo_update('mc_members', array('nickname' => $dephp_57['nickname'], 'gender' => $dephp_57['sex'], 'nationality' => $dephp_57['country'], 'resideprovince' => $dephp_57['province'], 'residecity' => $dephp_57['city'], 'avatar' => $dephp_57['headimgurl']), array('uid' => $dephp_58));
            }
            pdo_update('mc_mapping_fans', array('nickname' => $dephp_57['nickname']), array('uniacid' => $_W['uniacid'], 'openid' => $dephp_0));
            $dephp_59 = m('member');
            $dephp_10 = $dephp_59 -> getMember($dephp_0);
            if (empty($dephp_10)){
                $dephp_60 = mc_fetch($dephp_58, array('realname', 'nickname', 'mobile', 'avatar', 'resideprovince', 'residecity', 'residedist'));
                $dephp_10 = array('uniacid' => $_W['uniacid'], 'uid' => $dephp_58, 'openid' => $dephp_0, 'realname' => $dephp_60['realname'], 'mobile' => $dephp_60['mobile'], 'nickname' => !empty($dephp_60['nickname']) ? $dephp_60['nickname'] : $dephp_57['nickname'], 'avatar' => !empty($dephp_60['avatar']) ? $dephp_60['avatar'] : $dephp_57['avatar'], 'gender' => !empty($dephp_60['gender']) ? $dephp_60['gender'] : $dephp_57['sex'], 'province' => !empty($dephp_60['resideprovince']) ? $dephp_60['resideprovince'] : $dephp_57['province'], 'city' => !empty($dephp_60['residecity']) ? $dephp_60['residecity'] : $dephp_57['city'], 'area' => $dephp_60['residedist'], 'createtime' => time(), 'status' => 0);
                pdo_insert('manor_shop_member', $dephp_10);
                $dephp_10['id'] = pdo_insertid();
                $dephp_10['isnew'] = true;
            }else{
                $dephp_10['nickname'] = $dephp_57['nickname'];
                $dephp_10['avatar'] = $dephp_57['headimgurl'];
                $dephp_10['province'] = $dephp_57['province'];
                $dephp_10['city'] = $dephp_57['city'];
                pdo_update('manor_shop_member', $dephp_10, array('id' => $dephp_10['id']));
                $dephp_10['isnew'] = false;
            }
            return $dephp_10;
        }
        function perms(){
            return array('poster' => array('text' => $this -> getName(), 'isplugin' => true, 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log', 'log' => '扫描记录', 'clear' => '清除缓存-log', 'setdefault' => '设置默认海报-log'));
        }
    }
}
