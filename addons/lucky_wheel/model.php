<?php
	if (!defined('IN_IA')){
		exit('Access Denied');
	}
	if (!class_exists('CouponModel')){
		class CouponModel{
			function payResult($logno){
				global $_W;
				if(empty($logno)){
					return false;
					//return error(-1);
				}
				$log = pdo_fetch('SELECT * FROM ' . tablename('manor_shop_coupon_log') . ' WHERE `logno`=:logno and `uniacid`=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':logno' => $logno));
				if (empty($log)){
					return array(-1, '服务器错误!');
				}
				if ($log['status'] >= 1){
					return true;
				}
				$coupon = pdo_fetch('select * from ' . tablename('manor_shop_coupon') . ' where id=:id limit 1', array(':id' => $log['couponid']));
				$coupon = $this -> setCoupon($coupon, time());
				if (empty($coupon['gettype'])){
					return array(-1, '无法领取');
				}
				if ($coupon['total'] != -1){
					if ($coupon['total'] <= 0){
						return array(-1, '优惠券数量不足');
					}
				}
				if (!$coupon['canget']){
					return array(-1, '您已超出领取次数限制');
				}
				if(empty($log['status'])){
					$update = array();
					if ($coupon['credit'] > 0 && empty($log['creditstatus'])){
						$this-> setCredit($log['openid'], 'credit1', - $coupon['credit'], "购买优惠券扣除积分 {$coupon['credit']}");
						$update['creditstatus'] = 1;
					}
					if ($coupon['money'] > 0 && empty($log['paystatus'])){
						if ($coupon['paytype'] == 0){
							$this -> setCredit($log['openid'], 'credit2', - $coupon['money'], "购买优惠券扣除余额 {$coupon['money']}");
						}
						$update['paystatus'] = 1;
					}
					$update['status'] = 1;
					pdo_update('manor_shop_coupon_log', $update, array('id' => $log['id']));
					$data = array('uniacid' => $_W['uniacid'], 'openid' => $log['openid'], 'couponid' => $log['couponid'], 'gettype' => $log['getfrom'], 'gettime' => time());
					pdo_insert('manor_shop_coupon_data', $data);
					//发消息
					return true;
				}
				return true;
			}

			public function setCredit($openid = '', $credittype = 'credit1', $credits = 0, $log = array()){
				global $_W;
				load() -> model('mc');
				$uid = mc_openid2uid($openid);
				if (!empty($uid)){
					$value = pdo_fetchcolumn("SELECT {$credittype} FROM " . tablename('mc_members') . " WHERE `uid` = :uid", array(':uid' => $uid));
					$newcredit = $credits + $value;
					if ($newcredit <= 0){
						$newcredit = 0;
					}
					pdo_update('mc_members', array($credittype => $newcredit), array('uid' => $uid));
					if (empty($log) || !is_array($log)){
						$log = array($uid, '未记录');
					}
					$data = array('uid' => $uid, 'credittype' => $credittype, 'uniacid' => $_W['uniacid'], 'num' => $credits, 'createtime' => TIMESTAMP, 'operator' => intval($log[0]), 'remark' => $log[1],);
					pdo_insert('mc_credits_record', $data);
				}else{
					$value = pdo_fetchcolumn("SELECT {$credittype} FROM " . tablename('manor_shop_member') . " WHERE  uniacid=:uniacid and openid=:openid limit 1", array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
					$newcredit = $credits + $value;
					if ($newcredit <= 0){
						$newcredit = 0;
					}
					pdo_update('manor_shop_member', array($credittype => $newcredit), array('uniacid' => $_W['uniacid'], 'openid' => $openid));
				}
			}


			function getCoupon($couponid = 0){
				global $_W;
				return pdo_fetch('select * from ' . tablename('manor_shop_coupon') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $couponid, ':uniacid' => $_W['uniacid']));
			}
			function setCoupon($row, $time, $withOpenid = true){
				global $_W;
				if($withOpenid){
					$openid = $withOpenid;
				}
				$row['free'] = false;
				$row['past'] = false;
				$row['thumb'] = tomedia($row['thumb']);
				if ($row['money'] > 0 && $row['credit'] > 0){
					$row['getstatus'] = 0;
					$row['gettypestr'] = "购买";
				}else if ($row['money'] > 0){
					$row['getstatus'] = 1;
					$row['gettypestr'] = "购买";
				}else if ($row['credit'] > 0){
					$row['getstatus'] = 2;
					$row['gettypestr'] = "兑换";
				}else{
					$row['getstatus'] = 3;
					$row['gettypestr'] = "领取";
				}
				$row['timestr'] = "0";
				if (empty($row['timelimit'])){
					if (!empty($row['timedays'])){
						$row['timestr'] = 1;
					}
				}else{
					if ($row['timestart'] >= $time){
						$row['timestr'] = date('Y-m-d', $row['timestart']) . '-' . date('Y-m-d', $row['timeend']);
					}else{
						$row['timestr'] = date('Y-m-d', $row['timeend']);
					}
				}
				$row['css'] = 'deduct';
				if ($row['backtype'] == 0){
					$row['backstr'] = '立减';
					$row['css'] = 'deduct';
					$row['backpre'] = true;
					$row['_backmoney'] = $row['deduct'];
				}else if ($row['backtype'] == 1){
					$row['backstr'] = '折';
					$row['css'] = 'discount';
					$row['_backmoney'] = $row['discount'];
				}else if ($row['backtype'] == 2){
					if (!empty($row['backredpack'])){
						$row['backstr'] = '返现';
						$row['css'] = "redpack";
						$row['backpre'] = true;
						$row['_backmoney'] = $row['backredpack'];
					}else if (!empty($row['backmoney'])){
						$row['backstr'] = '返利';
						$row['css'] = "money";
						$row['backpre'] = true;
						$row['_backmoney'] = $row['backmoney'];
					}else if (!empty($row['backcredit'])){
						$row['backstr'] = '返积分';
						$row['css'] = "credit";
						$row['_backmoney'] = $row['backcredit'];
					}
				}
				if($withOpenid){
					$row['cangetmax'] = -1;
					$row['canget'] = true;
					if ($row['getmax'] > 0){
						$gets = pdo_fetchcolumn('select count(*) from ' . tablename('manor_shop_coupon_data') . ' where couponid=:couponid and openid=:openid and uniacid=:uniacid and gettype=1 limit 1', array(':couponid' => $row['id'], ':openid' => $openid, ':uniacid' => $_W['uniacid']));
						$row['cangetmax'] = $row['getmax'] - $gets;
						if ($row['cangetmax'] <= 0){
							$row['cangetmax'] = 0;
							$row['canget'] = false;
						}
					}
				}
				return $row;
			}
			function setMyCoupon($row, $time){
				global $_W;
				$row['past'] = false;
				$row['thumb'] = tomedia($row['thumb']);
				$row['timestr'] = "";
				if (empty($row['timelimit'])){
					if (!empty($row['timedays'])){
						$row['timestr'] = date('Y-m-d', $row['gettime'] + $row['timedays'] * 86400);
						if ($row['gettime'] + $row['timedays'] * 86400 < $time){
							$row['past'] = true;
						}
					}
				}else{
					if ($row['timestart'] >= $time){
						$row['timestr'] = date('Y-m-d H:i', $row['timestart']) . '-' . date('Y-m-d', $row['timeend']);
					}else{
						$row['timestr'] = date('Y-m-d H:i', $row['timeend']);
					}
					if ($row['timeend'] < $time){
						$row['past'] = true;
					}
				}
				$row['css'] = 'deduct';
				if ($row['backtype'] == 0){
					$row['backstr'] = '立减';
					$row['css'] = 'deduct';
					$row['backpre'] = true;
					$row['_backmoney'] = $row['deduct'];
				}else if ($row['backtype'] == 1){
					$row['backstr'] = '折';
					$row['css'] = 'discount';
					$row['_backmoney'] = $row['discount'];
				}else if ($row['backtype'] == 2){
					if (!empty($row['backredpack'])){
						$row['backstr'] = '返现';
						$row['css'] = "redpack";
						$row['backpre'] = true;
						$row['_backmoney'] = $row['backredpack'];
					}else if (!empty($row['backmoney'])){
						$row['backstr'] = '返利';
						$row['css'] = "money";
						$row['backpre'] = true;
						$row['_backmoney'] = $row['backmoney'];
					}else if (!empty($row['backcredit'])){
						$row['backstr'] = '返积分';
						$row['css'] = "credit";
						$row['_backmoney'] = $row['backcredit'];
					}
				}
				if ($row['past']){
					$row['css'] = 'past';
				}
				return $row;
			}

			public function getCredit($openid = '', $credittype = 'credit1'){
				global $_W;
				load() -> model('mc');
				$uid = mc_openid2uid($openid);
				if (!empty($uid)){
					return pdo_fetchcolumn("SELECT {$credittype} FROM " . tablename('mc_members') . " WHERE `uid` = :uid", array(':uid' => $uid));
				}else{
					return pdo_fetchcolumn("SELECT {$credittype} FROM " . tablename('manor_shop_member') . " WHERE  openid=:openid and uniacid=:uniacid limit 1", array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
				}
			}
		}
	}
