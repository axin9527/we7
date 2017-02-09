<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
if (!class_exists('DesignerModel')){
    class DesignerModel extends PluginModel{
        public function getPage($dephp_0 = 1){
            global $_W, $_GPC;
            $dephp_1 = pdo_fetch('SELECT * FROM ' . tablename('manor_shop_designer') . ' WHERE uniacid= :uniacid and pagetype=:type and setdefault=:default', array(':uniacid' => $_W['uniacid'], ':type' => $dephp_0, ':default' => '1'));
            if (empty($dephp_1)){
                return false;
            }
            return $this -> getData($dephp_1);
        }
        public function change(& $dephp_2, $dephp_3){
            $dephp_2[$dephp_4['k1']][$dephp_4['k2']]['name'] = $dephp_3['title'];
            $dephp_2[$dephp_4['k1']][$dephp_4['k2']]['priceold'] = $dephp_3['productprice'];
            $dephp_2[$dephp_4['k1']][$dephp_4['k2']]['pricenow'] = $dephp_3['marketprice'];
            $dephp_2[$dephp_4['k1']][$dephp_4['k2']]['img'] = $dephp_3['thumb'];
            $dephp_2[$dephp_4['k1']][$dephp_4['k2']]['sales'] = $dephp_3['sales'];
            $dephp_2[$dephp_4['k1']][$dephp_4['k2']]['unit'] = $dephp_3['unit'];
        }
        public function getData($dephp_1){
            global $_W;
            if(strexists($dephp_1['datas'], '{')){
                $dephp_5 = htmlspecialchars_decode($dephp_1['datas']);
            }else{
                $dephp_5 = htmlspecialchars_decode(base64_decode($dephp_1['datas']));
            }
            $dephp_2 = json_decode($dephp_5, true);
            $dephp_6 = array();
            foreach ($dephp_2 as $dephp_7 => & $dephp_8){
                if ($dephp_8['temp'] == 'goods'){
                    foreach ($dephp_8['data'] as $dephp_9 => $dephp_10){
                        $dephp_6[] = array('id' => $dephp_10['goodid'], 'k1' => $dephp_7, 'k2' => $dephp_9);
                    }
                }elseif ($dephp_8['temp'] == 'richtext'){
                    $dephp_8['content'] = $this -> unescape($dephp_8['content']);
                }
            }
            unset($dephp_8);
            $dephp_11 = array();
            foreach ($dephp_6 as $dephp_12){
                $dephp_11[] = $dephp_12['id'];
            }
            if (count($dephp_11) > 0){
                $dephp_13 = pdo_fetchall('SELECT id,title,productprice,marketprice,thumb,sales,unit FROM ' . tablename('manor_shop_goods') . ' WHERE id in ( ' . implode(',', $dephp_11) . ') and uniacid= :uniacid ', array(':uniacid' => $_W['uniacid']), 'id');
                $dephp_13 = set_medias($dephp_13, 'thumb');
                foreach ($dephp_2 as $dephp_7 => & $dephp_8){
                    if ($dephp_8['temp'] == 'goods'){
                        foreach ($dephp_8['data'] as $dephp_9 => & $dephp_10){
                            $dephp_3 = $dephp_13[$dephp_10['goodid']];
                            $dephp_10['name'] = $dephp_3['title'];
                            $dephp_10['priceold'] = $dephp_3['productprice'];
                            $dephp_10['pricenow'] = $dephp_3['marketprice'];
                            $dephp_10['img'] = $dephp_3['thumb'];
                            $dephp_10['sales'] = $dephp_3['sales'];
                            $dephp_10['unit'] = $dephp_3['unit'];
                        }
                        unset($dephp_10);
                    }
                }
                unset($dephp_8);
            }
            $dephp_5 = json_encode($dephp_2);
            $dephp_5 = rtrim($dephp_5, ']');
            $dephp_5 = ltrim($dephp_5, '[');
            if(strexists($dephp_1['pageinfo'], '{')){
                $dephp_14 = htmlspecialchars_decode($dephp_1['pageinfo']);
            }else{
                $dephp_14 = htmlspecialchars_decode(base64_decode($dephp_1['pageinfo']));
            }
            $dephp_15 = json_decode($dephp_14, true);
            $dephp_16 = empty($dephp_15[0]['params']['title']) ?'未设置页面标题' : $dephp_15[0]['params']['title'];
            $dephp_17 = empty($dephp_15[0]['params']['desc']) ? '未设置页面简介' : $dephp_15[0]['params']['desc'];
            $dephp_18 = empty($dephp_15[0]['params']['img']) ? "" : tomedia($dephp_15[0]['params']['img']);
            $dephp_19 = empty($dephp_15[0]['params']['kw']) ? "" : $dephp_15[0]['params']['kw'];
            $dephp_20 = m('common') -> getSysset(array('shop', 'share'));
            $dephp_21 = $dephp_20;
            $dephp_21['shop'] = set_medias($dephp_21['shop'], 'logo');
            $dephp_21 = json_encode($dephp_21);
            $dephp_14 = rtrim($dephp_14, ']');
            $dephp_14 = ltrim($dephp_14, '[');
            $dephp_22 = array('page' => $dephp_1, 'pageinfo' => $dephp_14, 'data' => $dephp_5, 'share' => array('title' => $dephp_16, 'desc' => $dephp_17, 'imgUrl' => $dephp_18), 'footertype' => intval($dephp_15[0]['params']['footer']), 'footermenu' => intval($dephp_15[0]['params']['footermenu']), 'system' => $dephp_21);
            if ($dephp_15[0]['params']['footer'] == 2){
                $dephp_23 = intval($dephp_15[0]['params']['footermenu']);
                $dephp_24 = pdo_fetch('select * from ' . tablename('manor_shop_designer_menu') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $dephp_23, ':uniacid' => $_W['uniacid']));
                if (!empty($dephp_24)){
                    $dephp_22['menus'] = json_decode($dephp_24['menus'], true);
                    $dephp_22['params'] = json_decode($dephp_24['params'], true);
                }
            }
            return $dephp_22;
        }
        public function escape($dephp_25){
            preg_match_all('/[\xc2-\xdf][\x80-\xbf]+|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}|[\x01-\x7f]+/e', $dephp_25, $dephp_26);
            $dephp_25 = $dephp_26 [0];
            $dephp_27 = count($dephp_25);
            for($dephp_28 = 0; $dephp_28 < $dephp_27; $dephp_28 ++){
                $dephp_29 = ord($dephp_25 [$dephp_28] [0]);
                if ($dephp_29 < 223){
                    $dephp_25 [$dephp_28] = rawurlencode(utf8_decode($dephp_25 [$dephp_28]));
                }else{
                    $dephp_25 [$dephp_28] = '%u' . strtoupper(bin2hex(iconv('UTF-8', 'UCS-2', $dephp_25 [$dephp_28])));
                }
            }
            return join("", $dephp_25);
        }
        public function unescape($dephp_25){
            $dephp_22 = '';
            $dephp_30 = strlen($dephp_25);
            for ($dephp_28 = 0; $dephp_28 < $dephp_30; $dephp_28++){
                if ($dephp_25[$dephp_28] == '%' && $dephp_25[$dephp_28 + 1] == 'u'){
                    $dephp_31 = hexdec(substr($dephp_25, $dephp_28 + 2, 4));
                    if ($dephp_31 < 0x7f) $dephp_22 .= chr($dephp_31);
                    else if ($dephp_31 < 0x800) $dephp_22 .= chr(0xc0 | ($dephp_31 >> 6)) . chr(0x80 | ($dephp_31 & 0x3f));
                    else $dephp_22 .= chr(0xe0 | ($dephp_31 >> 12)) . chr(0x80 | (($dephp_31 >> 6) & 0x3f)) . chr(0x80 | ($dephp_31 & 0x3f));
                    $dephp_28 += 5;
                }else if ($dephp_25[$dephp_28] == '%'){
                    $dephp_22 .= urldecode(substr($dephp_25, $dephp_28, 3));
                    $dephp_28 += 2;
                }else $dephp_22 .= $dephp_25[$dephp_28];
            }
            return $dephp_22;
        }
        public function getGuide($dephp_21, $dephp_14){
            global $_W, $_GPC;
            if (!empty($_GPC['preview'])){
                $dephp_32['followed'] = '0';
            }else{
                $dephp_32['openid2'] = m('user') -> getOpenid();
                $dephp_32['followed'] = m('user') -> followed($dephp_32['openid2']);
            }
            if ($dephp_32['followed'] != '1'){
                $dephp_21 = json_decode($dephp_21, true);
                $dephp_21['shop'] = set_medias($dephp_21['shop'], 'logo');
                $dephp_14 = json_decode($dephp_14, true);
                if (!empty($_GPC['mid'])){
                    $dephp_32['member1'] = pdo_fetch('SELECT id,nickname,openid,avatar FROM ' . tablename('manor_shop_member') . ' WHERE id=:mid and uniacid= :uniacid limit 1 ', array(':uniacid' => $_W['uniacid'], ':mid' => $_GPC['mid']));
                    $dephp_32['member2'] = pdo_fetch('SELECT id,nickname,openid FROM ' . tablename('manor_shop_member') . ' WHERE openid=:openid and uniacid= :uniacid limit 1 ', array(':uniacid' => $_W['uniacid'], ':openid' => $dephp_32['openid2']));
                }
                $dephp_32['followurl'] = $dephp_21['share']['followurl'];
                if (empty($dephp_32['member1'])){
                    $dephp_32['title1'] = $dephp_14['params']['guidetitle1'];
                    $dephp_32['title2'] = $dephp_14['params']['guidetitle2'];
                    $dephp_32['logo'] = $dephp_21['shop']['logo'];
                }else{
                    $dephp_14['params']['guidetitle1s'] = str_replace('[邀请人]', $dephp_32['member1']['nickname'], $dephp_14['params']['guidetitle1s']);
                    $dephp_14['params']['guidetitle2s'] = str_replace('[邀请人]', $dephp_32['member1']['nickname'], $dephp_14['params']['guidetitle2s']);
                    $dephp_14['params']['guidetitle1s'] = str_replace('[访问者]', $dephp_32['member2']['nickname'], $dephp_14['params']['guidetitle1s']);
                    $dephp_14['params']['guidetitle2s'] = str_replace('[访问者]', $dephp_32['member2']['nickname'], $dephp_14['params']['guidetitle2s']);
                    $dephp_32['title1'] = $dephp_14['params']['guidetitle1s'];
                    $dephp_32['title2'] = $dephp_14['params']['guidetitle2s'];
                    $dephp_32['logo'] = $dephp_32['member1']['avatar'];
                }
            }
            return $dephp_32;
        }
        public function getMenu($dephp_23 = 0){
            if (empty($dephp_23)){
            }
        }
        public function getDefaultMenuID(){
            global $_W;
            return pdo_fetchcolumn('select id from ' . tablename('manor_shop_designer_menu') . ' where isdefault=1 and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
        }
        public function getDefaultMenu(){
            global $_W;
            return pdo_fetch('select * from ' . tablename('manor_shop_designer_menu') . ' where isdefault=1 and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
        }
        public function perms(){
            return array('designer' => array('text' => $this -> getName(), 'isplugin' => true, 'child' => array('page' => array('text' => '页面设置', 'view' => '浏览', 'edit' => '添加修改-log', 'delete' => '删除-log', 'setdefault' => '设置默认-log'), 'menu' => array('text' => '菜单设置', 'view' => '浏览', 'edit' => '添加修改-log', 'delete' => '删除-log', 'setdefault' => '设置默认-log'))));
        }
    }
}
