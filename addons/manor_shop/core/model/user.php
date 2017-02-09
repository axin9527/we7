<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
class Ewei_DShop_User{
    private $sessionid;
    public function __construct(){
        global $_W;
        $this -> sessionid = "__cookie_manor_shop_201507200000_{$_W['uniacid']}";
    }
    function getOpenid(){
        $dephp_0 = $this -> getInfo(false, true);
        return $dephp_0['openid'];
    }
    function getPerOpenid(){
        global $_W, $_GPC;
        $dephp_1 = 24 * 3600 * 3;
        session_set_cookie_params($dephp_1);
        @session_start();
        $dephp_2 = "__cookie_manor_shop_openid_{$_W['uniacid']}";
        $dephp_3 = base64_decode($_COOKIE[$dephp_2]);
        if (!empty($dephp_3)){
            return $dephp_3;
        }
        load() -> func('communication');
        $dephp_4 = $_W['account']['key'];
        $dephp_5 = $_W['account']['secret'];
        $dephp_6 = "";
        $dephp_7 = $_GPC['code'];
        $dephp_8 = $_W['siteroot'] . 'app/index.php?' . $_SERVER['QUERY_STRING'];
        if (empty($dephp_7)){
            $dephp_9 = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $dephp_4 . '&redirect_uri=' . urlencode($dephp_8) . '&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
            header('location: ' . $dephp_9);
            exit();
        }else{
            $dephp_10 = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $dephp_4 . '&secret=' . $dephp_5 . '&code=' . $dephp_7 . '&grant_type=authorization_code';
            $dephp_11 = ihttp_get($dephp_10);
            $dephp_12 = @json_decode($dephp_11['content'], true);
            if (!empty($dephp_12) && is_array($dephp_12) && $dephp_12['errmsg'] == 'invalid code'){
                $dephp_9 = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $dephp_4 . '&redirect_uri=' . urlencode($dephp_8) . '&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
                header('location: ' . $dephp_9);
                exit();
            }
            if (is_array($dephp_12) && !empty($dephp_12['openid'])){
                $dephp_6 = $dephp_12['access_token'];
                $dephp_3 = $dephp_12['openid'];
                setcookie($dephp_2, base64_encode($dephp_3));
            }else{
                $dephp_13 = explode('&', $_SERVER['QUERY_STRING']);
                $dephp_14 = array();
                foreach($dephp_13 as $dephp_15){
                    if(!strexists($dephp_15, 'code=') && !strexists($dephp_15, 'state=') && !strexists($dephp_15, 'from=') && !strexists($dephp_15, 'isappinstalled=')){
                        $dephp_14[] = $dephp_15;
                    }
                }
                $dephp_16 = $_W['siteroot'] . 'app/index.php?' . implode('&', $dephp_14);
                $dephp_9 = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $dephp_4 . '&redirect_uri=' . urlencode($dephp_16) . '&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
                header('location: ' . $dephp_9);
                exit;
            }
        }
        return $dephp_3;
    }
    function getInfo($dephp_17 = false, $dephp_18 = false){
        global $_W, $_GPC;
        $dephp_0 = array();
        if (manor_shop_DEBUG){
            $dephp_0 = array('openid' => 'oT-ihv9XGkJbX9owJiLZcZPAJcog', 'nickname' => '狸小狐', 'headimgurl' => 'https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo/bd_logo1_31bdc765.png', 'province' => '山东', 'city' => '青岛');
        }else{
            load() -> model('mc');
            if (empty($_GPC['directopenid'])){
                $dephp_0 = mc_oauth_userinfo();
            }else{
                $dephp_0 = array('openid' => $this -> getPerOpenid());
            }
            $dephp_19 = true;
            if ($_W['container'] != 'wechat'){
                if($_GPC['do'] == 'order' && $_GPC['p'] == 'pay'){
                    $dephp_19 = false;
                }
                if($_GPC['do'] == 'member' && $_GPC['p'] == 'recharge'){
                    $dephp_19 = false;
                }
                if($_GPC['do'] == 'plugin' && $_GPC['p'] == 'article' && $_GPC['preview'] == '1'){
                    $dephp_19 = false;
                }
            }
            if(empty($dephp_0['openid']) && $dephp_19){
                die('<!DOCTYPE html>
                <html>
                    <head>
                        <meta name=\'viewport\' content=\'width=device-width, initial-scale=1, user-scalable=0\'>
                        <title>抱歉，出错了</title><meta charset=\'utf-8\'><meta name=\'viewport\' content=\'width=device-width, initial-scale=1, user-scalable=0\'><link rel=\'stylesheet\' type=\'text/css\' href=\'https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css\'>
                    </head>
                    <body>
                    <div class=\'page_msg\'><div class=\'inner\'><span class=\'msg_icon_wrp\'><i class=\'icon80_smile\'></i></span><div class=\'msg_content\'><h4>请在微信客户端打开链接</h4></div></div></div>
                    </body>
                </html>');
            }
        }
        if ($dephp_17){
            return urlencode(base64_encode(json_encode($dephp_0)));
        }
        return $dephp_0;
    }
    function oauth_info(){
        global $_W, $_GPC;
        if ($_W['container'] != 'wechat'){
            if($_GPC['do'] == 'order' && $_GPC['p'] == 'pay'){
                return array();
            }
            if($_GPC['do'] == 'member' && $_GPC['p'] == 'recharge'){
                return array();
            }
        }
        $dephp_1 = 24 * 3600 * 3;
        session_set_cookie_params($dephp_1);
        @session_start();
        $dephp_20 = "__cookie_manor_shop_201507100000_{$_W['uniacid']}";
        $dephp_21 = json_decode(base64_decode($_SESSION[$dephp_20]), true);
        $dephp_3 = is_array($dephp_21) ? $dephp_21['openid'] : '';
        $dephp_22 = is_array($dephp_21) ? $dephp_21['openid'] : '';
        if (!empty($dephp_3)){
            return $dephp_21;
        }
        load() -> func('communication');
        $dephp_4 = $_W['account']['key'];
        $dephp_5 = $_W['account']['secret'];
        $dephp_6 = "";
        $dephp_7 = $_GPC['code'];
        $dephp_8 = $_W['siteroot'] . 'app/index.php?' . $_SERVER['QUERY_STRING'];
        if (empty($dephp_7)){
            $dephp_9 = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $dephp_4 . '&redirect_uri=' . urlencode($dephp_8) . '&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect';
            header('location: ' . $dephp_9);
            exit();
        }else{
            $dephp_10 = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $dephp_4 . '&secret=' . $dephp_5 . '&code=' . $dephp_7 . '&grant_type=authorization_code';
            $dephp_11 = ihttp_get($dephp_10);
            $dephp_12 = @json_decode($dephp_11['content'], true);
            if (!empty($dephp_12) && is_array($dephp_12) && $dephp_12['errmsg'] == 'invalid code'){
                $dephp_9 = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $dephp_4 . '&redirect_uri=' . urlencode($dephp_8) . '&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect';
                header('location: ' . $dephp_9);
                exit();
            }
            if (empty($dephp_12) || !is_array($dephp_12) || empty($dephp_12['access_token']) || empty($dephp_12['openid'])){
                die('获取token失败,请重新进入!');
            }else{
                $dephp_6 = $dephp_12['access_token'];
                $dephp_3 = $dephp_12['openid'];
            }
        }
        $dephp_23 = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $dephp_6 . '&openid=' . $dephp_3 . '&lang=zh_CN';
        $dephp_11 = ihttp_get($dephp_23);
        $dephp_0 = @json_decode($dephp_11['content'], true);
        if (isset($dephp_0['nickname'])){
            $_SESSION[$dephp_20] = base64_encode(json_encode($dephp_0));
            return $dephp_0;
        }else{
            die('获取用户信息失败，请重新进入!');
        }
    }
    function followed($dephp_3 = ''){
        global $_W;
        $dephp_24 = !empty($dephp_3);
        if ($dephp_24){
            $dephp_25 = pdo_fetch('select follow from ' . tablename('mc_mapping_fans') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':openid' => $dephp_3, ':uniacid' => $_W['uniacid']));
            $dephp_24 = $dephp_25['follow'] == 1;
        }
        return $dephp_24;
    }
}
