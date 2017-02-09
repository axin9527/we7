<?php
/**
 * 订单关闭任务
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

    $days = intval($trade['closeorder']);

    if ($days <= 0){
        continue;
    }
    $daytimes = 86400 * $days;
    $orders = pdo_fetchall('select id from ' . tablename('manor_shop_order') . " where  uniacid={$_W['uniacid']} and status=0 and paytype<>3  and createtime + {$daytimes} <=unix_timestamp() ");
    $p = p('coupon');
    foreach ($orders as $o){
        $onew = pdo_fetch('select status from ' . tablename('manor_shop_order') . " where id=:id and status=0 and paytype<>3  and createtime + {$daytimes} <=unix_timestamp()  limit 1", array(':id' => $o['id']));
        if(!empty($onew) && $onew['status'] == 0){
            if ($p){
                if (!empty($o['couponid'])){
                    $p -> returnConsumeCoupon($o['id']);
                }
            }
            m('order') -> setStocksAndCredits($o['id'], 2);
            pdo_query('update ' . tablename('manor_shop_order') . ' set status=-1,canceltime=' . time() . ' where id=' . $o['id']);
        }
    }
}
