<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
if (!class_exists('VirtualModel')){
    class VirtualModel extends PluginModel{
        public function updateGoodsStock($dephp_0 = 0){
            global $_W, $_GPC;
            $dephp_1 = pdo_fetch('select `virtual` from ' . tablename('manor_shop_goods') . ' where id=:id and type=3 and uniacid=:uniacid limit 1', array(':id' => $dephp_0, ':uniacid' => $_W['uniacid']));
            if (empty($dephp_1)){
                return;
            }
            $dephp_2 = 0;
            if (!empty($dephp_1['virtual'])){
                $dephp_2 = pdo_fetchcolumn('select count(*) from ' . tablename('manor_shop_virtual_data') . ' where typeid=:typeid and uniacid=:uniacid and openid=\'\' limit 1', array(':typeid' => $dephp_1['virtual'], ':uniacid' => $_W['uniacid']));
            }else{
                $dephp_3 = array();
                $dephp_4 = pdo_fetchall('select id, `virtual` from ' . tablename('manor_shop_goods_option') . " where goodsid=$dephp_0");
                foreach ($dephp_4 as $dephp_5){
                    if (empty($dephp_5['virtual'])){
                        continue;
                    }
                    $dephp_6 = pdo_fetchcolumn('select count(*) from ' . tablename('manor_shop_virtual_data') . ' where typeid=:typeid and uniacid=:uniacid and openid=\'\' limit 1', array(':typeid' => $dephp_5['virtual'], ':uniacid' => $_W['uniacid']));
                    pdo_update('manor_shop_goods_option', array('stock' => $dephp_6), array('id' => $dephp_5['id']));
                    if (!in_array($dephp_5['virtual'], $dephp_3)){
                        $dephp_3[] = $dephp_5['virtual'];
                        $dephp_2 += $dephp_6;
                    }
                }
            }
            pdo_update('manor_shop_goods', array('total' => $dephp_2), array('id' => $dephp_0));
        }
        public function updateStock($dephp_7 = 0){
            global $_W;
            $dephp_8 = array();
            $dephp_1 = pdo_fetchall('select id from ' . tablename('manor_shop_goods') . ' where type=3 and virtual=:virtual and uniacid=:uniacid limit 1', array(':virtual' => $dephp_7, ':uniacid' => $_W['uniacid']));
            foreach ($dephp_1 as $dephp_9){
                $dephp_8[] = $dephp_9['id'];
            }
            $dephp_4 = pdo_fetchall('select id, goodsid from ' . tablename('manor_shop_goods_option') . ' where virtual=:virtual and uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':virtual' => $dephp_7));
            foreach ($dephp_4 as $dephp_5){
                if (!in_array($dephp_5['goodsid'], $dephp_8)){
                    $dephp_8[] = $dephp_5['goodsid'];
                }
            }
            foreach ($dephp_8 as $dephp_10){
                $this -> updateGoodsStock($dephp_10);
            }
        }
        public function pay($dephp_11){
            global $_W, $_GPC;
            $dephp_1 = pdo_fetch('select id,goodsid,total,realprice from ' . tablename('manor_shop_order_goods') . ' where  orderid=:orderid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':orderid' => $dephp_11['id']));
            $dephp_9 = pdo_fetch('select id,credit,sales,salesreal from ' . tablename('manor_shop_goods') . ' where  id=:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $dephp_1['goodsid']));
            $dephp_12 = pdo_fetchall('SELECT id,typeid,fields FROM ' . tablename('manor_shop_virtual_data') . ' WHERE typeid=:typeid and openid=:openid and uniacid=:uniacid order by rand() limit ' . $dephp_1['total'], array(':openid' => '', ':typeid' => $dephp_11['virtual'], ':uniacid' => $_W['uniacid']));
            $dephp_13 = pdo_fetch('select fields from ' . tablename('manor_shop_virtual_type') . ' where id=:id and uniacid=:uniacid limit 1 ', array(':id' => $dephp_11['virtual'], ':uniacid' => $_W['uniacid']));
            $dephp_14 = iunserializer($dephp_13['fields'], true);
            $dephp_15 = array();
            $dephp_16 = array();
            foreach ($dephp_12 as $dephp_17){
                $dephp_15[] = $dephp_17['fields'];
                $dephp_18 = array();
                $dephp_19 = iunserializer($dephp_17['fields']);
                foreach($dephp_19 as $dephp_20 => $dephp_21){
                    $dephp_18[] = $dephp_14[$dephp_20] . ': ' . $dephp_21;
                }
                $dephp_16[] = implode(' ', $dephp_18);
                pdo_update('manor_shop_virtual_data', array('openid' => $dephp_11['openid'], 'orderid' => $dephp_11['id'], 'ordersn' => $dephp_11['ordersn'], 'price' => round($dephp_1['realprice'] / $dephp_1['total'], 2), 'usetime' => time()), array('id' => $dephp_17['id']));
                pdo_update('manor_shop_virtual_type', 'usedata=usedata+1', array('id' => $dephp_17['typeid']));
                $this -> updateStock($dephp_17['typeid']);
            }
            $dephp_16 = implode('
', $dephp_16);
            $dephp_15 = '[' . implode(',', $dephp_15) . ']';
            $dephp_22 = time();
            pdo_update('manor_shop_order', array('virtual_info' => $dephp_15, 'virtual_str' => $dephp_16, 'status' => '3', 'paytime' => $dephp_22, 'sendtime' => $dephp_22, 'finishtime' => $dephp_22), array('id' => $dephp_11['id']));
            if ($dephp_11['deductcredit2'] > 0){
                $dephp_23 = m('common') -> getSysset('shop');
                m('member') -> setCredit($dephp_11['openid'], 'credit2', - $dephp_11['deductcredit2'], array(0, $dephp_23['name'] . "余额抵扣: {$dephp_11['deductcredit2']} 订单号: " . $dephp_11['ordersn']));
            }
            $dephp_24 = $dephp_1['total'] * $dephp_9['credit'];
            if($dephp_24 > 0){
                $dephp_23 = m('common') -> getSysset('shop');
                m('member') -> setCredit($dephp_11['openid'], 'credit1', $dephp_24, array(0, $dephp_23['name'] . '购物积分 订单号: ' . $dephp_11['ordersn']));
            }
            $dephp_25 = pdo_fetchcolumn('select ifnull(sum(total),0) from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join ' . tablename('manor_shop_order') . ' o on o.id = og.orderid ' . ' where og.goodsid=:goodsid and o.status>=1 and o.uniacid=:uniacid limit 1', array(':goodsid' => $dephp_9['id'], ':uniacid' => $_W['uniacid']));
            pdo_update('manor_shop_goods', array('salesreal' => $dephp_25), array('id' => $dephp_9['id']));
            m('member') -> upgradeLevel($dephp_11['openid']);
            m('notice') -> sendOrderMessage($dephp_11['id']);
            if(p('coupon') && !empty($dephp_11['couponid'])){
                p('coupon') -> backConsumeCoupon($dephp_11['id']);
            }
            if (p('commission')){
                p('commission') -> checkOrderPay($dephp_11['id']);
                p('commission') -> checkOrderFinish($dephp_11['id']);
            }
        }
        public function perms(){
            return array('virtual' => array('text' => $this -> getName(), 'isplugin' => true, 'child' => array('temp' => array('text' => '模板', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log'), 'data' => array('text' => '数据', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log', 'import' => '导入-log', 'export' => '导出已使用数据-log'), 'category' => array('text' => '分类', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log'))));
        }
    }
}
