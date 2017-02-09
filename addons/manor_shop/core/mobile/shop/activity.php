<?php
	if(!defined('IN_IA')) {
		exit('Access Denied');
	}
	//活动
	global $_W, $_GPC;
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
	$openid = m('user')->getOpenid();
	$uniacid = $_W['uniacid'];
	$shopset = set_medias(m('common')->getSysset('shop'), 'catadvimg');
	$commission = p('commission');
	if($commission) {
		$shopid = intval($_GPC['shopid']);
		$shop = set_medias($commission->getShop($openid), array('img', 'logo'));
	}
	//活动列表
	if($operation == 'index') {
		if($_W['isajax']) {
			$ishome = $_GPC['ishome'] >=0 ? $_GPC['ishome'] : '';
			$enabled = $_GPC['ishome'] >=0 ? $_GPC['enabled'] : '';
			$where = array('uniacid'=>$uniacid);
			if($ishome !== '' && $ishome != null) {
				$where['ishome'] = in_array($ishome, array(0, 1)) ? $ishome : 1;
			}
			if($enabled !== '' && $enabled  != null) {
				$where['enabled'] = in_array($enabled, array(0, 1)) ? $enabled : 1;
			}
			$activity = pdo_getall('manor_shop_activity', $where, array('id', 'name', 'advimg', 'advurl', 'content'));
			if($activity) {
				foreach($activity as $key=>$item) {
					$activity[$key]['advimg'] = set_medias($item, array('advimg'));
				}
			}
			show_json(1, array('activity'=>$activity, 'shop'=>$shop));
		}
	}elseif($operation == 'detail') {
		if($_W['isajax']) {
			//活动详情
			$id = $_GPC['id'];
			if(!$id) {
				message('抱歉，活动不存在！');
			}
			if($_W['isajax']) {
				$info = pdo_get('manor_shop_activity', array('uniacid' => $uniacid, 'id' => $id, 'enabled' => 1));
				if(!$info) {
					message('抱歉,活动不存在');
				}
				set_medias($info, array('advimg'));
				$info['content'] = htmlspecialchars_decode(htmlspecialchars_decode($info['content']));
				show_json(1, $info);
			}
		}
	}
	include $this->template('shop/activity');