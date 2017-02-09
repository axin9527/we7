<?php
/**
 * 前台排行榜
 * Author：Alan
 * [TangShengMonar System]Copyright (c) 2016 fresh.tangshengmanor.com
 * Time:2016-12-19 13
 */

defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$operation = !empty($_GPC['op'])? $_GPC['op'] : 'display';
$uniacid = $_W['uniacid'];
$openid = $_W['openid'];
//$openid = 123456;
$sql = "select wish from ims_ticket_user where openid = :openid";
$my_wish = pdo_fetchcolumn($sql,array(':openid' => $openid));
wl_load()->func('vote');

if($operation == 'casual'){
    $friend_number = 50;
    $_rank = vote_range($friend_number);
    $num = 10;
    $page = $_GPC['page'] ? $_GPC['page'] : 1 ;
    $count = intval(($friend_number / $num) + ($friend_number % $num == 0 ? 0: 1));
    if ($page > $count){
        $rank = "";
    }else{
        if ($page <= 1 || empty($page)) $page = 1;
        $start = ($page - 1 ) * $num;
        $rank_list = array_chunk($_rank, $num);
        $rank = $rank_list[$page - 1];
    }
    $user = getUser($openid);
    if ($user['wish']) {
        $my_rank = my_range($user['id']);
        $my_vote = my_vote_number($user['id']);
    } else {
        $my_rank = 0;
        $my_vote = 0;
    }
    if(!is_file($poster_path)){
        $poster_path = "";
    }
    $ranking_list = [
        'status' => 1,
        'result' => [
            'my_wish' => [
                'my_rank'    => $my_rank,
                'my_vote'    => $my_vote,
                'my_wish'    => $user['wish'],
                'my_headimg' => $user['headimg']
            ],
            'rank'    => $rank,
            'number'  => $num,
            'page'  => $page
        ]
    ];
    die(
        json_encode($ranking_list));

}
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
include ticket_template('ranking/index');


/**
 * @param $openid
 * @return bool
 */
function getUser($openid)
{
    $user = pdo_fetch("select id,wish,headimg from ims_ticket_user where openid = :openid ",
        array(
            ":openid" => $openid
        ));
    return $user;
}
