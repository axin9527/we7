<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
global $_W, $_GPC;

function upload_cert($dephp_0){
    global $_W;
    $dephp_1 = IA_ROOT . '/addons/manor_shop/cert';
    load() -> func('file');
    mkdirs($dephp_1, '0777');
    $dephp_2 = $dephp_0 . '_' . $_W['uniacid'] . '.pem';
    $dephp_3 = $dephp_1 . '/' . $dephp_2;
    $dephp_4 = $_FILES[$dephp_0]['name'];
    $dephp_5 = $_FILES[$dephp_0]['tmp_name'];
    if (!empty($dephp_4) && !empty($dephp_5)){
        $dephp_6 = strtolower(substr($dephp_4, strrpos($dephp_4, '.')));
        if ($dephp_6 != '.pem'){
            $dephp_7 = "";
            if($dephp_0 == 'weixin_cert_file'){
                $dephp_7 = 'CERT文件格式错误';
            }else if($dephp_0 == 'weixin_key_file'){
                $dephp_7 = 'KEY文件格式错误';
            }else if($dephp_0 == 'weixin_root_file'){
                $dephp_7 = 'ROOT文件格式错误';
            }
            message($dephp_7 . ',请重新上传!', '', 'error');
        }
        return file_get_contents($dephp_5);
    }
    return "";
}
$op = empty($_GPC['op']) ? 'shop' : trim($_GPC['op']);
$setdata = pdo_fetch('select * from ' . tablename('manor_shop_sysset') . ' where uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
$set = unserialize($setdata['sets']);
$oldset = unserialize($setdata['sets']);
if ($op == 'template'){
    $styles = array();
    $dir = IA_ROOT . '/addons/manor_shop/template/mobile/';
    if ($handle = opendir($dir)){
        while (($file = readdir($handle)) !== false){
            if ($file != '..' && $file != '.'){
                if (is_dir($dir . '/' . $file)){
                    $styles[] = $file;
                }
            }
        }
        closedir($handle);
    }
}else if ($op == 'notice'){
    $salers = array();
    if (isset($set['notice']['openid'])){
        if (!empty($set['notice']['openid'])){
            $openids = array();
            $strsopenids = explode(',', $set['notice']['openid']);
            foreach ($strsopenids as $openid){
                $openids[] = '\'' . $openid . '\'';
            }
            $salers = pdo_fetchall('select id,nickname,avatar,openid from ' . tablename('manor_shop_member') . ' where openid in (' . implode(',', $openids) . ") and uniacid={$_W['uniacid']}");
        }
    }
    $newtype = explode(',', $set['notice']['newtype']);
}else if ($op == 'pay'){
    $sec = m('common') -> getSec();
    $sec = iunserializer($sec['sec']);
} else if($op == 'activity' && $_GPC['pp'] == 'send_grapefruit') {
    if($_GPC['act'] == 'set') {
        $id = $_GPC['id'];
        if(!$id) {
            message('参数错误', referer(), 'error');
        }
        pdo_update('manor_shop_grapefruit', array('status'=>1), array('id'=>$id, 'uniacid'=>$_W['uniacid']));
        message('设置中奖成功', referer(), 'success');
    } elseif($_GPC['act'] == 'del') {
        $id = $_GPC['id'];
        if(!$id) {
            message('参数错误', referer(), 'error');
        }
        pdo_delete('manor_shop_grapefruit', array('id'=>$id, 'uniacid'=>$_W['uniacid']));
        message('删除成功', referer(), 'success');
    }
    $pindex = max(1, intval($_GPC['page']));
    $psize = 20;
    $condition = " and uniacid=:uniacid";
    $params = array(':uniacid' => $_W['uniacid']);
    if (!empty($_GPC['keyword'])){
        $_GPC['keyword'] = trim($_GPC['keyword']);
        $condition .= ' and ( real_name like :keyword or mobile like :keyword)';
        $params[':keyword'] = "%{$_GPC['keyword']}%";
    }
    if (empty($starttime) || empty($endtime)){
        $starttime = strtotime('-1 month');
        $endtime = time();
    }
    if (!empty($_GPC['searchtime'])){
        $starttime = strtotime($_GPC['time']['start']);
        $endtime = strtotime($_GPC['time']['end']);
        if (!empty($timetype)){
            $condition .= " AND createtime >= :starttime AND createtime <= :endtime ";
            $params[':starttime'] = $starttime;
            $params[':endtime'] = $endtime;
        }
    }
    $list = pdo_fetchall("SELECT  * FROM " . tablename('manor_shop_grapefruit') . " WHERE 1 {$condition} ORDER BY createtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
    $total = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('manor_shop_grapefruit') . " WHERE 1 {$condition} ", $params);
    $pager = pagination($total, $pindex, $psize);
}
if (checksubmit()){
    if($op == 'shop'){
        $shop = is_array($_GPC['shop']) ? $_GPC['shop'] : array();
        $set['shop']['name'] = trim($shop['name']);
        $set['shop']['img'] = save_media($shop['img']);
        $set['shop']['logo'] = save_media($shop['logo']);
        $set['shop']['signimg'] = save_media($shop['signimg']);
        $set['shop']['diycode'] = $_POST['shop']['diycode'];
        $set['shop']['close'] = intval($shop['close']);
        $set['shop']['closedetail'] = htmlspecialchars_decode($shop['closedetail']);
        $set['shop']['closeurl'] = trim($shop['closeurl']);
        $set['shop']['search'] = trim($shop['search']);
        plog('sysset.save.shop', '修改系统设置-商城设置');
    }elseif($op == 'follow'){
        $set['share'] = is_array($_GPC['share']) ? $_GPC['share'] : array();
        $set['share']['icon'] = save_media($set['share']['icon']);
        plog('sysset.save.follow', '修改系统设置-分享及关注设置');
    }else if($op == 'notice'){
        $set['notice'] = is_array($_GPC['notice']) ? $_GPC['notice'] : array();
        if (is_array($_GPC['openids'])){
            $set['notice']['openid'] = implode(',', $_GPC['openids']);
        }
        $set['notice']['newtype'] = $_GPC['notice']['newtype'];
        if(is_array($set['notice']['newtype'])){
            $set['notice']['newtype'] = implode(',', $set['notice']['newtype']);
        }
        plog('sysset.save.notice', '修改系统设置-模板消息通知设置');
    }elseif($op == 'trade'){
        $set['trade'] = is_array($_GPC['trade']) ? $_GPC['trade'] : array();
        if (!$_W['isfounder']){
            unset($set['trade']['receivetime']);
            unset($set['trade']['closeordertime']);
            unset($set['trade']['paylog']);
        }else{
            m('cache') -> set('receive_time', $set['trade']['receivetime'], 'global');
            m('cache') -> set('closeorder_time', $set['trade']['closeordertime'], 'global');
            m('cache') -> set('paylog', $set['trade']['paylog'], 'global');
        }
        plog('sysset.save.trade', '修改系统设置-交易设置');
    }elseif($op == 'pay'){
        $set['pay'] = is_array($_GPC['pay']) ? $_GPC['pay'] : array();
        if($_FILES['weixin_cert_file']['name']){
            $sec['cert'] = upload_cert('weixin_cert_file');
        }
        if($_FILES['weixin_key_file']['name']){
            $sec['key'] = upload_cert('weixin_key_file');
        }
        if($_FILES['weixin_root_file']['name']){
            $sec['root'] = upload_cert('weixin_root_file');
        }
        if(empty($sec['cert']) || empty($sec['key']) || empty($sec['root'])){
        }
        pdo_update('manor_shop_sysset', array('sec' => iserializer($sec)), array('uniacid' => $_W['uniacid']));
        plog('sysset.save.pay', '修改系统设置-支付设置');
    }elseif($op == 'template'){
        $shop = is_array($_GPC['shop']) ? $_GPC['shop'] : array();
        $set['shop']['style'] = save_media($shop['style']);
        m('cache') -> set('template_shop', $set['shop']['style']);
        plog('sysset.save.pay', '修改系统设置-模板设置');
    }elseif($op == 'member'){
        $shop = is_array($_GPC['shop']) ? $_GPC['shop'] : array();
        $set['shop']['levelname'] = trim($shop['levelname']);
        $set['shop']['levelurl'] = trim($shop['levelurl']);
        $set['shop']['leveltype'] = intval($shop['leveltype']);
        plog('sysset.save.pay', '修改系统设置-会员设置');
    }elseif($op == 'category'){
        $shop = is_array($_GPC['shop']) ? $_GPC['shop'] : array();
        $set['shop']['catlevel'] = trim($shop['catlevel']);
        $set['shop']['catshow'] = intval($shop['catshow']);
        $set['shop']['catadvimg'] = save_media($shop['catadvimg']);
        $set['shop']['catadvurl'] = trim($shop['catadvurl']);
        $set['shop']['tuijian'] = intval($shop['tuijian']);
        plog('sysset.save.pay', '修改系统设置-分类层级设置');
    }elseif($op == 'contact'){
        $shop = is_array($_GPC['shop']) ? $_GPC['shop'] : array();
        $set['shop']['qq'] = trim($shop['qq']);
        $set['shop']['address'] = trim($shop['address']);
        $set['shop']['phone'] = trim($shop['phone']);
        $set['shop']['description'] = trim($shop['description']);
        plog('sysset.save.pay', '修改系统设置-联系方式设置');
    } elseif($op == 'activity') {
        $p = $_GPC['pp'];
        if($p == 'haier') {
            $shop = is_array($_GPC['shop']) ? $_GPC['shop'] : array();
            $set['shop']['haier_power'] = intval($shop['haier_power']);
            $set['shop']['haier_coupon'] = $_GPC['haier_coupon'];
            plog('sysset.save.activity', '修改系统活动设置-海尔活动设置');
        } elseif($p == 'ayimabang') {
            //姨妈帮公众号活动设置
            $shop = is_array($_GPC['shop']) ? $_GPC['shop'] : array();
            $set['shop']['yimabang_power'] = intval($shop['yimabang_power']);
            $set['shop']['yimabang_coupon'] = $shop['ayibang_coupon'];
            $set['shop']['ayibang_qr'] = $shop['ayibang_qr'];
            plog('sysset.save.activity', '修改系统活动设置-阿姨帮活动设置');
        } elseif ($p == 'shuangshiyi') {
            //双十一生成图片分享
            $shop = is_array($_GPC['shop']) ? $_GPC['shop'] : array();
            $set['shop']['shuangshiyi_power'] = intval($shop['shuangshiyi_power']);
            plog('sysset.save.activity', '修改系统活动设置-双十一生成图分享活动设置');
        }elseif ($p == 'send_grapefruit') {
            //免费送柚子
            $shop = is_array($_GPC['shop']) ? $_GPC['shop'] : array();
            $set['shop']['grapefruit_power'] = intval($shop['grapefruit_power']);
            $set['shop']['grapefruit_coupon'] = intval($shop['grapefruit_coupon']);
            $set['shop']['grapefruit_num'] = intval($shop['grapefruit_num']);
            $set['shop']['grapefruit_use_num'] = intval($shop['grapefruit_num']);
            plog('sysset.save.activity', '修改系统活动设置-0元抽送柚子动设置');
        }
    }
    $data = array('uniacid' => $_W['uniacid'], 'sets' => iserializer($set));
    if (empty($setdata)){
        pdo_insert('manor_shop_sysset', $data);
    }else{
        pdo_update('manor_shop_sysset', $data, array('uniacid' => $_W['uniacid']));
    }
    $setdata = pdo_fetch('select * from ' . tablename('manor_shop_sysset') . ' where uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
    m('cache') -> set('sysset', $setdata);
    message('设置保存成功!', $this -> createWebUrl('sysset', array('op' => $op, 'pp'=>$p)), 'success');
}
$coupon = pdo_getall('manor_shop_coupon', array('uniacid'=>$_W['uniacid']), array('id', 'couponname'));
$_coupon = json_encode($coupon);
$coupon_group = pdo_getall('manor_shop_coupon_category', array('uniacid'=>$_W['uniacid']));
if(pdo_tableexists('alan_qrcode')) {
    $qr = pdo_getall('alan_qrcode', array('uniacid'=>$_W['uniacid']));
} else {
    $qr = array();
}
$h = range(1, 23);
$m = range(0, 59);
load() -> func('tpl');
include $this -> template('web/sysset/' . $op);
exit;
