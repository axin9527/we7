<?php

    /**
     * 操作类名称: PhpStorm.
     * 作者名称: alan
     * 创建时间: 2016/11/4 15:45
     */
    if(!defined('IN_IA')){
        exit('Access Denied');
    }
    global $_W, $_GPC;
    if($_W['isajax']) {
        $mobile = $_GPC['mobile'];
        $content = htmlspecialchars($_GPC['content']);
        if(strlen($mobile) <11) {
            show_json(-1, '请输入正确的手机号');
        }
        if(!$content) {
            show_json(-1, '请输入意见内容');
        }
        $insert = array(
            'open_id'=>$_W['openid'],
            'nickname'=>$_W['fans']['nickname'],
            'avatar'=>$_W['fans']['avatar'],
            'mobile'=>$mobile,
            'content'=>$content,
            'uniacid'=>$_W['uniacid'],
            'create_time'=>TIMESTAMP
        );
        $ret = pdo_insert('manor_shop_feedbacks', $insert);
        if($ret) {
            show_json(1, '提交成功');
        }
        show_json(1, '提交失败');
    }
    include $this -> template('member/feedback');