<?php
/**
 * 场景二维码模块定义
 *
 * @author 诗意的边缘
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
    require IA_ROOT. '/addons/alan_qrcode/core/common/defines.php';
    require ALAN_CORE . 'class/alanloader.class.php';
    alanload()->func('global');
class Alan_qrcodeModule extends WeModule {
	public function fieldsFormDisplay($rid = 0) {

	    global $_W;
        if(!empty($rid)) {
            $item = pdo_fetch("SELECT * FROM ".tablename('alan_qrcode')." WHERE rid = :rid", array(':rid' => $rid));
        }
		//要嵌入规则编辑页的自定义内容，这里 $rid 为对应的规则编号，新增时为 0
        $fansgroup = pdo_getall('alan_qrcode_fans_group', array('uniacid'=>$_W['uniacid']), array('id', 'name'));
        load()->func('tpl');
        include $this->template('rule');
	}

	public function fieldsFormValidate($rid = 0) {
		//规则编辑保存时，要进行的数据验证，返回空串表示验证无误，返回其他字符串将呈现为错误提示。这里 $rid 为对应的规则编号，新增时为 0
        global $_W, $_GPC;
        if(empty($_GPC['name'])) {
            return '请填写场景名称';
        }
        if(empty($_GPC['group_id'])) {
            return '选择分组';
        }
		return '';
	}

	public function fieldsFormSubmit($rid) {
		//规则验证无误保存入库时执行，这里应该进行自定义字段的保存。这里 $rid 为对应的规则编号
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        load()->func('communication');
        $acid = intval($_W['acid']);
        load()->func('file');
        $path = IA_ROOT.'/attachment/'.ALAN_NAME.'/'.date('Y').'/'.date('m').'/'.date('d').'/'.time().'.png';
        if(!is_dir(IA_ROOT.'/attachment/'.ALAN_NAME.'/'.date('Y').'/'.date('m').'/'.date('d'))) {
            mkdirs(IA_ROOT.'/attachment/'.ALAN_NAME.'/'.date('Y').'/'.date('m').'/'.date('d'));
        }
        $id = intval($rid);
        $uniacid = $_W['uniacid'];
        load()->func('communication');
        $acid = intval($_W['acid']);
        $insert_data = array(
            'name'=>$_GPC['name'],
            'title'=>$_GPC['name'],
            'keyword'=>$_GPC['name'],
            'group_id'=>$_GPC['group_id'],
            'rid'=>$id
        );
        $config = cache_load('alan_qrcode_config');
        if(!$config) {
            message('请设置配置信息', 'referer', 'error');
        }
        $insert_data['model'] = $config['mode'];
        $info = pdo_fetch( 'SELECT * FROM '.tablename('alan_qrcode').' WHERE uniacid = :uniacid AND rid = :id' , array(':uniacid' => $uniacid,':id'=>$id));
        if( $info ){
            pdo_update('alan_qrcode', $insert_data, array('id'=>$id));
        } else {
            $insert_data['uniacid'] = $uniacid;
            $insert_data['create_time'] = TIMESTAMP;
            $uniacccount = WeAccount::create($acid);
            $barcode = array(
                'expire_seconds' => '',
                'action_name'    => '',
                'action_info'    => array(
                    'scene' => array(),
                ),
            );
            $insert_data['scan_key'] = getRandChar(11);
            $barcode['action_info']['scene']['scene_str'] = $insert_data['scan_key'];
            $barcode['action_name'] = 'QR_LIMIT_STR_SCENE';
            $result = $uniacccount->barCodeCreateFixed($barcode);
            if(!is_error($result)) {
                $_wx_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$result['ticket'];
                $insert_data['ticket'] = $result['ticket'];
                $insert_data['wx_url'] = $_wx_url;
                $insert_data['expire'] = $result['expire_seconds'];
                $cont = file_get_contents($_wx_url);
                if($config['mode'] == 1) {
                    $config['logoqrwidth'] = 5;
                    $save_path = qrcode_logo($config, $path, $result['url'], TRUE);
                }else {
                    $config['logoqrwidth'] = 20;
                    $save_path = qrcode_logo($config, $path, $result['url']);
                }
                if(is_error($result)) {
                    message("生成失败");
                }
            }else {
                message("公众平台返回接口错误. <br />错误代码为: {$result['errorcode']} <br />错误信息为: {$result['message']}");
            }
            $insert_data['path'] = $save_path;
            $insert_data['wx_data'] = json_encode($result);
            pdo_insert('alan_qrcode', $insert_data);
        }
	}

	public function ruleDeleted($rid) {
		//删除规则时调用，这里 $rid 为对应的规则编号
        global $_W;
        $qrcode = pdo_get('alan_qrcode', array('uniacid'=>$_W['uniacid'], 'rid'=>$rid));
        pdo_delete('alan_qrcode', array('uniacid'=>$_W['uniacid'], 'rid'=>$rid));
        pdo_delete('alan_qrcode_fans', array('uniacid'=>$_W['uniacid'], 'qrcode_id'=>$qrcode['id'], 'group_id'=>$qrcode['group_id']));
        pdo_delete('alan_qrcode_stat', array('uniacid'=>$_W['uniacid'], 'qrcode_id'=>$qrcode['id'], 'group_id'=>$qrcode['group_id']));
        pdo_delete('alan_qrcode_record', array('uniacid'=>$_W['uniacid'], 'qrcode_id'=>$qrcode['id'], 'group_id'=>$qrcode['group_id']));
	}

	public function settingsDisplay($settings) {
		global $_W, $_GPC;
		//点击模块设置时将调用此方法呈现模块设置页面，$settings 为模块设置参数, 结构为数组。这个参数系统针对不同公众账号独立保存。
		//在此呈现页面中自行处理post请求并保存设置参数（通过使用$this->saveSettings()来实现）
		if(checksubmit()) {
			//字段验证, 并获得正确的数据$dat
            $dat = array(
                'mode'=>$_GPC['mode'],
                'qrcode_background'=>$_GPC['qrcode_background'],
                'qrleft'=>$_GPC['qrleft'],
                'qrtop'=>$_GPC['qrtop'],
                'qrwidth'=>$_GPC['qrwidth'],
                'qrheight'=>$_GPC['qrheight'],
                'qrcode_logo'=>$_GPC['qrcode_logo'],
                'logowidth'=>$_GPC['logowidth'],
                'logoheight'=>$_GPC['logoheight'],
                'logoqrwidth'=>$_GPC['logoqrwidth'],
                'logoqrheight'=>$_GPC['logoqrheight'],
                'integral'=>intval($_GPC['integral']),
                'balance'=>intval($_GPC['balance']),
            );
            if ($this->saveSettings($dat)) {
                cache_write('alan_qrcode_config', $dat);
                message('保存成功', 'refresh');
            }
		}
		//这里来展示设置项表单
		include $this->template('setting');
	}

}