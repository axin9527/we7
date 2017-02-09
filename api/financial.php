<?php
	/**
	 * 操作类名称: 对接金融超市api.
	 * 作者名称: alan
	 * 创建时间: 16/8/8 13:33
	 */
/*define('IN_SYS', TRUE);*/
//include '../framework/bootstrap.inc.php';
include(IA_ROOT.'/framework/library/request/Requests.php');
include(IA_ROOT.'/framework/library/request/Requests/Cookie.php');
include(IA_ROOT.'/framework/library/rsa/RSA.php');

	class financial
	{
		protected $_W = '';
		protected $_GPC = '';
		protected $header = '';
		//const DOMAIN = 'https://sso.tangcredit.com/';
//		const DOMAIN = 'http://192.168.31.34:8888/sso/', TEST = '/sso/';
		const DOMAIN = 'http://123.56.143.169:8015/sso/', TEST = '/sso/';
		//登录
		const LOGIN = 'auth/login';
		//注册
		const REGISTER = 'auth/register';
		//发送短信验证码
		const SEND_MSG = 'auth/verificationCode/';
		const SEND_MSG_TEST = 'auth/verificationCode/test';
		//查询手机号是否存在
		const CHECK_MOBILE = 'auth/phone/exist';
		//关注数据同步收集
		const INIT_COLLECT = 'auth/collect';
		//修改用户信息
		const UPDATE_USER_INFO = 'auth/user';
		//修改密码
		const UPDATE_PASSWORD = 'auth/user/password';
		//手机号更换
		const UPDATE_MOBILE = 'auth/user/phone';
		//绑定邮箱
		const BIND_EMAIL = 'auth/user/mail';
		//绑定微信
		const BIND_WECHAT = 'auth/unite/phone';
		//验证token是否失效
		const AUTH_TOKEN = 'auth/authToken';

		/**
		 * 组建header 头部签名信息
		 * financial constructor.
		 * @param string $uid
		 */
		public function __construct($uid='')
		{
            global $_W, $_GPC;
			$this->$_W = $_W;
			$this->$_GPC = $_GPC;
			load()->func('communication');
			$timestamp = TIMESTAMP;
			Requests::register_autoloader();
			$nonce = random(8);
			$http_user_agent = random(8);
			$toe = $nonce . $timestamp . "*uri*" . "*method*" . $http_user_agent . $nonce . "*body*" . "*token*";
			$header_array = array(
				'token'      => '',
				'Timestamp'  => $timestamp,
				'Nonce'      => $nonce,
				'User-Agent' => $http_user_agent . $nonce,
				'Toe'        => $toe,
				'Content-Type'=>'application/json'
			);
			$this->header = $header_array;
			//$this->actionAuthToken($uid);
		}

		/**
		 * 1、验证手机号是否存在
		 * @author 兰辉
		 * @access public
		 * @param $mobile
		 * @since  1.0
		 * @return array
		 */
		public function phoneExistAction($mobile)
		{
			$toe = $this->header['Toe'];
			$toe = str_replace(array('*uri*', '*method*', '*body*', '*token*'), array(self::TEST . self::CHECK_MOBILE . '/' . $mobile, 'GET', '', ''), $toe);
			$this->header['Toe'] = md5($toe);
			if(!$this->header['token']) {
				unset($this->header['token']);
			}
			$result = Requests::get(self::DOMAIN . self::CHECK_MOBILE . '/' . $mobile, $this->header);

			$res = json_decode(json_encode($result));

			return json_decode($res->body, TRUE);
		}

		/**
		 * 2、发送手机号验证码
		 * @author 兰辉
		 * @access public
		 * @param $mobile
		 * @since  1.0
		 * @return array
		 */
		public function sendMsgAction($mobile)
		{
			$toe = $this->header['Toe'];
			$toe = str_replace(array('*uri*', '*method*', '*body*', '*token*'), array(self::TEST . self::SEND_MSG . '/' . $mobile, 'GET', '', ''), $toe);
			$this->header['Toe'] = md5($toe);
			if(!$this->header['token']) {
				unset($this->header['token']);
			}
			$result = Requests::get(self::DOMAIN . self::SEND_MSG . '/' . $mobile, $this->header);
			$res = json_decode(json_encode($result));

			return json_decode($res->body, TRUE);
		}

		public function sendMsgTestAction($mobile)
		{
			$toe = $this->header['Toe'];
			$toe = str_replace(array('*uri*', '*method*', '*body*', '*token*'), array(self::TEST . self::SEND_MSG_TEST . '/' . $mobile, 'GET', '', ''), $toe);
			$this->header['Toe'] = md5($toe);
			if(!$this->header['token']) {
				unset($this->header['token']);
			}
			$result = Requests::get(self::DOMAIN . self::SEND_MSG_TEST . '/' . $mobile, $this->header);
			$res = json_decode(json_encode($result));
			return json_decode($res->body, TRUE);
		}

		/**
		 * 3、登录
		 * @author 兰辉
		 * @access public
		 * @param $phone
		 * @param $password
		 * @param $uid
		 * @since  1.0
		 * @return array
		 */
		public function loginAction($phone, $password, $uid)
		{
			/*$return_error = array(201, '该手机号已经存在');
			$mobile_exist = $this->phoneExistAction($phone);
			if($mobile_exist['ret'] == 1) {
				return $return_error;
			}*/
			$params = array(
				'source'   => 'tszy',
				'phone' => trim($phone),
				'password' => sha1(md5($password).$phone),
				'loginIp'  => getip(),
			);
			$_toe = $this->header['Toe'];
			$toe = str_replace(array('*uri*', '*method*', '*body*', '*token*'), array(self::TEST . self::LOGIN, 'PUT', json_encode($params),''), $_toe);
			$toe2 = str_replace(array('*uri*', '*method*', '*body*', '*token*'), array("*uri*", 'GET', '','*token*'), $_toe);
			$this->header['Toe'] = md5($toe);
			if(!$this->header['token']) {
				unset($this->header['token']);
			}
			$result = Requests::put(self::DOMAIN . self::LOGIN, $this->header, json_encode($params));
			$res = json_decode(json_encode($result));
			$result_data = json_decode($res->body, TRUE);
			if($result_data['ret'] == 1) {
				$this->actionForAuthUrl($result, $toe2);
			}
			return $result_data;
		}

		/**
		 * 分发url设置cookie
		 * @author 兰辉
		 * @access public
		 * @param $result
		 * @since  1.0
		 */
		protected function actionForAuthUrl($result, $toe2) {
			$cookie = (array)$result->cookies;
			$cookie_token = $cookie_attributes = array();
			foreach($cookie as  $k=>$v){
				$cookie_token = (array)$v['token'];
			}
			$_attributes = (array)$cookie_token['attributes'];
			foreach($_attributes as $Key=>$value) {
				$cookie_attributes = $value;
			}
			$res = json_decode(json_encode($result));
			$result_data = json_decode($res->body, TRUE);
			$_toe2 = str_replace(array('*token*'), array(""), $toe2);
			$cookie = new Requests_Cookie($cookie_token['name'], $cookie_token['value'], $cookie_attributes);
			$set_cookie = $cookie->formatForSetCookie();
			$this->header['Cookie'] = $set_cookie;
			if($result_data['urls']) {
				foreach($result_data['urls'] as $kk=>$vv) {
					$uri = substr($vv, strpos($vv, '/sso/'));
					$md5_toe = str_replace('*uri*', $uri, $_toe2);
					$this->header['Toe'] = md5($md5_toe);
					Requests::get($vv, $this->header, array('timeout'=>1.5));
				}
			}
		}


		/**
		 * 对rsa进行解密操作
		 * @author 兰辉
		 * @access public
		 * @since  1.0
		 */
		public function actionAuth() {
			$encrypted = $_GET['t'];
			$path = IA_ROOT.'/data/cert/priv.key';
			$private_key = file_get_contents($path);
			$pi_key = openssl_pkey_get_private($private_key);
			openssl_private_decrypt(base64_decode($encrypted),$decrypted,$pi_key);//私钥解密
			$decrypted = json_decode($decrypted, TRUE);
			if($decrypted['token']) {
				$this->refresh_token($decrypted['token'], $decrypted['id'], $decrypted['lifeTime']);
			}
			echo json_encode(array('status'=>1, 'msg'=>'成功', 'data'=>$decrypted));
		}

		/*public function gender(){
			$rsa = new RSA(IA_ROOT.'/data/cert/');
			var_dump($rsa->createKey());
		}*/

		/**
		 * 4、注册
		 * @author 兰辉
		 * @access public
		 * @param        $openid
		 * @param        $phone
		 * @param        $verificationCode
		 * @param        $openidPass
		 * @param string $phonePass
		 * @param int $follow   0
		 * @param  $uid
		 * @since  1.0
		 * @return array
		 */
		public function registerAction($openid, $phone, $verificationCode, $openidPass, $phonePass = '',$follow=0, $uid)
		{
			/*$return_error = array(201, '该手机号已经存在');
			$mobile_exist = $this->phoneExistAction($phone);
			if($mobile_exist['ret'] == 1) {
				return $return_error;
			}*/
			$params = array(
				'source'           => 'tszy',
				'openid'           => $openid,
				'phone'            => (string)$phone,
				'verificationCode' => $verificationCode,
				'openidPass'       => $openidPass,
				'phonePass'        => sha1(md5($phonePass).$phone),
				'wxStatus'         => $follow
			);
			$toe = $this->header['Toe'];
			$toe2 = str_replace(array('*uri*', '*method*', '*body*', '*token*'), array("*uri*", 'GET', '','*token*'), $toe);
			$this->header['body'] = json_encode($params);
			$toe = str_replace(array('*uri*', '*method*', '*body*', '*token*'), array(self::TEST . self::REGISTER, 'POST',json_encode($params), ''), $toe);
			$this->header['Toe'] = md5($toe);
			if(!$this->header['token']) {
				unset($this->header['token']);
			}
			$result = Requests::post(self::DOMAIN . self::REGISTER, $this->header, json_encode($params));
			$res_data = json_decode($result->body, TRUE);
			if($res_data['ret'] == 1) {
				$this->actionForAuthUrl($result, $toe2);
			}
			return $res_data;
		}

		/**
		 * 5、收集信息
		 * @author 兰辉
		 * @access public
		 * @param $phone
		 * @param $info
		 * @since  1.0
		 * @return array
		 */
		public function collectAction($phone, $info)
		{
			$params = array(
				'source' => 'tszy',
				'phone' => $phone,
				'info'   => $info,
			);
			$toe = $this->header['Toe'];
			$this->header['body'] = $params;
			$toe = str_replace(array('*uri*', '*method*', '*body*', '*token*'), array(self::TEST . self::INIT_COLLECT, 'PUT',json_encode($params), ''), $toe);
			$this->header['Toe'] = md5($toe);
			if(!$this->header['token']) {
				unset($this->header['token']);
			}
			$result = Requests::put(self::DOMAIN . self::INIT_COLLECT, $this->header, json_encode($params));
			$res = json_decode(json_encode($result));

			return json_decode($res->body, TRUE);
		}

		/**
		 * 6、忘记密码
		 * @author 兰辉
		 * @access public
		 * @param $phone
		 * @param $verificationCode
		 * @param $password
		 * @since  1.0
		 * @return array
		 */
		public function forgetPasswordAction($phone, $verificationCode, $password)
		{
			$params = array(
				'phone'            => $phone,
				'verificationCode' => $verificationCode,
				'password'         => md5($password),
			);
			$toe = $this->header['Toe'];
			$toe = str_replace(array('*uri*', '*method*', '*body*', '*token*'), array(self::TEST . self::UPDATE_PASSWORD, 'PUT', json_encode($params), ''), $toe);
			$this->header['Toe'] = md5($toe);
			if(!$this->header['token']) {
				unset($this->header['token']);
			}
			$result = Requests::put(self::DOMAIN . self::UPDATE_PASSWORD, $this->header, json_encode($params));
			$res = json_decode(json_encode($result));

			return json_decode($res->body, TRUE);
		}

		/**
		 * 7、修改手机号
		 * @author 兰辉
		 * @access public
		 * @param $oldPhone
		 * @param $newPhone
		 * @param $oldCode
		 * @param $newCode
		 * @param $password
		 * @since  1.0
		 * @return array
		 */
		public function updateMobileAction($oldPhone, $newPhone, $oldCode, $newCode, $password)
		{
			$params = array(
				'oldPhone' => $oldPhone,
				'newPhone' => $newPhone,
				'oldCode'  => $oldCode,
				'newCode'  => $newCode,
				'password' => md5($password),
			);
			$toe = $this->header['Toe'];
			$toe = str_replace(array('*uri*', '*method*', '*body*', '*token*'), array(self::TEST . self::UPDATE_MOBILE, 'PUT', '', json_encode($params)), $toe);
			$this->header['Toe'] = md5($toe);
			if(!$this->header['token']) {
				unset($this->header['token']);
			}
			$result = Requests::put(self::DOMAIN . self::TEST . self::UPDATE_MOBILE, $this->header, $params);
			$res = json_decode(json_encode($result));

			return json_decode($res->body, TRUE);
		}

		/**
		 * 绑定手机号
		 * @author 兰辉
		 * @access public
		 * @param $verificationCode
		 * @param $phone
		 * @param $wxOpenid
		 * @since  1.0
		 * @return array
		 */
		public function bindWchatAction($verificationCode, $phone, $wxOpenid)
		{
			$params = array(
				'verificationCode' => $verificationCode,
				'phone'  => $phone,
				'source'  => 'tszy',
				'wxOpenid' => $wxOpenid,
			);
			$toe = $this->header['Toe'];
			$toe = str_replace(array('*uri*', '*method*', '*body*', '*token*'), array(self::TEST . self::BIND_WECHAT, 'PUT', '', json_encode($params)), $toe);
			$this->header['Toe'] = md5($toe);
			if(!$this->header['token']) {
				unset($this->header['token']);
			}
			$result = Requests::put(self::DOMAIN  . self::BIND_WECHAT, $this->header, json_encode($params));
			$res = json_decode($result->body, true);
			return $res;
		}

		protected function refresh_token($token, $uid, $lifeTime)
		{
			$res = true;
			$result = pdo_fetch('select uid,token,expire from '.tablename('manor_shop_financial_token').' where uid = :uid', array(':uid'=>$uid));
			if(!$result) {
				$res = pdo_insert('manor_shop_financial_token', array('uid'=>$uid, 'token'=>$token, 'expire'=>time() + $lifeTime, 'create_time'=>time()));
			} else {
				if(!$result['token'] || $result['expire'] < (time() + $lifeTime)) {
					$res = pdo_update('manor_shop_financial_token', array('token'=>$token, 'expire'=>(time() + $lifeTime)), array('uid'=>$uid));
				}
			}
			return $res ? TRUE : FALSE;
		}

		/**
		 * 验证token是否过期
		 * @author 兰辉
		 * @access public
		 * @param $uid
		 * @since  1.0
		 * @return array
		 */
		public function actionAuthToken($uid) {
			$_token = pdo_get('manor_shop_financial_token', array('uid'=>$uid), array('token', 'expire'));
			$token = $_token['token'];
			if(!$token) {
				$token = random(8);
			} else {
				if($_token['expire'] > time()) {
					$this->header['Toe'] = str_replace('*token*',$token, $this->header['Toe']);
					$this->header['token'] = $token;
					return TRUE;
				}
			}
			$params = array(
				'source'  => 'tszy',
				'token' => $token,
			);
			$toe = $this->header['Toe'];
			$toe = str_replace(array('*uri*', '*method*', '*body*', '*token*'), array(self::TEST . self::AUTH_TOKEN, 'PUT',  json_encode($params), ''), $toe);
			$this->header['Toe'] = md5($toe);
			$result = Requests::put(self::DOMAIN  . self::AUTH_TOKEN, $this->header, json_encode($params));
			$res = json_decode($result->body, true);
			if($res['ret'] == 1) {
				$this->header['Toe'] = str_replace('*token*',$token, $this->header['Toe']);
				$this->header['token'] = $token;
				$this->refresh_token($token, $uid, $res['lifeTime']);
				return true;
			}
			return false;
		}


		/**
		 * 绑定邮箱
		 * @author 兰辉
		 * @access public
		 * @since  1.0
		 */
		public function bindEmailAction()
		{

		}

		/**
		 * 修改个人信息
		 * @author 兰辉
		 * @access public
		 * @since  1.0
		 */
		public function updateUserInfoAction()
		{

		}

    /**
     * 验证优惠卷
     * @param $code
     * @param $info
     * @return array|mixed|stdClass
     */
		public function check_coupon_code($code, $info) {
            $params = array(
                'code' => $code,
                'info'  => json_encode($info),
            );
            $toe = $this->header['Toe'];
            //$Url = 'http://123.56.143.169:8014/tdfs3/auth/double11/coupon/auth/test';
            if($_SERVER['HTTP_HOST'] == 'fresh.tangshengmanor.com') {
                $ip ='http://api.tangcredit.com';//线上
            } else {
                $ip ='http://123.56.143.169:8013';//开发
            }
            $Url = '/tdfs3/auth/double11/coupon/auth/';
            $toe = str_replace(array('*uri*', '*method*', '*body*', '*token*'), array($Url, 'POST', '', json_encode($params)), $toe);
            $this->header['Toe'] = md5($toe);
            if(!$this->header['token']) {
                unset($this->header['token']);
            }
            $result = Requests::post($ip.$Url, $this->header, json_encode($params));
            $res = json_decode($result->body, true);
            return $res;
        }

        /**
         * 验证红包
         * @param $code
         * @param $info
         * @return array|mixed|stdClass
         */
        public function check_red_code($code, $info) {
            $params = array(
                'code' => $code,
                'info'  => json_encode($info),
            );
            $toe = $this->header['Toe'];
            if($_SERVER['HTTP_HOST'] == 'fresh.tangshengmanor.com') {
                $ip ='http://api.tangcredit.com';//线上
            } else {
                $ip ='http://123.56.143.169:8013';//开发
            }

            $Url = '/tdfs3/auth/double11/redEnvelopeCheck/';
            //$Url = 'http://192.168.31.34:8088/tdfs3/auth/double11/redEnvelopeCheck/test';
            $toe = str_replace(array('*uri*', '*method*', '*body*', '*token*'), array($Url, 'POST', '', json_encode($params)), $toe);
            $this->header['Toe'] = md5($toe);
            if(!$this->header['token']) {
                unset($this->header['token']);
            }
            $result = Requests::post($ip.$Url, $this->header, json_encode($params));
            $res = json_decode($result->body, true);
            return $res;
        }
	}