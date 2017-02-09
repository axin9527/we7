<?php
/**
 * 投票圆梦计划模块订阅器
 *
 * @author alan51
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Alan_ticketModuleReceiver extends WeModuleReceiver {
	public function receive() {
        global $_W;
		$type = $this->message['type'];
        $openid = $this->message['fromusername'];
        $this->send_message_to_get_ticket_over($openid);die;
        if($type == 'qr' || $type == 'subscribe')
        {
            $config = $this->module['config']['setting'];
            if($config['power'] != 1) {
                $this->salerEmpty();
            }
            //用户信息
            $member = pdo_fetch('select * from ' . tablename('ticket_user') . ' where openid=:openid  limit 1', array(':openid' => $openid));
            $userinfo = '';
            load()->model('mc');
            $userinfo = mc_fansinfo($openid);
            if(!$member) {
                $member = array(
                    'openid' => $openid,
                    'uniacid'=>$_W['uniacid'],
                    'nickname' => $userinfo['nickname'],
                    'headimg' => $userinfo['avatar'],
                    'wish' => '',
                    'ismock' => 0,
                    'group_id'=>$userinfo['groupid'],
                    'created_time' => TIMESTAMP,
                    'follow'=>1
                );
                pdo_insert('ticket_user', $member);
                $uid = pdo_insertid();
            } else {
                $upgrade = array();
                if ($userinfo['nickname'] != $member['nickname']) {
                    $upgrade['nickname'] = $userinfo['nickname'];
                }
                if ($userinfo['avatar'] != $member['headimg']) {
                    $upgrade['headimg'] = $userinfo['avatar'];
                }
                $upgrade['follow'] = 1;
                if (!empty($upgrade)) {
                    pdo_update('ticket_user', $upgrade, array('id' => $member['id']));
                    $uid = $member['id'];
                }
                else {
                    $uid = $member['id'];
                }
            }
            //投票
            $ticket = $this->message['ticket'];
            if($ticket) {
                $ticket_user = pdo_get('ticket_user', array('ticket'=>$ticket));
                if($ticket_user) {
                    require IA_ROOT.'/addons/alan_ticket/core/function/vote.func.php';
                    $ret = vote_add($uid, $ticket_user['id']);
                    if($ret) {
                        //通知
                        $this->send_message_to_get_ticket($openid, time(), $ticket_user['nickname']);
                        $total = pdo_fetchcolumn('select vote_number from  '.tablename('ticket_range').' where user_id=:user_id limit 1', array(':user_id'=>$ticket_user['id']));
                        $this->send_template_message_to_share_ticket($ticket_user['openid'], $member['nickname'], $total);
                    } else {
                        $this->send_message_to_get_ticket_has($openid, time(), $ticket_user['nickname']);
                    }
                }
            }
        }
        else if($type == 'unsubscribe') {
            pdo_update('ticket_user', array('follow'=>0), array('uniacid'=>$_W['uniacid'], 'openid'=>$openid));
        }
        //$this->salerEmpty();
	}

    /**
     * 获得票提醒
     * @param $open_id
     * @param $who
     * @param $total
     * @return array|bool|string
     */
    public function send_template_message_to_share_ticket($open_id, $who, $total)
    {
        global $_W;
        if(!$open_id) {
            return '';
        }
        $config = $this->module['config']['setting'];
        $template = array(
            "touser" => $open_id,
            "template_id" => $config['get_ticket_template_id'],
            "url" => $_W['siteroot'] . 'app/index.php?i='.$_W['acid'].'&c=entry&do=apply&m=alan_ticket',
            "topcolor" => "#FF0000",
            "data" => array(
                'first' => array(
                    'value' => urlencode($config['get_ticket_template_subject']),
                    'color' => "#743A3A"
                ),
                'keyword1' => array(
                    'value' => urlencode($who.'给我投了一票，我共获得'.$total.'票！'),
                    'color' => "#743A3A"
                ),
                'keyword2' => array(
                    'value' => urlencode('投票通知'),
                    'color' => '#000000'
                ),
                'remark' => array(
                    'value' => urlencode('如有疑问，请联系客服'),
                    'color' => "#008000"
                )
            )
        );
        $this->send_template_message(urldecode(json_encode($template)));
    }

    /**
     * 投票成功
     * @param $open_id
     * @param $vote_time
     * @param $to_username
     * @return array|bool|string
     */
    function send_message_to_get_ticket($open_id,$vote_time,$to_username){
        global $_W;
        if(!$open_id) {
            return '';
        }
        $config = $this->module['config']['setting'];
        $template = array(
            "touser" => $open_id,
            "template_id" => $config['ticket_template_id'],
            "url" => $_W['siteroot'] . 'app/index.php?i='.$_W['acid'].'&c=entry&do=apply&m=alan_ticket',
            "topcolor" => "#FF0000",
            "data" => array(
                'first' => array(
                    'value' => urlencode($config['ticket_template_subject']),
                    'color' => "#743A3A"
                ),
                'keyword1' => array(
                    'value' => urlencode('我给'.$to_username.'成功投了一票，帮助他实现愿望更近了一步'),
                    'color' => "#743A3A"
                ),
                'keyword2' => array(
                    'value' => urlencode(date('Y-m-d H:i', $vote_time)),
                    'color' => '#000000'
                ),
                'remark' => array(
                    'value' => urlencode('2017年你有什么愿望？点击[详情]，上传你的愿望，让别人帮你去实现'),
                    'color' => "#008000"
                )
            )
        );
        $this->send_template_message(urldecode(json_encode($template)));
    }

    function send_message_to_get_ticket_over($open_id){
        global $_W;
        if(!$open_id) {
            return '';
        }
        $config = $this->module['config']['setting'];
        $template = array(
            "touser" => $open_id,
            "template_id" => $config['get_ticket_template_id'],
            "url" => $_W['siteroot'] . 'app/index.php?i='.$_W['acid'].'&c=entry&do=apply&m=alan_ticket',
            "topcolor" => "#FF0000",
            "data" => array(
                'first' => array(
                    'value' => urlencode('活动结束'),
                    'color' => "#743A3A"
                ),
                'keyword1' => array(
                    'value' => urlencode('投票活动已经结束，点击详情查看排行榜！'),
                    'color' => "#743A3A"
                ),
                'keyword2' => array(
                    'value' => urlencode('活动通知'),
                    'color' => '#000000'
                ),
                'remark' => array(
                    'value' => urlencode('如有疑问，请联系客服'),
                    'color' => "#008000"
                )
            )
        );
        $this->send_template_message(urldecode(json_encode($template)));
    }

    function send_message_to_get_ticket_has($open_id,$vote_time,$to_username){
        global $_W;
        if(!$open_id) {
            return '';
        }
        $config = $this->module['config']['setting'];
        $template = array(
            "touser" => $open_id,
            "template_id" => $config['get_ticket_template_id'],
            "url" => $_W['siteroot'] . 'app/index.php?i='.$_W['acid'].'&c=entry&do=apply&m=alan_ticket',
            "topcolor" => "#FF0000",
            "data" => array(
                'first' => array(
                    'value' => urlencode('投票失败'),
                    'color' => "#743A3A"
                ),
                'keyword1' => array(
                    'value' => urlencode('您已为'.$to_username.'投过票，每人限给同一人投票一次，多次投票算一次。'),
                    'color' => "#743A3A"
                ),
                'keyword2' => array(
                    'value' => urlencode('投票通知'),
                    'color' => '#000000'
                ),
                'remark' => array(
                    'value' => urlencode('如有疑问，请联系客服'),
                    'color' => "#008000"
                )
            )
        );
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

    private function salerEmpty() {
        ob_clean();
        ob_start();
        echo '';
        ob_flush();
        ob_end_flush();
        exit(0);
    }
}