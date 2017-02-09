<?php
/**
 * 用户信息
 * Author：Alan
 * [TangShengMonar System]Copyright (c) 2016 fresh.tangshengmanor.com
 * Time:2016-12-20 11
 */

function getMember($openid = '')
{
    global $_W;
    if (empty($openid)) {
        return;
    }
    $info = pdo_fetch('select * from ' . tablename('ticket_user') . ' where openid=:openid  limit 1', array(':openid' => $openid));
    return $info;
}

function mc_openidTouid($openid='' ) {
    global $_W;
    if (is_numeric($openid)) {
        return $openid;
    }
    if (is_string($openid)) {
        $sql = 'SELECT uid FROM ' . tablename('mc_mapping_fans') . ' WHERE `uniacid`=:uniacid AND `openid`=:openid';
        $pars = array();
        $pars[':uniacid'] =  $_W['uniacid'];
        $pars[':openid'] = $openid;
        $uid = pdo_fetchcolumn($sql, $pars);
        return $uid;
    }
    if (is_array($openid)) {
        $uids = array();
        foreach ($openid as $k => $v) {
            if (is_numeric($v)) {
                $uids[] = $v;
            } elseif (is_string($v)) {
                $fans[] = $v;
            }
        }
        if (!empty($fans)) {
            $sql = 'SELECT uid, openid FROM ' . tablename('mc_mapping_fans') . " WHERE `uniacid`=:uniacid AND `openid` IN ('" . implode("','", $fans) . "')";
            $pars = array(':uniacid' =>  $_W['uniacid']);
            $fans = pdo_fetchall($sql, $pars, 'uid');
            $fans = array_keys($fans);
            $uids = array_merge((array)$uids, $fans);
        }
        return $uids;
    }
    return false;
}

function checkMember($openid = '')
{
    global $_W;
    if (empty($openid)) {
        $openid = getOpenid();
    }
    if (empty($openid)) {
        return;
    }

    $member = getMember($openid);
    $userinfo = getInfo();
    $uid = 0;
    if ($_W['fans']['follow'] == 1) {
        $uid = mc_openidTouid($openid);
    }
    if (empty($member)) {
        $member = array(
            'openid' => $openid,
            'uniacid'=>$_W['uniacid'],
            'nickname' => $userinfo['nickname'],
            'headimg' => $userinfo['avatar'],
            'wish' => '',
            'ismock' => 0,
            'group_id'=>$userinfo['groupid'],
            'created_time' => TIMESTAMP,
            'follow'=>$userinfo['subscribe']
        );
        pdo_insert('ticket_user', $member);
    } else {
        if (!empty($member['id'])) {
            $upgrade = array();
            if ($userinfo['nickname'] != $member['nickname']) {
                $upgrade['nickname'] = $userinfo['nickname'];
            }
            if ($userinfo['avatar'] != $member['headimg']) {
                $upgrade['headimg'] = $userinfo['avatar'];
            }
            $upgrade['follow'] = $userinfo['subscribe'];
            if (!empty($upgrade)) {
                pdo_update('ticket_user', $upgrade, array('id' => $member['id']));
            }
        }
    }
}

function getOpenid()
{
    $userinfo = getInfo();
    return $userinfo['openid'];
}

function getInfo()
{
    global $_W, $_GPC;
    $userinfo = array();
    load()->model('mc');
    $userinfo = mc_oauth_userinfo();
    if (TICKET_DEBUG == FALSE) {
        if (empty($userinfo['openid'])) {
            die("<!DOCTYPE html>
	            <html>
	                <head>
	                    <meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'>
	                    <title>抱歉，出错了</title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'><link rel='stylesheet' type='text/css' href='https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css'>
	                </head>
	                <body>
	                <div class='page_msg'><div class='inner'><span class='msg_icon_wrp'><i class='icon80_smile'></i></span><div class='msg_content'><h4>请在微信客户端打开链接</h4></div></div></div>
	                </body>
	            </html>");
        }
    }
    return $userinfo;
}