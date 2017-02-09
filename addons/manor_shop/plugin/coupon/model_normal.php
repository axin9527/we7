<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
if (!class_exists('CouponModel')){
    class CouponModel{
        /**
         * 发送优惠卷 优惠卷卷组
         * @param $id 优惠卷或者优惠卷组id
         * @param array $open_ids 用户得openid 为空就给用户自己发。
         * @param bool $is_group 是否双组
         * @param int $send_total 发送数量 默认是1
         * @param array $send_message_template 发送模版。默认是优惠卷得模版
         * 格式 array('resptitle' => '','respthumb' => '','respdesc' => '','respurl' => ''));
         * @return array|string
         */
        public function send_coupon ($id, $open_ids=array(), $is_group=false, $send_total=1, $send_message_template = array()) {
            global $_W;
            if(!$id) {
                return '未找到优惠券';
            }
            if($is_group) {
                $group = pdo_get('manor_shop_coupon_category', array('id'=>$id));
                if(!$group) {
                    return '未找到优惠券~';
                }
                if($group['status'] != 1) {
                    return '该优惠劵暂未启用';
                }
                $coupons = pdo_fetchall('select * from '.tablename('manor_shop_coupon') . ' where catid=:catid and uniacid=:uniacid', array(':catid'=>$id, ':uniacid'=>$_W['uniacid']));
            } else {
                $coupons = pdo_fetchall('SELECT * FROM ' . tablename('manor_shop_coupon') . ' WHERE id=:id and uniacid=:uniacid ', array(':id' => $id, ':uniacid' => $_W['uniacid']));
            }
            if(empty($coupons)) {
                return '未找到优惠券';
            }
            if(empty($open_ids)) {
                $open_ids = array($_W['openid']);
            }
            foreach ($open_ids as $openid){
                $mopenids[] = "'" . str_replace("'", "''", $openid) . "'";
            }
            $members = pdo_fetchall('select id,openid,nickname from ' . tablename('manor_shop_member') . ' where openid in (' . implode(',', $mopenids) . ") and uniacid={$_W['uniacid']}");
            foreach ($coupons as $coupon) {
                if($coupon['total'] != -1) {
                    $last = $this -> get_last_count($coupon['id']);
                    if ($last <= 0){
                        return '优惠券数量不足,无法发放!';
                    }
                    $need = $send_total - $last;
                    if ($need > 0){
                        return '优惠券数量不足,请补充 {$need} 张优惠券才能发放!';
                    }
                }
                foreach ($open_ids as $open_id) {
                    $coupon = $this -> setCoupon($coupon, time(), $open_id);
                    if (!$coupon['canget']){
                        return "您已超出{$coupon['gettypestr']}次数限制";
                    }
                }


            }
            //发送并且推送消息
            $account = $this -> getAccount();
            $set = $this->getSet();
            $time = time();
            foreach ($members as $m){
                foreach ($coupons as $coupon) {
                    for ($i = 1;$i <= $send_total;$i++){
                        $log = array('uniacid' => $_W['uniacid'], 'openid' => $m['openid'], 'logno' => $this->createNO('coupon_log', 'logno', 'CC'), 'couponid' => $coupon['id'], 'status' => 1, 'paystatus' => -1, 'creditstatus' => -1, 'createtime' => $time, 'getfrom' => 0);
                        pdo_insert('manor_shop_coupon_log', $log);
                        $logid = pdo_insertid();
                        $data = array('uniacid' => $_W['uniacid'], 'openid' => $m['openid'], 'couponid' => $coupon['id'], 'gettype' => 1, 'gettime' => $time, 'senduid' => $_W['uid']);
                        pdo_insert('manor_shop_coupon_data', $data);
                    }
                    //重置默认发送模版
                    if($send_message_template) {
                        $coupon = $send_message_template;
                    }
                    if(!$is_group) {
                        $this->sendMessage($coupon, $send_total, $m, $set['templateid'], $account);
                    }
                }
                if($is_group) {
                    $send_message_template = array(
                        'resptitle' => $group['sub_title'],
                        'respthumb' => tomedia($group['sub_thumb']),
                        'respdesc' => trim($group['sub_desc']),
                        'respurl' => $group['sub_url'],
                    );
                    $this->sendMessage($send_message_template, $send_total, $m, $set['templateid'], $account);
                }
            }
            return array('优惠券发放成功');
        }
        public function getAccount(){
            global $_W;
            load() -> model('account');
            if (!empty($_W['acid'])){
                return WeAccount :: create($_W['acid']);
            }else{
                $acid = pdo_fetchcolumn("SELECT acid FROM " . tablename('account_wechats') . " WHERE `uniacid`=:uniacid LIMIT 1", array(':uniacid' => $_W['uniacid']));
                return WeAccount :: create($acid);
            }
            return false;
        }
        public function getSet(){
            global $_W, $_GPC;
            $set = $this -> getSetData();
            $allset = iunserializer($set['plugins']);
            if (is_array($allset) && isset($allset['coupon'])){
                return $allset['coupon'];
            }
            return array();
        }
        public function getSetData($uniacid = 0){
            global $_W;
            if (empty($uniacid)){
                $uniacid = $_W['uniacid'];
            }
            $set = pdo_fetch("select * from " . tablename('manor_shop_sysset') . ' where uniacid=:uniacid limit 1', array(':uniacid' => $uniacid));
            if (empty($set)){
                $set = array();
            }
            return $set;
        }
        function sendMessage($coupon, $send_total, $member, $templateid = '', $account = null){
            global $_W;
            $articles = array();
            $title = str_replace('[nickname]', $member['nickname'], $coupon['resptitle']);
            $desc = str_replace('[nickname]', $member['nickname'], $coupon['respdesc']);
            $title = str_replace('[total]', $send_total, $title);
            $desc = str_replace('[total]', $send_total, $desc);
            $url = empty($coupon['respurl'])? $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=manor_shop&do=plugin&p=coupon&method=my' : $coupon['respurl'];
            if (!empty($coupon['resptitle'])){
                $articles[] = array("title" => urlencode($title), "description" => urlencode($desc), "url" => $url, "picurl" => tomedia($coupon['respthumb']));
            }
            if (!empty($articles)){
                $account = $this->getAccount();
                $resp = $account -> sendCustomNotice(array('touser' => $member['openid'], 'msgtype' => 'news', 'news' => array('articles' => $articles)));
                if (is_error($resp)){
                    $msg = array('keyword1' => array('value' => $title, "color" => "#73a68d"), 'keyword2' => array('value' => $desc, "color" => "#73a68d"));
                    if (!empty($templateid)){
                        $account -> sendTplNotice($member['openid'], $templateid, $msg, $url);
                    }
                }
            }
        }
        function get_last_count($couponid = 0){
            global $_W;
            $coupon = pdo_fetch('SELECT id,total FROM ' . tablename('manor_shop_coupon') . ' WHERE id=:id and uniacid=:uniacid ', array(':id' => $couponid, ':uniacid' => $_W['uniacid']));
            if (empty($coupon)){
                return 0;
            }
            if ($coupon['total'] == -1){
                return -1;
            }
            $gettotal = pdo_fetchcolumn('select count(*) from ' . tablename('manor_shop_coupon_data') . ' where couponid=:couponid and uniacid=:uniacid limit 1', array(':couponid' => $couponid, ':uniacid' => $_W['uniacid']));
            return $coupon['total'] - $gettotal;
        }
        public function createNO($table, $field, $prefix){
            $billno = date('YmdHis') . random(6, true);
            while (1){
                $count = pdo_fetchcolumn('select count(*) from ' . tablename('manor_shop_' . $table) . " where {$field}=:billno limit 1", array(':billno' => $billno));
                if ($count <= 0){
                    break;
                }
                $billno = date('YmdHis') . random(6, true);
            }
            return $prefix . $billno;
        }
        function setCoupon($row, $time, $withOpenid){
            global $_W;
            if($withOpenid){
                $openid = $withOpenid;
            }
            $row['free'] = false;
            $row['past'] = false;
            $row['thumb'] = tomedia($row['thumb']);
            if ($row['money'] > 0 && $row['credit'] > 0){
                $row['getstatus'] = 0;
                $row['gettypestr'] = "购买";
            }else if ($row['money'] > 0){
                $row['getstatus'] = 1;
                $row['gettypestr'] = "购买";
            }else if ($row['credit'] > 0){
                $row['getstatus'] = 2;
                $row['gettypestr'] = "兑换";
            }else{
                $row['getstatus'] = 3;
                $row['gettypestr'] = "领取";
            }
            $row['timestr'] = "0";
            if (empty($row['timelimit'])){
                if (!empty($row['timedays'])){
                    $row['timestr'] = 1;
                }
            }else{
                if ($row['timestart'] >= $time){
                    $row['timestr'] = date('Y-m-d', $row['timestart']) . '-' . date('Y-m-d', $row['timeend']);
                }else{
                    $row['timestr'] = date('Y-m-d', $row['timeend']);
                }
            }
            $row['css'] = 'deduct';
            if ($row['backtype'] == 0){
                $row['backstr'] = '立减';
                $row['css'] = 'deduct';
                $row['backpre'] = true;
                $row['_backmoney'] = $row['deduct'];
            }else if ($row['backtype'] == 1){
                $row['backstr'] = '折';
                $row['css'] = 'discount';
                $row['_backmoney'] = $row['discount'];
            }else if ($row['backtype'] == 2){
                if (!empty($row['backredpack'])){
                    $row['backstr'] = '返现';
                    $row['css'] = "redpack";
                    $row['backpre'] = true;
                    $row['_backmoney'] = $row['backredpack'];
                }else if (!empty($row['backmoney'])){
                    $row['backstr'] = '返利';
                    $row['css'] = "money";
                    $row['backpre'] = true;
                    $row['_backmoney'] = $row['backmoney'];
                }else if (!empty($row['backcredit'])){
                    $row['backstr'] = '返积分';
                    $row['css'] = "credit";
                    $row['_backmoney'] = $row['backcredit'];
                }
            }
            if($withOpenid){
                $row['cangetmax'] = -1;
                $row['canget'] = true;
                if ($row['getmax'] > 0){
                    $gets = pdo_fetchcolumn('select count(*) from ' . tablename('manor_shop_coupon_data') . ' where couponid=:couponid and openid=:openid and uniacid=:uniacid and gettype=1 limit 1', array(':couponid' => $row['id'], ':openid' => $openid, ':uniacid' => $_W['uniacid']));
                    $row['cangetmax'] = $row['getmax'] - $gets;
                    if ($row['cangetmax'] <= 0){
                        $row['cangetmax'] = 0;
                        $row['canget'] = false;
                    }
                }
            }
            return $row;
        }
    }
}
