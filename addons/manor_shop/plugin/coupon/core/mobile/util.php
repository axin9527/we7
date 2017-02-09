<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'query';
$openid = m('user') -> getOpenid();
$_set = pdo_fetch("select * from " . tablename('manor_shop_sysset') . ' where uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
$set = unserialize($_set['plugins']);
if ($operation == 'query'){
    $type = intval($_GPC['type']);
    $money = floatval($_GPC['money']);
	$goods_id = $_GPC['goods_id'];
	$g_ids = array_filter(array_unique(explode('|', $goods_id)));
	$goods_arr = array();$coupon_ids=array();$new_coupon_ids = '';
	if($g_ids) {
		foreach($g_ids as $g_id) {
			$g_r = explode(',', $g_id);
			$goods_arr[] = $g_r[0];
		}
	}
	if($goods_arr) {
		foreach($goods_arr as $_goods_id) {
			$g_item[] = pdo_fetchcolumn('select coupon_id from '.tablename('manor_shop_coupon_goods').' where goods_id=:goods_id and uniacid=:uniacid', array(":goods_id"=>$_goods_id, ':uniacid'=>$_W['uniacid']));
		}
		$g_item_real = array_unique(array_filter($g_item));
		if($g_item_real) {
			$new_coupon_ids = rtrim(implode(',', $g_item_real), ",");
		}
		$coupon_ids = rtrim($new_coupon_ids, ",");
	}
    $time = time();
    $sql = 'select c.coupon_goods_id,d.id,d.couponid,d.gettime,c.timelimit,c.timedays,c.timestart,c.timeend,c.thumb,c.couponname,c.enough,c.backtype,c.deduct,c.discount,c.backmoney,c.backcredit,c.backredpack,c.bgcolor,c.thumb from ' . tablename('manor_shop_coupon_data') . ' d';
    $sql .= ' left join ' . tablename('manor_shop_coupon') . ' c on d.couponid = c.id';
    if (isset($_GPC['type']) && $type == 0) {
        $sql .= " where d.openid=:openid and d.uniacid=:uniacid and  c.coupontype !=1 and {$money}>=c.enough and d.used=0 ";
    } else {
        $sql .= " where d.openid=:openid and d.uniacid=:uniacid and  c.coupontype={$type} and {$money}>=c.enough and d.used=0 ";
    }
	$sql .=" and c.coupon_goods_id <=''";
	$sql .= " and (   (c.timelimit = 0 and ( c.timedays=0 or c.timedays*86400 + d.gettime >=unix_timestamp() ) )  or  (c.timelimit =1 and c.timestart<={$time} && c.timeend>={$time})) order by d.gettime desc";
    $list = set_medias(pdo_fetchall($sql, array(':openid' => $openid, ':uniacid' => $_W['uniacid'])), 'thumb');
	if($coupon_ids){
		$sql2 = str_replace(" and c.coupon_goods_id <=''", ' and c.coupon_goods_id IS NOT NULL and d.couponid in ('.$coupon_ids.')', $sql);
		$list2 = set_medias(pdo_fetchall($sql2, array(':openid' => $openid, ':uniacid' => $_W['uniacid'])), 'thumb');
		if($list2) {
			foreach($list2 as $item) {
				array_push($list, $item);
			}
		}
	}

	foreach($list as $key=> &$row){
        $row['thumb'] = tomedia($row['thumb']);
        $row['timestr'] = '永久有效';
        if(empty($row['timelimit'])){
            if(!empty($row['timedays'])){
                $row['timestr'] = date('Y-m-d H:i', $row['gettime'] + $row['timedays'] * 86400);
            }
        }else{
            if($row['timestart'] >= $time){
                $row['timestr'] = date('Y-m-d H:i', $row['timestart']) . '-' . date('Y-m-d H:i', $row['timeend']);
            }else{
                $row['timestr'] = date('Y-m-d H:i', $row['timeend']);
            }
        }
        if($row['backtype'] == 0){
            $row['backstr'] = '立减';
            $row['css'] = 'deduct';
            $row['backmoney'] = $row['deduct'];
            $row['backpre'] = true;
        }else if($row['backtype'] == 1){
            $row['backstr'] = '折';
            $row['css'] = 'discount';
            $row['backmoney'] = $row['discount'];
        }else if($row['backtype'] == 2){
            if($row['backredpack'] > 0){
                $row['backstr'] = '返现';
                $row['css'] = 'redpack';
                $row['backmoney'] = $row['backredpack'];
                $row['backpre'] = true;
            }else if($row['backmoney'] > 0){
                $row['backstr'] = '返利';
                $row['css'] = 'money';
                $row['backmoney'] = $row['backmoney'];
                $row['backpre'] = true;
            }else if (!empty($row['backcredit'])){
                $row['backstr'] = '返积分';
                $row['css'] = 'credit';
                $row['backmoney'] = $row['backcredit'];
            }
        }
    }
    unset($row);
	sort($list);
    show_json(1, array('coupons' => $list));
}
elseif($operation == 'exchange') {
    //兑换码领取优惠劵
    $code = trim($_GPC['code']);
    if(!$code) {
        show_json(-1, '请输入正确的劵码');
    }
    $codeinfo = pdo_get('manor_shop_coupon_gen', array('code'=>$code, 'uniacid'=>$_W['uniacid']));
    if(!$codeinfo) {
        show_json(-1, '请输入正确的劵码');
    }

    if($codeinfo['is_used'] == 1) {
        show_json(-1, '该劵码无效');
    }
    //更新劵码
    $openid = m('user') -> getOpenid();
    $member = m('member') -> getMember($openid);
    if($_GPC['type'] == 'success') {
        $is_used = 1;
    } else if($_GPC['type'] == 'error'){
        $is_used = 0;
        $member = array();
    } else {
        $is_used = 2;
    }
    pdo_update('manor_shop_coupon_gen', array('is_used'=>$is_used, 'used_time'=>time(),'user_id'=>$member['uid'], 'nickname'=>$member['nickname'], 'avastar'=>$member['avatar']), array('id'=>$codeinfo['id']));
    //更新数量
    $this->model->check_td_coupon($code, $openid);
    show_json(1, $codeinfo['coupon_id']);
}
else if($operation == 'get_tips') {
    $price = intval($_GPC['price']);
    //获得购物返券提示
    if($set['enoughs_coupon_power'] != 1) {
        show_json(-1);
    }
    //满100 送卷
    $coupon_id = '';
    $coupon_amount = 0;
    foreach($set['enoughs_coupon'] as $key=>$item) {
        if($price > $item) {
            $coupon_id = $key;
            $coupon_amount = $item;
            break;
        }
    }
    show_json(1, array('coupon_id'=>$coupon_id, 'coupon_amount'=>$coupon_amount));
}
elseif($operation == 'get_haier'){
    $enoughs_coupon = array();
    if($_W['ispost']) {
        if(!$_GPC['coupon_id']) {
            show_json(-1, '参数错误');
        }

        $re =  receive_coupon(intval($_GPC['coupon_id']));
        if(!is_array($re)) {
            show_json(-1, $re);
        }
        show_json(1, $re);
    }
    $_set = unserialize($_set['sets']);
    $shop_set = $_set['shop'];
    if($shop_set) {
        if($shop_set['haier_power'] == 1 && $shop_set['haier_coupon']) {
            $ids = array_filter(array_unique($shop_set['haier_coupon']));
            if($ids) {
                foreach($ids as $k=>$item) {
                    $enoughs_coupon[$k] = pdo_get('manor_shop_coupon', array('id'=>$item));
                }
            }
        }
    }
    show_json(1, set_medias(array_values($enoughs_coupon), 'thumb'));
}
elseif($operation == 'shuangshiyi') {

} else if($operation == 'send_coupon') {
    $coupon_id = $_GPC['coupon_id'];
    $is_group = $_GPC['is_group'] ? $_GPC['is_group'] : false;
    if(!$coupon_id) {
        show_json(-1, '无效的优惠劵');
    }
    if($is_group) {
        $cate = pdo_get('manor_shop_coupon_category', array('id'=>$coupon_id));
        if($cate > 0) {
            $w = array(':cate_id'=>$coupon_id, ':uniacid'=>$_W['uniacid'], ':openid'=>$openid);
            $get_num = pdo_fetchcolumn('select count(*) from '.tablename('manor_shop_coupon_category_record').' where  cate_id=:cate_id and uniacid=:uniacid and openid=:openid', $w);
            if($get_num >= $cate['get_max'] ) {
                show_json(-1, '您已经领取过了');
            }
        }
    }
    $res = $this -> model ->send_coupon($coupon_id, array($openid), $is_group);
    if(!is_array($res)) {
        show_json(-1, $res);
    } elseif($res) {
        if($is_group) {
            $member = m('member') -> getMember($openid);
            $coupon_category_record = array(
                'uniacid'=>$_W['uniacid'],
                'cate_id'=>$coupon_id,
                'openid'=>$openid,
                'nickname'=>$member['nickname'],
                'userdata'=>iserializer($member),
                'cate_data'=>iserializer($cate),
                'catename'=>$cate['name'],
                'createtime'=>TIMESTAMP
            );
            pdo_insert('manor_shop_coupon_category_record', $coupon_category_record);
        }
        show_json(1, '兑换成功');
    } else {
        show_json(-1, '兑换失败,请稍后重新兑换');
    }
}



function receive_coupon($coupon_id) {
    global $_W;
    $coupon = pdo_get('manor_shop_coupon', array('id'=>$coupon_id, 'uniacid'=>$_W['uniacid']));
    if(!$coupon) {
        return '优惠劵不存在';
    }
    load()->func('communication');
    $url = $_W['siteurl'].'&m=manor_shop&p=coupon&method=detail&do=plugin';
    $result = ihttp_post($url, array('op'=>'pay', 'id'=>$coupon['id']));
    $result_data = json_decode($result['content'], true);
    if($result_data['status'] == -1) {
        return $result_data['result'];
    }
    $_ob = ihttp_post($url, array('op'=>'payresult', 'id'=>$coupon['id'], 'logid'=>$result_data['result']['logid']));
    $_ob_cont = json_decode($_ob['content'], true);
    if($_ob_cont['status'] == 1) {
        if($_ob_cont['result']['coupontype'] == 0) {
            return $coupon;
        }
    }
    return  $_ob_cont['result'];
}


