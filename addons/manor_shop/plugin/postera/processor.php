<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
require IA_ROOT . '/addons/manor_shop/defines.php';
require manor_shop_INC . 'plugin/plugin_processor.php';
class PosteraProcessor extends PluginProcessor{
    public function __construct(){
        parent :: __construct('postera');
    }
    public function respond($dephp_0 = null){
        global $_W;
        $dephp_1 = $dephp_0 -> message;
        $dephp_2 = strtolower($dephp_1['msgtype']);
        $dephp_3 = strtolower($dephp_1['event']);
        $dephp_0 -> member = $this -> model -> checkMember($dephp_1['from']);
        if ($dephp_2 == 'text' || $dephp_3 == 'click'){
            return $this -> responseText($dephp_0);
        }else if ($dephp_2 == 'event'){
            if ($dephp_3 == 'scan'){
                return $this -> responseScan($dephp_0);
            }else if ($dephp_3 == 'subscribe'){
                return $this -> responseSubscribe($dephp_0);
            }
        }
    }
    private function responseText($dephp_0){
        global $_W;
        $dephp_4 = 4;
        load() -> func('communication');
        $dephp_5 = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=manor_shop&do=plugin&p=postera&method=build&timestamp=' . time();
        $dephp_6 = ihttp_request($dephp_5, array('openid' => $dephp_0 -> message['from'], 'content' => urlencode($dephp_0 -> message['content'])), array(), $dephp_4);
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
    private function responseDefault($dephp_0){
        global $_W;
        return $dephp_0 -> respText('感谢您的关注!');
    }
    private function responseScan($dephp_0){
        global $_W;
        $dephp_7 = $dephp_0 -> message['from'];
        $dephp_8 = $dephp_0 -> message['eventkey'];
        $dephp_9 = $dephp_0 -> message['ticket'];
        if (empty($dephp_9)){
            return $this -> responseDefault($dephp_0);
        }
        $dephp_10 = $this -> model -> getQRByTicket($dephp_9);
        if (empty($dephp_10)){
            return $this -> responseDefault($dephp_0);
        }
        $dephp_11 = pdo_fetch('select * from ' . tablename('manor_shop_postera') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $dephp_10['posterid'], ':uniacid' => $_W['uniacid']));
        if (empty($dephp_11)){
            return $this -> responseDefault($dephp_0);
        }
        $dephp_12 = m('member') -> getMember($dephp_10['openid']);
        $this -> commission($dephp_11, $dephp_0 -> member, $dephp_12);
        $dephp_5 = trim($dephp_11['respurl']);
        if (empty($dephp_5)){
            if ($dephp_12['isagent'] == 1 && $dephp_12['status'] == 1){
                $dephp_5 = $_W['siteroot'] . "app/index.php?i={$_W['uniacid']}&c=entry&m=manor_shop&do=plugin&p=commission&method=myshop&mid=" . $dephp_12['id'];
            }else{
                $dephp_5 = $_W['siteroot'] . "app/index.php?i={$_W['uniacid']}&c=entry&m=manor_shop&do=shop&mid=" . $dephp_12['id'];
            }
        }
        if ($dephp_11['resptype'] == '0'){
            if(!empty($dephp_11['resptitle'])){
                $dephp_13 = array(array('title' => $dephp_11['resptitle'], 'description' => $dephp_11['respdesc'], 'picurl' => tomedia($dephp_11['respthumb']), 'url' => $dephp_5));
                return $dephp_0 -> respNews($dephp_13);
            }
        }
        if ($dephp_11['resptype'] == '1'){
            if(!empty($dephp_11['resptext'])){
                return $dephp_0 -> respText($dephp_11['resptext']);
            }
        }
        return $this -> responseEmpty();
    }
    private function responseSubscribe($dephp_0){
        global $_W;
        $dephp_7 = $dephp_0 -> message['from'];
        $dephp_14 = explode('_', $dephp_0 -> message['eventkey']);
        $dephp_8 = isset($dephp_14[1]) ? $dephp_14[1] : '';
        $dephp_9 = $dephp_0 -> message['ticket'];
        $dephp_15 = $dephp_0 -> member;
        if (empty($dephp_9)){
            return $this -> responseDefault($dephp_0);
        }
        $dephp_10 = $this -> model -> getQRByTicket($dephp_9);
        if (empty($dephp_10)){
            return $this -> responseDefault($dephp_0);
        }
        $dephp_11 = pdo_fetch('select * from ' . tablename('manor_shop_postera') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $dephp_10['posterid'], ':uniacid' => $_W['uniacid']));
        if (empty($dephp_11)){
            return $this -> responseDefault($dephp_0);
        }
        $dephp_12 = m('member') -> getMember($dephp_10['openid']);
        $dephp_16 = pdo_fetch('select * from ' . tablename('manor_shop_postera_log') . ' where openid=:openid and posterid=:posterid and uniacid=:uniacid limit 1', array(':openid' => $dephp_7, ':posterid' => $dephp_11['id'], ':uniacid' => $_W['uniacid']));
        if (empty($dephp_16) && $dephp_7 != $dephp_10['openid']){
            $dephp_16 = array('uniacid' => $_W['uniacid'], 'posterid' => $dephp_11['id'], 'openid' => $dephp_7, 'from_openid' => $dephp_10['openid'], 'subcredit' => $dephp_11['subcredit'], 'submoney' => $dephp_11['submoney'], 'reccredit' => $dephp_11['reccredit'], 'recmoney' => $dephp_11['recmoney'], 'createtime' => time());
            pdo_insert('manor_shop_postera_log', $dephp_16);
            $dephp_16['id'] = pdo_insertid();
            $dephp_17 = $dephp_11['subpaycontent'];
            if (empty($dephp_17)){
                $dephp_17 = '您通过 [nickname] 的推广二维码扫码关注的奖励';
            }
            $dephp_17 = str_replace('[nickname]', $dephp_12['nickname'], $dephp_17);
            $dephp_18 = $dephp_11['recpaycontent'];
            if (empty($dephp_18)){
                $dephp_18 = '推荐 [nickname] 扫码关注的奖励';
            }
            $dephp_18 = str_replace('[nickname]', $dephp_15['nickname'], $dephp_17);
            if ($dephp_11['subcredit'] > 0){
                m('member') -> setCredit($dephp_7, 'credit1', $dephp_11['subcredit'], array(0, '扫码关注积分+' . $dephp_11['subcredit']));
            }
            if ($dephp_11['submoney'] > 0){
                $dephp_19 = $dephp_11['submoney'];
                if ($dephp_11['paytype'] == 1){
                    $dephp_19 *= 100;
                }
                m('finance') -> pay($dephp_7, $dephp_11['paytype'], $dephp_19, '', $dephp_17);
            }
            if ($dephp_11['reccredit'] > 0){
                m('member') -> setCredit($dephp_10['openid'], 'credit1', $dephp_11['reccredit'], array(0, '推荐扫码关注积分+' . $dephp_11['reccredit']));
            }
            if ($dephp_11['recmoney'] > 0){
                $dephp_19 = $dephp_11['recmoney'];
                if ($dephp_11['paytype'] == 1){
                    $dephp_19 *= 100;
                }
                m('finance') -> pay($dephp_10['openid'], $dephp_11['paytype'], $dephp_19, '', $dephp_18);
            }
            $dephp_20 = false;
            $dephp_21 = false;
            $dephp_22 = p('coupon');
            if($dephp_22){
                if(!empty($dephp_11['reccouponid']) && $dephp_11['reccouponnum'] > 0){
                    $dephp_23 = $dephp_22 -> getCoupon($dephp_11['reccouponid']);
                    if(!empty($dephp_23)){
                        $dephp_20 = true;
                    }
                }
                if(!empty($dephp_11['subcouponid']) && $dephp_11['subcouponnum'] > 0){
                    $dephp_24 = $dephp_22 -> getCoupon($dephp_11['subcouponid']);
                    if(!empty($dephp_24)){
                        $dephp_21 = true;
                    }
                }
            }
            if (!empty($dephp_11['subtext'])){
                $dephp_25 = $dephp_11['subtext'];
                $dephp_25 = str_replace('[nickname]', $dephp_15['nickname'], $dephp_25);
                $dephp_25 = str_replace('[credit]', $dephp_11['reccredit'], $dephp_25);
                $dephp_25 = str_replace('[money]', $dephp_11['recmoney'], $dephp_25);
                if($dephp_23){
                    $dephp_25 = str_replace('[couponname]', $dephp_23['couponname'], $dephp_25);
                    $dephp_25 = str_replace('[couponnum]', $dephp_11['reccouponnum'], $dephp_25);
                }
                if (!empty($dephp_11['templateid'])){
                    m('message') -> sendTplNotice($dephp_10['openid'], $dephp_11['templateid'], array('first' => array('value' => '推荐关注奖励到账通知', 'color' => '#4a5077'), 'keyword1' => array('value' => '推荐奖励', 'color' => '#4a5077'), 'keyword2' => array('value' => $dephp_25, 'color' => '#4a5077'), 'remark' => array('value' => '
谢谢您对我们的支持！', 'color' => '#4a5077'),), '');
                }else{
                    m('message') -> sendCustomNotice($dephp_10['openid'], $dephp_25);
                }
            }
            if (!empty($dephp_11['entrytext'])){
                $dephp_26 = $dephp_11['entrytext'];
                $dephp_26 = str_replace('[nickname]', $dephp_12['nickname'], $dephp_26);
                $dephp_26 = str_replace('[credit]', $dephp_11['subcredit'], $dephp_26);
                $dephp_26 = str_replace('[money]', $dephp_11['submoney'], $dephp_26);
                if($dephp_24){
                    $dephp_26 = str_replace('[couponname]', $dephp_24['couponname'], $dephp_26);
                    $dephp_26 = str_replace('[couponnum]', $dephp_11['subcouponnum'], $dephp_26);
                }
                if (!empty($dephp_11['templateid'])){
                    m('message') -> sendTplNotice($dephp_7, $dephp_11['templateid'], array('first' => array('value' => '关注奖励到账通知', 'color' => '#4a5077'), 'keyword1' => array('value' => '关注奖励', 'color' => '#4a5077'), 'keyword2' => array('value' => $dephp_26, 'color' => '#4a5077'), 'remark' => array('value' => '
谢谢您对我们的支持！', 'color' => '#4a5077'),), '');
                }else{
                    m('message') -> sendCustomNotice($dephp_7, $dephp_26);
                }
            }
            $dephp_27 = array();
            if($dephp_20){
                $dephp_27['reccouponid'] = $dephp_11['reccouponid'];
                $dephp_27['reccouponnum'] = $dephp_11['reccouponnum'];
                $dephp_22 -> poster($dephp_12, $dephp_11['reccouponid'], $dephp_11['reccouponnum']);
            }
            if($dephp_21){
                $dephp_27['subcouponid'] = $dephp_11['subcouponid'];
                $dephp_27['subcouponnum'] = $dephp_11['subcouponnum'];
                $dephp_22 -> poster($dephp_15, $dephp_11['subcouponid'], $dephp_11['subcouponnum']);
            }
            if(!empty($dephp_27)){
                pdo_update('manor_shop_postera_log', $dephp_27, array('id' => $dephp_16['id']));
            }
        }
        $this -> commission($dephp_11, $dephp_15, $dephp_12);
        $dephp_5 = trim($dephp_11['respurl']);
        if (empty($dephp_5)){
            if ($dephp_12['isagent'] == 1 && $dephp_12['status'] == 1){
                $dephp_5 = $_W['siteroot'] . "app/index.php?i={$_W['uniacid']}&c=entry&m=manor_shop&do=plugin&p=commission&method=myshop&mid=" . $dephp_12['id'];
            }else{
                $dephp_5 = $_W['siteroot'] . "app/index.php?i={$_W['uniacid']}&c=entry&m=manor_shop&do=shop&mid=" . $dephp_12['id'];
            }
        }
        if ($dephp_11['resptype'] == '0'){
            if(!empty($dephp_11['resptitle'])){
                $dephp_13 = array(array('title' => $dephp_11['resptitle'], 'description' => $dephp_11['respdesc'], 'picurl' => tomedia($dephp_11['respthumb']), 'url' => $dephp_5));
                return $dephp_0 -> respNews($dephp_13);
            }
        }
        if ($dephp_11['resptype'] == '1'){
            if(!empty($dephp_11['resptext'])){
                return $dephp_0 -> respText($dephp_11['resptext']);
            }
        }
        return $this -> responseEmpty();
    }
    private function commission($dephp_11, $dephp_15, $dephp_12){
        $dephp_28 = time();
        $dephp_29 = p('commission');
        if ($dephp_29){
            $dephp_30 = $dephp_29 -> getSet();
            if (!empty($dephp_30)){
                if ($dephp_15['isagent'] != 1){
                    if ($dephp_12['isagent'] == 1 && $dephp_12['status'] == 1){
                        if (!empty($dephp_11['bedown'])){
                            if (empty($dephp_15['agentid'])){
                                if(empty($dephp_15['fixagentid'])){
                                    pdo_update('manor_shop_member', array('agentid' => $dephp_12['id'], 'childtime' => $dephp_28), array('id' => $dephp_15['id']));
                                    $dephp_15['agentid'] = $dephp_12['id'];
                                    $dephp_29 -> sendMessage($dephp_12['openid'], array('nickname' => $dephp_15['nickname'], 'childtime' => $dephp_28), TM_COMMISSION_AGENT_NEW);
                                    $dephp_29 -> upgradeLevelByAgent($dephp_12['id']);
                                }
                            }
                            if (!empty($dephp_11['beagent'])){
                                $dephp_31 = intval($dephp_30['become_check']);
                                pdo_update('manor_shop_member', array('isagent' => 1, 'status' => $dephp_31, 'agenttime' => $dephp_28), array('id' => $dephp_15['id']));
                                if ($dephp_31 == 1){
                                    $dephp_29 -> sendMessage($dephp_15['openid'], array('nickname' => $dephp_15['nickname'], 'agenttime' => $dephp_28), TM_COMMISSION_BECOME);
                                    $dephp_29 -> upgradeLevelByAgent($dephp_12['id']);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
