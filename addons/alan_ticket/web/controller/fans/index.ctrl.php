<?php
/**
 * 粉丝管理
 * Author：Alan
 * [TangShengMonar System]Copyright (c) 2016 fresh.tangshengmanor.com
 * Time:2016-12-19 10
 */
defined('IN_IA') or exit('Access Denied');
$pindex = max(1, intval($_GPC['page']));
$psize = 20;
$condition = ' ';
global $_W;
if($_W['isajax'] && $_W['ispost'] && $_GPC['op'] == 'add_ranking') {
    if(!$_GPC['id']) {
        die(json_encode(['status'=>-1, 'result'=>'参数错误']));
    }
    $user_info = pdo_get('ticket_user', ['id'=>$_GPC['id']]);
    if(!$user_info) {
        die(json_encode(['status'=>-1, 'result'=>'参数错误']));
    }
    if(!pdo_get('ticket_range_mock', ['user_id'=>$_GPC['id'],'is_mock'=>1])) {
        pdo_insert('ticket_range_mock', [
           'user_id'=>$user_info['id'],
            'nickname'=>$user_info['nickname'],
            'headimg'=>$user_info['headimg'],
            'wish'=>$_GPC['wish'],
            'vote_number'=>1,
            'is_mock'=>1,
            'updated_time'=>time()
        ]);
    }
    die(json_encode(['status'=>1, 'result'=>'成功']));
}
if (!empty($_GPC['keyword'])){
    $_GPC['keyword'] = trim($_GPC['keyword']);
    $condition .= " nickname LIKE '%{$_GPC['keyword']}%'";
}
if (isset($_GPC['status']) && $_GPC['status'] != ''){
    $_GPC['status'] = trim($_GPC['status']);
    if(trim($condition)) {
        $condition .= " and ismock = ".$_GPC['status'];
    } else {
        $condition .= " ismock = ".$_GPC['status'];
    }
}
if (isset($_GPC['iswish']) && $_GPC['iswish'] != ''){
    $_GPC['iswish'] = trim($_GPC['iswish']);
    if($_GPC['iswish'] == 1) {
        if(trim($condition)) {
            $condition .= " and wish != '' ";
        } else {
            $condition .= " wish != ''  ";
        }
    } else {
        if(trim($condition)) {
            $condition .= " and wish='' ";
        } else {
            $condition .= " wish ='' ";
        }
    }

}

if(trim($condition)) {
    $condition = ' where '.$condition;
}
$sql = 'select * from '.tablename('ticket_user') . $condition . ' order by created_time desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$list = pdo_fetchall($sql);
$total = pdo_fetchcolumn('select count(1) from '.tablename('ticket_user').$condition);
$pager = pagination($total, $pindex, $psize);
//渲染到后台视图
include ticket_template('fans/index');