<?php
/**
 * 场景二维码模块订阅器
 *
 * @author 诗意的边缘
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
class Alan_qrcodeModuleReceiver extends WeModuleReceiver {
	public function receive() {
	    global $_W;
        $uniacid = $_W['uniacid'];
		$type = $this->message['type'];
        $message = $this->message;
        $open_id = $message['fromusername'];
        $table_user_info = pdo_get('alan_qrcode_fans', array('uniacid'=>$uniacid, 'openid'=>$open_id));
        if($message['ticket']) {
            $qrcode = pdo_get('alan_qrcode', array('uniacid' => $uniacid, 'ticket' => $message['ticket']));
        } else {
            $wx = json_decode($table_user_info['wx_data'], true);
            $qrcode = pdo_get('alan_qrcode', array('uniacid' => $uniacid, 'ticket' => $wx['wx_ticket']['ticket']));
        }
        if(!$qrcode) {
            return '';
        }
        if($table_user_info) {
            if($type == 'unsubscribe') {
                pdo_update('alan_qrcode_fans', array('cancel_sub_time'=>$message['time']), array('id'=>$table_user_info['id']));
            }
            $user_info = json_decode($table_user_info['wx_data'], true);
            $user_info['nickname'] = $table_user_info['nickname'];
        } else if($message['ticket']){
            $user_info = $this->getIoUserInfoByOpenId($open_id);
            $this->create_qrcode_fans($user_info, $qrcode, $message);
        }
        if(!$user_info) {
            //todo::做记录
            return ;
        }
        //创建记录
        $this->create_qrcode_record($user_info, $qrcode, $message);
        if($type == 'subscribe') {
            if(pdo_tableexists('alan_qrcode_cash_record')) {
                $this->operation_cash($user_info,$qrcode['id']);
            }
        } elseif ($type == 'unsubscribe') {
            if(pdo_tableexists('alan_qrcode_cash_record')) {
                $this->operation_cash($user_info,$qrcode['id'], '-');
            }
        }
        //更新用户信息
        $this->update_fans($open_id, $message['type'], $message['time']);
        //统计
        $this->qrcode_stat($qrcode, $message, $user_info);
        //发送优惠卷 // 正式 amxEMC8aqjH
        if (strpos($_W['siteroot'], 'fresh') !== false) {
            //线上
            if($message['scene'] == 'amxEMC8aqjH') {
                $send_coupon = true;
            }
        } elseif($message['scene'] == 'IhnYNNfVDYX') {
            $send_coupon = true;
        } else {
            $send_coupon = false;
        }
        if($send_coupon) {
            if ($type == 'subscribe' && $message['ticket']) {
                //$send_ret = $this->send_cate_coupon($open_id);
                $send_ret = '111';
            }
            if ($message['ticket'] && !is_array($send_ret)) {
                $template = array(
                    "touser" => $open_id,
                    "template_id" => '1vdBWjz_5SU14HPWn3jKrfVRBELK_e_LjoKvKMszwok',
                    "url" => 'http://fresh.tangshengmanor.com/app/index.php?i=2&c=entry&p=activity&id=5&op=detail&do=shop&m=manor_shop',
                    "topcolor" => "#FF0000",
                    "data" => array(
                        'first' => array(
                            'value' => $user_info['nickname'] . ' ，本活动每名参与者限领一张0元换购平和蜜柚优惠券,您之前已经成功领取,点击"详情"进入购买',
                            'color' => "#743A3A"
                        ),
                        'keyword1' => array(
                            'value' => '0元换购平和蜜柚',
                            'color' => "#743A3A"
                        ),
                        'keyword2' => array(
                            'value' => '优惠卷领取',
                            'color' => '#000000'
                        ),
                        'remark' => array(
                            'value' => '详情',
                            'color' => "#008000"
                        )
                    )
                );
                if (!$user_info['nickname']) {
                    $template['data']['first']['value'] = '本活动每名参与者限领一张0元换购平和蜜柚优惠券,您之前已经成功领取,点击"详情"进入购买';
                }
                if (strpos($_W['siteroot'], 'fresh') !== false) {
                    //线上
                    $template['template_id'] = 'N6BzyJ4lL3r2bFV-6QKLA5gYQDt5dIrIjaaIdDcdnAA';
                }
                $this->send_template_message(urldecode(json_encode($template)));
            }
        }
	}

    public function operation_cash($user,$qr_id,$direct='')
    {
        global $_W;
        load() -> model('mc');
        $config = $this->module['config'];
        $uid = mc_openid2uid($user['openid']);
        if($config) {
            if($config['integral'] > 0) {
                $this->setCredit($user['openid'], 'credit1', $direct.intval($config['integral']), array($uid, '场景二维码-扫码送积分'));
            }
            if($config['balance'] > 0) {
                $this->setCredit($user['openid'], 'credit2', $direct.intval($config['balance']), array($uid, '场景二维码-扫码送余额'));
            }
        }
        $dit = $direct=='-' ? 0 : 1;
        $cash_log = array(
            'uniacid'=>$_W['uniacid'],
            'qr_id'=>$qr_id,
            'openid'=>$user['openid'],
            'direct'=>$dit,
            'create_time'=>TIMESTAMP
        );
        pdo_insert('alan_qrcode_cash_record', $cash_log);
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

    public function create_qrcode_record($user_info, $qrcode, $wx_data) {
        global $_W;
        $value = array(
            'nickname' => $user_info['nickname'],
            'avartar'  => $user_info['headimgurl'],
            'qrcode_id' => $qrcode['id'],
            'ticket' => $wx_data['ticket'] ? $wx_data['ticket'] : $user_info['wx_ticket']['ticket'],
            'type' => $wx_data['type'],
            'wx_data'=>json_encode($wx_data ? $wx_data: $user_info['wx_ticket']),
            'create_time'=>TIMESTAMP,
            'date'=>date('Ymd'),
            'group_id'=>$qrcode['group_id'],
            'uniacid'=>$_W['uniacid'],
            'qr_data'=>json_encode($qrcode)
        );
        pdo_insert('alan_qrcode_record', $value);
        return true;
    }

    public function create_qrcode_fans($user_info, $qrcode, $message) {
        $user_info['wx_ticket'] = $message;
        global $_W;
        $value = array(
            'uniacid'=>$_W['uniacid'],
            'openid'=>$user_info['openid'],
            'sex'=>$user_info['sex'],
            'nickname'=>$user_info['nickname'],
            'city'=>$user_info['city'],
            'province'=>$user_info['province'],
            'country'=>$user_info['country'],
            'headimgurl'=>$user_info['headimgurl'],
            'group_id'=>$qrcode['group_id'],
            'qrcode_id'=>$qrcode['id'],
            'subscribe_time'=>$user_info['subscribe_time'],
            'create_time'=>TIMESTAMP,
            'wx_data'=>json_encode($user_info),
            'follow'=>1
        );
        pdo_insert('alan_qrcode_fans', $value);
        return true;
    }

    public function update_fans($open_id, $type, $time) {
        global $_W;
        $where = array(
            'uniacid'=>$_W['uniacid'],
            'openid'=>$open_id
        );
        //pdo_update('alan_qrcode_fans', array('follow'=>2), $where);
        $up_data = array();
        if($type == 'subscribe') {
            $up_data['follow'] = 1;
            $up_data['subscribe_time'] = $time;
        } elseif($type == 'unsubscribe') {
            $up_data['cancel_sub_time'] = $time;
            $up_data['follow'] = 2;
        }
        if($up_data) {
            pdo_update('alan_qrcode_fans', $up_data, $where);
        }
        /*$fans = pdo_get('alan_qrcode_fans', $where);
        $group = pdo_get('alan_qrcode_fans_group', array('id'=>$fans['group_id']));
        //更新分组
        load()->classs('account');
        $account = WeAccount::create($_W['acid'] ? $_W['acid'] : $_W['uniaccid']);
        $account->updateFansGroupid($open_id, $group['mc_group_id']);*/
        return true;
    }

    public function qrcode_stat($qrcode, $message, $user_info) {
        global $_W;
        $uniacid = $_W['uniacid'];
        //统计组关注人数
        $group_num = $this->pdo_select_count('alan_qrcode_fans', array('uniacid'=>$uniacid, 'group_id'=>$qrcode['group_id'], 'follow'=>1));
        pdo_update('alan_qrcode_fans_group', array('count' => $group_num), array('id' => $qrcode['group_id']));
        //统计取消关注数量
        $cancel_group_num = $this->pdo_select_count('alan_qrcode_fans', array('uniacid'=>$uniacid, 'group_id'=>$qrcode['group_id'], 'follow'=>2));
        pdo_update('alan_qrcode_fans_group', array('cancel_count' => $cancel_group_num), array('id' => $qrcode['group_id']));
        //统计扫描数
        pdo_update('alan_qrcode_fans_group', array('scan_count +=' => 1), array('id' => $qrcode['group_id']));
        //统计二维码
        $qrcode_subnum = $this->pdo_select_count('alan_qrcode_fans', array('uniacid'=>$uniacid, 'group_id'=>$qrcode['group_id'], 'qrcode_id'=>$qrcode['id'], 'follow'=>1));
        $qrcode_cancelnum = $this->pdo_select_count('alan_qrcode_fans', array('uniacid'=>$uniacid, 'group_id'=>$qrcode['group_id'], 'qrcode_id'=>$qrcode['id'], 'follow'=>2));
        //扫码数
        $qrcode_scannum = $this->pdo_select_count('alan_qrcode_record', array('uniacid'=>$uniacid, 'group_id'=>$qrcode['group_id'], 'qrcode_id'=>$qrcode['id'], 'type'=>'qr'));
        $up_qrcode = array(
            'subnum'=>$qrcode_subnum,
            'cancelnum'=>$qrcode_cancelnum,
            'scannum'=>$qrcode_scannum
        );
        if($message['type'] =='subscribe') {
            $up_qrcode = array(
                'subnum +='=>1,
                'cancelnum -='=>1,
                'scannum'=>$qrcode_scannum
            );
            if($qrcode['cancelnum'] <= 0) {
                $up_qrcode['cancelnum'] = 0;
            }
        }
        pdo_update('alan_qrcode', $up_qrcode, array('id'=>$qrcode['id']));
        //画出统计数据
        $stat_where = array(
            'uniacid'=>$uniacid,
            'qrcode_id'=>$qrcode['id'],
            'group_id'=>$qrcode['group_id'],
            'date'=>date('Ymd'),
        );
        $stat_info = pdo_get('alan_qrcode_stat', $stat_where);
        //扫码数
        $qrcode_scan_num = $this->pdo_select_count('alan_qrcode_record', array_merge($stat_where, array('date'=>date('Ymd'))));
        //关注数
        $qrcode_sub_num = $this->pdo_select_count('alan_qrcode_record', array_merge($stat_where, array('date'=>date('Ymd'), 'type'=>'subscribe')));
        //取消关注数
        $qrcode_cancel_num = $this->pdo_select_count('alan_qrcode_record', array_merge($stat_where, array('date'=>date('Ymd'), 'type'=>'unsubscribe')));
        $stat_value = array(
            'scan_count'=>$qrcode_scan_num,
            'sub_num'=>$qrcode_sub_num,
            'cancel_num'=>$qrcode_cancel_num,
        );
        if(!$stat_info) {
            $stat_value['create_time'] = TIMESTAMP;
            pdo_insert('alan_qrcode_stat', array_merge($stat_where, $stat_value));
        } else {
            $stat_value['update_time'] = TIMESTAMP;
            pdo_update('alan_qrcode_stat', $stat_value, array('id'=>$stat_info['id']));
        }
        return TRUE;
    }



    //=== 受保护方法
    protected function pdo_select_count($tablename, $params = array()){
        $condition = $this->alan_implode($params, 'AND');
        $sql = 'SELECT COUNT(*) AS total FROM ' . tablename($tablename) . (!empty($condition['fields']) ? " WHERE {$condition['fields']}" : '') . " LIMIT 1";
        $result = pdo()->fetch($sql, $condition['params']);
        if (empty($result)) {
            return 0;
        }
        return intval($result['total']);
    }

    protected function alan_implode($params, $glue = ',') {
        $result = array('fields' => ' 1 ', 'params' => array());
        $split = '';
        $suffix = '';
        if (in_array(strtolower($glue), array('and', 'or'))) {
            $suffix = '__';
        }
        if (!is_array($params)) {
            $result['fields'] = $params;
            return $result;
        }
        if (is_array($params)) {
            $result['fields'] = '';
            foreach ($params as $fields => $value) {
                if (is_array($value)) {
                    $result['fields'] .= $split . "`$fields` IN ('".implode("','", $value)."')";
                    $split = ' ' . $glue . ' ';
                } else {
                    $result['fields'] .= $split . "`$fields` =  :{$suffix}$fields";
                    $split = ' ' . $glue . ' ';
                    $result['params'][":{$suffix}$fields"] = is_null($value) ? '' : $value;
                }
            }
        }
        return $result;
    }

    protected function send_cate_coupon($openid) {
        global $_W;
        $file = IA_ROOT.'/addons/manor_shop/plugin/coupon/model_normal.php';
        if (!is_file($file)){
            return false;
        }
        include $file;
        if(!pdo_tableexists('manor_shop_sysset')) {
            return '';
        }
        $uniacid = $_W['uniacid'];
        $set = pdo_fetch("select * from " . tablename('manor_shop_sysset') . ' where uniacid=:uniacid limit 1', array(':uniacid' => $uniacid));
        $allset = unserialize($set['sets']);
        $retsets = isset($allset['shop'])? $allset['shop'] : array();
        if($retsets['yimabang_power'] ==0) {
            return '';
        }
        if(!$retsets['yimabang_coupon']) {
            return '';
        }
        $couponModel = new CouponModel();
        $send_ret = $couponModel->send_coupon($retsets['yimabang_coupon'], array($openid), true, 1);
        if(!is_array($send_ret)) {
            return '';
        }
        return $send_ret;
        //发送领取成功的通知
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

}