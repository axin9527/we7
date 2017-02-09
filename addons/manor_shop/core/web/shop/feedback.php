<?php
 if(!defined('IN_IA')){
    exit('Access Denied');
}
global $_W, $_GPC;

ca('shop.feedback.view');
$operation = !empty($_GPC['op'])? $_GPC['op'] : 'display';
load() -> model('user');
if ($operation == 'display'){
    $pindex = max(1, intval($_GPC['page']));
    $psize = 20;
    $condition = " and uniacid=:uniacid and deleted=0";
    $params = array(':uniacid' => $_W['uniacid']);
    if (!empty($_GPC['keyword'])){
        $_GPC['keyword'] = trim($_GPC['keyword']);
        $condition .= ' and ( nickname like :keyword or mobile like :keyword)';
        $params[':keyword'] = "%{$_GPC['keyword']}%";
    }
    if (empty($starttime) || empty($endtime)){
        $starttime = strtotime('-1 month');
        $endtime = time();
    }
    if (!empty($_GPC['searchtime'])){
        $starttime = strtotime($_GPC['time']['start']);
        $endtime = strtotime($_GPC['time']['end']);
        if (!empty($timetype)){
            $condition .= " AND create_time >= :starttime AND create_time <= :endtime ";
            $params[':starttime'] = $starttime;
            $params[':endtime'] = $endtime;
        }
    }
    $list = pdo_fetchall("SELECT  * FROM " . tablename('manor_shop_feedbacks'). " WHERE 1 {$condition} ORDER BY create_time desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
    $total = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('manor_shop_feedbacks') ." WHERE 1 {$condition} ", $params);
    $pager = pagination($total, $pindex, $psize);
}elseif ($operation == 'delete'){
    ca('shop.feedback.delete');
    $id = intval($_GPC['id']);
    $item = pdo_fetch("SELECT id,content FROM " . tablename('manor_shop_feedbacks') . " WHERE id ='$id'");
    if (empty($item)){
        message('抱歉，反馈不存在或是已经被删除！', $this -> createWebUrl('shop/feedback', array('op' => 'display')), 'error');
    }
    pdo_update('manor_shop_feedbacks', array('deleted' => 1), array('id' => $id, 'uniacid' => $_W['uniacid']));
    plog('shop.feedback.delete', "删除反馈 ID: {$id} 反馈ID: {$goods['id']} 信息: {$item['content']}");
    message('删除成功！', $this -> createWebUrl('shop/feedback', array('op' => 'display')), 'success');
}
load() -> func('tpl');
include $this -> template('web/shop/feedback');