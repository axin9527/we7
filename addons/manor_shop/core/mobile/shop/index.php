<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$openid = m('user') -> getOpenid();
$uniacid = $_W['uniacid'];
$designer = p('designer');
if ($designer){
    $pagedata = $designer -> getPage();
    if ($pagedata){
        extract($pagedata);
        $guide = $designer -> getGuide($system, $pageinfo);
        $_W['shopshare'] = array('title' => $share['title'], 'imgUrl' => $share['imgUrl'], 'desc' => $share['desc'], 'link' => $this -> createMobileUrl('shop'));
        if (p('commission')){
            $set = p('commission') -> getSet();
            if (!empty($set['level'])){
                $member = m('member') -> getMember($openid);
                if (!empty($member) && $member['status'] == 1 && $member['isagent'] == 1){
                    $_W['shopshare']['link'] = $this -> createMobileUrl('shop', array('mid' => $member['id']));
                    if (empty($set['become_reg']) && (empty($member['realname']) || empty($member['mobile']))){
                        $trigger = true;
                    }
                }else if (!empty($_GPC['mid'])){
                    $_W['shopshare']['link'] = $this -> createMobileUrl('shop', array('mid' => $_GPC['mid']));
                }
            }
        }
        include $this -> template('shop/index_diy');
        exit;
    }
}
$set = set_medias(m('common') -> getSysset('shop'), array('logo', 'img'));
$hot_search = m('common') -> getSysset('shop');
$hot_search = array_filter(array_unique(explode("\n", $hot_search['search'])));
if ($_W['isajax']){
    if ($operation == 'index'){
        $advs = pdo_fetchall('select id,advname,link,thumb from ' . tablename('manor_shop_adv') . ' where uniacid=:uniacid and enabled=1 order by displayorder desc', array(':uniacid' => $uniacid));
        $advs = set_medias($advs, 'thumb');
        $category = pdo_fetchall('select id,name,thumb,parentid,level from ' . tablename('manor_shop_category') . ' where uniacid=:uniacid and ishome=1 and enabled=1 order by displayorder desc', array(':uniacid' => $uniacid));
        $category = set_medias($category, 'thumb');
        foreach($category as & $c){
            $c['thumb'] = tomedia($c['thumb']);
            if($c['level'] == 3){
                $c['url'] = $this -> createMobileUrl('shop/list', array('tcate' => $c['id']));
            }else if($c['level'] == 2){
                $c['url'] = $this -> createMobileUrl('shop/list', array('ccate' => $c['id']));
            }
        }
        unset($c);
	    //首页活动推荐,添加的活动必须是首页推荐的,并且是显示的活动,排序规则按照先活动推荐,排序大到小的优先级
	    $sql = "SELECT id,advimg,name,advurl,act_group,subtitle,act_goods,is_activity,thumb,goods_sort_type FROM " . tablename('manor_shop_activity') . " where `ishome`=1 and `enabled`=1 and uniacid=:uniacid ORDER BY isrecommand desc,displayorder desc";
	    $activity = pdo_fetchall($sql, array(':uniacid'=>$uniacid));
        if($activity) {
            foreach($activity as $k=>$act) {
                if($act['act_group']) {
                    $activity[$k]['act_group'] = pdo_fetchcolumn('select name from '. tablename('manor_shop_activity_group').' where id=:id and uniacid=:uniacid', array(':id'=>$act['act_group'], ':uniacid'=>$_W['uniacid']));
                } else {
                    $activity[$k]['act_group'] = '';
                }
                $activity[$k]['act_goods'] = json_decode($act['act_goods'], true);
                if($activity[$k]['act_goods']) {
                    $activity[$k]['act_goods'] = array_map(function ($val){
                        $val['index_img'] =  tomedia($val['index_img']);
                        $val['goods_img'] =  tomedia($val['goods_img']);
                        $val['thumb'] =  tomedia($val['thumb']);
                        $_good = pdo_get('manor_shop_goods', array('id'=>$val['id']));
                        $val['thumb'] =  tomedia($_good['thumb']);
                        $val['old_price'] =  $_good['productprice'];
                        $val['price'] =  $_good['marketprice'];
                        $val['title'] =  $_good['title'];
                        return $val;
                    }, $activity[$k]['act_goods'] );
                }
            }
        }

	    $activity = set_medias($activity, 'advimg');
	    $nav_cache = m('cache')->get('cache_navigation', $uniacid);
	    if($nav_cache && is_array($nav_cache, true)) {
	    	$navigation = $nav_cache;
	    } else {
		    $navigation = pdo_fetchall("SELECT id,name,url,thumb FROM " . tablename('manor_shop_navigation') . " WHERE uniacid = '{$_W['uniacid']}' and enabled=1 order by displayorder DESC");
		    $navigation = set_medias($navigation, 'thumb');
		    m('cache')->set('cache_navigation', $navigation, $uniacid);
	    }
        show_json(1, array('set' => $set, 'advs' => $advs, 'category' => $category, 'activity'=>$activity, 'navigation'=>$navigation));
    }else if ($operation == 'goods'){
	    //筛选出定位城市单独配送的商品数据
        $type = $_GPC['type'];
	    $city = rtrim(trim($_GPC['city']), "区市");
        $args = array('page' => $_GPC['page'], 'pagesize' => 6, 'isrecommand' => 1, 'order' => 'displayorder desc,createtime desc', 'by' => '', 'city'=>$city);
        $goods = m('goods') -> getList($args);
	    if(count($goods) < 6) {
	    	//获取全国配送的商品数据
		    unset($args['city']);
		    $goods_for_country = m('goods') -> getList($args);
	    } else {
		    $goods_for_country = array();
	    }
        show_json(1, array('goods' => array_merge($goods, $goods_for_country), 'pagesize' => $args['pagesize']));
    }
}
$this -> setHeader();
include $this -> template('shop/index');
