<?php
	if (!defined('IN_IA')){
		exit('Access Denied');
	}
	global $_W, $_GPC;
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
	$uniacid = $_W['uniacid'];
	if ($_W['isajax']){
		if ($operation == 'index'){
			$pagesize = 2;
			$page = $_GPC['page'] ? intval($_GPC['page']) : 1;
			$live_video = pdo_fetchall('select id,name,thumb,url,view,comment,description from ' . tablename('manor_shop_live_video') . ' where uniacid=:uniacid and enabled=1 order by displayorder desc LIMIT ' .($page - 1) * $pagesize . ',' . $pagesize, array(':uniacid' => $uniacid));
			$live_video = set_medias($live_video, 'thumb');
			show_json(1, array('list'=>$live_video, 'pagesize'=>'6'));
		}elseif ($operation == 'detail'){
			$id = intval($_GPC['id']);
			$live_video = pdo_fetch('select id,name,thumb,url,view,comment,description from ' . tablename('manor_shop_live_video') . ' where uniacid=:uniacid and enabled=1 and id=:id order by displayorder desc', array(':uniacid' => $uniacid, ':id'=>$id));
			$live_video = set_medias($live_video, 'thumb');
			pdo_update('manor_shop_live_video', array('view'=>intval($live_video['view'])+1), array('id'=>$id));
			show_json(1, array('item'=>$live_video));
		}
	}
	include $this -> template('shop/live');
