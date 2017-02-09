<?php
	if(!defined('IN_IA')){
		exit('Access Denied');
	}
	global $_GPC, $_W;

	$shopset = m('common') -> getSysset('shop');
	$operation = !empty($_GPC['op'])? $_GPC['op'] : 'display';
	$navigation = pdo_fetchall("SELECT * FROM " . tablename('manor_shop_navigation') . " WHERE uniacid = '{$_W['uniacid']}' order by displayorder DESC");
	if ($operation == 'display'){
		ca('shop.navigation.view');
		if (!empty($_GPC['datas'])){
			ca('shop.navigation.edit');
			$datas = json_decode(html_entity_decode($_GPC['datas']), true);
			if(!is_array($datas)){
				message('首页导航保存失败，请重试!', '', 'error');
			}
			$cateids = array();
			$displayorder = count($datas);
			foreach($datas as $row){
				$cateids[] = $row['id'];
				pdo_update('manor_shop_navigation', array('displayorder' => $displayorder), array('id' => $row['id']));
				$displayorder--;
			}
			if(!empty($cateids)){
				pdo_query('delete from ' . tablename('manor_shop_navigation') . ' where id not in (' . implode(',', $cateids) . ') and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
			}
			plog('shop.navigation.edit', '批量修改首页导航的层级及排序');
			message('首页导航更新成功！', $this -> createWebUrl('shop/navigation', array('op' => 'display')), 'success');
		}
	}elseif ($operation == 'post'){
		$id = intval($_GPC['id']);
		if(!empty($id)) {
			ca('shop.activity.edit|shop.activity.view');
			$item = pdo_fetch("SELECT * FROM " . tablename('manor_shop_navigation') . " WHERE id = '$id' limit 1");
		} else {
			ca('shop.activity.add');
		}
		if(checksubmit('submit')) {
			if(empty($_GPC['thumb'])) {
				message('抱歉，请上传首页导航图标！');
			}
			$data = array('uniacid' => $_W['uniacid'], 'name' => trim($_GPC['name']), 'enabled' => intval($_GPC['enabled']), 'displayorder' => intval($_GPC['displayorder']), 'thumb' => save_media($_GPC['thumb']), 'url' => $_GPC['advurl']);
			if(!empty($id)) {
				pdo_update('manor_shop_navigation', $data, array('id' => $id));
				load()->func('file');
				file_delete($_GPC['thumb_old']);
				plog('shop.navigation.edit', "修改首页导航 ID: {$id}");
			} else {
				pdo_insert('manor_shop_navigation', $data);
				$id = pdo_insertid();
				plog('shop.navigation.add', "添加首页导航 ID: {$id}");
			}
			$cache_navigation = pdo_fetchall("SELECT id,name,url,thumb FROM " . tablename('manor_shop_navigation') . " WHERE uniacid = '{$_W['uniacid']}' and enabled=1 order by displayorder DESC");
			$_cache_navigation = set_medias($cache_navigation, 'thumb');
			m('cache')->set('cache_navigation', $_cache_navigation, $_W['uniacid']);
			message('更新首页导航成功！', $this->createWebUrl('shop/navigation', array('op' => 'display')), 'success');
		}
	}elseif ($operation == 'delete'){
		ca('shop.navigation.delete');
		$id = intval($_GPC['id']);
		$item = pdo_fetch("SELECT id, name FROM " . tablename('manor_shop_navigation') . " WHERE id = '$id'");
		if (empty($item)){
			message('抱歉，首页导航不存在或是已经被删除！', $this -> createWebUrl('shop/navigation', array('op' => 'display')), 'error');
		}
		pdo_delete('manor_shop_navigation', array('id' => $id), 'OR');
		plog('shop.navigation.delete', "删除首页导航 ID: {$id} 首页导航名称: {$item['name']}");
		$cache_navigation = pdo_fetchall("SELECT id,name,url,thumb FROM " . tablename('manor_shop_navigation') . " WHERE uniacid = '{$_W['uniacid']}' and enabled=1 order by displayorder DESC");
		$_cache_navigation = set_medias($cache_navigation, 'thumb');
		m('cache')->set('cache_navigation', $_cache_navigation, $_W['uniacid']);
		message('首页导航删除成功！', $this -> createWebUrl('shop/navigation', array('op' => 'display')), 'success');
	}
	load() -> func('tpl');
	include $this -> template('web/shop/navigation');