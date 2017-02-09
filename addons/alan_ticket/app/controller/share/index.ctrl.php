<?php
/**
 * 我的朋友
 * Author：Alan
 * [TangShengMonar System]Copyright (c) 2016 fresh.tangshengmanor.com
 * Time:2016-12-19 13
 */

defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$operation = !empty($_GPC['op'])? $_GPC['op'] : 'display';
$uniacid = $_W['uniacid'];
wl_load()->func('vote');
$openid = $_W['openid'];
header("Location: index.php?i=8&c=entry&do=Ranking&m=alan_ticket");die;
//$openid=123456;
if($operation == 'my_friend'){
        $sql = "select id from ims_ticket_user where openid = :openid ";
        $id = pdo_fetchcolumn($sql,array(':openid' => $openid));
        $friend_number = my_vote_number($id);
        $num = 10;
        $page = $_GPC['page'] ? $_GPC['page'] : 1 ;
        $count = intval(($friend_number / $num) + ($friend_number % $num == 0 ? 0: 1));
        if ($page > $count) $page = -1;
        if ($page < 1 || empty($page)) $page = -1;
        $start = ($page - 1 ) * $num;
        if ($page == -1) {
            $friend = array();
        } else {
            $friend = my_friend($id,$start,$num);
        }
        $friend = array_map(
            function($x){
                $x['vote_time'] = date('Y-m-d H:i:s', $x['vote_time']);
                return $x;
            }, $friend);
        $ret = [
            'status' =>  '1',
            'result' =>  [
                'number' => $friend_number,
                'friend' => $friend,
                'page_num' => $num
            ]
        ];
        echo json_encode($ret);die;

}
//渲染到前台视图
include ticket_template('share/index');
