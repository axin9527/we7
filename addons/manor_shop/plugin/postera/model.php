<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
if (!class_exists('PosteraModel')){
    class PosteraModel extends PluginModel{
        public function getSceneTicket($dephp_0, $dephp_1){
            global $_W, $_GPC;
            $dephp_2 = m('common') -> getAccount();
            $dephp_3 = '{"expire_seconds":' . $dephp_0 . ',"action_info":{"scene":{"scene_id":' . $dephp_1 . '}},"action_name":"QR_SCENE"}';
            $dephp_4 = $dephp_2 -> fetch_token();
            $dephp_5 = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $dephp_4;
            $dephp_6 = curl_init();
            curl_setopt($dephp_6, CURLOPT_URL, $dephp_5);
            curl_setopt($dephp_6, CURLOPT_POST, 1);
            curl_setopt($dephp_6, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($dephp_6, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($dephp_6, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($dephp_6, CURLOPT_POSTFIELDS, $dephp_3);
            $dephp_7 = curl_exec($dephp_6);
            $dephp_8 = @json_decode($dephp_7, true);
            if (!is_array($dephp_8)){
                return false;
            }
            if (!empty($dephp_8['errcode'])){
                return error(-1, $dephp_8['errmsg']);
            }
            $dephp_9 = $dephp_8['ticket'];
            return array('barcode' => json_decode($dephp_3, true), 'ticket' => $dephp_9);
        }
        function getSceneID(){
            global $_W;
            $dephp_10 = $_W['acid'];
            $dephp_11 = 1;
            $dephp_12 = 2147483647;
            $dephp_1 = rand($dephp_11, $dephp_12);
            if(empty($dephp_1)){
                $dephp_1 = rand($dephp_11, $dephp_12);
            } while(1){
                $dephp_13 = pdo_fetchcolumn('select count(*) from ' . tablename('qrcode') . ' where qrcid=:qrcid and acid=:acid and model=0 limit 1', array(':qrcid' => $dephp_1, ':acid' => $dephp_10));
                if($dephp_13 <= 0){
                    break;
                }
                $dephp_1 = rand($dephp_11, $dephp_12);
                if(empty($dephp_1)){
                    $dephp_1 = rand($dephp_11, $dephp_12);
                }
            }
            return $dephp_1;
        }
        public function getQR($dephp_14, $dephp_15){
            global $_W, $_GPC;
            $dephp_10 = $_W['acid'];
            $dephp_16 = time();
            $dephp_17 = $dephp_14['timeend'];
            $dephp_0 = $dephp_17 - $dephp_16;
            if($dephp_0 > 86400 * 30 -15){
                $dephp_0 = 86400 * 30 -15;
            }
            $dephp_18 = $dephp_16 + $dephp_0;
            $dephp_19 = pdo_fetch('select * from ' . tablename('manor_shop_postera_qr') . ' where openid=:openid and acid=:acid and posterid=:posterid limit 1', array(':openid' => $dephp_15['openid'], ':acid' => $dephp_10, ':posterid' => $dephp_14['id']));
            if (empty($dephp_19)){
                $dephp_19['current_qrimg'] = '';
                $dephp_1 = $this -> getSceneID();
                $dephp_8 = $this -> getSceneTicket($dephp_0, $dephp_1);
                if (is_error($dephp_8)){
                    return $dephp_8;
                }
                if (empty($dephp_8)){
                    return error(-1, '生成二维码失败');
                }
                $dephp_20 = $dephp_8['barcode'];
                $dephp_9 = $dephp_8['ticket'];
                $dephp_21 = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $dephp_9;
                $dephp_22 = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'qrcid' => $dephp_1, 'model' => 0, 'name' => 'manor_shop_POSTERA_QRCODE', 'keyword' => 'manor_shop_POSTERA', 'expire' => $dephp_0, 'createtime' => time(), 'status' => 1, 'url' => $dephp_8['url'], 'ticket' => $dephp_8['ticket']);
                pdo_insert('qrcode', $dephp_22);
                $dephp_19 = array('acid' => $dephp_10, 'openid' => $dephp_15['openid'], 'sceneid' => $dephp_1, 'type' => $dephp_14['type'], 'ticket' => $dephp_8['ticket'], 'qrimg' => $dephp_21, 'posterid' => $dephp_14['id'], 'expire' => $dephp_0, 'url' => $dephp_8['url'], 'goodsid' => $dephp_14['goodsid'], 'endtime' => $dephp_18);
                pdo_insert('manor_shop_postera_qr', $dephp_19);
                $dephp_19['id'] = pdo_insertid();
            }else{
                $dephp_19['current_qrimg'] = $dephp_19['qrimg'];
                if(time() > $dephp_19['endtime']){
                    $dephp_1 = $dephp_19['sceneid'];
                    $dephp_8 = $this -> getSceneTicket($dephp_0, $dephp_1);
                    if (is_error($dephp_8)){
                        return $dephp_8;
                    }
                    if (empty($dephp_8)){
                        return error(-1, '生成二维码失败');
                    }
                    $dephp_20 = $dephp_8['barcode'];
                    $dephp_9 = $dephp_8['ticket'];
                    $dephp_21 = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $dephp_9;
                    pdo_update('qrcode', array('ticket' => $dephp_8['ticket'], 'url' => $dephp_8['url']), array('acid' => $_W['acid'], 'qrcid' => $dephp_1));
                    pdo_update('manor_shop_postera_qr', array('ticket' => $dephp_9, 'qrimg' => $dephp_21, 'url' => $dephp_8['url'], 'endtime' => $dephp_18), array('id' => $dephp_19['id']));
                    $dephp_19['ticket'] = $dephp_9;
                    $dephp_19['qrimg'] = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $dephp_19['ticket'];
                }
            }
            return $dephp_19;
        }
        public function getRealData($dephp_23){
            $dephp_23['left'] = intval(str_replace('px', '', $dephp_23['left'])) * 2;
            $dephp_23['top'] = intval(str_replace('px', '', $dephp_23['top'])) * 2;
            $dephp_23['width'] = intval(str_replace('px', '', $dephp_23['width'])) * 2;
            $dephp_23['height'] = intval(str_replace('px', '', $dephp_23['height'])) * 2;
            $dephp_23['size'] = intval(str_replace('px', '', $dephp_23['size'])) * 2;
            $dephp_23['src'] = tomedia($dephp_23['src']);
            return $dephp_23;
        }
        public function createImage($dephp_24){
            load() -> func('communication');
            $dephp_25 = ihttp_request($dephp_24);
            return imagecreatefromstring($dephp_25['content']);
        }
        public function mergeImage($dephp_26, $dephp_23, $dephp_24){
            $dephp_27 = $this -> createImage($dephp_24);
            $dephp_28 = imagesx($dephp_27);
            $dephp_29 = imagesy($dephp_27);
            imagecopyresized($dephp_26, $dephp_27, $dephp_23['left'], $dephp_23['top'], 0, 0, $dephp_23['width'], $dephp_23['height'], $dephp_28, $dephp_29);
            imagedestroy($dephp_27);
            return $dephp_26;
        }
        public function mergeText($dephp_26, $dephp_23, $dephp_30){
            $dephp_31 = IA_ROOT . '/addons/manor_shop/static/fonts/msyh.ttf';
            $dephp_32 = $this -> hex2rgb($dephp_23['color']);
            $dephp_33 = imagecolorallocate($dephp_26, $dephp_32['red'], $dephp_32['green'], $dephp_32['blue']);
            imagettftext($dephp_26, $dephp_23['size'], 0, $dephp_23['left'], $dephp_23['top'] + $dephp_23['size'], $dephp_33, $dephp_31, $dephp_30);
            return $dephp_26;
        }
        function hex2rgb($dephp_34){
            if ($dephp_34[0] == '#'){
                $dephp_34 = substr($dephp_34, 1);
            }
            if (strlen($dephp_34) == 6){
                list($dephp_35, $dephp_36, $dephp_37) = array($dephp_34[0] . $dephp_34[1], $dephp_34[2] . $dephp_34[3], $dephp_34[4] . $dephp_34[5]);
            }elseif (strlen($dephp_34) == 3){
                list($dephp_35, $dephp_36, $dephp_37) = array($dephp_34[0] . $dephp_34[0], $dephp_34[1] . $dephp_34[1], $dephp_34[2] . $dephp_34[2]);
            }else{
                return false;
            }
            $dephp_35 = hexdec($dephp_35);
            $dephp_36 = hexdec($dephp_36);
            $dephp_37 = hexdec($dephp_37);
            return array('red' => $dephp_35, 'green' => $dephp_36, 'blue' => $dephp_37);
        }
        public function createPoster($dephp_14, $dephp_15, $dephp_19, $dephp_38 = true){
            global $_W;
            $dephp_39 = IA_ROOT . '/addons/manor_shop/data/postera/' . $_W['uniacid'] . '/';
            if (!is_dir($dephp_39)){
                load() -> func('file');
                mkdirs($dephp_39);
            }
            if (!empty($dephp_19['goodsid'])){
                $dephp_40 = pdo_fetch('select id,title,thumb,commission_thumb,marketprice,productprice from ' . tablename('manor_shop_goods') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $dephp_19['goodsid'], ':uniacid' => $_W['uniacid']));
                if (empty($dephp_40)){
                    m('message') -> sendCustomNotice($dephp_15['openid'], '未找到商品，无法生成海报');
                    exit;
                }
            }
            $dephp_41 = md5(json_encode(array('openid' => $dephp_15['openid'], 'goodsid' => $dephp_19['goodsid'], 'bg' => $dephp_14['bg'], 'data' => $dephp_14['data'], 'version' => 1)));
            $dephp_42 = $dephp_41 . '.png';
            if (!is_file($dephp_39 . $dephp_42) || $dephp_19['qrimg'] != $dephp_19['current_qrimg']){
                set_time_limit(0);
                @ini_set('memory_limit', '256M');
                $dephp_26 = imagecreatetruecolor(640, 1008);
                $dephp_43 = $this -> createImage(tomedia($dephp_14['bg']));
                imagecopy($dephp_26, $dephp_43, 0, 0, 0, 0, 640, 1008);
                imagedestroy($dephp_43);
                $dephp_23 = json_decode(str_replace('&quot;', '\'', $dephp_14['data']), true);
                foreach ($dephp_23 as $dephp_44){
                    $dephp_44 = $this -> getRealData($dephp_44);
                    if ($dephp_44['type'] == 'head'){
                        $dephp_45 = preg_replace('/\/0$/i', '/96', $dephp_15['avatar']);
                        $dephp_26 = $this -> mergeImage($dephp_26, $dephp_44, $dephp_45);
                    }else if ($dephp_44['type'] == 'time'){
                        $dephp_16 = date('Y-m-d H:i', $dephp_19['endtime']);
                        $dephp_26 = $this -> mergeText($dephp_26, $dephp_44, $dephp_16);
                    }else if ($dephp_44['type'] == 'img'){
                        $dephp_26 = $this -> mergeImage($dephp_26, $dephp_44, $dephp_44['src']);
                    }else if ($dephp_44['type'] == 'qr'){
                        $dephp_26 = $this -> mergeImage($dephp_26, $dephp_44, tomedia($dephp_19['qrimg']));
                    }else if ($dephp_44['type'] == 'nickname'){
                        $dephp_26 = $this -> mergeText($dephp_26, $dephp_44, $dephp_15['nickname']);
                    }else{
                        if (!empty($dephp_40)){
                            if ($dephp_44['type'] == 'title'){
                                $dephp_26 = $this -> mergeText($dephp_26, $dephp_44, $dephp_40['title']);
                            }else if ($dephp_44['type'] == 'thumb'){
                                $dephp_46 = !empty($dephp_40['commission_thumb']) ? tomedia($dephp_40['commission_thumb']) : tomedia($dephp_40['thumb']);
                                $dephp_26 = $this -> mergeImage($dephp_26, $dephp_44, $dephp_46);
                            }else if ($dephp_44['type'] == 'marketprice'){
                                $dephp_26 = $this -> mergeText($dephp_26, $dephp_44, $dephp_40['marketprice']);
                            }else if ($dephp_44['type'] == 'productprice'){
                                $dephp_26 = $this -> mergeText($dephp_26, $dephp_44, $dephp_40['productprice']);
                            }
                        }
                    }
                }
                imagepng($dephp_26, $dephp_39 . $dephp_42);
                imagedestroy($dephp_26);
            }
            $dephp_27 = $_W['siteroot'] . 'addons/manor_shop/data/poster/' . $_W['uniacid'] . '/' . $dephp_42;
            if (!$dephp_38){
                return $dephp_27;
            }
            if ($dephp_19['qrimg'] != $dephp_19['current_qrimg'] || empty($dephp_19['mediaid']) || empty($dephp_19['createtime']) || $dephp_19['createtime'] + 3600 * 24 * 3 - 7200 < time()){
                $dephp_47 = $this -> uploadImage($dephp_39 . $dephp_42);
                $dephp_19['mediaid'] = $dephp_47;
                pdo_update('manor_shop_postera_qr', array('mediaid' => $dephp_47, 'createtime' => time()), array('id' => $dephp_19['id']));
            }
            return array('img' => $dephp_27, 'mediaid' => $dephp_19['mediaid']);
        }
        public function uploadImage($dephp_27){
            load() -> func('communication');
            $dephp_2 = m('common') -> getAccount();
            $dephp_48 = $dephp_2 -> fetch_token();
            $dephp_5 = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token={$dephp_48}&type=image";
            $dephp_6 = curl_init();
            $dephp_23 = array('media' => '@' . $dephp_27);
            if (version_compare(PHP_VERSION, '5.5.0', '>')){
                $dephp_23 = array('media' => curl_file_create($dephp_27));
            }
            curl_setopt($dephp_6, CURLOPT_URL, $dephp_5);
            curl_setopt($dephp_6, CURLOPT_POST, 1);
            curl_setopt($dephp_6, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($dephp_6, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($dephp_6, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($dephp_6, CURLOPT_POSTFIELDS, $dephp_23);
            $dephp_49 = @json_decode(curl_exec($dephp_6), true);
            if (!is_array($dephp_49)){
                $dephp_49 = array('media_id' => '');
            }
            curl_close($dephp_6);
            return $dephp_49['media_id'];
        }
        public function getQRByTicket($dephp_9 = ''){
            global $_W;
            if (empty($dephp_9)){
                return false;
            }
            $dephp_50 = pdo_fetchall('select * from ' . tablename('manor_shop_postera_qr') . ' where ticket=:ticket and acid=:acid limit 1', array(':ticket' => $dephp_9, ':acid' => $_W['acid']));
            $dephp_13 = count($dephp_50);
            if ($dephp_13 <= 0){
                return false;
            }
            if ($dephp_13 == 1){
                return $dephp_50[0];
            }
            return false;
        }
        public function checkMember($dephp_51 = ''){
            global $_W;
            $dephp_52 = WeiXinAccount :: create($_W['acid']);
            $dephp_53 = $dephp_52 -> fansQueryInfo($dephp_51);
            $dephp_53['avatar'] = $dephp_53['headimgurl'];
            load() -> model('mc');
            $dephp_54 = mc_openid2uid($dephp_51);
            if (!empty($dephp_54)){
                pdo_update('mc_members', array('nickname' => $dephp_53['nickname'], 'gender' => $dephp_53['sex'], 'nationality' => $dephp_53['country'], 'resideprovince' => $dephp_53['province'], 'residecity' => $dephp_53['city'], 'avatar' => $dephp_53['headimgurl']), array('uid' => $dephp_54));
            }
            pdo_update('mc_mapping_fans', array('nickname' => $dephp_53['nickname']), array('uniacid' => $_W['uniacid'], 'openid' => $dephp_51));
            $dephp_55 = m('member');
            $dephp_15 = $dephp_55 -> getMember($dephp_51);
            if (empty($dephp_15)){
                $dephp_56 = mc_fetch($dephp_54, array('realname', 'nickname', 'mobile', 'avatar', 'resideprovince', 'residecity', 'residedist'));
                $dephp_15 = array('uniacid' => $_W['uniacid'], 'uid' => $dephp_54, 'openid' => $dephp_51, 'realname' => $dephp_56['realname'], 'mobile' => $dephp_56['mobile'], 'nickname' => !empty($dephp_56['nickname']) ? $dephp_56['nickname'] : $dephp_53['nickname'], 'avatar' => !empty($dephp_56['avatar']) ? $dephp_56['avatar'] : $dephp_53['avatar'], 'gender' => !empty($dephp_56['gender']) ? $dephp_56['gender'] : $dephp_53['sex'], 'province' => !empty($dephp_56['resideprovince']) ? $dephp_56['resideprovince'] : $dephp_53['province'], 'city' => !empty($dephp_56['residecity']) ? $dephp_56['residecity'] : $dephp_53['city'], 'area' => $dephp_56['residedist'], 'createtime' => time(), 'status' => 0);
                pdo_insert('manor_shop_member', $dephp_15);
                $dephp_15['id'] = pdo_insertid();
                $dephp_15['isnew'] = true;
            }else{
                $dephp_15['nickname'] = $dephp_53['nickname'];
                $dephp_15['avatar'] = $dephp_53['headimgurl'];
                $dephp_15['province'] = $dephp_53['province'];
                $dephp_15['city'] = $dephp_53['city'];
                pdo_update('manor_shop_member', $dephp_15, array('id' => $dephp_15['id']));
                $dephp_15['isnew'] = false;
            }
            return $dephp_15;
        }
        function perms(){
            return array('postera' => array('text' => $this -> getName(), 'isplugin' => true, 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log', 'log' => '扫描记录', 'clear' => '清除缓存-log', 'setdefault' => '设置默认海报-log'));
        }
    }
}
