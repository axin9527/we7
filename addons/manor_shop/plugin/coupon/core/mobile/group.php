<?php
 global $_W, $_GPC;
$openid = m('user') -> getOpenid();
$op = empty($_GPC['op']) ? 'display' : trim($_GPC['op']);
$id = intval($_GPC['id']);
$coupon = pdo_fetch('select * from ' . tablename('manor_shop_coupon_category') . ' where id=:id and uniacid=:uniacid  limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
if (empty($coupon)){
    if ($_W['isajax']){
        show_json(-1, '未找到优惠券');
    }
    header('location: ' . $this -> createMobileUrl('shop/index'));
    exit;
}
$coupon['sub_desc'] = str_replace("\n", "<br/>", $coupon['sub_desc']);
$set = $this -> model -> getSet();
if ($op == 'display'){
    $this -> model -> setShare();
    $credit = m('member') -> getCredit($openid, 'credit1');
    include $this -> template('group');
}
