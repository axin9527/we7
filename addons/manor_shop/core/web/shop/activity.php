<?php
 if(!defined('IN_IA')){
    exit('Access Denied');
}
global $_GPC, $_W;

$shopset = m('common') -> getSysset('shop');
$operation = !empty($_GPC['op'])? $_GPC['op'] : 'display';
$children = array();
$activity = pdo_fetchall("SELECT * FROM " . tablename('manor_shop_activity') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder DESC");
$pages = pdo_fetchall('SELECT id,pagename,pagetype,setdefault FROM ' . tablename('manor_shop_designer') . ' WHERE uniacid= :uniacid  ', array(':uniacid' => $_W['uniacid']));
$categorys = pdo_fetchall('SELECT id,name,parentid FROM ' . tablename('manor_shop_category') . ' WHERE enabled=:enabled and uniacid= :uniacid  ', array(':uniacid' => $_W['uniacid'], ':enabled' => '1'));
foreach ($activity as $index => $row){
    if (!empty($row['parentid'])){
        $children[$row['parentid']][] = $row;
        unset($activity[$index]);
    }
}
$act_group_list = pdo_getall('manor_shop_activity_group', array('uniacid'=>$_W['uniacid']));
if ($operation == 'display'){
    ca('shop.activity.view');
    if (!empty($_GPC['datas'])){
        ca('shop.activity.edit');
        $datas = json_decode(html_entity_decode($_GPC['datas']), true);
        if(!is_array($datas)){
            message('活动保存失败，请重试!', '', 'error');
        }
        $cateids = array();
        $displayorder = count($datas);
        foreach($datas as $row){
            $cateids[] = $row['id'];
            pdo_update('manor_shop_activity', array('parentid' => 0, 'displayorder' => $displayorder, 'level' => 1), array('id' => $row['id']));
            if($row['children'] && is_array($row['children'])){
                $displayorder_child = count($row['children']);
                foreach($row['children'] as $child){
                    $cateids[] = $child['id'];
                    pdo_query('update ' . tablename('manor_shop_activity') . ' set  parentid=:parentid,displayorder=:displayorder,level=2 where id=:id', array(':displayorder' => $displayorder_child, ":parentid" => $row['id'], ":id" => $child['id']));
                    $displayorder_child--;
                    if($child['children'] && is_array($child['children'])){
                        $displayorder_third = count($child['children']);
                        foreach($child['children'] as $third){
                            $cateids[] = $third['id'];
                            pdo_query('update ' . tablename('manor_shop_activity') . ' set  parentid=:parentid,displayorder=:displayorder,level=3 where id=:id', array(':displayorder' => $displayorder_third, ":parentid" => $child['id'], ":id" => $third['id']));
                            $displayorder_third--;
                        }
                    }
                }
            }
            $displayorder--;
        }
        if(!empty($cateids)){
            pdo_query('delete from ' . tablename('manor_shop_activity') . ' where id not in (' . implode(',', $cateids) . ') and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
        }
        plog('shop.activity.edit', '批量修改活动的层级及排序');
        message('活动更新成功！', $this -> createWebUrl('shop/activity', array('op' => 'display')), 'success');
    }
}
elseif ($operation == 'post'){
    $parentid = intval($_GPC['parentid']);
    $id = intval($_GPC['id']);
    if (!empty($id)){
        ca('shop.activity.edit|shop.activity.view');
        $item = pdo_fetch("SELECT * FROM " . tablename('manor_shop_activity') . " WHERE id = '$id' limit 1");
        $parentid = $item['parentid'];
        if($item['act_group']) {
            $item['act_group'] = pdo_fetchcolumn('select name from '. tablename('manor_shop_activity_group').' where id=:id and uniacid=:uniacid', array(':id'=>$item['act_group'], ':uniacid'=>$_W['uniacid']));
        }

    }else{
        ca('shop.activity.add');
        $item = array('displayorder' => 0,);
    }
    $item['act_goods'] = json_decode($item['act_goods'],true);
    $item['tui_goods'] = array_unique(array_filter(explode(',', $item['tui_goods'])));
    if (!empty($parentid)){
        $parent = pdo_fetch("SELECT id, parentid, name FROM " . tablename('manor_shop_activity') . " WHERE id = '$parentid' limit 1");
        if (empty($parent)){
            message('抱歉，所属活动不存在或是已经被删除！', $this -> createWebUrl('post'), 'error');
        }
        if(!empty($parent['parentid'])){
            $parent1 = pdo_fetch("SELECT id, name FROM " . tablename('manor_shop_activity') . " WHERE id = '{$parent['parentid']}' limit 1");
        }
    }
    if(empty($parent)){
        $level = 1;
    }else{
        if(empty($parent['parentid'])){
            $level = 2;
        }else{
            $level = 3;
        }
    }
    if($item['is_mention'] == 1) {
        $mention = 2;
    } else {
        $mention = 1;
    }
    $goods = pdo_getall('manor_shop_goods', array('uniacid'=>$_W['uniacid'], 'total >'=>0, 'deleted'=>0, 'status'=>1, 'isverify'=>$mention), array('id', 'title'));
    $_goods = json_encode($goods);
    $act_goods = json_encode($item['act_goods']);
    if (checksubmit('submit')){
        if (empty($_GPC['catename'])){
            message('抱歉，请输入活动名称！');
        }
        /* if (empty($_GPC['advimg'])){
            message('抱歉，请上传活动广告图！');
        }*/
        $data = array(
            'uniacid'        => $_W['uniacid'],
            'name'           => trim($_GPC['catename']),
            'enabled'        => intval($_GPC['enabled']),
            'displayorder'   => intval($_GPC['displayorder']),
            'isrecommand'    => intval($_GPC['isrecommand']),
            'ishome'         => intval($_GPC['ishome']),
            'description'    => $_GPC['description'],
            'parentid'       => intval($parentid),
            'thumb'          => save_media($_GPC['thumb']),
            'advimg'         => save_media($_GPC['advimg']),
            'advurl'         => $_GPC['advurl'], 'level' => $level,
            'content'        => htmlspecialchars($_GPC['content']),
            'subtitle'       => $_GPC['subtitle'],
            'background_img' => $_GPC['background_img'],
            'is_tui'        => intval($_GPC['is_tui']),
            'is_activity'        => intval($_GPC['is_activity']),
            'is_mention'    => intval($_GPC['is_mention']),
            'goods_sort_type'    => intval($_GPC['goods_sort_type'])
        );
        if($data['is_mention'] == 1) {
            unset($data['is_tui']);
        }
        if($_GPC['act_goods']) {
            $act_goods = $_GPC['act_goods'];
            $goods_img = $_GPC['goods_img'];
            if($act_goods) {
                $act_goods_arr = array();
                foreach ($act_goods as $k=>$act_god) {
                    $act_real_goods = pdo_get('manor_shop_goods', array('id'=>$act_god));
                    $act_goods_arr[$act_god] = array(
                        'id'=>$act_god,
                        'index_img'=>$goods_img[$k],
                        'goods_img'=>$act_real_goods['thumb'],
                        'title'=>$act_real_goods['title'],
                        'old_price'=>$act_real_goods['productprice'],
                        'price'=>$act_real_goods['marketprice'],
                    );
                }
                $data['act_goods'] = json_encode($act_goods_arr);
            }
        }
        if($_GPC['act_group']) {
            $act_group = rtrim($_GPC['act_group']);
            if($act_group) {
                $act_group_info = pdo_get('manor_shop_activity_group', array('uniacid'=>$_W['uniacid'], 'name'=>$act_group));
                if($act_group_info) {
                    $data['act_group'] = $act_group_info['id'];
                } else {
                    $group_data = array(
                        'uniacid'=>$_W['uniacid'],
                        'name'=>$act_group,
                        'create_time'=>TIMESTAMP
                    );
                    pdo_insert('manor_shop_activity_group', $group_data);
                    $act_group_id = pdo_insertid();
                    $data['act_group'] = $act_group_id;
                }
            }
        }
        if($_GPC['is_tui'] == 1 && $_GPC['tui_goods']) {
            $act_goods = rtrim(implode(',', array_filter(array_unique($_GPC['tui_goods']))), ",");
            if($act_goods) {
                $data['tui_goods'] = $act_goods;
            }
        }
	    if (!empty($id)){
            unset($data['parentid']);
            pdo_update('manor_shop_activity', $data, array('id' => $id));
            load() -> func('file');
            file_delete($_GPC['thumb_old']);
            plog('shop.activity.edit', "修改活动 ID: {$id}");
        }else{
            pdo_insert('manor_shop_activity', $data);
            $id = pdo_insertid();
            plog('shop.activity.add', "添加活动 ID: {$id}");
        }
        message('更新活动成功！', $this -> createWebUrl('shop/activity', array('op' => 'display')), 'success');
    }
}
elseif ($operation == 'delete'){
    ca('shop.activity.delete');
    $id = intval($_GPC['id']);
    $item = pdo_fetch("SELECT id, name, parentid FROM " . tablename('manor_shop_activity') . " WHERE id = '$id'");
    if (empty($item)){
        message('抱歉，活动不存在或是已经被删除！', $this -> createWebUrl('shop/activity', array('op' => 'display')), 'error');
    }
    pdo_delete('manor_shop_activity', array('id' => $id, 'parentid' => $id), 'OR');
    plog('shop.activity.delete', "删除活动 ID: {$id} 活动名称: {$item['name']}");
    message('活动删除成功！', $this -> createWebUrl('shop/activity', array('op' => 'display')), 'success');
}
elseif ($operation == 'util') {
    if($_W['isajax']) {
        $is_mention = $_GPC['is_mention'];
        if($is_mention == 1) {
            $goods = pdo_getall('manor_shop_goods', array('uniacid'=>$_W['uniacid'], 'total >'=>0, 'deleted'=>0, 'status'=>1, 'isverify'=>2), array('id', 'title'));
        } else {
            $goods = pdo_getall('manor_shop_goods', array('uniacid'=>$_W['uniacid'], 'total >'=>0, 'deleted'=>0, 'status'=>1, 'isverify'=>1), array('id', 'title'));
        }
        if(!$goods) {
            show_json(-1, '没有自提商品');
        };
        show_json(1, $goods);
    }
}
load() -> func('tpl');
include $this -> template('web/shop/activity');