<?php
/**
* 订单回收任务
* Author：Alan
* [TangShengMonar System]Copyright (c) 2016 fresh.tangshengmanor.com
* Time:2016-12-12 15
*/
 error_reporting(0);
require '../../../../../framework/bootstrap.inc.php';
require '../../../../../addons/manor_shop/defines.php';
require '../../../../../addons/manor_shop/core/inc/functions.php';
require '../../../../../addons/manor_shop/core/inc/plugin/plugin_model.php';
global $_W, $_GPC;
ignore_user_abort();
set_time_limit(0);
$sets = pdo_fetchall('select uniacid from ' . tablename('manor_shop_sysset'));
foreach ($sets as $set){
    $_W['uniacid'] = $set['uniacid'];
    if (empty($_W['uniacid'])){
        continue;
    }
    $trade = m('common') -> getSysset('trade', $_W['uniacid']);
    $days = intval($trade['receive']);
    if($days <= 0){
        continue;
    }
    $daytimes = 86400 * $days;
    $p = p('commission');
    $pcoupon = p('coupon');
    $orders = pdo_fetchall('select id,couponid from ' . tablename('manor_shop_order') . " where uniacid={$_W['uniacid']} and status=2 and sendtime + {$daytimes} <=unix_timestamp() ", array(), 'id');
    if (!empty($orders)){
        $orderkeys = array_keys($orders);
        $orderids = implode(',', $orderkeys);
        if (!empty($orderids)){
            pdo_query('update ' . tablename('manor_shop_order') . ' set status=3,finishtime=' . time() . ' where id in (' . $orderids . ')');
            foreach ($orders as $orderid => $o){
                m('notice') -> sendOrderMessage($orderid);
                if($pcoupon){
                    if(!empty($o['couponid'])){
                        $pcoupon -> backConsumeCoupon($o['id']);
                    }
                }
                if ($p){
                    $p -> checkOrderFinish($orderid);
                }
            }
        }
    }
}
