<script>
    localStorage.clear();
    echo '清除成功';
</script>
<?php
	/**
	 * 操作类名称: PhpStorm.
	 * 作者名称: alan
	 * 创建时间: 16/8/11 09:47
	 */

die;
require '../framework/bootstrap.inc.php';
$url = 'https://mp.weixin.qq.com/mp/profile_ext?action=home&__biz=MzAwMDc2MTM3MQ==#wechat_redirect';
load()->func('communication');
echo '<p><a href="weixin://contacts/profile/gh_7a5bcc46363d">唐盛庄园</a></p>';
echo '<p><a href="weixin://contacts/profile/gh_4356331966ed">黄陂微生活</a></p>';
die;
var_dump($resutl);



die;
require  IA_ROOT.'/addons/alan_ticket/core/function/global.func.php';

var_dump(getTicket(1,2));die;
/*$r = getUrlByTicket('gQEH8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyX0FEak0xcllick8xMDAwME0wN2sAAgR6LFpYAwQAAAAA',2);
var_dump($r);*/
$r = getTicket(1,2);
var_dump($r);
die;
set_time_limit(0);
global $_W;
@ini_set('memory_limit', '256M');
$dephp_29 = imagecreatetruecolor(414, 736);
$dephp_46 = createImage('http://127.0.0.1/tangshengmanor/api/bac.jpg');
imagecopy($dephp_29, $dephp_46, 0, 0, 0, 0, 640, 1008);
imagedestroy($dephp_46);
//头像 $dephp_26['left'], $dephp_26['top'], 0, 0, $dephp_26['width'], $dephp_26['height']
$dephp_47 =  array(
    'left'=>10,
    'top'=>10,
    'width'=>100,
    'height'=>100,
    'size'=>4,
    'color'=>'#fefefe'
);
$avatar = 'http://127.0.0.1/tangshengmanor/api/avtar.jpg';
$dephp_48 = preg_replace('/\/0$/i', '/96', $avatar);
$dephp_29 = mergeImage($dephp_29, $dephp_47, $dephp_48);
//二维码

//文字
$dephp_47 =  array(
    'left'=>45,
    'top'=>120,
    'width'=>100,
    'height'=>100,
    'size'=>10,
    'color'=>'#fefefe'
);
$dephp_29 = mergeText($dephp_29, $dephp_47, '兰辉');

$dephp_47 =  array(
    'left'=>175,
    'top'=>30,
    'width'=>100,
    'height'=>100,
    'size'=>10,
    'color'=>'#fefefe'
);
$dephp_29 = mergeText($dephp_29, $dephp_47, '我的梦想是成为科学家');
$dephp_47['top'] = 50;
$dephp_29 = mergeText($dephp_29, $dephp_47, '专门钻研自动化的伟大科学家');
$dephp_42 = IA_ROOT . '/api/';
$dephp_45 = time() . '.jpg';
imagepng($dephp_29, $dephp_42 . $dephp_45);
imagedestroy($dephp_29);


function getQR($dephp_2, $dephp_10, $dephp_8 = 0){
    global $_W, $_GPC;
    $dephp_12 = WeAccount :: create($dephp_21);
    $dephp_11 = pdo_fetch('select * from ' . tablename('manor_shop_poster_qr') . ' where openid=:openid and acid=:acid and type=4 limit 1', array(':openid' => $dephp_10['openid'], ':acid' => $dephp_21));
    if (empty($dephp_11)){
        $dephp_19 = $this -> getFixedTicket($dephp_2, $dephp_10, $dephp_12);
        if (is_error($dephp_19)){
            return $dephp_19;
        }
        if (empty($dephp_19)){
            return error(-1, '生成二维码失败');
        }
        $dephp_24 = $dephp_19['barcode'];
        $dephp_20 = $dephp_19['ticket'];
        $dephp_22 = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $dephp_20;
        $dephp_25 = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'scene_str' => $dephp_24['action_info']['scene']['scene_str'], 'model' => 2, 'name' => 'manor_shop_POSTER_QRCODE', 'keyword' => 'manor_shop_POSTER', 'expire' => 0, 'createtime' => time(), 'status' => 1, 'url' => $dephp_19['url'], 'ticket' => $dephp_19['ticket']);
        pdo_insert('qrcode', $dephp_25);
        $dephp_11 = array('acid' => $dephp_21, 'openid' => $dephp_10['openid'], 'type' => 4, 'scenestr' => $dephp_24['action_info']['scene']['scene_str'], 'ticket' => $dephp_19['ticket'], 'qrimg' => $dephp_22, 'url' => $dephp_19['url']);
        pdo_insert('manor_shop_poster_qr', $dephp_11);
        $dephp_11['id'] = pdo_insertid();
        $dephp_11['current_qrimg'] = $dephp_22;
    }else{
        $dephp_11['current_qrimg'] = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $dephp_11['ticket'];
    }
    return $dephp_11;
}

