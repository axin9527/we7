<?php
	/**
	 * 中秋节活动
	 * 操作类名称: PhpStorm.
	 * 作者名称: alan
	 * 创建时间: 16/9/5 09:50
	 */
	global $_W, $_GPC;
	if($_GPC['from'] &&  !$_W['isajax']) {
		$redirect = $this->createMobileUrl('activity/mid_autumn');
		header("Location:$redirect");die;
	}
	$year = date('Y');
	$month = date('m');
	$day = date('d');
	$hour = date('H');
	$uniacid = $_W['uniacid'];
	$common_where = array('uniacid'=>$uniacid, 'activity_id'=>$_GPC['rid'], 'activity_type'=>'turntable');
	$op = $_GPC['op'];
	$step = intval($_GPC['step']) ? intval($_GPC['step']) : 1;
	$openid = m('user') -> getOpenid();
	$member = m('member') -> getMember($openid);
	$type = $_GPC['type'] ? $_GPC['type'] : 'pv';
	if(!in_array($type, array('pv', 'uv', 'sv', 'tv', 'sav'))) {
		$redirect = $this->createMobileUrl('activity/mid_autumn');
		header("Location:$redirect");die;
	}
	$city_base = array(
		'北京市'=>2017,
		'天津市'=>2017,
		'河北省'=>6371,
		'山西省'=>6371,
		'内蒙古省'=>6371,
		'辽宁省'=>6371,
		'吉林省'=>6371,
		'黑龙江省'=>6371,
		'上海市'=>2017,
		'江苏省'=>6371,
		'浙江省'=>6371,
		'安徽省'=>6371,
		'福建省'=>6371,
		'江西省'=>2017,
		'山东省'=>6371,
		'河南省'=>6371,
		'湖北省'=>6371,
		'湖南省'=>6371,
		'广东省'=>6371,
		'广西省'=>2017,
		'海南省'=>2017,
		'重庆市'=>2017,
		'四川省'=>6371,
		'贵州省'=>2017,
		'云南省'=>2017,
		'西藏区'=>2017,
		'陕西省'=>6371,
		'甘肃省'=>2017,
		'青海省'=>2017,
		'宁夏省'=>821,
		'新疆区'=>2017,
		'台湾'=>821,
		'香港'=>821,
		'澳门'=>821
	);
	$base_friend = 0;
	//初次访问自动uv
	$data_where = array(
		'year'=>$year,
		'month'=>$month,
		'day'=>$day,
		'hour'=>$hour,
		'time'=>$year.$month.$day,
		'step' =>$step
	);
	$uv_data = array(
		'year'=>$year,
		'month'=>$month,
		'day'=>$day,
		'hour'=>$hour,
		'time'=>$year.$month.$day,
		'pv' =>1,//访问量
		'uv' =>1,//用户量
		'sv' =>0,//分享量
		'sav' =>0,//分享量
		'tv' =>0,//转跳量
		'create_time'=>time()
	);
	$ins_log_data = array(
		'uid'=>$member['uid'],
		'username'=>$member['nickname'],
		'step'=>$step,
		'type'=>$type,
		'time'=>date('Ymd'),
		'create_date'=>date('Y-m-d H:i:s'),
		'create_time'=>time(),
		'user_info'=>json_encode($member),
		'client_ip'=>getip()
	);

	$fields = array('id','pv', 'uv', 'tv', 'step', 'sv');
	$init_where = array_merge($common_where, $data_where);
	//统计pv访问量
	$today_data = pdo_get('manor_shop_mid_autumn_sta', $init_where, $fields);
	if($today_data) {
		//$up_sta_data['pv'] = $today_data['pv'] + 1;
		$log_where = $ins_log_data;
		unset($log_where['create_date'], $log_where['user_info'], $log_where['create_time'], $log_where['username']);
		$up_sta_data[$type] = $today_data[$type] + 1;
		/*if(!pdo_get('manor_shop_mid_autumn_log', array_merge($log_where, array('type'=>$type)))) {
			$up_sta_data[$type] = $today_data[$type] + 1;
		}*/
		$up_res = pdo_update('manor_shop_mid_autumn_sta', $up_sta_data, array('id'=>$today_data['id']));
	} else {
		$uv_data[$type] = 1;
		$ins_res =pdo_insert('manor_shop_mid_autumn_sta', array_merge($uv_data, $init_where));
	}
	if($op == 'get_distance') {
		$ins_log_data['type'] = 'info';
	}
	pdo_insert('manor_shop_mid_autumn_log', array_merge($common_where, $ins_log_data));
	//是否分享
	$js_share = 0;
	$share = pdo_get('manor_shop_mid_autumn_log', array_merge($common_where, array('uid'=>$member['uid'], 'type'=>'sav')));
	if($share) {
		$js_share = 1;
	}
	if($_W['isajax']) {
		if($op = 'get_distance') {
			$province = trim($_GPC['province']);
			if(isset($city_base[$province])) {
				$base_friend = $city_base[$province];
			}
			$city = trim($_GPC['city']);
			if(strpos($province, "区")) {
				$province = rtrim($province, "区");
			}
			if(strpos($city, "辖区") !== false) {
				$city = '';
			}
			if(strpos($city, "辖县") !== false) {
				$city = '';
			}
			if(strpos($city, "单位") !== false) {
				$city = $address;
				$address = '';
			}
			$address = trim($_GPC['address']);
			$ak = 'OIkIe527m5ecyM8TeHTiD9vjoObfBT2h';
			$geocoder = getcity($province, $city, $address);
			$ding_data = $geocoder['location'];
			$ip_url = 'http://api.map.baidu.com/location/ip?ak='.$ak.'&ip='.getip();
			$ip_data = json_decode(file_get_contents($ip_url), true);
			/*if($ip_data['status'] != 0) {
				$current_data = getcity('北京');
				$current_point = $current_data['location'];
			} else {
				$current_point['lng'] = $ip_data['content']['point']['y'];
				$current_point['lat'] = $ip_data['content']['point']['x'];
			}*/
			$current_data = getcity('北京');
			$current_point = $current_data['location'];
			//计算两地之间的距离
			//飞机~~~~
			/*$aircraft = calculate_city($ding_data, $current_point, 1);
			pdo_insert('log', array('cont'=>var_export($aircraft, TRUE)));
			//火车~~~~
			$train = calculate_city($ding_data, $current_point, 0);
			pdo_insert('log', array('cont'=>var_export($train, TRUE)));
			//汽车~~~~
			$car = calculate_car($ding_data, $current_point, 'driving');
			pdo_insert('log', array('cont'=>var_export($car, TRUE)));
			//步行~~~~
			$walk = sprintf("%.2f", $aircraft['distance']/5000);*/
			$distance = getDistance($ding_data['lng'],$ding_data['lat'], $current_point['lng'],  $current_point['lat']);
			/*if(is_array($aircraft)) {
				$distance = $aircraft['distance'];
			} elseif(is_array($train)){
				$distance = $train['distance'];
			}*/
			/*$calculate_data = array(
				'distance'=>$distance,
				'distance_format'=>rtrim(sprintf("%.2f", $distance/1000),"0")."公里",
				'items' => array(
					'air'   => ($aircraft['duration'] / 3600) > 1 ? rtrim(sprintf("%.2f", $aircraft['duration'] / 3600), "0") : $aircraft['duration'] / 3600,
					'train' => ($train['duration'] / 3600) > 1 ? rtrim(sprintf("%.2f", $train['duration'] / 3600), "0") : $train['duration'] / 3600,
					'car'   => $car > 1 ? rtrim($car, "0") : $car,
					'walk'  => '约' . $walk > 1 ? rtrim($walk, "0") : $walk,
				),
				'friend_num'=>pdo_fetchcolumn("select count(*) from ".tablename('manor_shop_mid_autumn_userinfo')." where uniacid=:uniacid and province=:province", array(':uniacid'=>$uniacid, ':province'=>$province))
			);*/
			$km = $distance/1000;
			//飞机
			$air = $km/550 > 1 ? rtrim(sprintf("%.1f", $km/550), "0") : sprintf("%.1f", $km/550);
			//火车
			$train = $km/140 >1 ? rtrim(sprintf("%.1f", $km/140), "0") : sprintf("%.1f", $km/140);
			//汽车
			$car = $km/70 > 1 ? rtrim(sprintf("%.2f", $km/70), "0") : sprintf("%.2f", $km/70);
			//步行
			$walk = $km/4.5 > 1 ? rtrim(sprintf("%.2f", $km/4.5), "0") : sprintf("%.2f", $km/4.5);
			if($walk < 24) {
				$walk = $walk;
				$walk_px = '小时';
			} elseif($walk < 240 && $walk >= 24) {
				$walk = ceil($walk/8);
				$walk_px = '天';
			} elseif($walk >= 240) {
				$walk = ceil($walk/240);
				$walk_px = '个月';
			}
			/*$walk_day = intval($walk/8) ? intval($walk/8) : '';
			$walk_hour = $walk - $walk_day*8 >0 ? $walk - $walk_day*8 : '';*/
			$calculate_data = array(
				'distance'=>$km,
				'distance_format'=>rtrim(sprintf("%.2f", $km),"0")."公里",
				'items' => array(
					'air'   => $air,
					'train' => ceil($train),
					'car'   => ceil($car),
					'walk'  => $walk,
					'walk_px'=>$walk_px
				),
				'friend_num'=>pdo_fetchcolumn("select count(*) from ".tablename('manor_shop_mid_autumn_userinfo')." where uniacid=:uniacid and province=:province", array(':uniacid'=>$uniacid, ':province'=>$province))
			);
			$user_info = array(
				'uid' => $member['uid'],
				'uniacid'=>$uniacid,
				'nickname' => $member['nickname'],
				'avstar' => $member['avatar'],
				'user_info' => json_encode($member),
				'province' => $province,
				'city' => $city,
				'address' => $address,
				'result' => json_encode($calculate_data),
				'friend_num' => $calculate_data['friend_num'],
				'create_time' => time()
			);
			pdo_insert('manor_shop_mid_autumn_userinfo', $user_info);
			$calculate_data['friend_num'] += $base_friend;
			show_json(1, $calculate_data);die();
		}
	}
	include $this->template('activity/mid_autumn');

	function getcity($province, $city, $address) {
		//地图导航测距
		$code = array('0'=>'正常', '1'=>'服务器内部错误', '2'=>'请求参数非法', '3'=>'权限校验失败', '4'=>'配额校验失败', '5'=>'ak不存在或者非法', '101'=>'服务禁用', '102'=>'不通过白名单或者安全码不对', '2xx'=>'无权限', '3xx'=>'配额错误');
		$ak = 'OIkIe527m5ecyM8TeHTiD9vjoObfBT2h';
		$geocoder_url = 'http://api.map.baidu.com/geocoder/v2/?address='.$province.$city.$address.'&output=json&ak='.$ak;
		$geocoder = json_decode(file_get_contents($geocoder_url), true);
		if($geocoder['status'] != 0) {
			return $code[$geocoder['status']];
		}
		return $geocoder['result'];
	}

	/**
	 * 计算两地的距离, 飞机 火车
	 * @author 兰辉
	 * @access public
	 * @param $from_city 开始地点 我离家乡
	 * @param $to_city 结束地点
	 * @param $type  结束地点
	 * @since  1.0
	 * @return array
	 */
	function calculate_city($from_city, $to_city, $type) {
		$ak = 'OIkIe527m5ecyM8TeHTiD9vjoObfBT2h';
		$code = array(0=>'成功', 1=>'服务器内部错误', 2=>'参数无效；', 1001=>'没有公交方案', 1002=>'没有匹配的POI');
		$api_path = 'http://api.map.baidu.com/direction/v2/transit?origin='.$from_city['lat'].','.$from_city['lng'].'&destination='.$to_city['lat'].','.$to_city['lng'].'&ak='.$ak.'&trans_type_intercity='.$type.'&output=json&page_size=1';
		$data = json_decode(file_get_contents($api_path), true);
		pdo_insert('log', array('cont'=>var_export($data, TRUE)));
		if($data['status'] != 0) {
			return $code[$data['status']];
		}
		$routes = $data['result']['routes'][0];
		unset($routes['steps'], $routes['price_detail']); 
		return $routes;
	}

	/**
	 * 计算汽车 步行的距离
	 * @author 兰辉
	 * @access public
	 * @param $from_city  开始地点 我离家乡
	 * @param $to_city
	 * @param $type
	 * @since  1.0
	 * @return array
	 */
	function calculate_car($from_city, $to_city, $type) {
		$ak = 'OIkIe527m5ecyM8TeHTiD9vjoObfBT2h';
		$url = 'http://api.map.baidu.com/routematrix/v2/'.$type.'?output=json&origins='.$from_city['lat'].','.$from_city['lng'].'&destinations='.$to_city['lat'].','.$to_city['lng'].'&ak='.$ak;
		$data = json_decode(file_get_contents($url), true);
		if($data['status'] != 0) {
			return 0;
		}
		$second = 0;
		foreach ($data['result'] as $key => $value) {
			$second += $value['duration']['value'];
		}
		return sprintf("%.2f", $second/3600);
	}

	function getDistance($lng1,$lat1,$lng2,$lat2){
		//将角度转为狐度
		$radLat1=deg2rad($lat1);//deg2rad()函数将角度转换为弧度
		$radLat2=deg2rad($lat2);
		$radLng1=deg2rad($lng1);
		$radLng2=deg2rad($lng2);
		$a=$radLat1-$radLat2;
		$b=$radLng1-$radLng2;
		$s=2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6378.137*1000;
		return $s;
	}




