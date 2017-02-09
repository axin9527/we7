<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
class Ewei_DShop_Notice{
    public function sendOrderMessage($dephp_0 = '0', $dephp_1 = false){
        global $_W;
        if (empty($dephp_0)){
            return;
        }
        $dephp_2 = pdo_fetch('select * from ' . tablename('manor_shop_order') . ' where id=:id limit 1', array(':id' => $dephp_0));
        if (empty($dephp_2)){
            return;
        }
        $dephp_3 = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=manor_shop&do=order&p=detail&id=' . $dephp_0;
        if (strexists($dephp_3, '/addons/manor_shop/')){
            $dephp_3 = str_replace('/addons/manor_shop/', '/', $dephp_3);
        }
        if (strexists($dephp_3, '/core/mobile/order/')){
            $dephp_3 = str_replace('/core/mobile/order/', '/', $dephp_3);
        }
        $dephp_4 = $dephp_2['openid'];
        $dephp_5 = pdo_fetchall('select g.id,g.title,og.orderid,og.realprice,og.total,og.price,og.optionname as optiontitle,og.is_give,g.noticeopenid,g.noticetype from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join ' . tablename('manor_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.orderid=:orderid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $dephp_0));
        $dephp_6 = "\n";
        foreach ($dephp_5 as $dephp_7){
        	if($dephp_7['is_give'] == 1) {
        		$title = '赠送: ';
	        } else {
		        $title = '购买: ';
	        }
            $dephp_6 .= $title."" . $dephp_7['title'] . '( ';
            if (!empty($dephp_7['optiontitle'])){
                $dephp_6 .= ' 规格: ' . $dephp_7['optiontitle'];
            }
            $dephp_6 .= ' 单价: ' . ($dephp_7['realprice'] / $dephp_7['total']) . '元 数量: ' . $dephp_7['total'] . ' 总价: ' . $dephp_7['realprice'] . '元); '."\n";
        }
        if($dephp_2['dispatchprice'] <= 0) {
        	$youfei_tips = '免邮费'."\n";
        } else {
	        $youfei_tips = '包含运费:'.$dephp_2['dispatchprice']."元\n";
        }
        $_order = pdo_get('manor_shop_order', array('id'=>$dephp_5[0]['orderid'], 'uniacid'=>$_W['uniacid']), array('deductenough', 'couponprice', 'couponid'));
        if($_order['deductenough'] > 0) {
	        $dephp_6 .=' 满减: '.$_order['deductenough']."元\n";
        }
        if($_order['couponprice'] >0 && $_order['couponid'] >0) {
        	$dephp_6 .= '使用优惠劵减免:'.$_order['couponprice']."元\n";
        }

        $dephp_8 = ' 实付总价: ' . $dephp_2['price'] . '('.$youfei_tips.')';
        $dephp_9 = m('member') -> getMember($dephp_4);
        $dephp_10 = unserialize($dephp_9['noticeset']);
        if (!is_array($dephp_10)){
            $dephp_10 = array();
        }
        $dephp_11 = m('common') -> getSysset();
        $dephp_12 = $dephp_11['shop'];
        $dephp_13 = $dephp_11['notice'];
        if ($dephp_1){
            $dephp_14 = array('0' => '退款', '1' => '退货退款', '2' => '换货');
            if (!empty($dephp_2['refundid'])){
                $dephp_15 = pdo_fetch('select * from ' . tablename('manor_shop_order_refund') . ' where id=:id limit 1', array(':id' => $dephp_2['refundid']));
                if (empty($dephp_15)){
                    return;
                }
                if (empty($dephp_15['status'])){
                    $dephp_16 = array('first' => array('value' => '您的' . $dephp_14[$dephp_15['rtype']] . '申请已经提交！', 'color' => '#4a5077'), 'orderProductPrice' => array('title' => '退款金额', 'value' => $dephp_15['rtype'] == 3?'-':('¥' . $dephp_15['applyprice'] . '元'), 'color' => '#4a5077'), 'orderProductName' => array('title' => '商品详情', 'value' => $dephp_6 . $dephp_8, 'color' => '#4a5077'), 'orderName' => array('title' => '订单编号', 'value' => $dephp_2['ordersn'], 'color' => '#4a5077'), 'remark' => array('value' => '
等待商家确认' . $dephp_14[$dephp_15['rtype']] . '信息！', 'color' => '#4a5077'),);
                    if (!empty($dephp_13['refund']) && empty($dephp_10['refund'])){
                        m('message') -> sendTplNotice($dephp_4, $dephp_13['refund'], $dephp_16, $dephp_3);
                    }else if (empty($dephp_10['refund'])){
                        m('message') -> sendCustomNotice($dephp_4, $dephp_16, $dephp_3);
                    }
                }else if ($dephp_15['status'] == 3){
                    $dephp_17 = iunserializer($dephp_15['refundaddress']);
                    $dephp_18 = '退货地址: ' . $dephp_17['province'] . ' ' . $dephp_17['city'] . ' ' . $dephp_17['area'] . ' ' . $dephp_17['address'] . ' 收件人: ' . $dephp_17['name'] . ' (' . $dephp_17['mobile'] . ')(' . $dephp_17['tel'] . ') ';
                    $dephp_16 = array('first' => array('value' => '您的' . $dephp_14[$dephp_15['rtype']] . '申请已经通过！', 'color' => '#4a5077'), 'orderProductPrice' => array('title' => '退款金额', 'value' => $dephp_15['rtype'] == 3?'-':('¥' . $dephp_15['applyprice'] . '元'), 'color' => '#4a5077'), 'orderProductName' => array('title' => '商品详情', 'value' => $dephp_6 . $dephp_8, 'color' => '#4a5077'), 'orderName' => array('title' => '订单编号', 'value' => $dephp_2['ordersn'], 'color' => '#4a5077'), 'remark' => array('value' => '
请您根据商家提供的退货地址将商品寄回！' . $dephp_18 . "", 'color' => '#4a5077'),);
                    if (!empty($dephp_13['refund']) && empty($dephp_10['refund'])){
                        m('message') -> sendTplNotice($dephp_4, $dephp_13['refund'], $dephp_16, $dephp_3);
                    }else if (empty($dephp_10['refund'])){
                        m('message') -> sendCustomNotice($dephp_4, $dephp_16, $dephp_3);
                    }
                }else if ($dephp_15['status'] == 5){
                    if(!empty($dephp_2['address'])){
                        $dephp_19 = iunserializer($dephp_2['address_send']);
                        if(!is_array($dephp_19)){
                            $dephp_19 = iunserializer($dephp_2['address']);
                            if(!is_array($dephp_19)){
                                $dephp_19 = pdo_fetch('select id,realname,mobile,address,province,city,area from ' . tablename('manor_shop_member_address') . ' where id=:id and uniacid=:uniacid limit 1' , array(':id' => $dephp_2['addressid'], ':uniacid' => $_W['uniacid']));
                            }
                        }
                    }
                    if (empty($dephp_19)){
                        return;
                    }
                    $dephp_16 = array('first' => array('value' => '您的换货物品已经发货！', 'color' => '#4a5077'), 'keyword1' => array('title' => '订单内容', 'value' => '【' . $dephp_2['ordersn'] . '】' . $dephp_6, 'color' => '#4a5077'), 'keyword2' => array('title' => '物流服务', 'value' => $dephp_15['rexpresscom'], 'color' => '#4a5077'), 'keyword3' => array('title' => '快递单号', 'value' => $dephp_15['rexpresssn'], 'color' => '#4a5077'), 'keyword4' => array('title' => '收货信息', 'value' => '地址: ' . $dephp_19['province'] . ' ' . $dephp_19['city'] . ' ' . $dephp_19['area'] . ' ' . $dephp_19['address'] . '收件人: ' . $dephp_19['realname'] . ' (' . $dephp_19['mobile'] . ') ', 'color' => '#4a5077'), 'remark' => array('value' => '
我们正加速送到您的手上，请您耐心等候。', 'color' => '#4a5077'));
                    if (!empty($dephp_13['send']) && empty($dephp_10['send'])){
                        m('message') -> sendTplNotice($dephp_4, $dephp_13['send'], $dephp_16, $dephp_3);
                    }else if (empty($dephp_10['send'])){
                        m('message') -> sendCustomNotice($dephp_4, $dephp_16, $dephp_3);
                    }
                }else if ($dephp_15['status'] == 1){
                    if ($dephp_15['rtype'] == 2){
                        $dephp_16 = array('first' => array('value' => '您的订单已经完成换货！', 'color' => '#4a5077'), 'orderProductPrice' => array('title' => '退款金额', 'value' => '-', 'color' => '#4a5077'), 'orderProductName' => array('title' => '商品详情', 'value' => $dephp_6 . $dephp_8, 'color' => '#4a5077'), 'orderName' => array('title' => '订单编号', 'value' => $dephp_2['ordersn'], 'color' => '#4a5077'), 'remark' => array('value' => '
 换货成功！
【' . $dephp_12['name'] . '】期待您再次购物！', 'color' => '#4a5077'));
                    }else{
                        $dephp_20 = '';
                        if(empty($dephp_15['refundtype'])){
                            $dephp_20 = ', 已经退回您的余额账户，请留意查收！';
                        }else if($dephp_15['refundtype'] == 1){
                            $dephp_20 = ', 已经退回您的对应支付渠道（如银行卡，微信钱包等, 具体到账时间请您查看微信支付通知)，请留意查收！';
                        }else{
                            $dephp_20 = ', 请联系客服进行退款事项！';
                        }
                        $dephp_16 = array('first' => array('value' => '您的订单已经完成退款！', 'color' => '#4a5077'), 'orderProductPrice' => array('title' => '退款金额', 'value' => '¥' . $dephp_15['orderprice'] . '元', 'color' => '#4a5077'), 'orderProductName' => array('title' => '商品详情', 'value' => $dephp_6 . $dephp_8, 'color' => '#4a5077'), 'orderName' => array('title' => '订单编号', 'value' => $dephp_2['ordersn'], 'color' => '#4a5077'), 'remark' => array('value' => '
 退款金额 ¥' . $dephp_15['orderprice'] . "{$dephp_20}\r\n 【" . $dephp_12['name'] . '】期待您再次购物！', 'color' => '#4a5077'));
                    }
                    if (!empty($dephp_13['refund1']) && empty($dephp_10['refund1'])){
                        m('message') -> sendTplNotice($dephp_4, $dephp_13['refund1'], $dephp_16, $dephp_3);
                    }else if (empty($dephp_10['refund1'])){
                        m('message') -> sendCustomNotice($dephp_4, $dephp_16, $dephp_3);
                    }
                }elseif ($dephp_15['status'] == -1){
                    $dephp_21 = '
驳回原因: ' . $dephp_15['reply'];
                    if (!empty($dephp_12['phone'])){
                        $dephp_21 .= '
客服电话:  ' . $dephp_12['phone'];
                    }
                    $dephp_16 = array('first' => array('value' => '您的' . $dephp_14[$dephp_15['rtype']] . '申请被商家驳回，可与商家协商沟通！', 'color' => '#4a5077'), 'orderProductPrice' => array('title' => '退款金额', 'value' => '¥' . $dephp_15['orderprice'] . '元', 'color' => '#4a5077'), 'orderProductName' => array('title' => '商品详情', 'value' => $dephp_6 . $dephp_8, 'color' => '#4a5077'), 'orderName' => array('title' => '订单编号', 'value' => $dephp_2['ordersn'], 'color' => '#4a5077'), 'remark' => array('value' => $dephp_21, 'color' => '#4a5077'));
                    if (!empty($dephp_13['refund2']) && empty($dephp_10['refund2'])){
                        m('message') -> sendTplNotice($dephp_4, $dephp_13['refund2'], $dephp_16, $dephp_3);
                    }else if (empty($dephp_10['refund2'])){
                        m('message') -> sendCustomNotice($dephp_4, $dephp_16, $dephp_3);
                    }
                }
                return;
            }
        }
        $dephp_22 = '';
        if(!empty($dephp_2['address'])){
            $dephp_19 = iunserializer($dephp_2['address_send']);
            if(!is_array($dephp_19)){
                $dephp_19 = iunserializer($dephp_2['address']);
                if(!is_array($dephp_19)){
                    $dephp_19 = pdo_fetch('select id,realname,mobile,address,province,city,area from ' . tablename('manor_shop_member_address') . ' where id=:id and uniacid=:uniacid limit 1' , array(':id' => $dephp_2['addressid'], ':uniacid' => $_W['uniacid']));
                }
            }
            if(!empty($dephp_19)){
                $dephp_22 = '收件人: ' . $dephp_19['realname'] . '
联系电话: ' . $dephp_19['mobile'] . '
收货地址: ' . $dephp_19['province'] . $dephp_19['city'] . $dephp_19['area'] . ' ' . $dephp_19['address'];
            }
        }else{
            $dephp_23 = iunserializer($dephp_2['carrier']);
            if(is_array($dephp_23)){
                $dephp_22 = '联系人: ' . $dephp_23['carrier_realname'] . '
联系电话: ' . $dephp_23['carrier_mobile'];
            }
        }
        if ($dephp_2['status'] == -1){
            if (empty($dephp_2['dispatchtype'])){
                $dephp_24 = array('title' => '收货信息', 'value' => '收货地址: ' . $dephp_19['province'] . ' ' . $dephp_19['city'] . ' ' . $dephp_19['area'] . ' ' . $dephp_19['address'] . ' 收件人: ' . $dephp_19['realname'] . ' 联系电话: ' . $dephp_19['mobile'], 'color' => '#4a5077');
            }else{
                $dephp_24 = array('title' => '收货信息', 'value' => '自提地点: ' . $dephp_23['address'] . ' 联系人: ' . $dephp_23['realname'] . ' 联系电话: ' . $dephp_23['mobile'], 'color' => '#4a5077');
            }
            if($dephp_2['dispatchprice'] <=0) {
            	$yunfei_tips = '免邮费';
            } else {
	            $yunfei_tips = '含运费:'.$dephp_2['dispatchprice'].'元';
            }
            $dephp_16 = array('first' => array('value' => '您的订单已取消!', 'color' => '#4a5077'), 'orderProductPrice' => array('title' => '订单金额', 'value' => '¥' . $dephp_2['price'] . '元('.$yunfei_tips.')', 'color' => '#4a5077'), 'orderProductName' => array('title' => '商品详情', 'value' => $dephp_6, 'color' => '#4a5077'), 'orderAddress' => $dephp_24, 'orderName' => array('title' => '订单编号', 'value' => $dephp_2['ordersn'], 'color' => '#4a5077'), 'remark' => array('value' => '
【' . $dephp_12['name'] . '】欢迎您的再次购物！', 'color' => '#4a5077'));
            if (!empty($dephp_13['cancel']) && empty($dephp_10['cancel'])){
                m('message') -> sendTplNotice($dephp_4, $dephp_13['cancel'], $dephp_16, $dephp_3);
            }else if (empty($dephp_10['cancel'])){
                m('message') -> sendCustomNotice($dephp_4, $dephp_16, $dephp_3);
            }
        }else if ($dephp_2['status'] == 0){
            $dephp_25 = explode(',', $dephp_13['newtype']);
            if (empty($dephp_13['newtype']) || (is_array($dephp_25) && in_array(0, $dephp_25))){
                $dephp_21 = '
订单下单成功,请到后台查看!';
                if(!empty($dephp_22)){
                    $dephp_21 .= '
下单者信息:
' . $dephp_22;
                }
                $dephp_16 = array('first' => array('value' => '订单下单通知!', 'color' => '#4a5077'), 'keyword1' => array('title' => '时间', 'value' => date('Y-m-d H:i:s', $dephp_2['createtime']), 'color' => '#4a5077'), 'keyword2' => array('title' => '商品名称', 'value' => $dephp_6 . $dephp_8, 'color' => '#4a5077'), 'keyword3' => array('title' => '订单号', 'value' => $dephp_2['ordersn'], 'color' => '#4a5077'), 'remark' => array('value' => $dephp_21, 'color' => '#4a5077'));
                $dephp_26 = m('common') -> getAccount();
                if (!empty($dephp_13['openid'])){
                    $dephp_27 = explode(',', $dephp_13['openid']);
                    foreach ($dephp_27 as $dephp_28){
                        if (empty($dephp_28)){
                            continue;
                        }
                        if (!empty($dephp_13['new'])){
                            m('message') -> sendTplNotice($dephp_28, $dephp_13['new'], $dephp_16, '', $dephp_26);
                        }else{
                            m('message') -> sendCustomNotice($dephp_28, $dephp_16, '', $dephp_26);
                        }
                    }
                }
            }
            $dephp_21 = '
商品已经下单，请及时备货，谢谢!';
            if(!empty($dephp_22)){
                $dephp_21 .= '
下单者信息:
' . $dephp_22;
            }
            foreach ($dephp_5 as $dephp_7){
                if (!empty($dephp_7['noticeopenid'])){
                    $dephp_29 = explode(',', $dephp_7['noticetype']);
                    if(empty($dephp_7['noticetype']) || (is_array($dephp_29) && in_array(0, $dephp_29))){
                        $dephp_30 = $dephp_7['title'] . '( ';
                        if (!empty($dephp_7['optiontitle'])){
                            $dephp_30 .= ' 规格: ' . $dephp_7['optiontitle'];
                        }
                        $dephp_30 .= ' 单价: ' . ($dephp_7['realprice'] / $dephp_7['total']) . ' 数量: ' . $dephp_7['total'] . ' 总价: ' . $dephp_7['realprice'] . '); ';
                        $dephp_16 = array('first' => array('value' => '商品下单通知!', 'color' => '#4a5077'), 'keyword1' => array('title' => '时间', 'value' => date('Y-m-d H:i:s', $dephp_2['createtime']), 'color' => '#4a5077'), 'keyword2' => array('title' => '商品名称', 'value' => $dephp_30, 'color' => '#4a5077'), 'keyword3' => array('title' => '订单号', 'value' => $dephp_2['ordersn'], 'color' => '#4a5077'), 'remark' => array('value' => $dephp_21 , 'color' => '#4a5077'));
                        if (!empty($dephp_13['new'])){
                            m('message') -> sendTplNotice($dephp_7['noticeopenid'], $dephp_13['new'], $dephp_16, '', $dephp_26);
                        }else{
                            m('message') -> sendCustomNotice($dephp_7['noticeopenid'], $dephp_16, '', $dephp_26);
                        }
                    }
                }
            }
            if(!empty($dephp_2['addressid'])){
                $dephp_21 = '
您的订单我们已经收到，支付后我们将尽快配送~~';
            }else if(!empty($dephp_2['isverify'])){
                $dephp_21 = '
您的订单我们已经收到，支付后您就可以到店使用了~~';
            }else if(!empty($dephp_2['virtual'])){
                $dephp_21 = '
您的订单我们已经收到，支付后系统将会自动发货~~';
            }else{
                $dephp_21 = '
您的订单我们已经收到，支付后您就可以到自提点提货物了~~';
            }
	        if($dephp_2['dispatchprice'] <=0) {
		        $yunfei_tips = '免邮费';
	        } else {
		        $yunfei_tips = '含运费:'.$dephp_2['dispatchprice'].'元';
	        }
            $dephp_16 = array('first' => array('value' => '您的订单已提交成功！', 'color' => '#4a5077'), 'keyword1' => array('title' => '店铺', 'value' => $dephp_12['name'], 'color' => '#4a5077'), 'keyword2' => array('title' => '下单时间', 'value' => date('Y-m-d H:i:s', $dephp_2['createtime']), 'color' => '#4a5077'), 'keyword3' => array('title' => '商品', 'value' => $dephp_6, 'color' => '#4a5077'), 'keyword4' => array('title' => '金额', 'value' => '¥' . $dephp_2['price'] . '元('.$yunfei_tips.')', 'color' => '#4a5077'), 'remark' => array('value' => $dephp_21, 'color' => '#4a5077'));
            if (!empty($dephp_13['submit']) && empty($dephp_10['submit'])){
                m('message') -> sendTplNotice($dephp_4, $dephp_13['submit'], $dephp_16, $dephp_3);
            }else if (empty($dephp_10['submit'])){
                m('message') -> sendCustomNotice($dephp_4, $dephp_16, $dephp_3);
            }
        }else if ($dephp_2['status'] == 1){
            $dephp_25 = explode(',', $dephp_13['newtype']);
            if ($dephp_13['newtype'] == 1 || (is_array($dephp_25) && in_array(1, $dephp_25))){
                $dephp_21 = '
订单已经下单支付，请及时备货，谢谢!';
                if(!empty($dephp_22)){
                    $dephp_21 .= '
购买者信息:
' . $dephp_22;
                }
                $dephp_16 = array('first' => array('value' => '订单下单支付通知!', 'color' => '#4a5077'), 'keyword1' => array('title' => '时间', 'value' => date('Y-m-d H:i:s', $dephp_2['createtime']), 'color' => '#4a5077'), 'keyword2' => array('title' => '商品名称', 'value' => $dephp_6 . $dephp_8, 'color' => '#4a5077'), 'keyword3' => array('title' => '订单号', 'value' => $dephp_2['ordersn'], 'color' => '#4a5077'), 'remark' => array('value' => $dephp_21, 'color' => '#4a5077'));
                $dephp_26 = m('common') -> getAccount();
                if (!empty($dephp_13['openid'])){
                    $dephp_27 = explode(',', $dephp_13['openid']);
                    foreach ($dephp_27 as $dephp_28){
                        if (empty($dephp_28)){
                            continue;
                        }
                        if (!empty($dephp_13['new'])){
	                        $dephp_16['first']['value'] = '您已成功支付订单';
                            m('message') -> sendTplNotice($dephp_28, $dephp_13['new'], $dephp_16, '', $dephp_26);
                        }else{
                            m('message') -> sendCustomNotice($dephp_28, $dephp_16, '', $dephp_26);
                        }
                    }
                }
            }
            $dephp_21 = '
商品已经下单支付，请及时备货，谢谢!';
            if(!empty($dephp_22)){
                $dephp_21 .= '
购买者信息:
' . $dephp_22;
            }
            foreach ($dephp_5 as $dephp_7){
                $dephp_29 = explode(',', $dephp_7['noticetype']);
                if($dephp_7['noticetype'] == '1' || (is_array($dephp_29) && in_array(1, $dephp_29))){
                    $dephp_30 = $dephp_7['title'] . '( ';
                    if (!empty($dephp_7['optiontitle'])){
                        $dephp_30 .= ' 规格: ' . $dephp_7['optiontitle'];
                    }
                    $dephp_30 .= ' 单价: ' . ($dephp_7['price'] / $dephp_7['total']) . ' 数量: ' . $dephp_7['total'] . ' 总价: ' . $dephp_7['price'] . '); ';
                    $dephp_16 = array('first' => array('value' => '商品下单支付通知!', 'color' => '#4a5077'), 'keyword1' => array('title' => '时间', 'value' => date('Y-m-d H:i:s', $dephp_2['createtime']), 'color' => '#4a5077'), 'keyword2' => array('title' => '商品名称', 'value' => $dephp_30, 'color' => '#4a5077'), 'keyword3' => array('title' => '订单号', 'value' => $dephp_2['ordersn'], 'color' => '#4a5077'), 'remark' => array('value' => $dephp_21, 'color' => '#4a5077'));
                    if (!empty($dephp_13['new'])){
                        m('message') -> sendTplNotice($dephp_7['noticeopenid'], $dephp_13['new'], $dephp_16, '', $dephp_26);
                    }else{
                        m('message') -> sendCustomNotice($dephp_7['noticeopenid'], $dephp_16, '', $dephp_26);
                    }
                }
            }
            $dephp_21 = '
【' . $dephp_12['name'] . '】欢迎您的再次购物！';
            if($dephp_2['isverify']){
                $dephp_21 = '
点击订单详情查看可消费门店, 【' . $dephp_12['name'] . '】欢迎您的再次购物！';
            }
	        if($dephp_2['dispatchprice'] <=0) {
		        $yunfei_tips = '免邮费';
	        } else {
		        $yunfei_tips = '含运费:'.$dephp_2['dispatchprice'].'元';
	        }
            $dephp_16 = array('first' => array('value' => '您已成功支付订单！', 'color' => '#4a5077'), 'keyword1' => array('title' => '订单', 'value' => $dephp_2['ordersn'], 'color' => '#4a5077'), 'keyword2' => array('title' => '支付状态', 'value' => '支付成功', 'color' => '#4a5077'), 'keyword3' => array('title' => '支付日期', 'value' => date('Y-m-d H:i:s', $dephp_2['paytime']), 'color' => '#4a5077'), 'keyword4' => array('title' => '商户', 'value' => $dephp_12['name'], 'color' => '#4a5077'), 'keyword5' => array('title' => '金额', 'value' => '¥' . $dephp_2['price'] . '元('.$yunfei_tips.')', 'color' => '#4a5077'), 'remark' => array('value' => $dephp_21, 'color' => '#4a5077'));
            $dephp_31 = $dephp_3;
            if (strexists($dephp_31, '/addons/manor_shop/')){
                $dephp_31 = str_replace('/addons/manor_shop/', '/', $dephp_31);
            }
            if (strexists($dephp_31, '/core/mobile/order/')){
                $dephp_31 = str_replace('/core/mobile/order/', '/', $dephp_31);
            }
            if (!empty($dephp_13['pay']) && empty($dephp_10['pay'])){
                m('message') -> sendTplNotice($dephp_4, $dephp_13['pay'], $dephp_16, $dephp_31);
            }else if (empty($dephp_10['pay'])){
                m('message') -> sendCustomNotice($dephp_4, $dephp_16, $dephp_31);
            }
            if ($dephp_2['dispatchtype'] == 1 && empty($dephp_2['isverify'])){
                $dephp_23 = iunserializer($dephp_2['carrier']);
                if (!is_array($dephp_23)){
                    return;
                }
                $dephp_16 = array('first' => array('value' => '自提订单提交成功!', 'color' => '#4a5077'), 'keyword1' => array('title' => '自提码', 'value' => $dephp_2['ordersn'], 'color' => '#4a5077'), 'keyword2' => array('title' => '商品详情', 'value' => $dephp_6 . $dephp_8, 'color' => '#4a5077'), 'keyword3' => array('title' => '提货地址', 'value' => $dephp_23['address'], 'color' => '#4a5077'), 'keyword4' => array('title' => '提货时间', 'value' => $dephp_23['content'], 'color' => '#4a5077'), 'remark' => array('value' => '
请您到选择的自提点进行取货, 自提联系人: ' . $dephp_23['realname'] . ' 联系电话: ' . $dephp_23['mobile'], 'color' => '#4a5077'));
                if (!empty($dephp_13['carrier']) && empty($dephp_10['carrier'])){
                    m('message') -> sendTplNotice($dephp_4, $dephp_13['carrier'], $dephp_16, $dephp_3);
                }else if (empty($dephp_10['carrier'])){
                    m('message') -> sendCustomNotice($dephp_4, $dephp_16, $dephp_3);
                }
            }
        }else if ($dephp_2['status'] == 2){
            if (empty($dephp_2['dispatchtype'])){
                if (empty($dephp_19)){
                    return;
                }
                $dephp_16 = array('first' => array('value' => '您的宝贝已经发货！', 'color' => '#4a5077'), 'keyword1' => array('title' => '订单内容', 'value' => '订单号:【' . $dephp_2['ordersn'] . '】' . $dephp_6 . $dephp_8, 'color' => '#4a5077'), 'keyword2' => array('title' => '物流服务', 'value' => $dephp_2['expresscom']?$dephp_2['expresscom']:'其他服务', 'color' => '#4a5077'), 'keyword3' => array('title' => '快递单号', 'value' => $dephp_2['expresssn'], 'color' => '#4a5077'), 'keyword4' => array('title' => '收货信息', 'value' => '地址: ' . $dephp_19['province'] . ' ' . $dephp_19['city'] . ' ' . $dephp_19['area'] . ' ' . $dephp_19['address'] . '收件人: ' . $dephp_19['realname'] . ' (' . $dephp_19['mobile'] . ') ', 'color' => '#4a5077'), 'remark' => array('value' => '
我们正加速送到您的手上，请您耐心等候。', 'color' => '#4a5077'));
                if (!empty($dephp_13['send']) && empty($dephp_10['send'])){
                    m('message') -> sendTplNotice($dephp_4, $dephp_13['send'], $dephp_16, $dephp_3);
                }else if (empty($dephp_10['send'])){
                    m('message') -> sendCustomNotice($dephp_4, $dephp_16, $dephp_3);
                }
            }
        }else if ($dephp_2['status'] == 3){
            $dephp_32 = p('virtual');
            if($dephp_32 && !empty($dephp_2['virtual'])){
                $dephp_33 = $dephp_32 -> getSet();
                $dephp_34 = '
' . $dephp_22 . '
' . $dephp_2['virtual_str'];
                $dephp_16 = array('first' => array('value' => '您购物的物品已自动发货!', 'color' => '#4a5077'), 'keyword1' => array('title' => '订单金额', 'value' => '¥' . $dephp_2['price'] . '元', 'color' => '#4a5077'), 'keyword2' => array('title' => '商品详情', 'value' => $dephp_6, 'color' => '#4a5077'), 'keyword3' => array('title' => '收货信息', 'value' => $dephp_34, 'color' => '#4a5077'), 'remark' => array('title' => '', 'value' => '
【' . $dephp_12['name'] . '】感谢您的支持与厚爱，欢迎您的再次购物！', 'color' => '#4a5077'));
                if (!empty($dephp_33['tm']['send']) && empty($dephp_10['finish'])){
                    m('message') -> sendTplNotice($dephp_4, $dephp_33['tm']['send'], $dephp_16, $dephp_3);
                }else if (empty($dephp_10['finish'])){
                    m('message') -> sendCustomNotice($dephp_4, $dephp_16, $dephp_3);
                }
                $dephp_35 = '买家购买的商品已经自动发货!';
                $dephp_21 = '
发货信息:' . $dephp_34;
                $dephp_25 = explode(',', $dephp_13['newtype']);
                if ($dephp_13['newtype'] == 2 || (is_array($dephp_25) && in_array(2, $dephp_25))){
                    $dephp_16 = array('first' => array('value' => $dephp_35, 'color' => '#4a5077'), 'keyword1' => array('title' => '订单号', 'value' => $dephp_2['ordersn'], 'color' => '#4a5077'), 'keyword2' => array('title' => '商品名称', 'value' => $dephp_6 . $dephp_8, 'color' => '#4a5077'), 'keyword3' => array('title' => '下单时间', 'value' => date('Y-m-d H:i:s', $dephp_2['createtime']), 'color' => '#4a5077'), 'keyword4' => array('title' => '发货时间', 'value' => date('Y-m-d H:i:s', $dephp_2['sendtime']), 'color' => '#4a5077'), 'keyword5' => array('title' => '确认收货时间', 'value' => date('Y-m-d H:i:s', $dephp_2['finishtime']), 'color' => '#4a5077'), 'remark' => array('title' => '', 'value' => $dephp_21, 'color' => '#4a5077'));
                    $dephp_26 = m('common') -> getAccount();
                    if (!empty($dephp_13['openid'])){
                        $dephp_27 = explode(',', $dephp_13['openid']);
                        foreach ($dephp_27 as $dephp_28){
                            if (empty($dephp_28)){
                                continue;
                            }
                            if (!empty($dephp_13['finish'])){
                                m('message') -> sendTplNotice($dephp_28, $dephp_13['finish'], $dephp_16, '', $dephp_26);
                            }else{
                                m('message') -> sendCustomNotice($dephp_28, $dephp_16, '', $dephp_26);
                            }
                        }
                    }
                }
                foreach ($dephp_5 as $dephp_7){
                    $dephp_29 = explode(',', $dephp_7['noticetype']);
                    if($dephp_7['noticetype'] == '2' || (is_array($dephp_29) && in_array(2, $dephp_29))){
                        $dephp_30 = $dephp_7['title'] . '( ';
                        if (!empty($dephp_7['optiontitle'])){
                            $dephp_30 .= ' 规格: ' . $dephp_7['optiontitle'];
                        }
                        $dephp_30 .= ' 单价: ' . ($dephp_7['price'] / $dephp_7['total']) . ' 数量: ' . $dephp_7['total'] . ' 总价: ' . $dephp_7['price'] . '); ';
                        $dephp_16 = array('first' => array('value' => $dephp_35, 'color' => '#4a5077'), 'keyword1' => array('title' => '订单号', 'value' => $dephp_2['ordersn'], 'color' => '#4a5077'), 'keyword2' => array('title' => '商品名称', 'value' => $dephp_30, 'color' => '#4a5077'), 'keyword3' => array('title' => '下单时间', 'value' => date('Y-m-d H:i:s', $dephp_2['createtime']), 'color' => '#4a5077'), 'keyword4' => array('title' => '发货时间', 'value' => date('Y-m-d H:i:s', $dephp_2['sendtime']), 'color' => '#4a5077'), 'keyword5' => array('title' => '确认收货时间', 'value' => date('Y-m-d H:i:s', $dephp_2['finishtime']), 'color' => '#4a5077'), 'remark' => array('title' => '', 'value' => $dephp_21, 'color' => '#4a5077'));
                        if (!empty($dephp_13['finish'])){
                            m('message') -> sendTplNotice($dephp_7['noticeopenid'], $dephp_13['finish'], $dephp_16, '', $dephp_26);
                        }else{
                            m('message') -> sendCustomNotice($dephp_7['noticeopenid'], $dephp_16, '', $dephp_26);
                        }
                    }
                }
            }else{
                $dephp_16 = array('first' => array('value' => '亲, 您购买的宝贝已经确认收货!', 'color' => '#4a5077'), 'keyword1' => array('title' => '订单号', 'value' => $dephp_2['ordersn'], 'color' => '#4a5077'), 'keyword2' => array('title' => '商品名称', 'value' => $dephp_6 . $dephp_8, 'color' => '#4a5077'), 'keyword3' => array('title' => '下单时间', 'value' => date('Y-m-d H:i:s', $dephp_2['createtime']), 'color' => '#4a5077'), 'keyword4' => array('title' => '发货时间', 'value' => date('Y-m-d H:i:s', $dephp_2['sendtime']), 'color' => '#4a5077'), 'keyword5' => array('title' => '确认收货时间', 'value' => date('Y-m-d H:i:s', $dephp_2['finishtime']), 'color' => '#4a5077'), 'remark' => array('title' => '', 'value' => '
【' . $dephp_12['name'] . '】感谢您的支持与厚爱，欢迎您的再次购物！', 'color' => '#4a5077'));
                if (!empty($dephp_13['finish']) && empty($dephp_10['finish'])){
                    m('message') -> sendTplNotice($dephp_4, $dephp_13['finish'], $dephp_16, $dephp_3);
                }else if (empty($dephp_10['finish'])){
                    m('message') -> sendCustomNotice($dephp_4, $dephp_16, $dephp_3);
                }
                $dephp_35 = '买家购买的商品已经确认收货!';
                if($dephp_2['isverify'] == 1){
                    $dephp_35 = '买家购买的商品已经确认核销!';
                }
                $dephp_21 = "";
                if(!empty($dephp_22)){
                    $dephp_21 = '
购买者信息:
' . $dephp_22;
                }
                $dephp_25 = explode(',', $dephp_13['newtype']);
                if ($dephp_13['newtype'] == 2 || (is_array($dephp_25) && in_array(2, $dephp_25))){
                    $dephp_16 = array('first' => array('value' => $dephp_35, 'color' => '#4a5077'), 'keyword1' => array('title' => '订单号', 'value' => $dephp_2['ordersn'], 'color' => '#4a5077'), 'keyword2' => array('title' => '商品名称', 'value' => $dephp_6 . $dephp_8, 'color' => '#4a5077'), 'keyword3' => array('title' => '下单时间', 'value' => date('Y-m-d H:i:s', $dephp_2['createtime']), 'color' => '#4a5077'), 'keyword4' => array('title' => '发货时间', 'value' => date('Y-m-d H:i:s', $dephp_2['sendtime']), 'color' => '#4a5077'), 'keyword5' => array('title' => '确认收货时间', 'value' => date('Y-m-d H:i:s', $dephp_2['finishtime']), 'color' => '#4a5077'), 'remark' => array('title' => '', 'value' => $dephp_21, 'color' => '#4a5077'));
                    $dephp_26 = m('common') -> getAccount();
                    if (!empty($dephp_13['openid'])){
                        $dephp_27 = explode(',', $dephp_13['openid']);
                        foreach ($dephp_27 as $dephp_28){
                            if (empty($dephp_28)){
                                continue;
                            }
                            if (!empty($dephp_13['finish'])){
                                m('message') -> sendTplNotice($dephp_28, $dephp_13['finish'], $dephp_16, '', $dephp_26);
                            }else{
                                m('message') -> sendCustomNotice($dephp_28, $dephp_16, '', $dephp_26);
                            }
                        }
                    }
                }
                foreach ($dephp_5 as $dephp_7){
                    $dephp_29 = explode(',', $dephp_7['noticetype']);
                    if($dephp_7['noticetype'] == '2' || (is_array($dephp_29) && in_array(2, $dephp_29))){
                        $dephp_30 = $dephp_7['title'] . '( ';
                        if (!empty($dephp_7['optiontitle'])){
                            $dephp_30 .= ' 规格: ' . $dephp_7['optiontitle'];
                        }
                        $dephp_30 .= ' 单价: ' . ($dephp_7['price'] / $dephp_7['total']) . ' 数量: ' . $dephp_7['total'] . ' 总价: ' . $dephp_7['price'] . '); ';
                        $dephp_16 = array('first' => array('value' => $dephp_35, 'color' => '#4a5077'), 'keyword1' => array('title' => '订单号', 'value' => $dephp_2['ordersn'], 'color' => '#4a5077'), 'keyword2' => array('title' => '商品名称', 'value' => $dephp_30, 'color' => '#4a5077'), 'keyword3' => array('title' => '下单时间', 'value' => date('Y-m-d H:i:s', $dephp_2['createtime']), 'color' => '#4a5077'), 'keyword4' => array('title' => '发货时间', 'value' => date('Y-m-d H:i:s', $dephp_2['sendtime']), 'color' => '#4a5077'), 'keyword5' => array('title' => '确认收货时间', 'value' => date('Y-m-d H:i:s', $dephp_2['finishtime']), 'color' => '#4a5077'), 'remark' => array('title' => '', 'value' => $dephp_21, 'color' => '#4a5077'));
                        if (!empty($dephp_13['finish'])){
                            m('message') -> sendTplNotice($dephp_7['noticeopenid'], $dephp_13['finish'], $dephp_16, '', $dephp_26);
                        }else{
                            m('message') -> sendCustomNotice($dephp_7['noticeopenid'], $dephp_16, '', $dephp_26);
                        }
                    }
                }
            }
        }
    }
    public function sendMemberUpgradeMessage($dephp_4 = '', $dephp_36 = null, $dephp_37 = null){
        global $_W, $_GPC;
        $dephp_9 = m('member') -> getMember($dephp_4);
        $dephp_10 = unserialize($dephp_9['noticeset']);
        if (!is_array($dephp_10)){
            $dephp_10 = array();
        }
        $dephp_12 = m('common') -> getSysset('shop');
        $dephp_13 = m('common') -> getSysset('notice');
        $dephp_3 = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=manor_shop&do=member';
        if (strexists($dephp_3, '/addons/manor_shop/')){
            $dephp_3 = str_replace('/addons/manor_shop/', '/', $dephp_3);
        }
        if (strexists($dephp_3, '/core/mobile/order/')){
            $dephp_3 = str_replace('/core/mobile/order/', '/', $dephp_3);
        }
        if (!$dephp_37){
            $dephp_37 = m('member') -> getLevel($dephp_4);
        }
        $dephp_38 = empty($dephp_12['levelname']) ? '普通会员' : $dephp_12['levelname'];
        $dephp_16 = array('first' => array('value' => '亲爱的' . $dephp_9['nickname'] . ', 恭喜您成功升级！', 'color' => '#4a5077'), 'keyword1' => array('title' => '任务名称', 'value' => '会员升级', 'color' => '#4a5077'), 'keyword2' => array('title' => '通知类型', 'value' => '您会员等级从 ' . $dephp_38 . ' 升级为 ' . $dephp_37['levelname'] . ', 特此通知!', 'color' => '#4a5077'), 'remark' => array('value' => '
您即可享有' . $dephp_37['levelname'] . '的专属优惠及服务！', 'color' => '#4a5077'));
        if (!empty($dephp_13['upgrade']) && empty($dephp_10['upgrade'])){
            m('message') -> sendTplNotice($dephp_4, $dephp_13['upgrade'], $dephp_16, $dephp_3);
        }else if (empty($dephp_10['upgrade'])){
            m('message') -> sendCustomNotice($dephp_4, $dephp_16, $dephp_3);
        }
    }
    public function sendMemberLogMessage($dephp_39 = ''){
        global $_W, $_GPC;
        $dephp_40 = pdo_fetch('select * from ' . tablename('manor_shop_member_log') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $dephp_39, ':uniacid' => $_W['uniacid']));
        $dephp_9 = m('member') -> getMember($dephp_40['openid']);
        $dephp_12 = m('common') -> getSysset('shop');
        $dephp_10 = unserialize($dephp_9['noticeset']);
        if (!is_array($dephp_10)){
            $dephp_10 = array();
        }
        $dephp_26 = m('common') -> getAccount();
        if (!$dephp_26){
            return;
        }
        $dephp_13 = m('common') -> getSysset('notice');
        if ($dephp_40['type'] == 0){
            if ($dephp_40['status'] == 1){
                $dephp_41 = '后台充值';
                if ($dephp_40['rechargetype'] == 'wechat'){
                    $dephp_41 = '微信支付';
                }else if ($dephp_40 == 'alipay'){
                    $dephp_41['rechargetype'] = '支付宝';
                }
                $dephp_42 = '¥' . $dephp_40['money'] . '元';
                if($dephp_40['gives'] > 0){
                    $dephp_43 = $dephp_40['money'] + $dephp_40['gives'];
                    $dephp_42 .= '，系统赠送' . $dephp_40['gives'] . '元，合计:' . $dephp_43 . '元';
                }
                $dephp_16 = array('first' => array('value' => '恭喜您充值成功!', 'color' => '#4a5077'), 'money' => array('title' => '充值金额', 'value' => $dephp_42, 'color' => '#4a5077'), 'product' => array('title' => '充值方式', 'value' => $dephp_41, 'color' => '#4a5077'), 'remark' => array('value' => '
谢谢您对我们的支持！', 'color' => '#4a5077'));
                $dephp_3 = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=manor_shop&do=member';
                if (strexists($dephp_3, '/addons/manor_shop/')){
                    $dephp_3 = str_replace('/addons/manor_shop/', '/', $dephp_3);
                }
                if (strexists($dephp_3, '/core/mobile/order/')){
                    $dephp_3 = str_replace('/core/mobile/order/', '/', $dephp_3);
                }
                if (!empty($dephp_13['recharge_ok']) && empty($dephp_10['recharge_ok'])){
                    m('message') -> sendTplNotice($dephp_40['openid'], $dephp_13['recharge_ok'], $dephp_16, $dephp_3);
                }else if (empty($dephp_10['recharge_ok'])){
                    m('message') -> sendCustomNotice($dephp_40['openid'], $dephp_16, $dephp_3);
                }
            }else if ($dephp_40['status'] == 3){
                $dephp_16 = array('first' => array('value' => '充值退款成功!', 'color' => '#4a5077'), 'reason' => array('title' => '退款原因', 'value' => '【' . $dephp_12['name'] . '】充值退款' , 'color' => '#4a5077'), 'refund' => array('title' => '退款金额', 'value' => '¥' . $dephp_40['money'] . '元', 'color' => '#4a5077'), 'remark' => array('value' => '
退款成功，请注意查收! 谢谢您对我们的支持！', 'color' => '#4a5077'));
                $dephp_3 = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=manor_shop&do=member';
                if (strexists($dephp_3, '/addons/manor_shop/')){
                    $dephp_3 = str_replace('/addons/manor_shop/', '/', $dephp_3);
                }
                if (strexists($dephp_3, '/core/mobile/order/')){
                    $dephp_3 = str_replace('/core/mobile/order/', '/', $dephp_3);
                }
                if (!empty($dephp_13['recharge_fund']) && empty($dephp_10['recharge_fund'])){
                    m('message') -> sendTplNotice($dephp_40['openid'], $dephp_13['recharge_fund'], $dephp_16, $dephp_3);
                }else if (empty($dephp_10['recharge_fund'])){
                    m('message') -> sendCustomNotice($dephp_40['openid'], $dephp_16, $dephp_3);
                }
            }
        }else if ($dephp_40['type'] == 1 && $dephp_40['status'] == 0){
            $dephp_16 = array('first' => array('value' => '提现申请已经成功提交!', 'color' => '#4a5077'), 'money' => array('title' => '提现金额', 'value' => '¥' . $dephp_40['money'] . '元', 'color' => '#4a5077'), 'timet' => array('title' => '提现时间', 'value' => date('Y-m-d H:i:s', $dephp_40['createtime']), 'color' => '#4a5077'), 'remark' => array('value' => '
请等待我们的审核并打款！', 'color' => '#4a5077'));
            $dephp_3 = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=manor_shop&do=member&p=log&type=1';
            if (strexists($dephp_3, '/addons/manor_shop/')){
                $dephp_3 = str_replace('/addons/manor_shop/', '/', $dephp_3);
            }
            if (!empty($dephp_13['withdraw']) && empty($dephp_10['withdraw'])){
                m('message') -> sendTplNotice($dephp_40['openid'], $dephp_13['withdraw'], $dephp_16, $dephp_3);
            }else if (empty($dephp_10['withdraw'])){
                m('message') -> sendCustomNotice($dephp_40['openid'], $dephp_16, $dephp_3);
            }
        }else if ($dephp_40['type'] == 1 && $dephp_40['status'] == 1){
            $dephp_16 = array('first' => array('value' => '恭喜您成功提现!', 'color' => '#4a5077'), 'money' => array('title' => '提现金额', 'value' => '¥' . $dephp_40['money'] . '元', 'color' => '#4a5077'), 'timet' => array('title' => '提现时间', 'value' => date('Y-m-d H:i:s', $dephp_40['createtime']), 'color' => '#4a5077'), 'remark' => array('value' => '
感谢您的支持！', 'color' => '#4a5077'));
            $dephp_3 = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=manor_shop&do=member&p=log&type=1';
            if (!empty($dephp_13['withdraw_ok']) && empty($dephp_10['withdraw_ok'])){
                m('message') -> sendTplNotice($dephp_40['openid'], $dephp_13['withdraw_ok'], $dephp_16, $dephp_3);
            }else if (empty($dephp_10['withdraw_ok'])){
                m('message') -> sendCustomNotice($dephp_40['openid'], $dephp_16, $dephp_3);
            }
        }else if ($dephp_40['type'] == 1 && $dephp_40['status'] == -1){
            $dephp_16 = array('first' => array('value' => '抱歉，提现申请审核失败!', 'color' => '#4a5077'), 'money' => array('title' => '提现金额', 'value' => '¥' . $dephp_40['money'] . '元', 'color' => '#4a5077'), 'timet' => array('title' => '提现时间', 'value' => date('Y-m-d H:i:s', $dephp_40['createtime']), 'color' => '#4a5077'), 'remark' => array('value' => '
有疑问请联系客服，谢谢您的支持！', 'color' => '#4a5077'));
            $dephp_3 = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=manor_shop&do=member&p=log&type=1';
            if (strexists($dephp_3, '/addons/manor_shop/')){
                $dephp_3 = str_replace('/addons/manor_shop/', '/', $dephp_3);
            }
            if (strexists($dephp_3, '/core/mobile/order/')){
                $dephp_3 = str_replace('/core/mobile/order/', '/', $dephp_3);
            }
            if (!empty($dephp_13['withdraw_fail']) && empty($dephp_10['withdraw_fail'])){
	            $dephp_16['time'] = $dephp_16['timet'];
	            unset($dephp_16['timet']);
                m('message') -> sendTplNotice($dephp_40['openid'], $dephp_13['withdraw_fail'], $dephp_16, $dephp_3);
            }else if (empty($dephp_10['withdraw_fail'])){
                m('message') -> sendCustomNotice($dephp_40['openid'], $dephp_16, $dephp_3);
            }
        }
    }
}
