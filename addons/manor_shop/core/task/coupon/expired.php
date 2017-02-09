<?php
/**
 * 优惠卷到期通知任务
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
$p = p('coupon');
$sets = pdo_fetchall('select uniacid from ' . tablename('manor_shop_sysset'));
foreach ($sets as $set){
    $_W['uniacid'] = $set['uniacid'];
    if (empty($_W['uniacid'])){
        continue;
    }
    $trade = p('coupon')->getSet();
    if(!$trade['expirednoticetime']) {
        continue;
    }
    if(!$trade['templateid'] || !$trade['expirednoticetime']) {
        continue;
    }
    $expired_time = time() - intval($trade['expirednoticetime'])*60;
    $expired_second = intval($trade['expirednoticetime'])*60;
    $coupon_data = pdo_fetchall('select d.id,d.couponid,c.couponname,d.openid,c.timelimit,c.timedays,c.timestart,c.timeend,d.gettime from '.tablename('manor_shop_coupon_data').' as d left join '.tablename('manor_shop_coupon').' as c on d.couponid=c.id where c.uniacid=:uniacid and d.couponid>0 and d.used=0 and d.openid !="" and gettime <= :expired_time', array(':uniacid'=>$_W['uniacid'], ':expired_time'=>$expired_time));
    if($coupon_data) {
        foreach ($coupon_data as $item) {
            if($item['timelimit'] == 1) {
                $has_second = intval($item['timeend']) - time();
            } else {
                $end_time = $item['gettime'] + $item['timedays']*24*3600;
                $has_second = intval($end_time) - time();
            }
            //过期前多久提醒
            if($has_second>0 && $has_second < $expired_second) {
                send_coupon_expired_notice($item);
            }
        }
    }
}

function send_coupon_expired_notice ($item){
    global $_W;
    $msg = array('first'=>array('value' => '优惠劵到期提醒', "color" => "#a3b10e"),'keyword1' => array('value' => $item['couponname'], "color" => "#4CAF50"), 'keyword2' => array('value' => '请尽快前往商城使用', "color" => "#666"));
    $url = $_SERVER['HTTP_HOST'].'/app/index.php?i='.$_W['uniacid'].'&c=entry&do=shop&m=manor_shop';
    if (!empty($trade['templateid'])){
        m('message') -> sendTplNotice($item['openid'], $trade['templateid'], $msg, $url);
    }
}

