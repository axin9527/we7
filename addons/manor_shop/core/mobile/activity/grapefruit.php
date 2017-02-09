<?php
/**
 * Created by PhpStorm.
 * User: alan
 * Date: 16-11-10
 * Time: 下午3:53
 */
global $_W, $_GPC;
if($_GPC['from'] &&  !$_W['isajax']) {
    $redirect = $this->createMobileUrl('activity/grapefruit');
    header("Location:$redirect");die;
}
$openid = m('user') -> getOpenid();
$member = m('member') -> getMember($openid);
$set = m('common') -> getSysset('shop');
if($_W['isajax']) {
    $username = trim($_GPC['username']);
    if($_GPC['op'] == 'share') {
        pdo_update('manor_shop_grapefruit',array('is_share'=>1, 'share_num +='=>1), array('open_id'=>$openid, 'uniacid'=>$_W['uniacid']));
        show_json(1, '成功');die;
    }
    if(!$username) {
        show_json(-1, '请输入姓名');
    }
    $mobile = trim($_GPC['mobile']);
    if(!$mobile || !is_numeric($mobile) || $mobile < 0) {
        show_json(-1, '请输入正确的手机号码');
    }
    $thumb = $_GPC['thumb'];
    if(!$thumb) {
        show_json(-1, '请上传分享截图');
    }
    $address = $_GPC['province'].$_GPC['city'].$_GPC['area'].$_GPC['address'];
    /* if(!$_GPC['address'] || !$_GPC['province'] || !$_GPC['area']) {
        show_json(-1, '请输入省市区及详细地址');
    }*/
    if(pdo_get('manor_shop_grapefruit', array('open_id'=>$openid, 'uniacid'=>$_W['uniacid']))){
        show_json(-1, '您已经参与过啦');
    }
    $insert_data = array(
        'uniacid'=>$_W['uniacid'],
        'open_id'=>$openid,
        'nickname'=>$_W['fans']['nickname'],
        'avastar'=>$_W['fans']['avatar'],
        'real_name'=>$username,
        'thumb'=>$thumb,
        'address'=>$address,
        'mobile'=>$mobile,
        'createtime'=>TIMESTAMP
    );
    $res = pdo_insert('manor_shop_grapefruit', $insert_data);
    if($res) {
        show_json(1, '提交成功');
    } else {
        show_json(-1, '提交失败');
    }
}
$ajax_url = $this->createMobileUrl('activity/grapefruit', array('op'=>'share'));
$_W['shopshare'] = array(
    'title'=>'我有100个柚子送给你',
    'imgUrl'=>tomedia('../addons/manor_shop/template/mobile/tangsheng/static/images/youzi/share.jpg'),
    'desc'=>'我家红柚堆成山了，赶快来领走！',
    'link'=>$this->createMobileUrl('activity/grapefruit'),
);
include $this->template('activity/grapefruit');
