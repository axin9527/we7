<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid = m('user') -> getOpenid();
$uniacid = $_W['uniacid'];
$r_type = array('0' => '退款', '1' => '退货退款', '2' => '换货');
if ($_W['isajax']){
    if ($operation == 'display'){
        $pindex = max(1, intval($_GPC['page']));
        $psize = 5;
        $status = $_GPC['status'];
        $condition = ' and openid=:openid  and userdeleted=0 and deleted=0 and uniacid=:uniacid ';
        $params = array(':uniacid' => $uniacid, ':openid' => $openid);
        if($status != ''){
            if($status < 4){
                if($status == 2){
                    $condition .= ' and (status=2 or status=0 and paytype=3)';
                }else if($status == 0){
                    $condition .= ' and status=0 and paytype!=3';
                }else{
                    $condition .= ' and status=' . intval($status);
                }
            }else{
                if($status == 4) {
                    $condition .= ' and refundstate>0';
                } else if($status == 5) {
                    $condition .= ' and status=-1 and refundstate=0 and refundtime >0';
                }
            }
        }else{
        }
        $list = pdo_fetchall('select id,addressid,ordersn,price,status,iscomment,isverify,verified,verifycode,iscomment,refundid,expresscom,express,expresssn,finishtime,`virtual`,paytype,expresssn,refundstate,full_cat_power,full_cat_power_money,gift_good from ' . tablename('manor_shop_order') . " where 1 {$condition} order by createtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('manor_shop_order') . " where 1 {$condition}", $params);
	    $tips_condition = ' and openid=:openid  and userdeleted=0 and deleted=0 and uniacid=:uniacid ';
	    $tips_params = array(':uniacid' => $uniacid, ':openid' => $openid);
	    $tips_num = array(
            'total'=>pdo_fetchcolumn('select count(id) from ' . tablename('manor_shop_order') . " where 1 {$tips_condition}", $params),
            'pay'=>pdo_fetchcolumn('select count(id) from ' . tablename('manor_shop_order') . " where 1 {$tips_condition} and status=0 and paytype!=3", $params),
            'send'=>pdo_fetchcolumn('select count(id) from ' . tablename('manor_shop_order') . " where 1 {$tips_condition} and status=1", $params),
            'give'=>pdo_fetchcolumn('select count(id) from ' . tablename('manor_shop_order') . " where 1 {$tips_condition} and (status=2 or status=0 and paytype=3)", $params),
            'finish'=>pdo_fetchcolumn('select count(id) from ' . tablename('manor_shop_order') . " where 1 {$tips_condition} and status=3", $params),
            'refund'=>pdo_fetchcolumn('select count(id) from ' . tablename('manor_shop_order') . " where 1 {$tips_condition} and refundid>0 and status>0 and refundstate=1", $params),
            'has_refund'=>pdo_fetchcolumn('select count(id) from ' . tablename('manor_shop_order') . " where 1 {$tips_condition} and status=-1 and refundstate=0 and refundtime >0", $params),
        );
	    $tradeset = m('common') -> getSysset('trade');
        $refunddays = intval($tradeset['refunddays']);
        $r_type = array('0' => '退款', '1' => '退货退款', '2' => '换货');
        foreach($list as & $row){
            $sql = 'SELECT og.goodsid,og.total,og.is_give,g.title,g.thumb,og.price,og.optionname,og.total as optiontitle,og.optionid FROM ' . tablename('manor_shop_order_goods') . ' og ' . ' left join ' . tablename('manor_shop_goods') . ' g on og.goodsid = g.id ' . ' where og.orderid=:orderid order by og.id asc';
            $row['goods'] = set_medias(pdo_fetchall($sql, array(':orderid' => $row['id'])), 'thumb');
            //$row['goodscount'] = count($row['goods']);
            $row['goodscount'] = array_sum(array_column($row['goods'], 'total'));
            $ref = pdo_fetchcolumn('select rtype from '.tablename('manor_shop_order_refund').' where id=:refundid', array(':refundid'=>$row['refundid']));
            $row['refunds_str'] = $r_type[$ref];
            switch($row['status']){
            case '-1': $status = '已取消';
                       if($row['refundid'] > 0) {
                           $status = '已'.$r_type[$ref];
                       }
                break;
            case "0": if($row['paytype'] == 3){
                    $status = '待发货';
                }else{
                    $status = '待付款';
                }
                break;
            case '1': if($row['isverify'] == 1){
                    $status = '待使用';
                }else if(empty($row['addressid'])){
                    $status = '待取货';
                }else{
                    $status = '待发货';
                }
                break;
            case '2': $status = '待收货';
                break;
            case '3': if(empty($row['iscomment'])){
                    $status = '待评价';
                }else{
                    $status = '交易完成';
                }
                break;
            }
            $row['statusstr'] = $status;
            if($row['refundstate'] > 0 && !empty($row['refundid'])){
            $refund = pdo_fetch('select * from ' . tablename('manor_shop_order_refund') . ' where id=:id and uniacid=:uniacid and orderid=:orderid limit 1' , array(':id' => $row['refundid'], ':uniacid' => $uniacid, ':orderid' => $row['id']));
            if (!empty($refund)){
            $row['statusstr'] = '待' . $r_type[$refund['rtype']];
        }
    }
    $canrefund = false;
    if($row['status'] == 1  || $row['status'] == 2){
        if($tradeset['power_can_refused_unsend'] == 1) {
            $canrefund = true;
        }
    }else if($row['status'] == 3){
        if($row['isverify'] != 1 && empty($row['virtual'])){
            if($refunddays > 0){
                $days = intval((time() - $row['finishtime']) / 3600 / 24);
                if($days <= $refunddays){
                    $canrefund = true;
                }
            }
        }
    }
    $row['canrefund'] = $canrefund;
}
unset($row);
show_json(1, array('total' => $total, 'list' => $list, 'pagesize' => $psize,'tips_num'=>$tips_num));
}
}
include $this -> template('order/list');
