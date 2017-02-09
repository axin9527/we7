<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
require IA_ROOT . '/addons/manor_shop/defines.php';
require manor_shop_INC . 'plugin/plugin_processor.php';
class VerifyProcessor extends PluginProcessor{
    public function __construct(){
        parent :: __construct('verify');
    }
    public function respond($dephp_0 = null){
        global $_W;
        $dephp_1 = $dephp_0 -> message;
        $dephp_2 = $dephp_0 -> message['from'];
        $dephp_3 = $dephp_0 -> message['content'];
        $dephp_4 = strtolower($dephp_1['msgtype']);
        $dephp_5 = strtolower($dephp_1['event']);
        if ($dephp_4 == 'text' || $dephp_5 == 'click'){
            $dephp_6 = pdo_fetch('select * from ' . tablename('manor_shop_saler') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $dephp_2));
            if (empty($dephp_6)){
                return $this -> responseEmpty();
            }
            $dephp_7 = m('common') -> getSysset('trade');
            if (!$dephp_0 -> inContext){
                $dephp_0 -> beginContext();
                return $dephp_0 -> respText('请输入订单消费码:');
            }else if ($dephp_0 -> inContext && is_numeric($dephp_3)){
                $dephp_8 = pdo_fetch('select * from ' . tablename('manor_shop_order') . ' where verifycode=:verifycode and uniacid=:uniacid  limit 1', array(':verifycode' => $dephp_3, ':uniacid' => $_W['uniacid']));
                if (empty($dephp_8)){
                    return $dephp_0 -> respText('未找到要核销的订单,请重新输入!');
                }
                $dephp_9 = $dephp_8['id'];
                if (empty($dephp_8['isverify'])){
                    $dephp_0 -> endContext();
                    return $dephp_0 -> respText('订单无需核销!');
                }
                if (!empty($dephp_8['verified'])){
                    $dephp_0 -> endContext();
                    return $dephp_0 -> respText('此订单已核销，无需重复核销!');
                }
                if ($dephp_8['status'] != 1){
                    $dephp_0 -> endContext();
                    return $dephp_0 -> respText('订单未付款，无法核销!');
                }
                $dephp_10 = array();
                $dephp_11 = pdo_fetchall('select og.goodsid,og.price,g.title,g.thumb,og.total,g.credit,og.optionid,g.isverify,g.storeids from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join ' . tablename('manor_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.orderid=:orderid and og.uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $dephp_8['id']));
                foreach ($dephp_11 as $dephp_12){
                    if (!empty($dephp_12['storeids'])){
                        $dephp_10 = array_merge(explode(',', $dephp_12['storeids']), $dephp_10);
                    }
                }
                if (!empty($dephp_10)){
                    if (!empty($dephp_6['storeid'])){
                        if (!in_array($dephp_6['storeid'], $dephp_10)){
                            return $dephp_0 -> respText('您无此门店的核销权限!');
                        }
                    }
                }
                $dephp_13 = time();
                pdo_update('manor_shop_order', array('status' => 3, 'sendtime' => $dephp_13, 'finishtime' => $dephp_13, 'verifytime' => $dephp_13, 'verified' => 1, 'verifyopenid' => $dephp_2, 'verifystoreid' => $dephp_6['storeid']), array('id' => $dephp_8['id']));
                m('notice') -> sendOrderMessage($dephp_9);
                if (p('commission')){
                    p('commission') -> checkOrderFinish($dephp_9);
                }
                $dephp_0 -> endContext();
                return $dephp_0 -> respText('核销成功!');
            }
        }
    }
    private function responseEmpty(){
        ob_clean();
        ob_start();
        echo '';
        ob_flush();
        ob_end_flush();
        exit(0);
    }
}
