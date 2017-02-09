<?php
/**
 * 双十一后专属表情包模块微站定义
 *
 * @author alan51
 * @url 
 */
if(!defined('IN_IA')) {
    exit('Access Denied');
}
defined('IN_IA') or exit('Access Denied');
require_once IA_ROOT. '/addons/eleven_expression/func/defines.php';
require_once IA_ROOT. '/addons/eleven_expression/func/functions.php';
class Eleven_expressionModuleSite extends WeModuleSite {

	public function doMobileIndex() {
		//这个操作被定义用来呈现 功能封面
        /**
         * 双十一
         * 操作类名称: PhpStorm.
         * 作者名称: alan
         * 创建时间: 16/9/5 09:50
         */

        global $_W, $_GPC;
        load()->model('mc');
        $usr_info = getInfo(false, true);
        $openid = $usr_info['openid'];
        $member = get_member($openid);
        if($_GPC['from'] &&  !$_W['isajax']) {
            $redirect = $this->createMobileUrl('index');
            header("Location:$redirect");die;
        }
        if($_W['isajax'] && $_W['ispost']) {
            if($_GPC['pp'] == 'again_time') {
                if(!$_GPC['pic']) {
                    show_json(-1, '');
                }
                if(!$log = pdo_get('eleven_expression_log', array('uniacid'=>$_W['uniacid'], 'pic_id'=>$_GPC['pic']))) {
                    $insert = array(
                        'uniacid'=>$_W['uniacid'],
                        'again_times'=>1,
                        'out_time'=>0,
                        'create_time'=>time(),
                        'pic_id'=>$_GPC['pic_id']
                    );
                    pdo_insert('eleven_expression_log', $insert);
                } else {
                    pdo_update('eleven_expression_log', array('again_times +='=>1, 'out_time -='=>1), array('id'=>$log['id']));
                }
                show_json(1, '成功');
            }

            $dict = array(
                1 => array(
                    1 => '卡还在，钱没了',
                    2 => '玛蛋，过完双十一，到年底都要吃土了',
                    3 => '剁手，再过双十一我是狗',
                    4 => '败家娘们，劳资一年的工资都被你花光了',
                    5 => '好想找个男朋友，来剁手',
                    6 => '为什么我的眼里常含泪水， 因为剁手之后疼的心碎',
                    7 => '快递，盼你像盼北方的暖气|秒杀成功，开心',
                    8 => '快递哥，不管你来不来，我都在这等着你|亲亲，清空购物车好感动',
                    9 => '还不发货，气的我咋了|快递，盼你像盼北方的暖气',
                    10 => '快递哥，不管你来不来，我都在这等着你',
                    11 => '还不发货，气的我咋了',

                ),
                2 => array(
                    1 => '双十一有男朋友买吗？',
                    2 => '静静看着你们剁手，我就是静静',
                    3 => '在最佳的时机买了最需要的东西，我，就是这么机智 ',
                    4 => '双十一，听说你剁手了？'
                ),
                3 => array(
                    1 => '双十一？关我毛事，NO care！',
                    2 => '一觉醒来，11.12昨天发生了什么',
                    3 => '听说双十一？听说你们又剁手了|就是四个光棍快乐的生活',
                    4 => '光棍节是啥，可以吃不'
                )
            );
            //数据库说明记录
            $username = trim($_GPC['username']) ? trim($_GPC['username']) : '马云';
            $type = intval($_GPC['type']);
            if (!in_array($type, array(1, 2, 3))) {
                $type = 1;
            }
            if ($username != '马云') {
                //更新用户信息
                $up = array('realname' => $username);
                if (pdo_tableexists('manor_shop_member')) {
                    pdo_update('manor_shop_member', $up, array('openid' => $openid, 'uniacid' => $_W['uniacid']));
                }
                if (!empty($member['uid'])) {
                    load()->model('mc');
                    mc_update($member['uid'], $up);
                }
            }
            $choose_pic = array_rand($dict[$type]);
            $image_url = '../addons/eleven_expression/data/base/'.$type.'_'.$choose_pic.'.jpg';
            //$image_url = '../addons/eleven_expression/data/base/1_8.jpg';
            //生成图片
            $dst = imagecreatefromstring(file_get_contents($image_url));
            $font = '../addons/eleven_expression/data/ziti.ttf';//字体
            $black = imagecolorallocate($dst, 0x00, 0x00, 0x00);//字体颜色
            //$black = imagecolorallocate($dst, 220, 20, 32);//字体颜色
            imagefttext($dst, 22, 10, 95, 535, $black, $font, $username);
            //输出图片
            list($dst_w, $dst_h, $dst_type) = getimagesize($image_url);
            $save_path = '../addons/eleven_expression/data/user/'.$openid.'_'.time().'.jpg';
            imagejpeg($dst, $save_path);
            imagedestroy($dst);
            if(!$log = pdo_get('eleven_expression_log', array('uniacid'=>$_W['uniacid'], 'pic_id'=>$type.'_'.$choose_pic))) {
                $insert = array(
                    'uniacid'=>$_W['uniacid'],
                    'again_times'=>0,
                    'out_time'=>1,
                    'create_time'=>time(),
                    'pic_id'=>$type.'_'.$choose_pic
                );
                pdo_insert('eleven_expression_log', $insert);
            } else {
                pdo_update('eleven_expression_log', array('out_time +='=>1), array('id'=>$log['id']));
            }
            show_json(1, array('path'=>$save_path.'?t='.time(), 'pic'=>$type.'_'.$choose_pic));
        }
        $share_data = json_encode(
            array(
                'title'=>'太好玩了!快来生成你的双11专属表情包。',
                'link'=>$_W['siteurl'],
                'imgUrl'=>tomedia('../addons/eleven_expression/data/base/1_2.jpg'),
                'desc'=>'长按二维码保存图片,把你的表情分享到朋友圈.'
            )
        );
        include $this->template('index');
	}

}