<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid = m('user') -> getOpenid();
$uniacid = $_W['uniacid'];
$orderid = intval($_GPC['id']);
$diyform_plugin = p('diyform');
$trade = m('common') -> getSysset('trade');
$tradeset = m('common') -> getSysset('trade');
$refunddays = intval($tradeset['refunddays']);
$set = set_medias(m('common') -> getSysset('shop'), array('logo', 'img'));
$order = pdo_fetch('select * from ' . tablename('manor_shop_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));
$day = $trade['closeorder']? $trade['closeorder'] : 0;
$second = $day*24*3600;
$has_second =$second -  (time() - $order['createtime']);
if (!empty($order)){
    $order['virtual_str'] = str_replace('
', '<br/>', $order['virtual_str']);
    $diyformfields = "";
    if ($diyform_plugin){
        $diyformfields = ',og.diyformfields,og.diyformdata';
    }
    $goods = pdo_fetchall("select og.goodsid,og.price,og.is_give,g.title,g.thumb,og.total,g.credit,og.optionid,og.optionname as optiontitle,g.isverify,g.storeids{$diyformfields},g.express_area  from " . tablename('manor_shop_order_goods') . ' og ' . ' left join ' . tablename('manor_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.orderid=:orderid and og.uniacid=:uniacid ', array(':uniacid' => $uniacid, ':orderid' => $orderid));
    $show = 1;
    $diyform_flag = 0;
    foreach ($goods as & $g){
        $g['thumb'] = tomedia($g['thumb']);
        if ($diyform_plugin){
            $diyformdata = iunserializer($g['diyformdata']);
            $fields = iunserializer($g['diyformfields']);
            $diyformfields = array();
            foreach($fields as $key => $value){
                $tp_value = "";
                $tp_css = "";
                if($value['data_type'] == 1 || $value['data_type'] == 3){
                    $tp_css .= ' dline1';
                }
                if($value['data_type'] == 5){
                    $tp_css .= ' dline2';
                }
                if($value['data_type'] == 0 || $value['data_type'] == 1 || $value['data_type'] == 2 || $value['data_type'] == 6 || $value['data_type'] == 7){
                    $tp_value = str_replace('
', '<br/>', $diyformdata[$key]);
                }else if($value['data_type'] == 3 || $value['data_type'] == 8){
                    if(is_array($diyformdata[$key])){
                        foreach($diyformdata[$key] as $k1 => $v1){
                            $tp_value .= $v1 . ' ';
                        }
                    }
                }else if($value['data_type'] == 5){
                    if(is_array($diyformdata[$key])){
                        foreach($diyformdata[$key] as $k1 => $v1){
                            $tp_value .= '<img style=\'height:25px;padding:1px;border:1px solid #ccc\'  src=\'' . tomedia($v1) . '\'/>';
                        }
                    }
                }else if($value['data_type'] == 9){
                    $tp_value = ($diyformdata[$key]['province'] != '请选择省份'?$diyformdata[$key]['province']:'') . ' - ' . ($diyformdata[$key]['city'] != '请选择城市'?$diyformdata[$key]['city']:'');
                }
                $diyformfields[] = array('tp_name' => $value['tp_name'], 'tp_value' => $tp_value, 'tp_css' => $tp_css);
            }
            $g['diyformfields'] = $diyformfields;
            $g['diyformdata'] = $diyformdata;
            if (!empty($g['diyformdata'])){
                $diyform_flag = 1;
            }
        }else{
            $g['diyformfields'] = array();
            $g['diyformdata'] = array();
        }
        unset($g);
    }
}
if ($_W['isajax']){

    if (empty($order)){
        show_json(0);
    }

     $op=empty($_GPC['op']) ? "" :$_GPC['op'];
     if($op=='share'){
        $fission_id=intval($_GPC['fid']);
        $ordersn=$_GPC['ordersn'];
        $sharedata = pdo_fetch('select id  from ' . tablename('manor_shop_coupon_share') . ' where  uniacid=:uniacid and ordersn=:ordersn ', array(':uniacid' => $_W['uniacid'],':ordersn'=>$ordersn));
         if(empty($sharedata)){

             $fissiondata = pdo_fetch('select total,"grant",fissionname  from ' . tablename('manor_shop_coupon_fission') . ' where  uniacid=:uniacid and id=:fission_id limit 1', array(':uniacid' => $_W['uniacid'],':fission_id'=>$fission_id));
             $newshare=array('uniacid'=>$_W['uniacid'],'ordersn'=>$order['ordersn'],'fid'=>intval($fission_id),'receivenum'=>'0','surplusnum'=>intval($fissiondata['total']),'sharetime'=>time(),'fissionname'=>$fissiondata['fissionname']);
              $sharestatus=pdo_insert('manor_shop_coupon_share',$newshare);
              if($sharestatus){

                    $newfission['grant']=$fissiondata['grant']+1;
                    pdo_update('manor_shop_coupon_fission',$newfission,array('id'=>$fission_id));
              }  


         }
        
     }
     

    $order['virtual_str'] = str_replace('
', '<br/>', $order['virtual_str']);
    $order['goodstotal'] = count($goods);
    $order['finishtimevalue'] = $order['finishtime'];
    $order['finishtime'] = date('Y-m-d H:i:s', $order['finishtime']);
    $address = false;
    $carrier = false;
    $stores = array();
    if ($order['isverify'] == 1){
        $storeids = array();
        foreach ($goods as $g){
            if (!empty($g['storeids'])){
                $storeids = array_merge(explode(',', $g['storeids']), $storeids);
            }
        }
        if (empty($storeids)){
            $stores = pdo_fetchall('select * from ' . tablename('manor_shop_store') . ' where  uniacid=:uniacid and status=1', array(':uniacid' => $_W['uniacid']));
        }else{
            $stores = pdo_fetchall('select * from ' . tablename('manor_shop_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and status=1', array(':uniacid' => $_W['uniacid']));
        }
    }else{
        if ($order['dispatchtype'] == 0){
            $address = iunserializer($order['address']);
            if (!is_array($address)){
                $address = pdo_fetch('select realname,mobile,address from ' . tablename('manor_shop_member_address') . ' where id=:id limit 1', array(':id' => $order['addressid']));
            }
        }
    }
    if ($order['dispatchtype'] == 1 || $order['isverify'] == 1 || !empty($order['virtual'])){
        $carrier = unserialize($order['carrier']);
    }
    $set = set_medias(m('common') -> getSysset('shop'), 'logo');
    $canrefund = false;
    if ($order['status'] == 1 || $order['status'] == 2){
        if($tradeset['power_can_refused_unsend'] == 1) {
            $canrefund = true;
        }
    }else if ($order['status'] == 3){
        if ($order['isverify'] != 1 && empty($order['virtual'])){
            $tradeset = m('common') -> getSysset('trade');
            $refunddays = intval($tradeset['refunddays']);
            if ($refunddays > 0){
                $days = intval((time() - $order['finishtimevalue']) / 3600 / 24);
                if ($days <= $refunddays){
                    $canrefund = true;
                }
            }
        }
    }
    $order['canrefund'] = $canrefund;
    if ($canrefund == true){
        if ($order['status'] == 1){
            $order['refund_button'] = '申请退款';
        }else{
            $order['refund_button'] = '申请售后';
        }
        if (!empty($order['refundstate'])){
            $order['refund_button'] .= '中';
        }
    }
	$order['gift_good_info'] = json_decode($order['gift_good_info'], TRUE);
    $refund = pdo_fetch('SELECT * FROM ' . tablename('manor_shop_order_refund') . ' WHERE orderid = :orderid and uniacid=:uniacid order by id desc', array(':orderid' => $order['id'], ':uniacid' => $_W['uniacid']));
    show_json(1, array('order' => $order,'refund'=>$refund, 'goods' => $goods, 'address' => $address, 'carrier' => $carrier, 'stores' => $stores, 'isverify' => $isverify, 'set' => $set));
}
$url=0;
if($order['status']==1){
    //查询合适的裂变红包
    $fission = pdo_fetch('select *  from ' . tablename('manor_shop_coupon_fission') . ' where  uniacid=:uniacid and status=0 and orderprice<= :orderprice order by orderprice desc limit 1', array(':uniacid' => $_W['uniacid'],':orderprice'=>$order['price']));
    if(!empty($fission)){
        //判断红包有效期
        if(time()-$order['paytime']<=(3600 * $fission['effective'])){
            $url=1;
            $_W['shopshare'] = array(
                'title'=>'免费领取优惠券',
                'imgUrl'=>tomedia('../addons/manor_shop/template/mobile/tangsheng/static/images/ayibang/share.jpg'),
                'desc'=>'免费领取优惠券',
                'link'=>$this->createMobileUrl('activity/fission',array('ordersn'=>$order['ordersn'],'fid'=>$fission['id']))
            );
        }
    }
}

$refund = pdo_fetch('SELECT * FROM ' . tablename('manor_shop_order_refund') . ' WHERE orderid = :orderid and uniacid=:uniacid order by id desc', array(':orderid' => $order['id'], ':uniacid' => $_W['uniacid']));
include $this -> template('order/detail');
