<?php
/**
 * 我的朋友
 * Author：Alan
 * [TangShengMonar System]Copyright (c) 2016 fresh.tangshengmanor.com
 * Time:2016-12-19 13
 */

defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
header("Location: index.php?i=8&c=entry&do=Ranking&m=alan_ticket");die;
//渲染到前台视图
$opp_id = $_GPC['p_id'];
$back_time = "2016-12-29";
if(!$opp_id) {
    header('Location:'.app_url('apply/index'));die;
}
$openid = $_W['openid'];
if(!$openid) {
    wl_load()->model('member');
    $openid = getOpenid();
}
$img_path = tomedia('../addons/alan_ticket/app/data/poster/'.$_W['uniacid'].'/'.$back_time.'/'.$opp_id.'.png');
$sql = "select wish from ims_ticket_user where openid = :openid";
$my_wish = pdo_fetchcolumn($sql,array(':openid' => $openid));
$shopshare = json_encode(array(
    'title'=>'2017年说出你的愿望，我们帮你实现',
    'imgUrl'=>tomedia('../addons/alan_ticket/app/resource/images/gold.jpeg'),
    'desc'=>'我的愿望是：'.$my_wish.',点击链接，长按二维码，说出你的愿望',
    'link'=>app_url('share/view', ['p_id'=>$openid])
));
if(!$my_wish) {
    $shopshare = json_encode(array(
        'title'=>'2017年说出你的愿望，我们帮你实现',
        'imgUrl'=>tomedia('../addons/alan_ticket/app/resource/images/gold.jpeg'),
        'desc'=>'只要你敢说，信不信我就能帮你实现，点击链接，说出你的愿望',
        'link'=>app_url('apply')
    ));
}
include ticket_template('share/view');
