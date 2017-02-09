<?php
/**
 * 个人中心
 * Author：Alan
 * [TangShengMonar System]Copyright (c) 2016 fresh.tangshengmanor.com
 * Time:2016-12-19 13
 */

defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
header("Location: index.php?i=8&c=entry&do=Ranking&m=alan_ticket");die;
$operation = !empty($_GPC['op'])? $_GPC['op'] : 'display';
wl_load()->func('vote');
wl_load()->func('poster');
wl_load()->func('global');
$openid = $_W['openid'];
//$openid = 123456;
$back_name = "2016-12-29";
if(!$openid) {
    wl_load()->model('member');
    $openid = getOpenid();
}
$sql = "select wish from ims_ticket_user where openid = :openid";
$my_wish = pdo_fetchcolumn($sql,array(':openid' => $openid));
if(!$my_wish) {
    load()->func('file');
    $source_path =  IA_ROOT.'/addons/alan_ticket/app/data/poster/' . $_W['uniacid'] . '/'.$back_name.'/'.$openid.'.png';
    file_delete($source_path);
    header('Location:'.app_url('apply'));die;
}
//$openid=123456;
$uniacid = $_W['uniacid'];
$sql = "select id from ims_ticket_user where openid = :openid ";
$id = pdo_fetchcolumn($sql,array(':openid' => $openid));
if($operation == 'my_info'){
    $votes = my_vote_number($id);
    $rank = my_range($id);
    $sql2 = "select headimg,ticket, wish,nickname from ims_ticket_user where id = :id ";
    $user = pdo_fetch($sql2,array(':id' => $id));
    //海报文件夹
    $path = IA_ROOT . '/addons/alan_ticket/app/data/poster/' . $_W['uniacid'] . '/'.$back_name;
    if(!file_exists($path)){
        mkdir($path,0777,true);
    }
    //生成海报地址
    $poster_path = $path.'/'.$openid.'.png';
    //二维码地址
    $qr_name = $openid.'.jpg';
    $qr_path = IA_ROOT . '/addons/alan_ticket/app/data/qrcode/' . $_W['uniacid'] . '/'.$qr_name;
    //头像地址
    $avatar_name = $openid.'.jpg';
    $av_path = IA_ROOT . '/addons/alan_ticket/app/data/avatar/' . $_W['uniacid'] . '/'.$avatar_name;

    if(!is_file($poster_path)){
    if(!$user['ticket']){
        $tickets = getTicket($id,$openid);
        $ticket = $tickets['ticket'];
        $ret = pdo_query("update ims_ticket_user set ticket = :ticket where openid = :openid",
            [
                ":ticket" => $ticket,
                ":openid" => $openid
            ]);
    }else{
        $ticket=$user['ticket'];
    }
    if(!is_file($qr_path)){
        getUrlByTicket($ticket,true);
    }
    if(!is_file($av_path)){
        getWxAvatarUrl($user['headimg']);
    }
    $bac=IA_ROOT . '/addons/alan_ticket/app/view/'.$back_name.'bac.jpg';
    $info = [
        "background" => [
            "path" => $bac
        ],
        "qrcode" => [
            "path" => $qr_path,
            "x"    => 190,
            "y"    => 415,
            "w"    => 175,
            "h"    => 175
        ],
        "head"   => [
            "path" => $av_path,
            "x"    => 50,
            "y"    => 70,
            "w"    => 150,
            "h"    => 150
        ],
        "wish"   => [
            "size"      => 28,
            "x"         => 50,
            "y"         => 310,
            "linespace" => 15,
            "width"     => 450,
            "font"      => "wqy.ttf",
            "text"      => " ${user['wish']}",
            "color"     => "0,0,0,0"
        ],
        "name" => [
            "size"      => 25,
            "x"         => 270,
            "y"         => 120,
            "width"     => 200,
            "font"      => "wqy.ttf",
            "color"     => "0,0,0,0",
            "text"      => mb_substr($user['nickname'], 0, 7, 'utf-8')
        ]
    ];
    make_poster($info,$poster_path);
    }

    $poster_name = $openid.'.png';
    $poster_path = tomedia('../addons/alan_ticket/app/data/poster/' . $_W['uniacid'] . '/'.$back_name.'/'.$poster_name);
    $friend = my_friend($id,0,10);
    $friend = array_map(
        function($x){
            $x['vote_time'] = date('Y-m-d H:i:s', $x['vote_time']);
            return $x;
        }, $friend);
    $my_info = [
        'status' =>  '1',
        'result' => [
            'vote_num'     => $votes,
            'rank'         => $rank,
            'poster_path'  => $poster_path,
            'friend'       => $friend
        ]
    ];
    echo json_encode($my_info);die;
}
$shopshare = json_encode(array(
    'title'=>'2017年说出你的愿望，我们帮你实现',
    'imgUrl'=>tomedia('../addons/alan_ticket/app/resource/images/gold.jpeg'),
    'desc'=>'我的愿望是：'.$my_wish.',点击链接，长按二维码，说出你的愿望',
    'link'=>app_url('share/view', ['p_id'=>$openid])
));
//渲染到前台视图
include ticket_template('share/share');
