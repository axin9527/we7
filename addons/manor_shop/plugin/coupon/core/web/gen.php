<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
global $_W, $_GPC;

ca('coupon.log.view');
$pindex = max(1, intval($_GPC['page']));
$psize = 20;
$condition = ' uniacid = :uniacid and coupon_id=:coupon_id';
$params = array(':uniacid' => $_W['uniacid'], ':coupon_id'=>intval($_GPC['coupon']));
if (!empty($_GPC['nickname'])){
    $_GPC['nickname'] = trim($_GPC['nickname']);
    $condition .= ' and ( nickname like :nickname ) ';
    $params[':nickname'] = "%{$_GPC['nickname']}%";
}
if (empty($starttime) || empty($endtime)){
    $starttime = strtotime('-1 month');
    $endtime = time();
}
if (empty($starttime1) || empty($endtime1)){
    $starttime1 = strtotime('-1 month');
    $endtime1 = time();
}
if (!empty($_GPC['searchtime'])){
    $starttime = strtotime($_GPC['time']['start']);
    $endtime = strtotime($_GPC['time']['end']);
    if ($_GPC['searchtime'] == '1'){
        $condition .= ' AND get_time >= :starttime AND get_time <= :endtime ';
        $params[':starttime'] = $starttime;
        $params[':endtime'] = $endtime;
    }
}
if (!empty($_GPC['searchtime1'])){
    $starttime1 = strtotime($_GPC['time1']['start']);
    $endtime1 = strtotime($_GPC['time1']['end']);
    if ($_GPC['searchtime'] == '1'){
        $condition .= ' AND used_time >= :starttime1 AND used_time <= :endtime1 ';
        $params[':starttime1'] = $starttime1;
        $params[':endtime1'] = $endtime1;
    }
}

if ($_GPC['is_used'] != ''){
    $condition .= ' AND is_used =' . intval($_GPC['is_used']);
}
$sql = 'SELECT * FROM ' . tablename('manor_shop_coupon_gen') ." where  1 and {$condition} ORDER BY get_time DESC";
if(empty($_GPC['export'])){
    $sql .= ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
}
$list = pdo_fetchall($sql, $params);
    foreach($list as & $row){
        if(!empty($row['used_time'])){
            $row['used_time'] = date('Y-m-d H:i', $row['used_time']);
        }else{
            $row['used_time'] = '---';
        }
        /* if(!empty($row['get_time'])){
            $row['get_time'] = date('Y-m-d H:i', $row['get_time']);
        }else{
            $row['get_time'] = '---';
        }*/
        if($row['is_used'] == 1) {
            $row['is_used'] = '已兑换';
        } else if($row['is_used'] == 2){
            $row['is_used'] = '兑换失败';
        }else{
            $row['is_used'] = '未兑换';
        }
    }
    unset($row);
if($_GPC['export'] == 1){
    ca('coupon.log.export');
    $columns = array(
        array('title' => '劵码号', 'field' => 'code', 'width' => 12),
        array('title' => '所属优惠券', 'field' => 'coupon_name', 'width' => 24),
        array('title' => '是否兑换', 'field' => 'is_used', 'width' => 12),
        array('title' => '微信昵称', 'field' => 'nickname', 'width' => 12),
//        array('title' => '获取时间', 'field' => 'get_time', 'width' => 12),
        array('title' => '使用时间', 'field' => 'used_time', 'width' => 12),
    );
    m('excel') -> export($list, array('title' => '优惠券劵码数据-' . date('Y-m-d-H-i', time()), 'columns' => $columns));
}
$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('manor_shop_coupon_gen')."where 1 and {$condition}", $params);
$pager = pagination($total, $pindex, $psize);
load() -> func('tpl');
include $this -> template('gen');
