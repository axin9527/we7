<?php
/**
 * 前台申请梦想首页
 * Author：Alan
 * [TangShengMonar System]Copyright (c) 2016 fresh.tangshengmanor.com
 * Time:2016-12-19 13
 */

defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$operation = !empty($_GPC['op'])? $_GPC['op'] : 'display';
$uniacid = $_W['uniacid'];
$openid = $_W['openid'];
header("Location: index.php?i=8&c=entry&do=Ranking&m=alan_ticket");die;
//$openid = 123456;
wl_load()->func('vote');
$sql = "select wish from ims_ticket_user where openid = :openid";
$my_wish = pdo_fetchcolumn($sql,array(':openid' => $openid));
if(!empty($my_wish) && $_GPC['back'] == "" ){
    header('Location:'.app_url('share/share'));die;
}
if ($operation == 'add_dream'){
    /*  $phone = $_GPC['phone'];
      $search ='/^(1(([34578][0-9])|(47)|[8][0126789]))\d{8}$/';
      if(!preg_match($search,$phone)) {
          die(jsons('0','手机号格式不正确'));
      }*/
      $wish = strip_tags(htmlspecialchars_decode($_GPC['wish']));
      if(mb_strlen($wish)>60){
          die(jsons('0','梦想不能大于20字'));
      }
      if(empty($my_wish)){
          $sql = pdo_query("update ims_ticket_user set wish = :wish where openid = :openid ",[
              ':wish'     => $wish,
              ':openid'   => $openid
          ]);
          die(jsons('1','添加成功'));
      }else{
          die(jsons('0','您已有梦想,不能重复提交'));
      }

}
function jsons($status,$result){
    $wish_add = [
        'status' => $status,
        'result' => $result
    ];
    return json_encode($wish_add);
}
$img_path = tomedia('../addons/alan_ticket/app/data/poster/'.$_W['uniacid'].'/'.date('Y-m-d').'/'.$opp_id.'.png.png');
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
//渲染到前台视图
include ticket_template('apply/index');
