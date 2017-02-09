<?php
	/**
	 * 优惠劵
	 * 操作类名称: PhpStorm.
	 * 作者名称: alan
	 * 创建时间: 16/9/5 09:50
	 */
	global $_W, $_GPC;
	if($_GPC['from'] &&  !$_W['isajax']) {
		$redirect = $this->createMobileUrl('activity/ayibang');
		header("Location:$redirect");die;
	}
	$set = m('common') -> getSysset('shop');
    if($set['yimabang_power'] == 0) {
        message('系统暂未开放该活动', '', 'info');
    }
    if(!$set['yimabang_coupon']) {
        message('管理员正在设置活动所需优惠劵,请稍后再试', '', 'info');
    }
    if(!$set['ayibang_qr']) {
        message('系统故障，请稍候再试', '', 'info');
    }
    if (pdo_tableexists('alan_qrcode')) {
        $qr = pdo_get('alan_qrcode', array('id'=>$set['ayibang_qr']));
    } else {
        $qr = array();
    }

    if(!$qr)  {
        message('二维码不存在', '', 'info');
    }
    $qr_path = $qr['path'];
    $js_data = array(
        'total'=>531,
        'reduce'=>200
    );
    $end_time = strtotime('2016-11-30 23:59:59');
    $start_time = strtotime('2016-11-25 00:00:00');
    $today = time();
    //时间段
/*  $one_start = strtotime("2016-11-18 18:00:00");
$one_end = strtotime("2016-11-18 20:30:00");

$two_start = strtotime("2016-11-18 20:30:00");
$two_end = strtotime("2016-11-19 1:00:00");

$four_start = strtotime("2016-11-19 8:00:00");
$four_end = strtotime("2016-11-19 22:00:00");

$fave_start = strtotime("2016-11-20 8:00:00");
$fave_end = strtotime("2016-11-20 22:00:00");

if($today < strtotime("2016-11-18 18:00:00")) {
    $js_data['total'] = 2000;
} elseif ($today>=$one_start && $today<$one_end) {
    $h = floor(($today-$one_start)/3600);
    if($h>0) {
        $js_data['total'] = 2000 - $h*200;
    } else {
        $js_data['total'] = floor(2000 - ($today-$one_start)/3600);
    }
    if($js_data['total'] < 1813) {
        $js_data['total'] = 1813;
    }
} elseif ($today>=$two_start && $today<$two_end) {
    $h = floor(($today-$two_start)/3600);
    if($h>0) {
        $js_data['total'] = 1813 - $h*200;
    } else {
        $js_data['total'] = floor(1813 - ($today-$two_start)/3600);
    }
    if($js_data['total'] < 784) {
        $js_data['total'] = 784;
    }
} elseif ($today>strtotime("2016-11-19 1:00:00") && $today<strtotime("2016-11-19 8:00:00")) {
    $js_data['total'] = 784;
} elseif($today >= $four_start && $today < $four_end) {
    $h = floor(($today-$four_start)/3600);
    if($h>0) {
        $js_data['total'] = 784 - $h*200;
    } else {
        $js_data['total'] = floor(784 - ($today-$four_start)/3600);
    }
    if($js_data['total'] < 191) {
        $js_data['total'] = 191;
    }
} elseif($today >= strtotime("2016-11-19 22:00:00") && $today < strtotime("2016-11-20 8:00:00")) {
    $js_data['total'] = 191;
} elseif($today >= $fave_start && $today < $fave_end) {
    $h = floor(($today-$four_start)/3600);
    if($h>0) {
        $js_data['total'] = 191 - $h*200;
    } else {
        $js_data['total'] = floor(191 - ($today-$four_start)/3600);
    }
    if($js_data['total'] < 35) {
        $js_data['total'] = 35;
    }
} else {
    $js_data['total'] = 531;
}*/
    $number =  ($today- $start_time) / (24*3600);
    if($number >1 && $number <3) {
        $js_data['total'] =  $js_data['total'] - intval($number) * $js_data['reduce'];
    } elseif($number > 2) {
        $js_data['total'] = 131;
    }
    $num = str_split($js_data['total']);
    $_W['shopshare'] = array(
        'title'=>'特级平和蜜柚0元领 | 唐盛庄园牵手阿姨帮',
        'imgUrl'=>tomedia('../addons/manor_shop/template/mobile/tangsheng/static/images/ayibang/share.jpg'),
        'desc'=>'产地直送 极致新鲜 在家体验来自庄园的极致“柚”惑',
        'link'=>$this->createMobileUrl('activity/ayibang')
    );
    $time = rand(1, 10);
    $re = rand(1, 3);
	include $this->template('activity/ayibang');




