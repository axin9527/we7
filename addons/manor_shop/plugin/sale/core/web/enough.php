<?php
 global $_W, $_GPC;

ca('sale.enough.view');
$set = $this -> getSet();
if (checksubmit('submit')){
    ca('sale.enough.save');
    $data = is_array($_GPC['data']) ? $_GPC['data'] : array();
    $set['enoughfree'] = intval($data['enoughfree']);
    $set['enoughorder'] = round(floatval($data['enoughorder']) , 2);
    $set['enoughareas'] = $data['enoughareas'];
    $set['enoughmoney'] = round(floatval($data['enoughmoney']) , 2);
    $set['enoughdeduct'] = round(floatval($data['enoughdeduct']) , 2);
    $enoughs =$enoughs_goods =array();
    $postenoughs = is_array($_GPC['enough']) ? $_GPC['enough'] : array();
	$kk = $vv = $kk_g = array();
    foreach($postenoughs as $key => $value){
        $enough = floatval($value);
        if($enough > 0){
        	$kk[] = floatval($_GPC['enough'][$key]);
        	$vv[] = floatval($_GPC['give'][$key]);
	        if( floatval($_GPC['give'][$key]) > floatval($_GPC['enough'][$key])) {
		        message('满的金额不能大于减的金额!', referer(), 'error');
	        }
            $enoughs[] = array('enough' => floatval($_GPC['enough'][$key]), 'give' => floatval($_GPC['give'][$key]));
        }
    }
	$postenoughs = is_array($_GPC['enough_good']) ? $_GPC['enough_good'] : array();
    foreach($postenoughs as $key => $value){
        $enough = floatval($value);
        if($enough > 0){
        	$kk_g[] = floatval($_GPC['enough_good'][$key]);
	        $enoughs_goods[] = array('enough_good' => floatval($_GPC['enough_good'][$key]), 'gift_good' => floatval($_GPC['gift_good'][$key]));
        }
    }
    $_enough_coupon = is_array($_GPC['enough_coupon_money']) ? $_GPC['enough_coupon_money'] : array();
    $enoughs_coupon = array();
    if($_enough_coupon) {
        foreach($_enough_coupon as $k=>$item) {
            $enoughs_coupon[$item] = $_GPC['enough_coupon_id'][$k];
        }
    }
	if(count(array_unique($kk)) != count($enoughs) || count(array_unique($kk)) != count($enoughs)|| count(array_unique($kk_g)) != count($enoughs_goods)) {
		message('请确认设置的数值不能重复!', referer(), 'error');
		die;
	}
    $set['enoughs'] = $enoughs;
    $set['enoughs_goods'] = $enoughs_goods;
    $set['enoughs_reduce_power'] = intval($data['enoughs_reduce_power']);
    $set['enoughs_goods_power'] = intval($data['enoughs_goods_power']);
    $set['enoughs_coupon'] = $enoughs_coupon;
    $set['enoughs_coupon_power'] = intval($data['enoughs_coupon_power']);
    $this -> updateSet($set);
    plog('sale.enough.save', '修改满额优惠');
    message('满额优惠设置成功!', referer(), 'success');
}
$areas = m('cache') -> getArray('areas', 'global');
if(!is_array($areas)){
    require_once manor_shop_INC . 'json/xml2json.php';
    $file = IA_ROOT . '/addons/manor_shop/static/js/dist/area/Area.xml';
    $content = file_get_contents($file);
    $json = xml2json :: transformXmlStringToJson($content);
    $areas = json_decode($json, true);
    m('cache') -> set('areas', $areas, 'global');
}
load() -> func('tpl');
	$sql = "SELECT id,title,thumb,marketprice,productprice,sales,total,description,score FROM " . tablename('manor_shop_goods');
	$list = pdo_fetchall($sql);
	$goods = json_encode($list);
    $coupon = pdo_getall('manor_shop_coupon', array('coupontype'=>3, 'uniacid'=>$_W['uniacid']), array('id', 'couponname'));
    $_coupon = json_encode($coupon);
	$decode_goods = $list;
include $this -> template('enough');
