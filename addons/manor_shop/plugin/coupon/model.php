<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
if (!class_exists('CouponModel')){
    class CouponModel extends PluginModel{
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
        function creditshop($logid = 0){
            global $_W, $_GPC;
            $pcreditshop = p('creditshop');
            if(!$pcreditshop){
                return;
            }
            $log = pdo_fetch('SELECT * FROM ' . tablename('manor_shop_creditshop_log') . ' WHERE `id`=:id and `uniacid`=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $logid));
            if(!empty($log)){
                $member = m('member') -> getMember($log['openid']);
                $goods = $pcreditshop -> getGoods($log['couponid'], $member);
                $couponlog = array('uniacid' => $_W['uniacid'], 'openid' => $log['openid'], 'logno' => m('common') -> createNO('coupon_log', 'logno', 'CC'), 'couponid' => $log['couponid'], 'status' => 1, 'paystatus' => $goods['money'] > 0 ? 0 : -1, 'creditstatus' => $goods['credit'] > 0 ? 0 : -1, 'createtime' => time(), 'getfrom' => 2);
                pdo_insert('manor_shop_coupon_log', $couponlog);
                $data = array('uniacid' => $_W['uniacid'], 'openid' => $log['openid'], 'couponid' => $log['couponid'], 'gettype' => 2, 'gettime' => time());
                pdo_insert('manor_shop_coupon_data', $data);
                $coupon = pdo_fetch('select * from ' . tablename('manor_shop_coupon') . ' where id=:id limit 1', array(':id' => $log['couponid']));
                $coupon = $this -> setCoupon($coupon, time());
                $set = $this -> getSet();
                $this -> sendMessage($coupon, 1, $member, $set['templateid']);
                pdo_update('manor_shop_creditshop_log', array('status' => 3), array('id' => $logid));
            }
        }
        function poster($member, $couponid, $couponnum){
            global $_W, $_GPC;
            $pposter = p('poster');
            if(!$pposter){
                return;
            }
            $coupon = $this -> getCoupon($couponid);
            if(empty($coupon)){
                return;
            }
            for($i = 1;$i <= $couponnum;$i++){
                $couponlog = array('uniacid' => $_W['uniacid'], 'openid' => $member['openid'], 'logno' => m('common') -> createNO('coupon_log', 'logno', 'CC'), 'couponid' => $couponid, 'status' => 1, 'paystatus' => -1, 'creditstatus' => -1, 'createtime' => time(), 'getfrom' => 3);
                pdo_insert('manor_shop_coupon_log', $couponlog);
                $data = array('uniacid' => $_W['uniacid'], 'openid' => $member['openid'], 'couponid' => $couponid, 'gettype' => 3, 'gettime' => time());
                pdo_insert('manor_shop_coupon_data', $data);
            }
            $set = $this -> getSet();
            $this -> sendMessage($coupon, $couponnum, $member, $set['templateid']);
        }
        function payResult($logno){
            global $_W;
            if(empty($logno)){
                return error(-1);
            }
            $log = pdo_fetch('SELECT * FROM ' . tablename('manor_shop_coupon_log') . ' WHERE `logno`=:logno and `uniacid`=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':logno' => $logno));
            if (empty($log)){
                return error(-1, '服务器错误!');
            }
            if ($log['status'] >= 1){
                return true;
            }
            $coupon = pdo_fetch('select * from ' . tablename('manor_shop_coupon') . ' where id=:id limit 1', array(':id' => $log['couponid']));
            $coupon = $this -> setCoupon($coupon, time());
            if (empty($coupon['gettype'])){
                return error(-1, '无法领取');
            }
            if ($coupon['total'] != -1){
                if ($coupon['total'] <= 0){
                    return error(-1, '优惠券数量不足');
                }
            }
            if (!$coupon['canget']){
                return error(-1, '您已超出领取次数限制');
            }
            if(empty($log['status'])){
                $update = array();
                if ($coupon['credit'] > 0 && empty($log['creditstatus'])){
                    m('member') -> setCredit($log['openid'], 'credit1', - $coupon['credit'], "购买优惠券扣除积分 {$coupon['credit']}");
                    $update['creditstatus'] = 1;
                }
                if ($coupon['money'] > 0 && empty($log['paystatus'])){
                    if ($coupon['paytype'] == 0){
                        m('member') -> setCredit($log['openid'], 'credit2', - $coupon['money'], "购买优惠券扣除余额 {$coupon['money']}");
                    }
                    $update['paystatus'] = 1;
                }
                $update['status'] = 1;
                pdo_update('manor_shop_coupon_log', $update, array('id' => $log['id']));
                $data = array('uniacid' => $_W['uniacid'], 'openid' => $log['openid'], 'couponid' => $log['couponid'], 'gettype' => $log['getfrom'], 'gettime' => time());
                pdo_insert('manor_shop_coupon_data', $data);
                $member = m('member') -> getMember($log['openid']);
                $set = $this -> getSet();
                $this -> sendMessage($coupon, 1, $member, $set['templateid']);
            }
            $url = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=manor_shop&do=member';
            if($coupon['coupontype'] == 0){
                $url = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=manor_shop&do=shop&p=list';
            }else{
                $url = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=manor_shop&do=member&p=recharge';
            }
            if (strexists($url, '/addons/manor_shop/plugin/coupon/core/mobile/')){
                $url = str_replace('/addons/manor_shop/plugin/coupon/core/mobile/', '/', $url);
            }
            if (strexists($url, '/addons/manor_shop/')){
                $url = str_replace('/addons/manor_shop/', '/', $url);
            }

            return array('url' => $url);
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
                $resp = m('message') -> sendNews($member['openid'], $articles, $account);
                if (is_error($resp)){
                    $msg = array('keyword1' => array('value' => $title, "color" => "#73a68d"), 'keyword2' => array('value' => $desc, "color" => "#73a68d"));
                    if (!empty($templateid)){
                        m('message') -> sendTplNotice($member['openid'], $templateid, $msg, $url);
                    }
                }
            }
        }
        function sendBackMessage($openid, $coupon, $gives){
            global $_W;
            if (empty($gives)){
                return;
            }
            $set = $this -> getSet();
            $templateid = $set['templateid'];
            $content = "您的优惠券【{$coupon['couponname']}】已返利 ";
            $givestr = '';
            if (isset($gives['credit'])){
                $givestr .= " {$gives['credit']}个积分";
            }
            if (isset($gives['money'])){
                if (!empty($givestr)){
                    $givestr .= "，";
                }
                $givestr .= "{$gives['money']}元余额";
            }
            if (isset($gives['redpack'])){
                if (!empty($givestr)){
                    $givestr .= "，";
                }
                $givestr .= "{$gives['redpack']}元现金";
            }
            $content .= $givestr;
            $content .= "，请查看您的账户，谢谢!";
            $msg = array('keyword1' => array('value' => "优惠券返利", "color" => "#73a68d"), 'keyword2' => array('value' => $content, "color" => "#73a68d"));
            $url = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=manor_shop&do=member';
            if (strexists($url, '/addons/manor_shop/plugin/coupon/core/mobile/')){
                $url = str_replace('/addons/manor_shop/plugin/coupon/core/mobile/', '/', $url);
            }
            if (strexists($url, '/addons/manor_shop/')){
                $url = str_replace('/addons/manor_shop/', '/', $url);
            }
            if (!empty($templateid)){
                m('message') -> sendTplNotice($openid, $templateid, $msg, $url);
            }else{
                m('message') -> sendCustomNotice($openid, $msg, $url);
            }
        }
        function sendReturnMessage($openid, $coupon){
            global $_W;
            $set = $this -> getSet();
            $templateid = $set['templateid'];
            $msg = array('keyword1' => array('value' => "优惠券退回", "color" => "#73a68d"), 'keyword2' => array('value' => "您的优惠券【{$coupon['couponname']}】已退回您的账户，您可以再次使用, 谢谢!", "color" => "#73a68d"));
            $url = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=manor_shop&do=plugin&p=coupon&method=my';
            if (!empty($templateid)){
                m('message') -> sendTplNotice($openid, $templateid, $msg, $url);
            }else{
                m('message') -> sendCustomNotice($openid, $msg, $url);
            }
        }
        function useRechargeCoupon($log){
            global $_W;
            if (empty($log['couponid'])){
                return;
            }
            $data = pdo_fetch('select id,openid,couponid,used from ' . tablename('manor_shop_coupon_data') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $log['couponid'], ':uniacid' => $_W['uniacid']));
            if (empty($data)){
                return;
            }
            if (!empty($data['used'])){
                return;
            }
            $coupon = pdo_fetch('select enough,backcredit,backmoney,backredpack from ' . tablename('manor_shop_coupon') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $data['couponid'], ':uniacid' => $_W['uniacid']));
            if (empty($coupon)){
                return;
            }
            if ($coupon['enough'] > 0 && $log['money'] < $coupon['enough']){
                return;
            }
            $gives = array();
            $backcredit = $coupon['backcredit'];
            if (!empty($backcredit)){
                if (strexists($backcredit, '%')){
                    $backcredit = intval(floatval(str_replace('%', '', $backcredit)) / 100 * $log['money']);
                }else{
                    $backcredit = intval($backcredit);
                }
                if ($backcredit > 0){
                    $gives['credit'] = $backcredit;
                    m('member') -> setCredit($data['openid'], 'credit1', $backcredit, array(0, '充值优惠券返积分'));
                }
            }
            $backmoney = $coupon['backmoney'];
            if (!empty($backmoney)){
                if (strexists($backmoney, '%')){
                    $backmoney = round(floatval(floatval(str_replace('%', '', $backmoney)) / 100 * $log['money']), 2);
                }else{
                    $backmoney = round(floatval($backmoney), 2);
                }
                if ($backmoney > 0){
                    $gives['money'] = $backmoney;
                    m('member') -> setCredit($data['openid'], 'credit2', $backmoney, array(0, '充值优惠券返利'));
                }
            }
            $backredpack = $coupon['backredpack'];
            if (!empty($backredpack)){
                if (strexists($backredpack, '%')){
                    $backredpack = round(floatval(floatval(str_replace('%', '', $backredpack)) / 100 * $log['money']), 2);
                }else{
                    $backredpack = round(floatval($backredpack), 2);
                }
                if ($backredpack > 0){
                    $gives['redpack'] = $backredpack;
                    $backredpack = intval($backredpack * 100);
                    m('finance') -> pay($data['openid'], 1, $backredpack, '', '充值优惠券-返现金');
                }
            }
            pdo_update('manor_shop_coupon_data', array('used' => 1, 'usetime' => time(), 'ordersn' => $log['logno']), array('id' => $data['id']));
            $this -> sendBackMessage($log['openid'], $coupon, $gives);
        }
        function consumeCouponCount($openid, $money = 0,$goods_ids = array()){
            global $_W, $_GPC;
            $time = time();
            //$sql = "select count(*) from " . tablename('manor_shop_coupon_data') . " d " . "  left join " . tablename('manor_shop_coupon') . " c on d.couponid = c.id " . "  where d.openid=:openid and d.uniacid=:uniacid and c.coupon_goods_id <= '' and  c.coupontype=0 and {$money}>=c.enough and d.used=0 " . " and (   (c.timelimit = 0 and ( c.timedays=0 or c.timedays*86400 + d.gettime >=unix_timestamp() ) )  or  (c.timelimit =1 and c.timestart<={$time} && c.timeend>={$time}))";
	        $sql = 'select count(*) from ' . tablename('manor_shop_coupon_data') . ' d';
	        $sql .= ' left join ' . tablename('manor_shop_coupon') . ' c on d.couponid = c.id';
	        $sql .= " where d.openid=:openid and d.uniacid=:uniacid and  c.coupontype !=1 and {$money}>=c.enough and d.used=0 ";
	        $sql .=" and c.coupon_goods_id <=''";
	        $sql .= " and (   (c.timelimit = 0 and ( c.timedays=0 or c.timedays*86400 + d.gettime >=unix_timestamp() ) )  or  (c.timelimit =1 and c.timestart<={$time} && c.timeend>={$time})) order by d.gettime desc";

	        if($goods_ids) {
		        foreach($goods_ids as $goods_id) {
			        $g_item[] = pdo_fetchcolumn('select coupon_id from '.tablename('manor_shop_coupon_goods').' where goods_id=:goods_id and uniacid=:uniacid', array(":goods_id"=>$goods_id, ':uniacid'=>$_W['uniacid']));
	        	}
		        $g_item_real = array_unique(array_filter($g_item));
		        if($g_item_real) {
			        $r_coupon_ids = rtrim(implode(',', $g_item_real), ",");
			        if($r_coupon_ids) {
				        //"select count(*) from " . tablename('manor_shop_coupon_data') . " d " . "  left join " . tablename('manor_shop_coupon') . " c on d.couponid = c.id " . "  where d.openid=:openid and d.uniacid=:uniacid and c.coupon_goods_id IS NOT NULL and d.couponid in ('".$r_coupon_ids."') and  c.coupontype=0 and {$money}>=c.enough and d.used=0 " . " and (   (c.timelimit = 0 and ( c.timedays=0 or c.timedays*86400 + d.gettime >=unix_timestamp() ) )  or  (c.timelimit =1 and c.timestart<={$time} && c.timeend>={$time}))";
				        $sql2 = str_replace("and c.coupon_goods_id <=''", ' and c.coupon_goods_id IS NOT NULL and d.couponid in ('.$r_coupon_ids.')', $sql);
			        }

		        }
	        }

	        if($goods_ids) {
	            $num1 = pdo_fetchcolumn($sql, array(':openid' => $openid, ':uniacid' => $_W['uniacid']));
		        if($r_coupon_ids) {
			        $num2 = pdo_fetchcolumn($sql2, array(':openid' => $openid, ':uniacid' => $_W['uniacid']));
		        } else {
			        $num2 = 0;
		        }
		        return intval($num1) + intval($num2);
	        }
            return pdo_fetchcolumn($sql, array(':openid' => $openid, ':uniacid' => $_W['uniacid']));
        }
        function useConsumeCoupon($orderid = 0){
            global $_W, $_GPC;
            if (empty($orderid)){
                return;
            }
            $order = pdo_fetch('select ordersn,createtime,couponid from ' . tablename('manor_shop_order') . ' where id=:id and status>=0 and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $_W['uniacid']));
            if (empty($order)){
                return;
            }
            $coupon = false;
            if (!empty($order['couponid'])){
                $coupon = $this -> getCouponByDataID($order['couponid']);
            }
            if (empty($coupon)){
                return;
            }
            pdo_update('manor_shop_coupon_data', array('used' => 1, 'usetime' => $order['createtime'], 'ordersn' => $order['ordersn']), array('id' => $order['couponid']));
        }
        function returnConsumeCoupon($order){
            global $_W;
            if (!is_array($order)){
                $order = pdo_fetch('select id,openid,ordersn,createtime,couponid,status,finishtime from ' . tablename('manor_shop_order') . ' where id=:id and status>=0 and uniacid=:uniacid limit 1', array(':id' => intval($order), ':uniacid' => $_W['uniacid']));
            }
            if (empty($order)){
                return;
            }
            $coupon = $this -> getCouponByDataID($order['couponid']);
            if (empty($coupon)){
                return;
            }
            if (!empty($coupon['returntype'])){
                if (!empty($coupon['used'])){
                    pdo_update('manor_shop_coupon_data', array('used' => 0, 'usetime' => 0, 'ordersn' => ''), array('id' => $order['couponid']));
                    $this -> sendReturnMessage($order['openid'], $coupon);
                }
            }
        }
        function backConsumeCoupon($order){
            global $_W;
            if (!is_array($order)){
                $order = pdo_fetch('select id,openid,ordersn,createtime,couponid,status,finishtime,`virtual` from ' . tablename('manor_shop_order') . ' where id=:id and status>=0 and uniacid=:uniacid limit 1', array(':id' => intval($order), ':uniacid' => $_W['uniacid']));
            }
            if (empty($order)){
                return;
            }
            $couponid = $order['couponid'];
            if (empty($couponid)){
                return;
            }
            $coupon = $this -> getCouponByDataID($order['couponid']);
            if (empty($coupon)){
                return;
            }
            if (!empty($coupon['back'])){
                return;
            }
            $gives = array();
            $canback = false;
            if ($order['status'] == 1 && $coupon['backwhen'] == 2){
                $canback = true;
            }else if ($order['status'] == 3){
                if(!empty($order['virtual'])){
                    $canback = true;
                }else{
                    if ($coupon['backwhen'] == 1){
                        $canback = true;
                    }else if ($coupon['backwhen'] == 0){
                        $canback = true;
                        $tradeset = m('common') -> getSysset('trade');
                        $refunddays = intval($tradeset['refunddays']);
                        if ($refunddays > 0){
                            $days = intval((time() - $order['finishtime']) / 3600 / 24);
                            if ($days <= $refunddays){
                                $canback = false;
                            }
                        }
                    }
                }
            }
            if ($canback){
                $ordermoney = pdo_fetchcolumn('select ifnull( sum(og.realprice),0) from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join ' . tablename('manor_shop_order') . ' o on o.id=og.orderid ' . ' where o.id=:orderid and o.openid=:openid and o.uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':openid' => $order['openid'], ':orderid' => $order['id']));
                $backcredit = $coupon['backcredit'];
                if (!empty($backcredit)){
                    if (strexists($backcredit, '%')){
                        $backcredit = intval(floatval(str_replace('%', '', $backcredit)) / 100 * $ordermoney);
                    }else{
                        $backcredit = intval($backcredit);
                    }
                    if ($backcredit > 0){
                        $gives['credit'] = $backcredit;
                        m('member') -> setCredit($order['openid'], 'credit1', $backcredit, array(0, '充值优惠券返积分'));
                    }
                }
                $backmoney = $coupon['backmoney'];
                if (!empty($backmoney)){
                    if (strexists($backmoney, '%')){
                        $backmoney = round(floatval(floatval(str_replace('%', '', $backmoney)) / 100 * $ordermoney), 2);
                    }else{
                        $backmoney = round(floatval($backmoney), 2);
                    }
                    if ($backmoney > 0){
                        $gives['money'] = $backmoney;
                        m('member') -> setCredit($order['openid'], 'credit2', $backmoney, array(0, '购物优惠券返利'));
                    }
                }
                $backredpack = $coupon['backredpack'];
                if (!empty($backredpack)){
                    if (strexists($backredpack, '%')){
                        $backredpack = round(floatval(floatval(str_replace('%', '', $backredpack)) / 100 * $ordermoney), 2);
                    }else{
                        $backredpack = round(floatval($backredpack), 2);
                    }
                    if ($backredpack > 0){
                        $gives['redpack'] = $backredpack;
                        $backredpack = intval($backredpack * 100);
                        m('finance') -> pay($order['openid'], 1, $backredpack, '', '购物优惠券-返现金');
                    }
                }
                pdo_update('manor_shop_coupon_data', array('back' => 1, 'backtime' => time()), array('id' => $order['couponid']));
                $this -> sendBackMessage($order['openid'], $coupon, $gives);
            }
        }
        function getCoupon($couponid = 0){
            global $_W;
            return pdo_fetch('select * from ' . tablename('manor_shop_coupon') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $couponid, ':uniacid' => $_W['uniacid']));
        }
        function getCouponByDataID($dataid = 0){
            global $_W;
            $data = pdo_fetch('select id,openid,couponid,used,back,backtime from ' . tablename('manor_shop_coupon_data') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $dataid, ':uniacid' => $_W['uniacid']));
            if (empty($data)){
                return false;
            }
            $coupon = pdo_fetch('select * from ' . tablename('manor_shop_coupon') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $data['couponid'], ':uniacid' => $_W['uniacid']));
            if (empty($coupon)){
                return false;
            }
            $coupon['back'] = $data['back'];
            $coupon['backtime'] = $data['backtime'];
            $coupon['used'] = $data['used'];
            $coupon['usetime'] = $data['usetime'];
            return $coupon;
        }
        function setCoupon($row, $time, $withOpenid = true){
            global $_W;
            if($withOpenid){
                $openid = m('user') -> getOpenid();
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
        function setMyCoupon($row, $time){
            global $_W;
            $row['past'] = false;
            $row['thumb'] = tomedia($row['thumb']);
            $row['timestr'] = "";
            if (empty($row['timelimit'])){
                if (!empty($row['timedays'])){
                    $row['timestr'] = date('Y-m-d', $row['gettime'] + $row['timedays'] * 86400);
                    if ($row['gettime'] + $row['timedays'] * 86400 < $time){
                        $row['past'] = true;
                    }
                }
            }else{
                if ($row['timestart'] >= $time){
                    $row['timestr'] = date('Y-m-d H:i', $row['timestart']) . '-' . date('Y-m-d', $row['timeend']);
                }else{
                    $row['timestr'] = date('Y-m-d H:i', $row['timeend']);
                }
                if ($row['timeend'] < $time){
                    $row['past'] = true;
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
            if ($row['past']){
                $row['css'] = 'past';
            }
            return $row;
        }
        function setShare(){
            global $_W, $_GPC;
            $set = $this -> getSet();
            $openid = m('user') -> getOpenid();
            $url = $_W['siteroot'] . "app/index.php?i={$_W['uniacid']}&c=entry&p=coupon&m=manor_shop&do=plugin";
            $_W['shopshare'] = array('title' => $set['title'], 'imgUrl' => tomedia($set['icon']), 'desc' => $set['desc'], 'link' => $url);
            if (p('commission')){
                $pset = p('commission') -> getSet();
                if (!empty($pset['level'])){
                    $member = m('member') -> getMember($openid);
                    if (!empty($member) && $member['status'] == 1 && $member['isagent'] == 1){
                        $_W['shopshare']['link'] = $url . "&mid=" . $member['id'];
                        if (empty($pset['become_reg']) && (empty($member['realname']) || empty($member['mobile']))){
                            $trigger = true;
                        }
                    }else if (!empty($_GPC['mid'])){
                        $_W['shopshare']['link'] = $url . "&mid=" . $_GPC['id'];
                    }
                }
            }
        }
        function perms(){
            return array(
                'coupon' => array(
                    'text' => $this->getName(),
                    'isplugin' => true,
                    'child' => array(
                        'coupon' => array(
                            'text' => '优惠券',
                            'view' => '查看',
                            'add' => '添加优惠券-log',
                            'edit' => '编辑优惠券-log',
                            'delete' => '删除优惠券-log',
                            'send' => '发放优惠券-log'),
                        'category' => array(
                            'text' => '分类',
                            'view' => '查看',
                            'add' => '添加分类-log',
                            'edit' => '编辑分类-log',
                            'delete' => '删除分类-log',
                            'sendgroup' => '发送优惠劵组-log',
                            'grouprecord' => '领取组记录-log'
                        ),
                        'log' => array(
                            'text' => '优惠券记录',
                            'view' => '查看',
                            'export' => '导出-log'
                        ),
                        'center' => array(
                            'text' => '领券中心设置',
                            'view' => '查看设置',
                            'save' => '保存设置-log'
                        ),
                        'set' => array(
                            'text' => '基础设置',
                            'view' => '查看设置',
                            'save' => '保存设置-log'
                        ),
                    )
                )
            );
        }

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
            if(!$members) {
                return '用户信息不存在';
            }
            foreach ($coupons as $coupon) {
                if($coupon['total'] != -1) {
                    $last = $this -> get_last_count($coupon['id']);
                    if ($last <= 0){
                        return '存在优惠券数量不足,无法发放!';
                    }
                    //$need = $coupon['getmax'] - $last;
                    if ($last < $coupon['getmax']){
                        return '存在优惠券数量不足,请补充 {$need} 张优惠券才能发放!';
                    }
                }
            }
            //发送并且推送消息
            $account = m('common') -> getAccount();
            $set = $this->getSet();
            $time = time();
            foreach ($members as $m){
                $send_num = 1;
                for ($j = 1; $j<=$send_total;$j++) {
                    foreach ($coupons as $coupon) {
                        $getmax = $coupon['getmax']?$coupon['getmax']:1;
                        for ($i = 1; $i <=$getmax;$i++) {
                            $log = array('uniacid' => $_W['uniacid'], 'openid' => $m['openid'], 'logno' => m('common') -> createNO('coupon_log', 'logno', 'CC'), 'couponid' => $coupon['id'], 'status' => 1, 'paystatus' => -1, 'creditstatus' => -1, 'createtime' => $time, 'getfrom' => 0);
                            pdo_insert('manor_shop_coupon_log', $log);
                            $logid = pdo_insertid();
                            $data = array('uniacid' => $_W['uniacid'], 'openid' => $m['openid'], 'couponid' => $coupon['id'], 'gettype' => 0, 'gettime' => $time, 'senduid' => $_W['uid']);
                            pdo_insert('manor_shop_coupon_data', $data);
                            $send_num ++;
                        }
                    }
                }
                //推送一个总的通知
                /* foreach ($coupons as $coupon) {
                    for ($i = 1;$i <= $send_total;$i++){
                        $log = array('uniacid' => $_W['uniacid'], 'openid' => $m['openid'], 'logno' => m('common') -> createNO('coupon_log', 'logno', 'CC'), 'couponid' => $coupon['id'], 'status' => 1, 'paystatus' => -1, 'creditstatus' => -1, 'createtime' => $time, 'getfrom' => 0);
                        pdo_insert('manor_shop_coupon_log', $log);
                        $logid = pdo_insertid();
                        $data = array('uniacid' => $_W['uniacid'], 'openid' => $m['openid'], 'couponid' => $coupon['id'], 'gettype' => 0, 'gettime' => $time, 'senduid' => $_W['uid']);
                        pdo_insert('manor_shop_coupon_data', $data);;
                    }
                    //重置默认发送模版
                    if($send_message_template) {
                        $coupon = $send_message_template;
                    }
                    if(!$is_group) {
                        $this->sendMessage($coupon, $send_total, $m, $set['templateid'], $account);
                    }
                }*/
                if($is_group && !$send_message_template) {
                    $send_message_template = array(
                        'resptitle' => $group['sub_tittle'],
                        'respthumb' => save_media($group['sub_thumb']),
                        'respdesc' => trim($group['sub_desc']),
                        'respurl' => $group['sub_url'],
                    );
                    $this->sendMessage($send_message_template, $send_total, $m, $set['templateid'], $account);
                } else {
                    $this->sendMessage($send_message_template, $send_total, $m, $set['templateid'], $account);
                }
            }
            return array('优惠券发放成功');
        }

        public function check_td_coupon($code,$openid) {
            global $_W;
            load()->model('mc');
            $fans = mc_fansinfo($openid);
            include IA_ROOT.'/api/financial.php';
            $financial = new financial($_W['uid']);
            $ret = $financial->check_coupon_code($code, $fans);
            return $ret;
        }
    }
}
