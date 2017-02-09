<?php
/**
 * 唐代金融发红包模块定义
 *
 * @author 诗意的边缘
 * @url http://s.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Td_redenvelopesModule extends WeModule {
	public function fieldsFormDisplay($rid = 0) {
		//要嵌入规则编辑页的自定义内容，这里 $rid 为对应的规则编号，新增时为 0
	}

	public function fieldsFormValidate($rid = 0) {
		//规则编辑保存时，要进行的数据验证，返回空串表示验证无误，返回其他字符串将呈现为错误提示。这里 $rid 为对应的规则编号，新增时为 0
		return '';
	}

	public function fieldsFormSubmit($rid) {

		//规则验证无误保存入库时执行，这里应该进行自定义字段的保存。这里 $rid 为对应的规则编号
	}

	public function ruleDeleted($rid) {
		//删除规则时调用，这里 $rid 为对应的规则编号
	}

	public function settingsDisplay($setting) {
		global $_W, $_GPC;

		//点击模块设置时将调用此方法呈现模块设置页面，$settings 为模块设置参数, 结构为数组。这个参数系统针对不同公众账号独立保存。
		//在此呈现页面中自行处理post请求并保存设置参数（通过使用$this->saveSettings()来实现）
		if(checksubmit()) {
			//字段验证, 并获得正确的数据$dat
            if(!$_GPC['app_id']) {
                message('请输入app_id');
            }
            if(!$_GPC['app_mchid']) {
                message('请输入app_mchid');
            }
            $dat = array(
                'app_id'=>trim($_GPC['app_id']),
                'app_mchid'=>trim($_GPC['app_mchid']),
                'total_amount'=>$_GPC['total_amount'],
                'notice_openid'=>$_GPC['notice_openid'],
                'red_power'=>$_GPC['red_power'] ? $_GPC['red_power'] : 1,
            );
            if($_FILES['weixin_cert_file']['name']){
                $dat['cert'] = $this->upload_cert('weixin_cert_file');
            } else {
                $dat['cert'] = $_GPC['cert'];
            }
            if($_FILES['weixin_key_file']['name']){
                $dat['key'] = $this->upload_cert('weixin_key_file');
            } else {
                $dat['key'] = $_GPC['key'];
            }
            if($_FILES['weixin_root_file']['name']){
                $dat['root'] = $this->upload_cert('weixin_root_file');
            } else {
                $dat['root'] = $_GPC['root'];
            }
			if($this->saveSettings($dat)) {
                message('修改成功', referer(), 'success');
            };
		}
		//这里来展示设置项表单
		include $this->template('setting');
	}
    function upload_cert($dephp_0){
        global $_W;
        $dephp_1 = IA_ROOT . '/addons/td_redenvelopes/hongbao/zhengshu';
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
            return file_put_contents($dephp_1.'/'.$dephp_2, file_get_contents($dephp_5));
        }
        return "";
    }
}