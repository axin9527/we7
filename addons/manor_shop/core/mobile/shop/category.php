<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op'])? $_GPC['op'] : 'index';
$hot_search = m('common') -> getSysset('shop');
$hot_search = array_filter(array_unique(explode("\n", $hot_search['search'])));
$openid = m('user') -> getOpenid();
$uniacid = $_W['uniacid'];
$shopset = set_medias(m('common') -> getSysset('shop'), 'catadvimg');
$commission = p('commission');
if($commission){
    $shopid = intval($_GPC['shopid']);
    $shop = set_medias($commission -> getShop($openid), array('img', 'logo'));
}
$shopset = m('common') -> getSysset('shop');
include $this -> template('shop/category');