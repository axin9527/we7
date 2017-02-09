<?php
	/**
	 * 操作类名称: PhpStorm.
	 * 作者名称: alan
	 * 创建时间: 16/7/3 14:47
	 */
	global $_W;
	global $_GPC;
	$title = '';
	if($_W['isajax']) {
		$price = $_POST['price'];
		if (!check_money($price)) {
			return false;
		}
		$params = array();
		$openid    = $_W['openid'];
		$d = explode(" ", microtime());
		$orderno = date('YmdHis').substr($d[0], 2).rand(1000000000, $d[1]);
		$params['tid'] = $orderno;
		$params['user'] = $openid;
		$params['fee'] = $price;
		$params['title'] = '快捷支付-田园好鲜'.$title;
		$setting = uni_setting($_W['uniacid'], array('payment'));
		$options = $setting['payment']['wechat'];
		$options['appid'] = $_W['account']['key'];
		$options['secret'] = $_W['account']['secret'];
		$common = m('common')->wechat_build($params, $options, 0);
		header('Content-type:application/text;Charset:utf-8;');
		echo json_encode($common);die;
	}
	include $this->template('index/pricepay');

	function check_money($money) {
		if($money > 0){
			if(strpos($money,'.') === FALSE){
				$price = intval($money);
				if($price == $money){
					return TRUE;
				}
			}
			else{
				if($money > 1){
					if(preg_match("/^[1-9][\d]*\.[\d]{0,2}$/", $money)) {
						return TRUE;
					}
				}
				else {
					if(preg_match("/^0\.[\d]{0,2}$/", $money)) {
						return TRUE;
					}
				}
			}
		}
		return FALSE;
	}