<?php
/**
 * 唐代金融发红包模块处理程序
 *
 * @author 诗意的边缘
 * @url http://s.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Td_redenvelopesModuleProcessor extends WeModuleProcessor {
	public function respond() {
        global $_W;
		$content = $this->message['content'];
		//这里定义此模块进行消息处理时的具体过程, 请查看微擎文档来编写你的代码
        $open_id = $this->message['from'];
        load()->model('mc');
        $fans = mc_fansinfo($open_id);
        $config = $this->module['config'];
        if(pdo_get('td_redenvelopes_record', array('open_id'=>$open_id))) {
            return $this->respText('一个用户只允许兑换红包一次');
        }
        $send_amount = pdo_fetchcolumn('select sum(money) as total_amount from '.tablename('td_redenvelopes_record').' where uniacid=:uniacid', array(':uniacid'=>$_W['uniacid']));
        if($config['total_amount'] && $send_amount >= $config['total_amount']) {
            return $this->respText('很抱歉，红包总额已经发放完毕');
        }
        $log_record =pdo_get('td_redenvelopes_record', array('uniacid'=>$_W['uniacid'], 'open_id'=>$open_id, 'code'=>$content));
        if($log_record['status'] >= 1) {
            return $this->respText('该兑换码已失效');
        }
        $check = $this->check_code($content);
        if($check || $log_record) {
            if($config['notice_openid']) {
                $this->send_message_to($config['notice_openid'], $send_amount);
            }
            if($config['red_power'] == 1) {
                $this->create_record_log($open_id, $content, $fans, array('return_msg'=>'记录成功,等待发放', 'wait'=>1, 'money'=>$this->get_pro_rand()),$check);
                return $this->respText('您好，劵码提交成功，请留意稍后公众号红包发放通知');
            }
            //发送红包
            $send_red_envelopes_ret = $this->pay($open_id, $log_record['money']);
            $this->create_record_log($open_id, $content, $fans, $send_red_envelopes_ret,$check);
            if($send_red_envelopes_ret['return_code'] == 'SUCCESS') {
                //return $this->respText('您的红包已发放，请及时领取，如有问题，请联系客服：020-10010');
            } else {
                return $this->respText('微信红包发放失败，请隔日在进行领取，如有问题，请联系客服');
            }
        } else {
            return $this->respText('您输入的兑换码不正确，请区分大小写');
        }
	}

    /**
     * 发送现金红包
     * @param $re_openid
     * @param $money
     * @return bool|SimpleXMLElement
     */
    public function pay($re_openid,$money=0.00)
    {
        global $_W;
        $config = $this->module['config'];
        if(!$config) {
            return false;
        }
        if(!$money || $money > 5.88) {
            $money = $this->get_pro_rand();
        }
        $money = $money * 100;
        include_once('hongbao/WxHongBaoHelper.php');
        $commonUtil = new CommonUtil();
        $wxHongBaoHelper = new WxHongBaoHelper();

        $wxHongBaoHelper->setParameter("nonce_str", $this->great_rand());//随机字符串，丌长于 32 位
        $wxHongBaoHelper->setParameter("mch_billno", $config['app_mchid'].date('YmdHis').rand(1000, 9999));//订单号
        $wxHongBaoHelper->setParameter("mch_id", $config['app_mchid']);//商户号
        $wxHongBaoHelper->setParameter("wxappid", $config['app_id']);
        $wxHongBaoHelper->setParameter("nick_name", '唐盛鲜果');//提供方名称
        $wxHongBaoHelper->setParameter("send_name", '唐盛鲜果');//红包发送者名称
        $wxHongBaoHelper->setParameter("re_openid", $re_openid);//相对于医脉互通的openid
        $wxHongBaoHelper->setParameter("total_amount", $money);//付款金额，单位分
        $wxHongBaoHelper->setParameter("min_value", $money);//最小红包金额，单位分
        $wxHongBaoHelper->setParameter("max_value", $money);//最大红包金额，单位分
        $wxHongBaoHelper->setParameter("total_num", 1);//红包収放总人数
        $wxHongBaoHelper->setParameter("wishing", '恭喜您，获得唐盛鲜果红包');//红包祝福诧
        $wxHongBaoHelper->setParameter("client_ip", '127.0.0.1');//调用接口的机器 Ip 地址
        $wxHongBaoHelper->setParameter("act_name", '唐代金融超市');//活劢名称
        $wxHongBaoHelper->setParameter("remark", '快来领！');//备注信息
        $postXml = $wxHongBaoHelper->create_hongbao_xml();
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        $responseXml = $wxHongBaoHelper->curl_post_ssl($url, $postXml, 30, array(), $_W['uniacid']);
        $responseObj = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $res_obj = (array)$responseObj;
        $res_obj['money'] = $money/100;
        return $res_obj;
    }

    /**
     * 获取随机字符串
     * @return string
     */
    public function great_rand(){
        $str = '1234567890abcdefghijklmnopqrstuvwxyz';
        $t1 = "";
        for($i=0;$i<30;$i++){
            $j=rand(0,35);
            $t1 .= $str[$j];
        }
        return $t1;
    }

    /**
     * 接口验证code是否正确。请求唐代金融接口
     * @param $code
     * @return mixed
     */
    protected function check_code($code) {
        global $_W;
        load()->model('mc');
        $fans = mc_fansinfo($_W['openid']);
        include IA_ROOT.'/api/financial.php';
        $financial = new financial($_W['uid'] ? $_W['uid'] : 1);
        $ret = $financial->check_red_code($code, $fans);
        pdo_insert('log', array('cont'=>var_export(array_merge($ret, array('code'=>$code)), true)));
        if($ret['ret'] == 1) {
            return $ret;
        } else {
            return false;
        }
    }

    /**
     * 记录红包领取日志
     * @param $open_id
     * @param $code
     * @param $fans
     * @param $ret
     * @param $check
     * @return bool|void
     */
    protected function create_record_log($open_id,$code,$fans, $ret,$check) {
        global $_W;
        $log = pdo_get('td_redenvelopes_record', array('uniacid'=>$_W['uniacid'], 'open_id'=>$open_id, 'code'=>$code));
        if($log && $log['status'] == 1) {
            return;
        } else if($log && in_array($log['status'], array(0, 2))){
            $up = array(
                'updatetime'=>TIMESTAMP,
                'times +='=>1,
                'money'=>$ret['money'],
                'mobile'=>$check['str']
            );
            if($ret['return_code'] == 'SUCCESS') {
                $up['status'] = 1;
            } else {
                $up['status'] = 0;
            }
            if($ret['wait']) {
                $value['status'] = 2;
            }
            $up['message'] = $ret['return_msg'];
            $this->update_user($up['mobile'], $open_id);
            return pdo_update('td_redenvelopes_record', $up, array('id'=>$log['id']));
        }
        $value = array(
            'uniacid'=>$_W['uniacid'],
            'open_id'=>$open_id,
            'nickname'=>$fans['nickname'],
            'avstar'=>$fans['avatar'],
            'code'=>$code,
            'createtime'=>TIMESTAMP,
            'updatetime'=>'',
            'times'=>1,
            'money'=>$ret['money'],
            'mobile'=>$check['str']
        );
        if($ret['return_code'] == 'SUCCESS') {
            $value['status'] = 1;
        } else {
            $value['status'] = 0;
        }
        if($ret['wait']) {
            $value['status'] = 2;
        }
        $value['message'] = $ret['return_msg'];
        $this->update_user($value['mobile'], $open_id);
        return pdo_insert('td_redenvelopes_record', $value);
    }

    function update_user($mobile, $openid) {
        global $_W;
        $m_data = array(
            'mobile'=>$mobile
        );
        if ($openid == 0){
            $member = pdo_fetch('select * from ' . tablename('manor_shop_member') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
        }else{
            $member = pdo_fetch('select * from ' . tablename('manor_shop_member') . ' where id=:id  and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $openid));
        }
        pdo_update('manor_shop_member', $m_data, array('openid' => $openid, 'uniacid' => $_W['uniacid']));
        if(!empty($member['uid'])){
            load() -> model('mc');
            if (!empty($mc_data)){
                mc_update($member['uid'], $mc_data);
            }
        }
        return true;
    }
    function send_message_to($open_id,$amount){
        global $_W;
        if(!$open_id) {
            return '';
        }
        $config = $this->module['config'];
        $template = array(
            "touser" => $open_id,
            "template_id" => '1vdBWjz_5SU14HPWn3jKrfVRBELK_e_LjoKvKMszwok',
            "url" => $_W['siteroot'] . 'app/index.php?i='.$_W['acid'].'&c=entry&do=member&m=manor_shop',
            "topcolor" => "#FF0000",
            "data" => array(
                'keyword1' => array(
                    'value' => urlencode('商户剩余余额不多，请及时充值,已发送红包'.$amount.'平台设置最高发放金额是:'.$config['total_amount']),
                    'color' => "#743A3A"
                ),
                'keyword2' => array(
                    'value' => urlencode('唐代金融红包'),
                    'color' => '#000000'
                ),
                'remark' => array(
                    'value' => urlencode('如有疑问，请联系客服'),
                    'color' => "#008000"
                )
            )
        );
        if(strpos($_W['siteroot'], 'fresh') !== false) {
            //线上
            $template['template_id'] = 'N6BzyJ4lL3r2bFV-6QKLA5gYQDt5dIrIjaaIdDcdnAA';
        }
        $this->send_template_message(urldecode(json_encode($template)));
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
    protected  function get_pro_rand() {
        $proArr = array(
            '1-2'=>0.95,
            '2-5.87'=>0.1,
            '5.88'=>0.05
        );
        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($proArr);
        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
        if($result == '1-2') {
            $money = round(mt_rand(10, 20)/10, 2);
        } elseif($result == '2-5.87') {
            $money = round(mt_rand(20, 58.7)/10, 2);
        } elseif($result == '5.88') {
            $money = 5.88;
        } else {
            $money = 1;
        }
        if($money > 5.88) {
            $money = 1;
        }
        return $money;
    }
}