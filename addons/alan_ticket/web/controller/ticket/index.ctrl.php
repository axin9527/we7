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
if (!empty($_GPC['keyword'])){
    $_GPC['keyword'] = trim($_GPC['keyword']);
    $condition .= " u.nickname LIKE '%{$_GPC['keyword']}%'";
}
if(trim($condition)) {
    $condition = ' where '.$condition;
}
$sql = 'select u.*,v.vote_time,v.from_id,v.id as vid,v.to_id from '.tablename('ticket_vote').' as v left join '.tablename('ticket_user'). ' as u on v.from_id=u.id' . $condition . ' order by v.vote_time desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$list = pdo_fetchall($sql);
if($list) {
    foreach ($list as &$item) {
        $to = pdo_fetch('select nickname, headimg from '. tablename('ticket_user').' where id = :id', array(':id'=>$item['to_id']));
        $item['to_nickname'] = $to['nickname'];
        $item['to_headimg'] = $to['headimg'];
    }
}
$total = count(pdo_fetchall('select u.*,v.vote_time,v.from_id,v.id as vid,v.to_id from '.tablename('ticket_vote').' as v left join '.tablename('ticket_user'). ' as u on v.from_id=u.id' . $condition . ' order by v.vote_time desc'));

$pager = pagination($total, $pindex, $psize);
//渲染到后台视图
include ticket_template('ticket/index');