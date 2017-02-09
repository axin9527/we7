<?php
	if(!defined('IN_IA')){
		exit('Access Denied');
	}
	global $_GPC, $_W;

	$shopset = m('common') -> getSysset('shop');
	$operation = !empty($_GPC['op'])? $_GPC['op'] : 'display';
	$live_video = pdo_fetchall("SELECT * FROM " . tablename('manor_shop_live_video') . " WHERE uniacid = '{$_W['uniacid']}' order by displayorder DESC");
	if ($operation == 'display'){
		ca('shop.live_video.view');
		if (!empty($_GPC['datas'])){
			ca('shop.live_video.edit');
			$datas = json_decode(html_entity_decode($_GPC['datas']), true);
			if(!is_array($datas)){
				message('直播视频保存失败，请重试!', '', 'error');
			}
			$cateids = array();
			$displayorder = count($datas);
			foreach($datas as $row){
				$cateids[] = $row['id'];
				pdo_update('manor_shop_live_video', array('displayorder' => $displayorder), array('id' => $row['id']));
				$displayorder--;
			}
			if(!empty($cateids)){
				pdo_query('delete from ' . tablename('manor_shop_live_video') . ' where id not in (' . implode(',', $cateids) . ') and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
			}
			plog('shop.live_video.edit', '批量修改直播视频的层级及排序');
			message('直播视频更新成功！', $this -> createWebUrl('shop/live_video', array('op' => 'display')), 'success');
		}
	}elseif ($operation == 'post'){
		$id = intval($_GPC['id']);
		if(!empty($id)) {
			ca('shop.activity.edit|shop.activity.view');
			$item = pdo_fetch("SELECT * FROM " . tablename('manor_shop_live_video') . " WHERE id = '$id' limit 1");
		} else {
			ca('shop.activity.add');
		}
		if(checksubmit('submit')) {
			if(empty($_GPC['name'])) {
				message('抱歉，请输入直播视频名称！');
			}
			$data = array(
				'uniacid' => $_W['uniacid'],
				'name' => trim($_GPC['name']),
				'enabled' => intval($_GPC['enabled']),
				'displayorder' => intval($_GPC['displayorder']),
				'thumb' => save_media($_GPC['thumb']),
				'url' => $_GPC['advurl'],
				'update_time' => time(),
				'description' => htmlspecialchars($_GPC['description'])
			);
			if(!empty($id)) {
				pdo_update('manor_shop_live_video', $data, array('id' => $id));
				load()->func('file');
				file_delete($_GPC['thumb_old']);
				plog('shop.live_video.edit', "修改直播视频 ID: {$id}");
			} else {
				pdo_insert('manor_shop_live_video', $data);
				$id = pdo_insertid();
				plog('shop.live_video.add', "添加直播视频 ID: {$id}");
			}
			$cache_live_video = pdo_fetchall("SELECT id,name,url,thumb FROM " . tablename('manor_shop_live_video') . " WHERE uniacid = '{$_W['uniacid']}' and enabled=1 order by displayorder DESC");
			$_cache_live_video = set_medias($cache_live_video, 'thumb');
			m('cache')->set('cache_live_video', $_cache_live_video, $_W['uniacid']);
			message('更新直播视频成功！', $this->createWebUrl('shop/live_video', array('op' => 'display')), 'success');
		}
	}elseif ($operation == 'delete'){
		ca('shop.live_video.delete');
		$id = intval($_GPC['id']);
		$item = pdo_fetch("SELECT id, name FROM " . tablename('manor_shop_live_video') . " WHERE id = '$id'");
		if (empty($item)){
			message('抱歉，直播视频不存在或是已经被删除！', $this -> createWebUrl('shop/live_video', array('op' => 'display')), 'error');
		}
		pdo_delete('manor_shop_live_video', array('id' => $id), 'OR');
		plog('shop.live_video.delete', "删除直播视频 ID: {$id} 直播视频名称: {$item['name']}");
		$cache_live_video = pdo_fetchall("SELECT id,name,url,thumb FROM " . tablename('manor_shop_live_video') . " WHERE uniacid = '{$_W['uniacid']}' and enabled=1 order by displayorder DESC");
		$_cache_live_video = set_medias($cache_live_video, 'thumb');
		m('cache')->set('cache_live_video', $_cache_live_video, $_W['uniacid']);
		message('直播视频删除成功！', $this -> createWebUrl('shop/live_video', array('op' => 'display')), 'success');
	}
	load() -> func('tpl');
	include $this -> template('web/shop/live_video');