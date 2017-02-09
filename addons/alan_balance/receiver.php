<?php
/**
 * 扫码送余额模块订阅器
 *
 * @author alan51
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Alan_balanceModuleReceiver extends WeModuleReceiver {
	public function receive() {
        global $_W;
		$type = $this->message['type'];
		//这里定义此模块进行消息订阅时的, 消息到达以后的具体处理过程, 请查看微擎文档来编写你的代码
        $uniacid = $_W['uniacid'];
        $type = $this->message['type'];
        $message = $this->message;
        $open_id = $message['fromusername'];
        $config = $this->module['config']['setting'];
        //判断是否领取
        if($type == 'subscribe' || $type == 'qr') {
            $user_info = $this->getIoUserInfoByOpenId($open_id);
            $qrcode = pdo_get('alan_scan_balance', array('uniacid' => $uniacid, 'ticket' => $message['ticket']));
            $times = pdo_get('alan_scan_balance_time', array('id'=>$qrcode['time_id']));
            if($qrcode['status'] == 1 && $times['price'] > 0) {
                //送余额500快
                $log = array('uniacid' => $_W['uniacid'], 'logno' => $logno, 'title' => '海尔购买电器送'.$times['price'].'元余额', 'openid' => $open_id, 'type' => 11, 'createtime' => time(), 'status' => 0, 'money'=>$times['price']);
                if(pdo_tableexists('manor_shop_member_log')) {
                    pdo_delete('manor_shop_member_log', array('openid' => $open_id, 'status' => 0, 'type' => 0, 'uniacid' => $_W['uniacid']));
                    $logno = $this->createNO('member_log', 'logno', 'RC');
                    pdo_insert('manor_shop_member_log', $log);
                    $logid = pdo_insertid();
                    $log['id'] = $logid;
                    pdo_update('manor_shop_member_log', array('status' => 1, 'rechargetype' => 'haier'), array('id' => $log['id']));
                }
                $this-> setCredit($open_id, 'credit2', $log['money']);
                $template = array(
                    "touser" => $open_id,
                    "template_id" => $config['get_amount_template_id'],
                    "url" => $_W['siteroot'] . 'app/index.php?i='.$_W['acid'].'&c=entry&do=member&m=manor_shop',
                    "topcolor" => "#FF0000",
                    "data" => array(
                        'keyword1' => array(
                            'value' => urlencode('恭喜您,成功获得'.$times['price'].'元'),
                            'color' => "#743A3A"
                        ),
                        'keyword2' => array(
                            'value' => urlencode('海尔合作'),
                            'color' => '#000000'
                        ),
                        'remark' => array(
                            'value' => urlencode('请前往个人中心查看'),
                            'color' => "#008000"
                        )
                    )
                );
                //设置码失效
                pdo_update('alan_scan_balance', array('status'=>3, 'open_id'=>$open_id, 'nickname'=>$user_info['nickname'], 'avarstar'=>$user_info['headimgurl'], 'user_data'=>json_encode($user_info), 'used_time'=>time()), array('id'=>$qrcode['id']));
                //jian少数量
                pdo_update('alan_scan_balance_time', array('valid_num -='=>1), array('id'=>$qrcode['time_id']));
                pdo_update('alan_scan_balance_time', array('failure_num +='=>1), array('id'=>$qrcode['time_id']));
                $this->send_template_message(urldecode(json_encode($template)));
            } else {
                //return $this->receive()
            }
        }

	}

    public function getIoUserInfoByOpenId ($openId){
        load()->model('account');
        load()->func('communication');
        $access_token = WeAccount::token();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openId.'&lang=zh_CN';
        $result = ihttp_get($url);
        if($result['code'] == 200) {
            $content = json_decode($result['content'], true);
            if($content && $content['openid']) {
                return $content;
            }
        }
        return false;
    }

    public function send_template_message($data)
    {
        global $_W, $_GPC;
        $atype        = 'weixin';
        $account_code = "account_weixin_code";
        load()->classs('weixin.account');
        $access_token = WeAccount::token();
        $url          = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $access_token;
        $response     = ihttp_request($url, $data);
        if (is_error($response)) {
            return error(-1, "访问公众平台接口失败, 错误: {$response['message']}");
        }
        $result = @json_decode($response['content'], true);
        if (empty($result)) {
            return error(-1, "接口调用失败, 原数据: {$response['meta']}");
        } elseif (!empty($result['errcode'])) {
            return error(-1, "访问微信接口错误, 错误代码: {$result['errcode']}, 错误信息: {$result['errmsg']},信息详情：{$this->error_code($result['errcode'])}");
        }
        return true;
    }
    public function createNO($table, $field, $prefix){
        $billno = date('YmdHis') . random(6, true);
        while (1){
            $count = pdo_fetchcolumn('select count(*) from ' . tablename('manor_shop_' . $table) . " where {$field}=:billno limit 1", array(':billno' => $billno));
            if ($count <= 0){
                break;
            }
            $billno = date('YmdHis') . random(6, true);
        }
        return $prefix . $billno;
    }
    public function getCredit($openid = '', $credittype = 'credit1'){
        global $_W;
        load() -> model('mc');
        $uid = mc_openid2uid($openid);
        if (!empty($uid)){
            return pdo_fetchcolumn("SELECT {$credittype} FROM " . tablename('mc_members') . " WHERE `uid` = :uid", array(':uid' => $uid));
        }else{
            return pdo_fetchcolumn("SELECT {$credittype} FROM " . tablename('manor_shop_member') . " WHERE  openid=:openid and uniacid=:uniacid limit 1", array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
        }
    }

    public function setCredit($openid = '', $credittype = 'credit1', $credits = 0, $log = array()){
        global $_W;
        load() -> model('mc');
        $uid = mc_openid2uid($openid);
        if (!empty($uid)){
            $value = pdo_fetchcolumn("SELECT {$credittype} FROM " . tablename('mc_members') . " WHERE `uid` = :uid", array(':uid' => $uid));
            $newcredit = $credits + $value;
            if ($newcredit <= 0){
                $newcredit = 0;
            }
            pdo_update('mc_members', array($credittype => $newcredit), array('uid' => $uid));
            if (empty($log) || !is_array($log)){
                $log = array($uid, '未记录');
            }
            $data = array('uid' => $uid, 'credittype' => $credittype, 'uniacid' => $_W['uniacid'], 'num' => $credits, 'createtime' => TIMESTAMP, 'operator' => intval($log[0]), 'remark' => $log[1],);
            pdo_insert('mc_credits_record', $data);
        }else{
            if(!pdo_tableexists('manor_shop_member')) {
                return '';
            }
            $value = pdo_fetchcolumn("SELECT {$credittype} FROM " . tablename('manor_shop_member') . " WHERE  uniacid=:uniacid and openid=:openid limit 1", array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
            $newcredit = $credits + $value;
            if ($newcredit <= 0){
                $newcredit = 0;
            }
            pdo_update('manor_shop_member', array($credittype => $newcredit), array('uniacid' => $_W['uniacid'], 'openid' => $openid));
        }
    }

}