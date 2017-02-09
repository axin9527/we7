<?php
	/**
	 * 操作类名称: PhpStorm.
	 * 作者名称: alan
	 * 创建时间: 16/7/3 14:47
	 */
	global $_W;
	$params = array();
	$openid    = $_W['openid'];
	$d = explode(" ", microtime());
	$orderno = date('YmdHis').substr($d[0], 2).rand(1000000000, $d[1]);
	$params['tid'] = $orderno;
	$params['user'] = $openid;
	$params['fee'] = 9.9;
	$params['title'] = '精品车厘子（线下推广专享）';
	$setting = uni_setting($_W['uniacid'], array('payment'));
	$options = $setting['payment']['wechat'];
	$options['appid'] = $_W['account']['key'];
	$options['secret'] = $_W['account']['secret'];
	$common = m('common')->wechat_build($params, $options, 0, 'NATIVE');
	$path = IA_ROOT . "/addons/manor_shop/data/qrcode/" . $_W['uniacid'] . "/";
	if(!is_dir($path)) {
		load()->func('file');
		mkdirs($path);
	}
	$file = 'native_pay_qrcode_'.$openid.'.png';
	$qrcode_file = $path . $file;
	require IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
	$file_url = $_W['siteroot'].'/addons/manor_shop/data/qrcode/'.$_W['uniacid'].'/'.$file;
	QRcode::png($common['code_url'], $qrcode_file, QR_ECLEVEL_L, 15, 4);
	include $this->template('index/scanpay');