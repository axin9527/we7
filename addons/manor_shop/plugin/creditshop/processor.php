<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
require IA_ROOT . '/addons/manor_shop/defines.php';
require manor_shop_INC . 'plugin/plugin_processor.php';
class CreditshopProcessor extends PluginProcessor{
    public function __construct(){
        parent :: __construct('creditshop');
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
            if (!$dephp_0 -> inContext){
                $dephp_0 -> beginContext();
                return $dephp_0 -> respText('请输入兑换码:');
            }else if ($dephp_0 -> inContext && is_numeric($dephp_3)){
                $dephp_7 = pdo_fetch('select * from ' . tablename('manor_shop_creditshop_log') . ' where eno=:eno and uniacid=:uniacid  limit 1', array(':eno' => $dephp_3, ':uniacid' => $_W['uniacid']));
                if (empty($dephp_7)){
                    return $dephp_0 -> respText('未找到要兑换码,请重新输入!');
                }
                $dephp_8 = $dephp_7['id'];
                if (empty($dephp_7)){
                    return $dephp_0 -> respText('未找到要兑换码,请重新输入!');
                }
                if (empty($dephp_7['status'])){
                    return $dephp_0 -> respText('无效兑换记录!');
                }
                if ($dephp_7['status'] >= 3){
                    return $dephp_0 -> respText('此记录已兑换过了!');
                }
                $dephp_9 = m('member') -> getMember($dephp_7['openid']);
                $dephp_10 = $this -> model -> getGoods($dephp_7['goodsid'], $dephp_9);
                if (empty($dephp_10['id'])){
                    return $dephp_0 -> respText('商品记录不存在!');
                }
                if (empty($dephp_10['isverify'])){
                    $dephp_0 -> endContext();
                    return $dephp_0 -> respText('此商品不支持线下兑换!');
                }
                if (!empty($dephp_10['type'])){
                    if ($dephp_7['status'] <= 1){
                        return $dephp_0 -> respText('未中奖，不能兑换!');
                    }
                }
                if ($dephp_10['money'] > 0 && empty($dephp_7['paystatus'])){
                    return $dephp_0 -> respText('未支付，无法进行兑换!');
                }
                if ($dephp_10['dispatch'] > 0 && empty($dephp_7['dispatchstatus'])){
                    return $dephp_0 -> respText('未支付运费，无法进行兑换!');
                }
                $dephp_11 = explode(',', $dephp_10['storeids']);
                if (!empty($dephp_12)){
                    if (!empty($dephp_6['storeid'])){
                        if (!in_array($dephp_6['storeid'], $dephp_12)){
                            return $dephp_0 -> respText('您无此门店的兑换权限!');
                        }
                    }
                }
                $dephp_13 = time();
                pdo_update('manor_shop_creditshop_log', array('status' => 3, 'usetime' => $dephp_13, 'verifyopenid' => $dephp_2), array('id' => $dephp_7['id']));
                $this -> model -> sendMessage($dephp_8);
                $dephp_0 -> endContext();
                return $dephp_0 -> respText('兑换成功!');
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
