<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
require IA_ROOT . '/addons/manor_shop/defines.php';
require manor_shop_INC . 'plugin/plugin_processor.php';
class CouponProcessor extends PluginProcessor{
    public function __construct(){
        parent :: __construct('coupon');
    }
    public function respond($dephp_0 = null){
        global $_W;
        $dephp_1 = $dephp_0 -> message;
        $dephp_2 = $dephp_0 -> message['content'];
        $dephp_3 = strtolower($dephp_1['msgtype']);
        $dephp_4 = strtolower($dephp_1['event']);
        if ($dephp_3 == 'text' || $dephp_4 == 'click'){
            return $this -> respondText($dephp_0);
        }
        return $this -> responseEmpty();
    }
    private function responseEmpty(){
        ob_clean();
        ob_start();
        echo '';
        ob_flush();
        ob_end_flush();
        exit(0);
    }
    function replaceCoupon($dephp_5, $dephp_6, $dephp_7, $dephp_8){
        $dephp_9 = array('pwdask' => '请输入优惠券口令: ', 'pwdfail' => '很抱歉，您猜错啦，继续猜~', 'pwdsuc' => '恭喜你，猜中啦！优惠券已发到您账户了! ', 'pwdfull' => '很抱歉，您已经没有机会啦~ ', 'pwdown' => '您已经参加过啦,等待下次活动吧~', 'pwdexit' => '0', 'pwdexitstr' => '好的，等待您下次来玩!');
        foreach ($dephp_9 as $dephp_10 => $dephp_11){
            if (empty($dephp_5[$dephp_10])){
                $dephp_5[$dephp_10] = $dephp_11;
            }else{
                $dephp_5[$dephp_10] = str_replace('[nickname]', $dephp_6['nickname'], $dephp_5[$dephp_10]);
                $dephp_5[$dephp_10] = str_replace('[couponname]', $dephp_5['couponname'], $dephp_5[$dephp_10]);
                $dephp_5[$dephp_10] = str_replace('[times]', $dephp_7, $dephp_5[$dephp_10]);
                $dephp_5[$dephp_10] = str_replace('[lasttimes]', $dephp_8, $dephp_5[$dephp_10]);
            }
        }
        return $dephp_5;
    }
    function getGuess($dephp_5, $dephp_12){
        global $_W;
        $dephp_8 = 1;
        $dephp_7 = 0;
        $dephp_13 = pdo_fetch('select id,times from ' . tablename('manor_shop_coupon_guess') . ' where couponid=:couponid and openid=:openid and pwdkey=:pwdkey and uniacid=:uniacid limit 1 ' , array(':couponid' => $dephp_5['id'], ':openid' => $dephp_12, ':pwdkey' => $dephp_5['pwdkey'], ':uniacid' => $_W['uniacid']));
        if ($dephp_5['pwdtimes'] > 0){
            $dephp_7 = $dephp_13['times'];
            $dephp_8 = $dephp_5['pwdtimes'] - intval($dephp_7);
            if ($dephp_8 <= 0){
                $dephp_8 = 0;
            }
        }
        return array('times' => $dephp_7, 'lasttimes' => $dephp_8);
    }
    function respondText($dephp_0){
        global $_W;
        @session_start();
        $dephp_2 = $dephp_0 -> message['content'];
        $dephp_12 = $dephp_0 -> message['from'];
        $dephp_6 = m('member') -> getMember($dephp_12);
        $dephp_14 = $dephp_2;
        if (isset($_SESSION['manor_shop_coupon_key'])){
            $dephp_14 = $_SESSION['manor_shop_coupon_key'];
        }else{
            $_SESSION['manor_shop_coupon_key'] = $dephp_2;
        }
        $dephp_5 = pdo_fetch('select id,couponname,pwdkey,pwdask,pwdsuc,pwdfail,pwdfull,pwdtimes,pwdurl,pwdwords,pwdown,pwdexit,pwdexitstr from ' . tablename('manor_shop_coupon') . ' where pwdkey=:pwdkey and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':pwdkey' => $dephp_14));
        $dephp_15 = explode(',', $dephp_5['pwdwords']);
        if (empty($dephp_5)){
            $dephp_0 -> endContext();
            unset($_SESSION['manor_shop_coupon_key']);
            return $this -> responseEmpty();
        }
        if (!$dephp_0 -> inContext){
            $dephp_16 = pdo_fetch('select id,times from ' . tablename('manor_shop_coupon_guess') . ' where couponid=:couponid and openid=:openid and pwdkey=:pwdkey and ok=1 and uniacid=:uniacid limit 1 ' , array(':couponid' => $dephp_5['id'], ':openid' => $dephp_12, ':pwdkey' => $dephp_5['pwdkey'], ':uniacid' => $_W['uniacid']));
            if (!empty($dephp_16)){
                $dephp_13 = $this -> getGuess($dephp_5, $dephp_12);
                $dephp_5 = $this -> replaceCoupon($dephp_5, $dephp_6, $dephp_13['times'], $dephp_13['lasttimes']);
                $dephp_0 -> endContext();
                unset($_SESSION['manor_shop_coupon_key']);
                return $dephp_0 -> respText($dephp_5['pwdown']);
            }
            $dephp_13 = $this -> getGuess($dephp_5, $dephp_12);
            $dephp_5 = $this -> replaceCoupon($dephp_5, $dephp_6, $dephp_13['times'], $dephp_13['lasttimes']);
            if ($dephp_13['lasttimes'] <= 0){
                $dephp_0 -> endContext();
                unset($_SESSION['manor_shop_coupon_key']);
                return $dephp_0 -> respText($dephp_5['pwdfull']);
            }
            $dephp_0 -> beginContext();
            return $dephp_0 -> respText($dephp_5['pwdask']);
        }else{
            if ($dephp_2 == $dephp_5['pwdexit']){
                unset($_SESSION['manor_shop_coupon_key']);
                $dephp_0 -> endContext();
                $dephp_13 = $this -> getGuess($dephp_5, $dephp_12);
                $dephp_5 = $this -> replaceCoupon($dephp_5, $dephp_6, $dephp_13['times'], $dephp_13['lasttimes']);
                return $dephp_0 -> respText($dephp_5['pwdexitstr']);
            }
            $dephp_13 = pdo_fetch('select id,times from ' . tablename('manor_shop_coupon_guess') . ' where couponid=:couponid and openid=:openid and pwdkey=:pwdkey and uniacid=:uniacid limit 1 ', array(':couponid' => $dephp_5['id'], ':openid' => $dephp_12, ':pwdkey' => $dephp_5['pwdkey'], ':uniacid' => $_W['uniacid']));
            $dephp_17 = in_array($dephp_2, $dephp_15);
            if (empty($dephp_13)){
                $dephp_13 = array('uniacid' => $_W['uniacid'], 'couponid' => $dephp_5['id'], 'openid' => $dephp_12, 'times' => 1, 'pwdkey' => $dephp_5['pwdkey'], 'ok' => $dephp_17 ? 1 : 0);
                pdo_insert('manor_shop_coupon_guess', $dephp_13);
            }else{
                pdo_update('manor_shop_coupon_guess', array('times' => $dephp_13['times'] + 1, 'ok' => $dephp_17 ? 1 : 0), array('id' => $dephp_13['id']));
            }
            $dephp_18 = time();
            if ($dephp_17){
                $dephp_19 = array('uniacid' => $_W['uniacid'], 'openid' => $dephp_12, 'logno' => m('common') -> createNO('coupon_log', 'logno', 'CC'), 'couponid' => $dephp_5['id'], 'status' => 1, 'paystatus' => -1, 'creditstatus' => -1, 'createtime' => $dephp_18, 'getfrom' => 5);
                pdo_insert('manor_shop_coupon_log', $dephp_19);
                $dephp_20 = array('uniacid' => $_W['uniacid'], 'openid' => $dephp_12, 'couponid' => $dephp_5['id'], 'gettype' => 5, 'gettime' => $dephp_18);
                pdo_insert('manor_shop_coupon_data', $dephp_20);
                unset($_SESSION['manor_shop_coupon_key']);
                $dephp_0 -> endContext();
                $dephp_21 = $this -> model -> getSet();
                $dephp_22 = $this -> model -> getCoupon($dephp_5['id']);
                $this -> model -> sendMessage($dephp_22, 1, $dephp_6, $dephp_21['templateid']);
                $dephp_13 = $this -> getGuess($dephp_5, $dephp_12);
                $dephp_5 = $this -> replaceCoupon($dephp_5, $dephp_6, $dephp_13['times'], $dephp_13['lasttimes']);
                return $dephp_0 -> respText($dephp_5['pwdsuc']);
            }else{
                $dephp_13 = $this -> getGuess($dephp_5, $dephp_12);
                $dephp_5 = $this -> replaceCoupon($dephp_5, $dephp_6, $dephp_13['times'], $dephp_13['lasttimes']);
                if ($dephp_13['lasttimes'] <= 0){
                    $dephp_0 -> endContext();
                    unset($_SESSION['manor_shop_coupon_key']);
                    return $dephp_0 -> respText($dephp_5['pwdfull']);
                }
                return $dephp_0 -> respText($dephp_5['pwdfail']);
            }
        }
    }
}
