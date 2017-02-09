<?php
 if(!defined('IN_IA')){
    exit('Access Denied');
}
global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display'){
    ca('coupon.category.view');
    if (!empty($_GPC['catname'])){
        ca('coupon.category.edit|coupon.category.add');
        foreach ($_GPC['catid'] as $k => $v){
            $data = array('name' => trim($_GPC['catname'][$k]), 'displayorder' => $_GPC['displayorder'][$k], 'status' => intval($_GPC['status'][$k]), 'uniacid' => $_W['uniacid']);
            if(empty($v)){
                ca('coupon.category.add');
                pdo_insert('manor_shop_coupon_category', $data);
                $insert_id = pdo_insertid();
                plog('coupon.category.add', "添加分类 ID: {$insert_id}");
            }else{
                pdo_update('manor_shop_coupon_category', $data, array('id' => $v));
                plog('coupon.category.edit', "修改分类 ID: {$v}");
            }
        }
        plog('coupon.category.edit', '批量修改分类');
        message('分类更新成功！', $this -> createPluginWebUrl('coupon/category', array('op' => 'display')), 'success');
    }
    $condition = ' uniacid = '.$_W['uniacid'];
    if($_GPC['keyword']) {
        $condition .= ' and name like %'.trim($_GPC['keyword']).'%';
    }
    $sql = 'SELECT * FROM ' . tablename('manor_shop_coupon_category') . " WHERE $condition ORDER BY displayorder DESC";
    $list = pdo_fetchall($sql);
    if($list) {
        foreach($list as $key=>$item) {
            $list[$key]['coupon'] = pdo_getall('manor_shop_coupon', array('uniacid'=>$_W['uniacid'], 'catid'=>$item['id']));
        }
    }
}elseif ($operation == 'delete'){
    ca('coupon.category.delete');
    $id = intval($_GPC['id']);
    $item = pdo_fetch('SELECT id,name,rid FROM ' . tablename('manor_shop_coupon_category') . " WHERE id = '$id' AND uniacid=" . $_W['uniacid'] . "");
    if (empty($item)){
        message('抱歉，分类不存在或是已经被删除！', $this -> createPluginWebUrl('coupon/category', array('op' => 'display')), 'error');
    }
    pdo_delete('manor_shop_coupon_category', array('id' => $id));
    plog('coupon.category.delete', "删除分类 ID: {$id} 标题: {$item['name']} ");
    if($item['rid']) {
        pdo_delete('rule', array('id' => $item['rid']));
        pdo_delete('rule_keyword', array('uniacid'=>$_W['uniacid'],'rid' => $item['rid']));
        pdo_delete('cover_reply', array('uniacid'=>$_W['uniacid'],'rid' => $item['rid']));
    }
    pdo_delete('manor_shop_coupon_category', array('id' => $id));
    message('分类删除成功！', $this -> createPluginWebUrl('coupon/category', array('op' => 'display')), 'success');
} else if($operation == 'add') {
    $id = $_GPC['id'];
    if($id) {
        $item = pdo_get('manor_shop_coupon_category', array('id'=>$id));
    }
    if(checksubmit()) {
        $sub_url = trim($_GPC['sub_url']) ? trim($_GPC['sub_url']) : '';
        if($id) {
            $sub_url = $this->createPluginMobileUrl('coupon/group', array('id'=>$id));
        }
        $cate_data = array(
            'status'=>$_GPC['status'],
            'displayorder'=>$_GPC['displayorder'],
            'name'=>$_GPC['name'],
            'thumb'=>$_GPC['thumb'],
            'sub_title'=>$_GPC['sub_title'],
            'sub_thumb'=>$_GPC['sub_thumb'],
            'sub_desc'=>htmlspecialchars($_GPC['sub_desc']),
            'sub_url'=>$sub_url,
            'keyword'=>$_GPC['keyword'],
            'get_max'=>intval($_GPC['get_max'])
        );
        $coverkeyword = $cate_data['keyword'];
        if (!empty($coverkeyword)){
            $rule = pdo_fetch('select * from ' . tablename('rule') . ' where uniacid=:uniacid and module=:module and name=:name limit 1', array(':uniacid' => $_W['uniacid'], ':module' => 'cover', ':name' => 'manor_shop优惠组入口设置'));
            if (!empty($rule)){
                $keyword = pdo_fetch('select * from ' . tablename('rule_keyword') . ' where uniacid=:uniacid and rid=:rid limit 1', array(':uniacid' => $_W['uniacid'], ':rid' => $rule['id']));
                $cover = pdo_fetch('select * from ' . tablename('cover_reply') . ' where uniacid=:uniacid and rid=:rid limit 1', array(':uniacid' => $_W['uniacid'], ':rid' => $rule['id']));
            }
            $kw = pdo_fetch('select * from ' . tablename('rule_keyword') . ' where uniacid=:uniacid and content=:content and id<>:id limit 1', array(':uniacid' => $_W['uniacid'], ':content' => trim($coverkeyword), ':id' => $keyword['id']));
            if (!empty($kw)){
                message("关键词 {$coverkeyword} 已经存在!", '', 'error');
            }
            $status = $cate_data['status'];
            $rule_data = array('uniacid' => $_W['uniacid'], 'name' => 'manor_shop优惠组入口设置', 'module' => 'cover', 'displayorder' => 0, 'status' => $status);
            if (empty($rule)){
                pdo_insert('rule', $rule_data);
                $rid = pdo_insertid();
            }else{
                pdo_update('rule', $rule_data, array('id' => $rule['id']));
                $rid = $rule['id'];
            }
            $keyword_data = array('uniacid' => $_W['uniacid'], 'rid' => $rid, 'module' => 'cover', 'content' => trim($coverkeyword), 'type' => 1, 'displayorder' => 0, 'status' => $status);
            if (empty($keyword)){
                pdo_insert('rule_keyword', $keyword_data);
            }else{
                pdo_update('rule_keyword', $keyword_data, array('id' => $keyword['id']));
            }
            $cover_data = array('uniacid' => $_W['uniacid'], 'rid' => $rid, 'module' => $this -> modulename, 'title' => trim($cate_data['sub_title']), 'description' => trim($cate_data['sub_desc']), 'thumb' => tomedia($cate_data['sub_thumb']), 'url' => $sub_url);
            if (empty($cover)){
                pdo_insert('cover_reply', $cover_data);
                $cover_id = pdo_insertid();
            }else{
                pdo_update('cover_reply', $cover_data, array('id' => $cover['id']));
                $cover_id = $cover['id'];
            }
        }
        $cate_data['rid'] = $rid;
        if(!$id) {
            $cate_data['create_time'] = TIMESTAMP;
            $cate_data['uniacid'] = $_W['uniacid'];
        }
        if(!$cate_data['name']) {
            message('请输入组名称', '', 'error');
        }
        if(!$id) {
            $r = pdo_insert('manor_shop_coupon_category', $cate_data);
            $c_id =  pdo_insertid();
            $sub_url = $this->createPluginMobileUrl('coupon/group', array('id'=>$c_id));
            pdo_update('cover_reply', array('url'=>$sub_url), array('id'=>$cover_id));
        } else {
            $r = pdo_update('manor_shop_coupon_category', $cate_data, array('id'=>$id));
            if($sub_url) {
                pdo_update('cover_reply', array('url'=>$sub_url), array('id'=>$cover_id));
            }
        }

        message('修改成功',$this -> createPluginWebUrl('coupon/category', array('op' => 'display')), 'success');
    }
} elseif($operation == 'query') {
    $condition = ' uniacid = '.$_W['uniacid'];
    if($_GPC['keyword']) {
        $condition .= ' and name like %'.trim($_GPC['keyword']).'%';
    }
    $sql = 'SELECT * FROM ' . tablename('manor_shop_coupon_category') . " WHERE $condition";
    $list = pdo_fetchall($sql);
    if($list) {
        foreach($list as $key=>$item) {
            $list[$key]['coupon'] = pdo_getall('manor_shop_coupon', array('uniacid'=>$_W['uniacid'], 'catid'=>$item['id']));
        }
    }
    include $this -> template('coupon/group');die;
}
load() -> func('tpl');
include $this -> template('category');
