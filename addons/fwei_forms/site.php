<?php
/**
 * 通用表单模块微站定义
 *
 * @author fwei.net
 * @url http://www.fwei.net/
 */
defined('IN_IA') or exit('Access Denied');

class Fwei_formsModuleSite extends WeModuleSite {

	public function getItemTiles() {
        global $_W;
        $urls = array();
        $forms = pdo_fetchall("SELECT rid, title FROM " . tablename('fwei_forms') . " WHERE uniacid = '{$_W['uniacid']}'");
        if (!empty($forms)) {
            foreach ($forms as $row) {
                $urls[] = array('title' => $row['title'], 'url' => $this->createMobileUrl('forms', array('id' => $row['rid'])));
            }
            return $urls;
        }
    }

	public function doWebCreate(){
		header('Location:'.url('platform/reply/post',array('m'=>'fwei_forms')));
	}

	public function doWebPreview(){
		global $_GPC;
		header('Location:'.'../app/'.$this->createMobileUrl('forms', array('m'=>'fwei_forms', 'id'=>$_GPC['id'],'_openid'=>'fromUser')));
	}

    public function send_template_message($data)
    {
        global $_W, $_GPC;
        $atype        = 'weixin';
        $account_code = "account_weixin_code";
        load()->classs('weixin.account');
        $access_token = WeAccount::token();
        $url          = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $access_token;
        $response     = ihttp_request($url, $data);
        if (is_error($response)) {
            return error(-1, "访问公众平台接口失败, 错误: {$response['message']}");
        }
        $result = @json_decode($response['content'], true);
        if (empty($result)) {
            return error(-1, "接口调用失败, 原数据: {$response['meta']}");
        } elseif (!empty($result['errcode'])) {
            return error(-1, "访问微信接口错误, 错误代码: {$result['errcode']}, 错误信息: {$result['errmsg']},信息详情：{$this->error_code($result['errcode'])}");
        }
        return true;
    }
}