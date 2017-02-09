<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
global $_W, $_GPC;
$op = $_GPC['op'] ? $_GPC['op'] : 'display';
$type = intval($_GPC['type']);
$id = intval($_GPC['groupid']);
if (!empty($id)){
    $coupon = pdo_fetch('SELECT * FROM ' . tablename('manor_shop_coupon_category') . ' WHERE id=:id and uniacid=:uniacid ', array(':id' => $id, ':uniacid' => $_W['uniacid']));
    if (empty($coupon)){
        message('未找到优惠劵组!', '', 'error');
    }
}
if($op == 'record') {
    ca('coupon.category.grouprecord');
    $pindex = max(1, intval($_GPC['page']));
    $psize = 20;
    $condition = ' uniacid = :uniacid';
    $params = array(':uniacid' => $_W['uniacid']);
    if (!empty($_GPC['keyword'])){
        $_GPC['keyword'] = trim($_GPC['keyword']);
        $condition .= ' AND userdata LIKE :nickname';
        $params[':nickname'] = '%' . trim($_GPC['keyword']) . '%';
    }
    if (empty($starttime) || empty($endtime)){
        $starttime = strtotime('-1 month');
        $endtime = time();
    }
    if (!empty($_GPC['searchtime'])){
        $starttime = strtotime($_GPC['time']['start']);
        $endtime = strtotime($_GPC['time']['end']);
        if ($_GPC['searchtime'] == '1'){
            $condition .= ' AND createtime >= :starttime AND createtime <= :endtime ';
            $params[':starttime'] = $starttime;
            $params[':endtime'] = $endtime;
        }
    }
    $sql = 'SELECT * FROM ' . tablename('manor_shop_coupon_category_record') . ' ' . " where  1 and {$condition} ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
    $list = pdo_fetchall($sql, $params);
    $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('manor_shop_coupon_category_record') . " where 1 and {$condition}", $params);
    $pager = pagination($total, $pindex, $psize);
    include $this -> template('grouprecord');
} elseif($op == 'display') {
    if (checksubmit('submit')) {
        ca('coupon.category.sendgroup');
        $class1 = intval($_GPC['send1']);
        $send_total = intval($_GPC['send_total']);
        $send_total <= 0 && $send_total = 1;
        $plog = '';
        if ($class1 == 1) {
            $openids = explode(",", trim($_GPC['send_openid']));
            $plog = "发放优惠劵组 ID: {$id} 方式: 指定 OPENID 人数: " . count($openids);
        } elseif ($class1 == 2) {
            $where = '';
            if (!empty($_GPC['send_level'])) {
                $where .= " and level =" . intval($_GPC['send_level']);
            }
            $members = pdo_fetchall("SELECT openid FROM " . tablename('manor_shop_member') . " WHERE uniacid = '{$_W['uniacid']}'" . $where, array(), 'openid');
            if (!empty($value1)) {
                $levelname = pdo_fetchcolumn('select levelname from ' . tablename('manor_shop_member_level') . ' where id=:id limit 1', array(':id' => $value1));
            } else {
                $levelname = "全部";
            }
            $openids = array_keys($members);
            $plog = "发放优惠劵组 ID: {$id} 方式: 等级-{$levelname} 人数: " . count($member);
        } elseif ($class1 == 3) {
            $where = '';
            if (!empty($_GPC['send_group'])) {
                $where .= " and groupid =" . intval($_GPC['send_group']);
            }
            $members = pdo_fetchall("SELECT openid FROM " . tablename('manor_shop_member') . " WHERE uniacid = '{$_W['uniacid']}'" . $where, array(), 'openid');
            if (!empty($_GPC['send_group'])) {
                $groupname = pdo_fetchcolumn('select groupname from ' . tablename('manor_shop_member_group') . ' where id=:id limit 1', array(':id' => $value1));
            } else {
                $groupname = "全部分组";
            }
            $openids = array_keys($members);
            $plog = "发放优惠劵组 ID: {$id}  方式: 分组-{$groupname} 人数: " . count($member);
        } elseif ($class1 == 4) {
            $members = pdo_fetchall("SELECT openid FROM " . tablename('manor_shop_member') . " WHERE uniacid = '{$_W['uniacid']}'" . $where, array(), 'openid');
            $openids = array_keys($members);
            $plog = "发放优惠劵组 ID: {$id}  方式: 全部会员  分组:{$groupname} 人数: " . count($member);
        } elseif ($class1 == 5) {
            $where = '';
            if (!empty($_GPC['send_agentlevel'])) {
                $where .= " and agentlevel =" . intval($_GPC['send_agentlevel']);
            }
            $members = pdo_fetchall("SELECT openid FROM " . tablename('manor_shop_member') . " WHERE uniacid = '{$_W['uniacid']}' and isagent=1 and status=1 " . $where, array(), 'openid');
            if ($value1 != '') {
                $levelname = pdo_fetchcolumn('select levelname from ' . tablename('manor_shop_commission_level') . ' where id=:id limit 1', array(':id' => $value1));
            } else {
                $levelname = "全部";
            }
            $openids = array_keys($members);
            $plog = "发放优惠劵组 ID: {$id}  方式: 分销商-{$levelname} 人数: " . count($member);
        }
        if (empty($openids)) {
            message('未找到发送的会员!', '', 'error');
        }
        $upgrade = array('resptitle' => trim($_GPC['send_title']), 'respthumb' => save_media($_GPC['send_thumb']), 'respdesc' => trim($_GPC['send_desc']), 'respurl' => trim($_GPC['send_url']),);
        if (!$upgrade['resptitle']) {
            $upgrade = array();
        }
        //send_coupon ($id, $open_ids=array(), $is_group=false, $send_total=1, $send_message_template = array()) {
        $result = $this->model->send_coupon($id, $openids, true, $send_total, $upgrade);
        if (is_array($result)) {
            message('优惠券发放成功!', $this->createPluginWebUrl('coupon/category'), 'success');
        } else {
            message($result, $this->createPluginWebUrl('coupon/category'), 'error');
        }
    }
    $list = pdo_fetchall("SELECT * FROM " . tablename('manor_shop_member_level') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY level asc");
    $list2 = pdo_fetchall("SELECT * FROM " . tablename('manor_shop_member_group') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY id asc");
    $list3 = pdo_fetchall("SELECT * FROM " . tablename('manor_shop_commission_level') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY id asc");
    load()->func('tpl');
    include $this -> template('sendgroup');
}
