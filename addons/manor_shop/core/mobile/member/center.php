<?php
 if(!defined('IN_IA')){
    exit('Access Denied');
}
global $_W, $_GPC;
$openid = m('user') -> getOpenid();
$set = m('common') -> getSysset(array('shop', 'trade'));
$member = m('member') -> getMember($openid);
$uniacid = $_W['uniacid'];
$hascom = false;
$plugc = p('commission');
if($plugc){
    $pset = $plugc -> getSet();
    if(!empty($pset['level'])){
        if($member['isagent'] == 1 && $member['status'] == 1){
            $hascom = true;
        }
    }
}
if(!$member) {
	//设置默认值
	$member['nickname'] = "田小鲜";
	$member['credit2'] = 0;
	$member['credit1'] = 0;
	$member['avatar'] = tomedia('images/global/avatars/avatar_9.jpg');
}
	//默认地址
	$address = pdo_get('manor_shop_member_address', array('uniacid'=>$_W['uniacid'], 'openid'=>$openid, 'deleted'=>0, 'isdefault'=>1));
$hascoupon = false;
$hascouponcenter = false;
$plugin_coupon = p('coupon');
if($plugin_coupon){
    $pcset = $plugin_coupon -> getSet();
    if(empty($pcset['closemember'])){
        $hascoupon = true;
    }
    if(empty($pcset['closecenter'])){
        $hascouponcenter = true;
    }
}
if($_W['isajax']){
    $level = array('levelname' => empty($set['shop']['levelname'])?'普通会员':$set['shop']['levelname']);
    if(!empty($member['level'])){
        $level = m('member') -> getLevel($openid);
    }
    $orderparams = array(':uniacid' => $_W['uniacid'], ':openid' => $openid);
    $order = array('status0' => pdo_fetchcolumn('select count(*) from ' . tablename('manor_shop_order') . ' where openid=:openid and status=0  and uniacid=:uniacid limit 1', $orderparams), 'status1' => pdo_fetchcolumn('select count(*) from ' . tablename('manor_shop_order') . ' where openid=:openid and status=1 and refundstate=0 and uniacid=:uniacid limit 1', $orderparams), 'status2' => pdo_fetchcolumn('select count(*) from ' . tablename('manor_shop_order') . ' where openid=:openid and status=2 and refundstate=0 and uniacid=:uniacid limit 1', $orderparams), 'status4' => pdo_fetchcolumn('select count(*) from ' . tablename('manor_shop_order') . ' where openid=:openid and refundstate>0 and uniacid=:uniacid limit 1', $orderparams),'status3' => pdo_fetchcolumn('select count(*) from ' . tablename('manor_shop_order') . ' where openid=:openid and status=3 and iscomment=0 and refundstate=0 and uniacid=:uniacid and deleted=0 and userdeleted=0 limit 1', $orderparams),);
    if(mb_strlen($member['nickname'], 'utf-8') > 6){
        $member['nickname'] = mb_substr($member['nickname'], 0, 6, 'utf-8');
    }
    $open_creditshop = false;
    $creditshop = p('creditshop');
    if($creditshop){
        $creditshop_set = $creditshop -> getSet();
        if(!empty($creditshop_set['centeropen'])){
            $open_creditshop = true;
        }
    }
    $counts = array('cartcount' => pdo_fetchcolumn('select ifnull(sum(total),0) from ' . tablename('manor_shop_member_cart') . ' where uniacid=:uniacid and openid=:openid and deleted=0 ', array(':uniacid' => $uniacid, ':openid' => $openid)), 'favcount' => pdo_fetchcolumn('select count(*) from ' . tablename('manor_shop_member_favorite') . ' where uniacid=:uniacid and openid=:openid and deleted=0 ', array(':uniacid' => $uniacid, ':openid' => $openid)));
    if($plugin_coupon){
        $time = time();
        $sql = 'select count(*) from ' . tablename('manor_shop_coupon_data') . ' d';
        $sql .= ' left join ' . tablename('manor_shop_coupon') . ' c on d.couponid = c.id';
        $sql .= ' where d.openid=:openid and d.uniacid=:uniacid and  d.used=0 ';
        $sql .= " and (   (c.timelimit = 0 and ( c.timedays=0 or c.timedays*86400 + d.gettime >=unix_timestamp() ) )  or  (c.timelimit =1 and c.timestart<={$time} && c.timeend>={$time})) order by d.gettime desc";
        $counts['couponcount'] = pdo_fetchcolumn($sql, array(':openid' => $openid, ':uniacid' => $_W['uniacid']));
    }
    show_json(1, array('member' => $member, 'order' => $order, 'level' => $level, 'open_creditshop' => $open_creditshop, 'counts' => $counts));
}
include $this -> template('member/center');
