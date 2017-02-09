<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op'])? $_GPC['op'] : 'display';
$openid = m('user') -> getOpenid();
if (empty($openid)){
    $openid = $_GPC['openid'];
}
$member = m('member') -> getMember($openid);
$uniacid = $_W['uniacid'];
$orderid = intval($_GPC['orderid']);
$logid = intval($_GPC['logid']);
$shopset = m('common') -> getSysset('shop');
require IA_ROOT.'/payment/unionpay/__init.php';
if ($_W['isajax']){
    if (!empty($orderid)){
        $order = pdo_fetch("select * from " . tablename('manor_shop_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1' , array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));
        if (empty($order)){
            show_json(0, '订单未找到!');
        }
        $log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(':uniacid' => $uniacid, ':module' => 'manor_shop', ':tid' => $order['ordersn']));
        if (!empty($log) && $log['status'] != '0'){
            show_json(0, '订单已支付, 无需重复支付!');
        }
        $param_title = $shopset['name'] . "订单: " . $order['ordersn'];
        $unionpay = array('success' => false);
        $params = array();
        $params['tid'] = $log['tid'];
        $params['certId'] = getSignCertId();
        $params['fee'] = $order['price'];
        $params['uniontid'] = $orderid;
        load() -> func('communication');
        load() -> model('payment');
        $setting = uni_setting($_W['uniacid'], array('payment'));
        if (is_array($setting['payment'])){
            $options = $setting['payment']['unionpay'];
            $unionpay = m('common') -> unionpay_build($params, $options, 0, $openid);
            sign($unionpay);
        }
    }elseif (!empty($logid)){
        $log = pdo_fetch('SELECT * FROM ' . tablename('manor_shop_member_log') . ' WHERE `id`=:id and `uniacid`=:uniacid limit 1', array(':uniacid' => $uniacid, ':id' => $logid));
        if (empty($log)){
            show_json(0, '充值出错!');
        }
        if (!empty($log['status'])){
            show_json(0, '已经充值成功,无需重复支付!');
        }
        $unionpay = array('success' => false);
        $params = array();
        $params['tid'] = $log['logno'];
        $params['certId'] = getSignCertId();
        $params['fee'] = $log['money'];
        $params['uniontid'] = $logid;
        load() -> func('communication');
        load() -> model('payment');
        $setting = uni_setting($_W['uniacid'], array('payment'));
        if (is_array($setting['payment'])){
            $options = $setting['payment']['unionpay'];
            $unionpay = m('common') -> unionpay_build($params, $options, 1, $openid);
            sign($unionpay);
        }
    }
    show_json(1, array('unionpay' => $unionpay, 'pay'=>true));
}
$action = SDK_FRONT_TRANS_URL;
include $this -> template('order/pay_unionpay');