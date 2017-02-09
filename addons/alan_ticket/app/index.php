<?php
/**
 * 前台入口分发文件
 * Author：Alan
 * [TangShengMonar System]Copyright (c) 2016 fresh.tangshengmanor.com
 * Time:2016-12-19 14
 */
define('IN_MOBILE', true);
global $_W,$_GPC;
if($_W['uniacid'] != 8) {
    header("Location: index.php?i=8&c=entry&do=Apply&ac=index&m=alan_ticket");die;
}
$finish_time = strtotime('2017-01-16 00:00:00');
if($_GPC['do'] != strtolower('Ranking') && time() > $finish_time) {
    //header("Location: index.php?i=8&c=entry&do=Ranking&m=alan_ticket");die;
}
$controller = $_GPC['do']?strtolower($_GPC['do']):$controller;
$action = $_GPC['ac']?strtolower($_GPC['ac']):'index';
if (empty($controller) || empty($action)) {
    $_GPC['do'] = $controller = 'Apply';
    $_GPC['ac'] = $action = 'index';
}
wl_load()->model('member');
checkMember();
$config = json_decode(cache_load('alan_ticket'), true);
if(empty($config) || $config['power'] !=1) {
    die('活动结束');
}
$file = TICKET_APP . 'controller/'.$controller.'/'.$action.'.ctrl.php';
if (!file_exists($file)) {
    header("Location: index.php?i={$_W['uniacid']}&c=entry&do=Apply&ac=index&m=alan_ticket");
    exit;
}
require $file;