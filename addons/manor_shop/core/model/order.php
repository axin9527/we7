<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
class Ewei_DShop_Order{
    function getDispatchPrice($dephp_0, $dephp_1, $dephp_2 = -1){
        if (empty($dephp_1)){
            return 0;
        }
        $dephp_3 = 0;
        if ($dephp_2 == -1){
            $dephp_2 = $dephp_1['calculatetype'];
        }
        if ($dephp_2 == 1){
            if ($dephp_0 <= $dephp_1['firstnum']){
                $dephp_3 = floatval($dephp_1['firstnumprice']);
            }else{
                $dephp_3 = floatval($dephp_1['firstnumprice']);
                $dephp_4 = $dephp_0 - floatval($dephp_1['firstnum']);
                $dephp_5 = floatval($dephp_1['secondnum']) <= 0 ? 1 : floatval($dephp_1['secondnum']);
                $dephp_6 = 0;
                if ($dephp_4 % $dephp_5 == 0){
                    $dephp_6 = ($dephp_4 / $dephp_5) * floatval($dephp_1['secondnumprice']);
                }else{
                    $dephp_6 = ((int) ($dephp_4 / $dephp_5) + 1) * floatval($dephp_1['secondnumprice']);
                }
                $dephp_3 += $dephp_6;
            }
        }else{
            if ($dephp_0 <= $dephp_1['firstweight']){
                $dephp_3 = floatval($dephp_1['firstprice']);
            }else{
                $dephp_3 = floatval($dephp_1['firstprice']);
                $dephp_4 = $dephp_0 - floatval($dephp_1['firstweight']);
                $dephp_5 = floatval($dephp_1['secondweight']) <= 0 ? 1 : floatval($dephp_1['secondweight']);
                $dephp_6 = 0;
                if ($dephp_4 % $dephp_5 == 0){
                    $dephp_6 = ($dephp_4 / $dephp_5) * floatval($dephp_1['secondprice']);
                }else{
                    $dephp_6 = ((int) ($dephp_4 / $dephp_5) + 1) * floatval($dephp_1['secondprice']);
                }
                $dephp_3 += $dephp_6;
            }
        }
        return $dephp_3;
    }
    function getCityDispatchPrice($dephp_7, $dephp_8, $dephp_0, $dephp_1){
        if (is_array($dephp_7) && count($dephp_7) > 0){
            foreach ($dephp_7 as $dephp_9){
                $dephp_10 = explode(';', $dephp_9['citys']);
                if (in_array($dephp_8, $dephp_10) && !empty($dephp_10)){
                    return $this -> getDispatchPrice($dephp_0, $dephp_9, $dephp_1['calculatetype']);
                }
            }
        }
        return $this -> getDispatchPrice($dephp_0, $dephp_1);
    }
    public function payResult($dephp_11){
        global $_W;
        $dephp_12 = intval($dephp_11['fee']);
        $dephp_13 = array('status' => $dephp_11['result'] == 'success' ? 1 : 0);
        $dephp_14 = $dephp_11['tid'];
        $dephp_15 = pdo_fetch('select id,ordersn, price,openid,dispatchtype,addressid,carrier,status,isverify,deductcredit2,`virtual`,isvirtual,couponid from ' . tablename('manor_shop_order') . ' where  ordersn=:ordersn and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':ordersn' => $dephp_14));
        $dephp_16 = $dephp_15['id'];
        if ($dephp_11['from'] == 'return'){
            $dephp_17 = false;
            if (empty($dephp_15['dispatchtype'])){
                $dephp_17 = pdo_fetch('select realname,mobile,address,province,city,area from ' . tablename('manor_shop_member_address') . ' where id=:id limit 1', array(':id' => $dephp_15['addressid']));
            }
            $dephp_18 = false;
            if ($dephp_15['dispatchtype'] == 1 || $dephp_15['isvirtual'] == 1){
                $dephp_18 = unserialize($dephp_15['carrier']);
            }
            if ($dephp_11['type'] == 'cash'){
                return array('result' => 'success', 'order' => $dephp_15, 'address' => $dephp_17, 'carrier' => $dephp_18);
            }else{
                if ($dephp_15['status'] == 0){
                    $dephp_19 = p('virtual');
                    if (!empty($dephp_15['virtual']) && $dephp_19){
                        $dephp_19 -> pay($dephp_15);
                    }else{
                        pdo_update('manor_shop_order', array('status' => 1, 'paytime' => time()), array('id' => $dephp_16));
                        if ($dephp_15['deductcredit2'] > 0){
                            $dephp_20 = m('common') -> getSysset('shop');
                            m('member') -> setCredit($dephp_15['openid'], 'credit2', - $dephp_15['deductcredit2'], array(0, $dephp_20['name'] . "余额抵扣: {$dephp_15['deductcredit2']} 订单号: " . $dephp_15['ordersn']));
                        }
                        $this -> setStocksAndCredits($dephp_16, 1);
                        if (p('coupon') && !empty($dephp_15['couponid'])){
                            p('coupon') -> backConsumeCoupon($dephp_15['id']);
                        }
                        m('notice') -> sendOrderMessage($dephp_16);
                        if (p('commission')){
                            p('commission') -> checkOrderPay($dephp_15['id']);
                        }
                    }
                }
                return array('result' => 'success', 'order' => $dephp_15, 'address' => $dephp_17, 'carrier' => $dephp_18, 'virtual' => $dephp_15['virtual']);
            }
        }
    }
    function setStocksAndCredits($dephp_16 = '', $dephp_21 = 0){
        global $_W;
        $dephp_15 = pdo_fetch('select id,ordersn,price,openid,dispatchtype,addressid,carrier,status from ' . tablename('manor_shop_order') . ' where id=:id limit 1', array(':id' => $dephp_16));
        $dephp_22 = pdo_fetchall('select og.goodsid,og.total,g.totalcnf,og.realprice, g.credit,og.optionid,g.total as goodstotal,og.optionid,g.sales,g.salesreal from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join ' . tablename('manor_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.orderid=:orderid and og.uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $dephp_16));
        $dephp_23 = 0;
        foreach ($dephp_22 as $dephp_24){
            $dephp_25 = 0;
            if ($dephp_21 == 0){
                if ($dephp_24['totalcnf'] == 0){
                    $dephp_25 = -1;
                }
            }else if ($dephp_21 == 1){
                if ($dephp_24['totalcnf'] == 1){
                    $dephp_25 = -1;
                }
            }else if ($dephp_21 == 2){
                if ($dephp_15['status'] >= 1){
                    if ($dephp_24['totalcnf'] == 1){
                        $dephp_25 = 1;
                    }
                }else{
                    if ($dephp_24['totalcnf'] == 0){
                        $dephp_25 = 1;
                    }
                }
            }
            if (!empty($dephp_25)){
                if (!empty($dephp_24['optionid'])){
                    $dephp_26 = m('goods') -> getOption($dephp_24['goodsid'], $dephp_24['optionid']);
                    if (!empty($dephp_26) && $dephp_26['stock'] != -1){
                        $dephp_27 = -1;
                        if ($dephp_25 == 1){
                            $dephp_27 = $dephp_26['stock'] + $dephp_24['total'];
                        }else if ($dephp_25 == -1){
                            $dephp_27 = $dephp_26['stock'] - $dephp_24['total'];
                            $dephp_27 <= 0 && $dephp_27 = 0;
                        }
                        if ($dephp_27 != -1){
                            pdo_update('manor_shop_goods_option', array('stock' => $dephp_27), array('uniacid' => $_W['uniacid'], 'goodsid' => $dephp_24['goodsid'], 'id' => $dephp_24['optionid']));
                        }
                    }
                }
                if ($dephp_24['goodstotal'] != -1){
                    $dephp_28 = -1;
                    if ($dephp_25 == 1){
                        $dephp_28 = $dephp_24['goodstotal'] + $dephp_24['total'];
                    }else if ($dephp_25 == -1){
                        $dephp_28 = $dephp_24['goodstotal'] - $dephp_24['total'];
                        $dephp_28 <= 0 && $dephp_28 = 0;
                        if($dephp_21 == 2) {
                            if($dephp_24['goodstotal'] == $dephp_24['total']) {
                                $dephp_28 = $dephp_24['total'];
                            }
                        }
                    }
                    if ($dephp_28 != -1){
                        pdo_update('manor_shop_goods', array('total' => $dephp_28), array('uniacid' => $_W['uniacid'], 'id' => $dephp_24['goodsid']));
                    }
                }
            }
            $dephp_29 = trim($dephp_24['credit']);
            if (!empty($dephp_29)){
                if (strexists($dephp_29, '%')){
                    $dephp_23 += intval(floatval(str_replace('%', '', $dephp_29)) / 100 * $dephp_24['realprice']);
                }else{
                    $dephp_23 += intval($dephp_24['credit']) * $dephp_24['total'];
                }
            }
            if ($dephp_21 == 0){
                pdo_update('manor_shop_goods', array('sales' => $dephp_24['sales'] + $dephp_24['total']), array('uniacid' => $_W['uniacid'], 'id' => $dephp_24['goodsid']));
            }elseif ($dephp_21 == 1){
                if ($dephp_15['status'] >= 1){
                    $dephp_30 = pdo_fetchcolumn('select ifnull(sum(total),0) from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join ' . tablename('manor_shop_order') . ' o on o.id = og.orderid ' . ' where og.goodsid=:goodsid and o.status>=1 and o.uniacid=:uniacid limit 1', array(':goodsid' => $dephp_24['goodsid'], ':uniacid' => $_W['uniacid']));
                    pdo_update('manor_shop_goods', array('salesreal' => $dephp_30), array('id' => $dephp_24['goodsid']));
                }
            }
        }
        if ($dephp_23 > 0){
            $dephp_20 = m('common') -> getSysset('shop');
            if ($dephp_21 == 1){
                m('member') -> setCredit($dephp_15['openid'], 'credit1', $dephp_23, array(0, $dephp_20['name'] . '购物积分 订单号: ' . $dephp_15['ordersn']));
            }elseif ($dephp_21 == 2){
                if ($dephp_15['status'] >= 1){
                    m('member') -> setCredit($dephp_15['openid'], 'credit1', - $dephp_23, array(0, $dephp_20['name'] . '购物取消订单扣除积分 订单号: ' . $dephp_15['ordersn']));
                }
            }
        }
    }
    function getDefaultDispatch(){
        global $_W;
        $dephp_31 = 'select * from ' . tablename('manor_shop_dispatch') . ' where isdefault=1 and uniacid=:uniacid and enabled=1 Limit 1';
        $dephp_11 = array(':uniacid' => $_W['uniacid']);
        $dephp_13 = pdo_fetch($dephp_31, $dephp_11);
        return $dephp_13;
    }
    function getNewDispatch(){
        global $_W;
        $dephp_31 = 'select * from ' . tablename('manor_shop_dispatch') . ' where uniacid=:uniacid and enabled=1 order by id desc Limit 1';
        $dephp_11 = array(':uniacid' => $_W['uniacid']);
        $dephp_13 = pdo_fetch($dephp_31, $dephp_11);
        return $dephp_13;
    }
    function getOneDispatch($dephp_32){
        global $_W;
        $dephp_31 = 'select * from ' . tablename('manor_shop_dispatch') . ' where id=:id and uniacid=:uniacid and enabled=1 Limit 1';
        $dephp_11 = array(':id' => $dephp_32, ':uniacid' => $_W['uniacid']);
        $dephp_13 = pdo_fetch($dephp_31, $dephp_11);
        return $dephp_13;
    }
}
