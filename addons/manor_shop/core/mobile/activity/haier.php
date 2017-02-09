<?php
	/**
	 * 优惠劵
	 * 操作类名称: PhpStorm.
	 * 作者名称: alan
	 * 创建时间: 16/9/5 09:50
	 */
	global $_W, $_GPC;
	if($_GPC['from'] &&  !$_W['isajax']) {
		$redirect = $this->createMobileUrl('activity/haier');
		header("Location:$redirect");die;
	}
	$set = m('common') -> getSysset('shop');
    if($set['haier_power'] == 0) {
        die('系统暂未开放该活动');
    }
    if(!$set['haier_coupon'][0]) {
        die('管理员正在设置活动所需优惠劵,请稍后再试');
    }
    $share = array(
        'title'=>'送优惠',
        ''
    );
    $ids = implode(',', array_filter(array_unique($set['haier_coupon'])));
	$coupon = pdo_getall('manor_shop_coupon', array('uniacid'=>$_W['uniacid'], 'id in'=>$ids));
	include $this->template('activity/haier');




