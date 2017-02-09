<?php
	/**
	 * 操作类名称: PhpStorm.
	 * 作者名称: alan
	 * 创建时间: 16/7/3 14:47
	 */

	include SEND_WALLET.'/WxPay.pub.config.php';
	require SEND_WALLET.'/sendWallet_app.php';
	global $_W;
	global $_GPC;
	$mch_billno = WxPayConf_pub::MCHID.date('YmdHis').rand(1000, 9999);
	$act_name = '红包';
	$openid = $_W['openid'];
	$amount = 100;
	$wishing = '恭喜您,获取红包';
//	if($_W['isajax']) {
		$sendWallet_app = new sendWallet();
		$SendRedpack = $sendWallet_app; //红包 与 企业付款公用 类

		$SendRedpack->set_mch_billno( $mch_billno );  				//唯一订单号
		//$SendRedpack->set_mch_id( WxPayConf_pub::MCHID ); 	     	// 商户号 默认已在配置文件中配置
		//$SendRedpack->set_wxappid( WxPayConf_pub::APPID );			// appid  默认已在配置文件中配置
		$SendRedpack->set_nick_name( $act_name );                		// 提供方名称     小农民科技
		$SendRedpack->set_send_name( $act_name );
		// 红包发送者名称  商户名称
		$SendRedpack->set_re_openid( $openid);						    // 用户在wxappid下的openid

		$SendRedpack->set_total_amount( $amount );  // 付款金额，单位分
		$SendRedpack->set_min_value( $amount );     // 最小红包金额，单位分
		$SendRedpack->set_max_value( $amount );     // 最大红包金额，单位分（ 最小金额等于最大金额： min_value=max_value =total_amount）
		$SendRedpack->set_total_num(1);		 	   // 红包发放总人数
		$SendRedpack->set_wishing($wishing);	   // 红包祝福语 感谢您参加猜灯谜活动，祝您元宵节快乐！
		$SendRedpack->set_client_ip( walletWeixinUtil::getRealIp() ); //调用接口的机器Ip地址

		$SendRedpack->set_act_name( $act_name );  // 活动名称 猜灯谜抢红包活动
		$SendRedpack->set_act_id(1);  					 // 活动id
		$SendRedpack->set_remark( $wishing ); 			 // 备注信息 猜越多得越多，快来抢！
		$SendRedpack->set_logo_imgurl('');			     // 商户logo的url
		$SendRedpack->set_share_content('');			 // 分享文案
		$SendRedpack->set_share_url('');				 // 分享链接
		$SendRedpack->set_share_imgurl('');				 // 分享的图片url
		$SendRedpack->set_nonce_str( walletWeixinUtil::getNonceStr() ); // 随机字符串
		// 得到签名和其它设置的 xml 数据
		$getNewData  = $SendRedpack->getSendRedpackXml($SendRedpack);
		$data 		 = walletWeixinUtil::curl_post_ssl($getNewData['api_url'], $getNewData['xml_data']);
		$res 		 = @simplexml_load_string($data,NULL,LIBXML_NOCDATA);


		if (!empty($res)){
			echo '<pre>';
			var_dump($res);
			echo '</pre>';
		}else{
			echo json_encode( array('return_code' => 'FAIL', 'return_msg' => 'redpack_接口出错', 'return_ext' => array() ));
		}

		exit;
//	}
	include $this->template('activity/index');