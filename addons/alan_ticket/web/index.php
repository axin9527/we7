<?php
/**
 * 后台入口分发文件
 * Author：Alan
 * [TangShengMonar System]Copyright (c) 2016 fresh.tangshengmanor.com
 * Time:2016-12-19 10
 */
define('IN_MOBILE', true);
global $_W,$_GPC;
$controller = $_GPC['do']?$_GPC['do']:strtolower($controller);
$action = $_GPC['ac']?strtolower($_GPC['ac']):'index';
$op = $_GPC['op'] ? $_GPC['op'] : 'index';
if(empty($controller) || empty($action)) {
    $_GPC['do'] = $controller = 'sys';
    $_GPC['ac'] = $action = 'setting';
}
$file = TICKET_WEB . 'controller/'.$controller.'/'.$action.'.ctrl.php';
if (!file_exists($file)) {
    header("Location: index.php?i={$_W['uniacid']}&c=home&a=welcome&do=ext&m=alan_ticket");
    exit;
}
$config = json_decode(cache_load('alan_ticket'), true);
if(!$config) {
    message('请配置活动信息', './index.php?c=profile&a=module&do=setting&m=alan_ticket', 'error');
}
$config = json_decode($config, true);
require $file;

