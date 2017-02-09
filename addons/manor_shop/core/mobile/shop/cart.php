<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op'])? $_GPC['op'] : 'display';
$openid = m('user') -> getOpenid();
$uniacid = $_W['uniacid'];
if ($_W['isajax']){
    if ($operation == 'display'){
        $condition = ' and f.uniacid= :uniacid and f.openid=:openid and f.deleted=0';
        $params = array(':uniacid' => $uniacid, ':openid' => $openid);
        $list = array();
        $total = $real_total = 0;
        $totalprice = 0;
        $sql = 'SELECT f.id,f.total,f.goodsid,g.total as stock, o.stock as optionstock, g.maxbuy,g.title,g.thumb,ifnull(o.marketprice, g.marketprice) as marketprice,g.productprice,o.title as optiontitle,f.optionid,o.specs,g.status FROM ' . tablename('manor_shop_member_cart') . ' f ' . ' left join ' . tablename('manor_shop_goods') . ' g on f.goodsid = g.id ' . ' left join ' . tablename('manor_shop_goods_option') . ' o on f.optionid = o.id ' . ' where 1 ' . $condition . ' ORDER BY `id` DESC ';
        $list = pdo_fetchall($sql, $params);
        $goods_id=array_column($list,'goodsid');
        foreach ($list as & $r){
            if (!empty($r['optionid'])){
                $r['stock'] = $r['optionstock'];
            }
	        if($r['status'] != 0) {
		        $totalprice += $r['marketprice'] * $r['total'];
	        }
	        if($r['status'] != 0) {
		        $real_total += $r['total'];
	        }
	        $total += $r['total'];
        }
        unset($r);
        $list = set_medias($list, 'thumb');
        $free=pdo_fetch('select plugins  from ' . tablename('manor_shop_sysset') . ' where  uniacid=:uniacid ', array(':uniacid' => $_W['uniacid']));
        $free=unserialize($free['plugins']);
        $enoughorder=$free['sale']['enoughorder'];
        $sumprice=$totalprice; 
        //查询用户的优惠券
        $coupon=coupon(0,$sumprice,$goods_id,$_W,$openid);
        if(!empty($coupon)){

            $newarr=optimal($coupon);

        }else{
            $newarr="";
        }
        $totalprice = number_format($totalprice, 2);

        show_json(1, array('total' => $total, 'list' => $list, 'totalprice' => $totalprice,'real_total'=>$real_total,'sumprice'=>$sumprice,'enoughorder'=>$enoughorder,'coupon'=>$newarr));
    }else if($operation=='upcoupon'){
        $sumprice=$_GPC['totalprice'];
        $goods_id=$_GPC['goodsid'];
        $coupon=coupon(0,$sumprice,$goods_id,$_W,$openid);
        if(empty($coupon)){
            show_json(0);
        }
        $coupons=optimal($coupon);

        if($coupons==""){
            show_json(0);
        }else{
            show_json(1,$coupons);
        }
    }else if ($operation == 'add' && $_W['ispost']){
        $id = intval($_GPC['id']);
        $total = intval($_GPC['total']);
        empty($total) && $total = 1;
        $optionid = intval($_GPC['optionid']);
        $goods = pdo_fetch('select id,marketprice from ' . tablename('manor_shop_goods') . ' where uniacid=:uniacid and id=:id limit 1', array(':uniacid' => $uniacid, ':id' => $id));
        if (empty($goods)){
            show_json(0, '商品未找到');
        }
        $diyform_plugin = p('diyform');
        $datafields = "id,total";
        if ($diyform_plugin){
            $datafields .= ",diyformdataid";
        }
        $data = pdo_fetch("select {$datafields} from " . tablename('manor_shop_member_cart') . ' where openid=:openid and goodsid=:id and  optionid=:optionid and deleted=0 and  uniacid=:uniacid   limit 1', array(':uniacid' => $uniacid, ':openid' => $openid, ':optionid' => $optionid, ':id' => $id));
        $diyformdataid = 0;
        $diyformfields = iserializer(array());
        $diyformdata = iserializer(array());
        if ($diyform_plugin){
            $diyformdata = $_GPC['diyformdata'];
            if (!empty($diyformdata) && is_array($diyformdata)){
                $diyformid = intval($diyformdata['diyformid']);
                $diydata = $diyformdata['diydata'];
                if(!empty($diyformid) && is_array($diydata)){
                    $formInfo = $diyform_plugin -> getDiyformInfo($diyformid);
                    if (!empty($formInfo)){
                        $diyformfields = $formInfo['fields'];
                        $insert_data = $diyform_plugin -> getInsertData($diyformfields, $diydata);
                        $idata = $insert_data['data'];
                        $diyformdata = $idata;
                        $diyformfields = iserializer($diyformfields);;
                    }
                }
            }
        }
        $cartcount = pdo_fetchcolumn('select sum(total) from ' . tablename('manor_shop_member_cart') . ' where openid=:openid and deleted=0 and uniacid=:uniacid limit 1', array(':uniacid' => $uniacid, ':openid' => $openid));
        if (empty($data)){
            $data = array('uniacid' => $uniacid, 'openid' => $openid, 'goodsid' => $id, 'optionid' => $optionid, 'marketprice' => $goods['marketprice'], 'total' => $total, 'diyformid' => $diyformid, 'diyformdata' => $diyformdata, 'diyformfields' => $diyformfields, 'createtime' => time());
            pdo_insert('manor_shop_member_cart', $data);
            $cartcount += $total;
            show_json(1, array('message' => '添加成功', 'cartcount' => $cartcount));
        }else{
	        $data['total'] = $data['total'] + intval($_GPC['total']);
            $data['diyformdataid'] = $diyformdataid;
            $data['diyformdata'] = $diyformdata;
            $data['diyformfields'] = $diyformfields;
            pdo_update('manor_shop_member_cart', $data, array('id' => $data['id']));
	        show_json(1, array('message' => '添加成功', 'cartcount' => $data['total']));
        }
        show_json(1, array('message' => '已在购物车', 'cartcount' => $cartcount));
    }else if ($operation == 'selectoption'){
        $id = intval($_GPC['id']);
        $goodsid = intval($_GPC['goodsid']);
        $cartdata = pdo_fetch("SELECT id,optionid,total FROM " . tablename('manor_shop_member_cart') . " WHERE id = :id and uniacid=:uniacid and openid=:openid limit 1", array(':id' => $id, ':uniacid' => $uniacid, ':openid' => $openid));
        $cartoption = pdo_fetch("select id,title,thumb,marketprice,productprice,costprice, stock,weight,specs from " . tablename('manor_shop_goods_option') . " " . " where uniacid=:uniacid and goodsid=:goodsid and id=:id limit 1 ", array(':id' => $cartdata['optionid'], ':uniacid' => $uniacid, ':goodsid' => $goodsid));
        $cartoption = set_medias($cartoption, 'thumb');
        $cartspecs = explode('_', $cartoption['specs']);
        $goods = pdo_fetch("SELECT id,title,thumb,total,marketprice FROM " . tablename('manor_shop_goods') . " WHERE id = :id", array(':id' => $goodsid));
        $goods = set_medias($goods, 'thumb');
        $allspecs = pdo_fetchall("select * from " . tablename('manor_shop_goods_spec') . " where goodsid=:id order by displayorder asc", array(':id' => $goodsid));
        foreach ($allspecs as & $s){
            $s['items'] = pdo_fetchall("select * from " . tablename('manor_shop_goods_spec_item') . " where  `show`=1 and specid=:specid order by displayorder asc", array(":specid" => $s['id']));
        }
        unset($s);
        $options = pdo_fetchall("select id,title,thumb,marketprice,productprice,costprice, stock,weight,specs from " . tablename('manor_shop_goods_option') . " where goodsid=:id order by id asc", array(':id' => $goodsid));
        $options = set_medias($options, 'thumb');
        $specs = array();
        if (count($options) > 0){
            $specitemids = explode("_", $options[0]['specs']);
            foreach ($specitemids as $itemid){
                foreach ($allspecs as $ss){
                    $items = $ss['items'];
                    foreach ($items as $it){
                        if ($it['id'] == $itemid){
                            $specs[] = $ss;
                            break;
                        }
                    }
                }
            }
        }
        show_json(1, array('cartdata' => $cartdata, 'cartoption' => $cartoption, 'cartspecs' => $cartspecs, 'goods' => $goods, 'options' => $options, 'specs' => $specs));
    }else if ($operation == 'setoption' && $_W['ispost']){
        $id = intval($_GPC['id']);
        $goodsid = intval($_GPC['goodsid']);
        $optionid = intval($_GPC['optionid']);
        $option = pdo_fetch("select id,title,thumb,marketprice,productprice,costprice, stock,weight,specs from " . tablename('manor_shop_goods_option') . " " . " where uniacid=:uniacid and goodsid=:goodsid and id=:id limit 1 ", array(':id' => $optionid, ':uniacid' => $uniacid, ':goodsid' => $goodsid));
        $option = set_medias($option, 'thumb');
        if (empty($option)){
            show_json(0, '规格未找到');
        }
        pdo_update('manor_shop_member_cart', array('optionid' => $optionid), array('id' => $id, 'uniacid' => $uniacid, 'goodsid' => $goodsid));
        show_json(1, array('optionid' => $optionid, 'optiontitle' => $option['title']));
    }else if ($operation == 'updatenum' && $_W['ispost']){
        $id = intval($_GPC['id']);
        $goodsid = intval($_GPC['goodsid']);
        $total = intval($_GPC['total']);
        empty($total) && $total = 1;
        $data = pdo_fetchall("select id,total from " . tablename('manor_shop_member_cart') . " " . " where id=:id and uniacid=:uniacid and goodsid=:goodsid  and openid=:openid limit 1 ", array(':id' => $id, ':uniacid' => $uniacid, ':goodsid' => $goodsid, ':openid' => $openid));
        if (empty($data)){
            show_json(0, '购物车数据未找到');
        }
        pdo_update('manor_shop_member_cart', array('total' => $total), array('id' => $id, 'uniacid' => $uniacid, 'goodsid' => $goodsid));
        show_json(1);
    }else if ($operation == 'tofavorite' && $_W['ispost']){
        $ids = $_GPC['ids'];
        if (empty($ids) || !is_array($ids)){
            show_json(0, '参数错误');
        }
        foreach ($ids as $id){
            $goodsid = pdo_fetchcolumn('select goodsid from ' . tablename('manor_shop_member_cart') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1 ', array(':id' => $id, ':uniacid' => $uniacid, ':openid' => $openid));
            if (!empty($goodsid)){
                $fav = pdo_fetchcolumn('select count(*) from ' . tablename('manor_shop_member_favorite') . ' where goodsid=:goodsid and uniacid=:uniacid and openid=:openid and deleted=0 limit 1 ', array(':goodsid' => $goodsid, ':uniacid' => $uniacid, ':openid' => $openid));
                if ($fav <= 0){
                    $fav = array('uniacid' => $uniacid, 'goodsid' => $goodsid, 'openid' => $openid, 'deleted' => 0, 'createtime' => time());
                    pdo_insert('manor_shop_member_favorite', $fav);
                }
            }
        }
        $sql = "update " . tablename('manor_shop_member_cart') . ' set deleted=1 where uniacid=:uniacid and openid=:openid and id in (' . implode(',', $ids) . ')';
        pdo_query($sql, array(':uniacid' => $uniacid, ':openid' => $openid));
        show_json(1);
    }else if ($operation == 'remove' && $_W['ispost']){
        $ids = $_GPC['ids'];
        if (empty($ids) || !is_array($ids)){
            show_json(0, '参数错误');
        }
        $sql = "update " . tablename('manor_shop_member_cart') . ' set deleted=1 where uniacid=:uniacid and openid=:openid and id in (' . implode(',', $ids) . ')';
        pdo_query($sql, array(':uniacid' => $uniacid, ':openid' => $openid));
        show_json(1);
    }
}
//查询最优优惠券
function optimal($coupon){
        foreach($coupon as $key=>$val){
            if($val['coupon_goods_id']!=""){
                if($val['deduct']>=0 && $val['enough']<=0){
                    $deducts[$key]=$val['deduct'];
                }else{
                    $enough[$key]=$val['deduct'];
                }

            }else if($val['deduct']>=0 && $val['enough']<=0){
                $newdeduct[$key]=$val['deduct'];
            }else if($val['enough']>0){
                $newenough[$key]=$val['deduct'];
            }

        }

        if(!empty($deducts)){

            $coupons=couponname($deducts,$coupon);

        }else if(!empty($enough)){
            $coupons=couponname($enough,$coupon);

        }else if(!empty($newduct)){

            $coupons=couponname($newduct,$coupon);

        }else if(!empty($newenough)){

            $coupons=couponname($newenough,$coupon);

        }else{
            $coupons="";
        }
        return $coupons;
}
function couponname($deducts,$coupon){
    $id=array_search(max($deducts),$deducts);
    $coupons['couponname']=$coupon[$id]['couponname'];
    $coupons['deduct']=$coupon[$id]['deduct'];
    $coupons['coupondataid']=$coupon[$id]['id'];
    return $coupons;
}
 function coupon($type,$money,$goods_id,$_W,$openid){
    $goods_id=implode('|',$goods_id);
    $type = intval($type);
    $money = floatval($money);
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
    if (isset($type) && $type == 0) {
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
    return $list;
   } 
include $this -> template('shop/cart');