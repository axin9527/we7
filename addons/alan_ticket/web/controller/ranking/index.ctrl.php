<?php
/**
 * 粉丝管理
 * Author：Alan
 * [TangShengMonar System]Copyright (c) 2016 fresh.tangshengmanor.com
 * Time:2016-12-19 10
 */
defined('IN_IA') or exit('Access Denied');
//wl_load()->func('vote');
global $_W,$_GPC;
require '../addons/alan_ticket/core/function/vote.func.php';
if($_GPC['op'] == 'op_ticket' && $_W['isajax']) {
    $user_id = intval($_GPC['user_id']);
    if(!$user_id) {
        echo json_encode(array('status'=>-1, 'result'=>'参数错误'));die;
    }
    if(!$_GPC['num'] || !is_numeric($_GPC['num']) || $_GPC['num'] < 0) {
        echo json_encode(array('status'=>-1, 'result'=>'请输入正确的数字'));die;
    }
    $user = pdo_get('ticket_user', array('id'=>$user_id));
    $user_info = pdo_fetch('select id from '.tablename('ticket_range_mock').' where user_id=:user_id', array(':user_id'=>$user_id));
    if(!$user_info) {
        $ret = pdo_insert('ticket_range_mock', array(
            "vote_number" => $_GPC['num'],
            "user_id"     => $user["id"],
            "headimg"     => $user["headimg"],
            "nickname"    => $user["nickname"],
            "wish"        => $user["wish"]));
    } else {
        $ret = pdo_update('ticket_range_mock', array('vote_number +='=>$_GPC['num']), array('user_id'=>$user_id));
    }
    if($ret) {
        echo json_encode(array('status'=>1, 'result'=>'修改成功'));die;
    } else {
        echo json_encode(array('status'=>-1, 'result'=>'修改失败'));die;
    }
} elseif($_GPC['op'] == 'shield_ticket' && $_W['isajax']) {
    $id = intval($_GPC['id']);
    $isshield = intval($_GPC['isshield']) == 1 ? 0 : 1;
    if(!$id) {
        echo json_encode(array('status'=>-1, 'result'=>'参数错误'));die;
    }
    $user = pdo_update('ticket_range', array('isshield'=>$isshield), array('user_id'=>$id));
    $user = pdo_update('ticket_range_mock', array('isshield'=>$isshield), array('user_id'=>$id));
    echo json_encode(array('status'=>1, 'result'=>'修改成功'));die;
} elseif($_GPC['op'] == 'edit_ticket' && $_W['isajax']) {
    $uid = intval($_GPC['user_id']);
    $dram = trim($_GPC['dram']);
    if(!$uid) {
        echo json_encode(array('status'=>-1, 'result'=>'参数错误'));die;
    }
    $user = pdo_update('ticket_user', array('wish'=>$dram), array('id'=>$uid));
    $user = pdo_update('ticket_range', array('wish'=>$dram), array('user_id'=>$uid));
    $user = pdo_update('ticket_range_mock', array('wish'=>$dram), array('user_id'=>$uid));
    echo json_encode(array('status'=>1, 'result'=>'修改成功'));die;
}
$list = vote_range_all_back();
$sql = "select * from ims_ticket_range order by vote_number desc limit 50";
$result1 = pdo_fetchall($sql);
//渲染到后台视图
include ticket_template('ranking/index');