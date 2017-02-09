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
		$data = $_POST['params'];
		$params = json_decode($data, true);
		if(empty($params)) {
			return FALSE;
		}
		$value = array();
		foreach($params as $param) {
			if(!empty($param)) {
				$value[] = $param;
			}
		}
		$price = 0;
		foreach($value as $k=>$v){
			if($v['id'] == 1) {
				$price += (9.9 * intval($v['num']));
				$title += '[购买'.$v['num'].'份樱桃]';
			}
			if($v['id'] == 2) {
				$price += (1.9 * intval($v['num']));
				$title += '[购买'.$v['num'].'份圣女果]';
			}
		}
		if($price <=0) {
			return FALSE;
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
	include $this->template('index/quick_payment');