function createImage($dephp_27){
    load() -> func('communication');
    $dephp_28 = ihttp_request($dephp_27);
    return imagecreatefromstring($dephp_28['content']);
}
function mergeImage($dephp_29, $dephp_26, $dephp_27){
    $dephp_30 = createImage($dephp_27);
    $dephp_31 = imagesx($dephp_30);
    $dephp_32 = imagesy($dephp_30);
    imagecopyresized($dephp_29, $dephp_30, $dephp_26['left'], $dephp_26['top'], 0, 0, $dephp_26['width'], $dephp_26['height'], $dephp_31, $dephp_32);
    imagedestroy($dephp_30);
    return $dephp_29;
}
function mergeText($dephp_29, $dephp_26, $dephp_33){
    $dephp_34 = IA_ROOT . '/addons/manor_shop/static/fonts/msyh.ttf';
    $dephp_35 = hex2rgb($dephp_26['color']);
    $dephp_36 = imagecolorallocate($dephp_29, $dephp_35['red'], $dephp_35['green'], $dephp_35['blue']);
    imagettftext($dephp_29, $dephp_26['size'], 0, $dephp_26['left'], $dephp_26['top'] + $dephp_26['size'], $dephp_36, $dephp_34, $dephp_33);
    return $dephp_29;
}
function hex2rgb($dephp_37){
    if ($dephp_37[0] == '#'){
        $dephp_37 = substr($dephp_37, 1);
    }
    if (strlen($dephp_37) == 6){
        list($dephp_38, $dephp_39, $dephp_40) = array($dephp_37[0] . $dephp_37[1], $dephp_37[2] . $dephp_37[3], $dephp_37[4] . $dephp_37[5]);
    }elseif (strlen($dephp_37) == 3){
        list($dephp_38, $dephp_39, $dephp_40) = array($dephp_37[0] . $dephp_37[0], $dephp_37[1] . $dephp_37[1], $dephp_37[2] . $dephp_37[2]);
    }else{
        return false;
    }
    $dephp_38 = hexdec($dephp_38);
    $dephp_39 = hexdec($dephp_39);
    $dephp_40 = hexdec($dephp_40);
    return array('red' => $dephp_38, 'green' => $dephp_39, 'blue' => $dephp_40);
}





































die;
include '../framework/bootstrap.inc.php';
	include './financial.php';

	$financial = new financial(130);
	$op = $_REQUEST['op'] ? trim($_REQUEST['op']) : '';

	switch ($op) {
		case  'login':
			//登录
			$r = $financial->loginAction(18588930193, 123456, $openid=111);
			var_dump($r);die;
			break;
		case  'exist':
			//登录
			$r = $financial->phoneExistAction(18588930161);
			var_dump($r);
			break;
		case  'send':
			//登录
			$r = $financial->sendMsgAction(18588930161);
			var_dump($r);
			break;
		case 'register':
			$code = $_GET['code'] ? $_GET['code'] : '111111';
			$r = $financial->registerAction(130,18588930193, $code, NULL, 123456, 1, 1);
			var_dump($r);
			//注册
			break;
		case 'msgtest':
			$code = $_GET['code'] ? $_GET['code'] : '111111';
			$r = $financial->sendMsgTestAction(18588930193);
			var_dump($r);
			//注册
			break;
		case 'bind':
			$r = $financial->bindWchatAction(111111,18588930174, 116);
			var_dump($r);
			break;
		case 'forget':
			$r = $financial->forgetPasswordAction(18588930174, 111111, 123123);
			var_dump($r);
			//注册
			break;
		case 'collect':
			$str = array(
				'subscribe'=>1,
				'openid'=>'o6_bmjrPTlm6_2sgVt7hMZOPfL2M',
				'nickname'=>'Band',
				'sex'=>1,
				'language'=>'zh_CN',
				'city'=>'广州',
				'province'=>'广东',
				'country'=>'中国',
				'headimgurl'=>'http://wx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0',
				'subscribe_time'=>'1382694957',
				'unionid'=>'o6_bmasdasdsad6_2sgVt7hMZOPfL',
				'remark'=>'',
				'groupid'=>0
			);
			$code = $_GET['code'] ? $_GET['code'] : '';
			$r = $financial->collectAction(18588930161, $str);
			var_dump($r);
			break;
		case 'init':
			//初始化
			break;
		case 'update':
			//完善资料
			break;
        case 'coupon':
            $rs = $financial->check_coupon_code(222222, 123);
            var_dump($rs);
            break;
        case 'red':
            $rs = $financial->check_red_code('TDnvdFj6b', array('id'=>12,'nickname'=>"次傲风"));
            var_dump($rs);
            break;
		default:
			/*$str = 'bIl0u4R51470910565/sso/auth/phone/exist/18588930161GETChromebIl0u4R5';
			echo md5($str);*/
			/*Requests::register_autoloader();
			$request = Requests::get('https://www.baidu.com');
			var_dump($request);*/
			//$r = $financial->actionAuth();
			break;
	}


	if($_GET['t']) {
		$r = $financial->actionAuth();
	}
	if($_GET['a']) {
		$r = $financial->gender();
	}


