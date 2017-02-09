<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
if (!class_exists('PermModel')){
    class PermModel extends PluginModel{
        public function allPerms(){
             $dephp_0 = array(
                'shop' => array(
                    'text'  => '商城管理',
                    'child' => array(
                        'goods'         => array(
                            'text'   => '商品',
                            'view'   => '浏览',
                            'add'    => '添加-log',
                            'edit'   => '修改-log',
                            'delete' => '删除-log',
                        ),
                        'category'      => array(
                            'text'   => '商品分类',
                            'view'   => '浏览',
                            'add'    => '添加-log',
                            'edit'   => '修改-log',
                            'delete' => '删除-log',
                        ),
                        'activity'      => array(
                            'text'   => '活动管理',
                            'view'   => '浏览',
                            'add'    => '添加-log',
                            'edit'   => '修改-log',
                            'delete' => '删除-log',
                        ),
                        'navigation'    => array(
                            'text'   => '导航管理',
                            'view'   => '浏览',
                            'add'    => '添加-log',
                            'edit'   => '修改-log',
                            'delete' => '删除-log',
                        ),
                        'live_video'    => array(
                            'text'   => '直播管理',
                            'view'   => '浏览',
                            'add'    => '添加-log',
                            'edit'   => '修改-log',
                            'delete' => '删除-log',
                        ),
                        'dispatch'      => array(
                            'text'   => '配送方式',
                            'view'   => '浏览',
                            'add'    => '添加-log',
                            'edit'   => '修改-log',
                            'delete' => '删除-log',
                        ),
                        'adv'           => array(
                            'text'   => '幻灯片',
                            'view'   => '浏览',
                            'add'    => '添加-log',
                            'edit'   => '修改-log',
                            'delete' => '删除-log',
                        ),
                        'notice'        => array(
                            'text'   => '公告',
                            'view'   => '浏览',
                            'add'    => '添加-log',
                            'edit'   => '修改-log',
                            'delete' => '删除-log',
                        ),
                        'comment'       => array(
                            'text'   => '评价',
                            'view'   => '浏览',
                            'add'    => '添加评论-log',
                            'edit'   => '回复-log',
                            'delete' => '删除-log',
                        ),
                        'refundaddress' => array(
                            'text'   => '退货地址',
                            'view'   => '浏览',
                            'add'    => '添加-log',
                            'edit'   => '修改-log',
                            'delete' => '删除-log',
                        ),
                        'feedback' => array(
                            'text'   => '反馈管理',
                            'view'   => '浏览',
                            'delete' => '删除-log',
                        ),
                    ),
                ),
                'member'        => array(
                    'text' => '会员管理', 'child' => array(
                        'member'   => array(
                            'text'   => '会员', 'view' => '浏览', 'edit' => '修改-log', 'delete' => '删除-log',
                            'export' => '导出-log',
                        ), 'group' => array(
                            'text'   => '会员组', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log',
                            'delete' => '删除-log',
                        ), 'level' => array(
                            'text'   => '会员等级', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log',
                            'delete' => '删除-log',
                        ),
                    ),
                ), 'order'      => array(
                    'text' => '订单管理', 'child' => array(
                        'view'  => array(
                            'text'    => '浏览', 'status_1' => '浏览关闭订单', 'status0' => '浏览待付款订单', 'status1' => '浏览已付款订单',
                            'status2' => '浏览已发货订单', 'status3' => '浏览完成的订单', 'status4' => '浏览退货申请订单',
                            'status5' => '浏览已退货订单',
                        ), 'op' => array(
                            'text'        => '操作', 'pay' => '确认付款-log', 'send' => '发货-log', 'sendcancel' => '取消发货-log',
                            'finish'      => '确认收货(快递单)-log', 'verify' => '确认核销(核销单)-log', 'fetch' => '确认取货(自提单)-log',
                            'close'       => '关闭订单-log', 'refund' => '退货处理-log', 'export' => '导出订单-log',
                            'changeprice' => '订单改价-log', 'changeaddress' => '修改订单地址-log',
                        ),
                    ),
                ), 'finance'    => array(
                    'text' => '财务管理', 'child' => array(
                        'recharge'        => array(
                            'text'   => '充值', 'view' => '浏览', 'credit1' => '充值积分-log', 'credit2' => '充值余额-log',
                            'refund' => '充值退款-log', 'export' => '导出充值记录-log',
                        ), 'withdraw'     => array(
                            'text' => '提现', 'view' => '浏览', 'withdraw' => '提现-log', 'export' => '导出提现记录-log',
                        ), 'downloadbill' => array('text' => '下载对账单'),
                    ),
                ), 'statistics' => array(
                    'text' => '数据统计', 'child' => array(
                        'view'      => array(
                            'text'        => '浏览权限', 'sale' => '销售指标', 'sale_analysis' => '销售统计', 'order' => '订单统计',
                            'goods'       => '商品销售统计', 'goods_rank' => '商品销售排行', 'goods_trans' => '商品销售转化率',
                            'member_cost' => '会员消费排行', 'member_increase' => '会员增长趋势',
                        ), 'export' => array(
                            'text'        => '导出', 'sale' => '导出销售统计-log', 'order' => '导出订单统计-log',
                            'goods'       => '导出商品销售统计-log', 'goods_rank' => '导出商品销售排行-log',
                            'goods_trans' => '商品销售转化率-log', 'member_cost' => '会员消费排行-log',
                        ),
                    ),
                ), 'sysset'     => array(
                    'text' => '系统设置',
                    'child' => array(
                        'view'    => array(
                            'text'     => '浏览', 'shop' => '商城设置', 'follow' => '引导及分享设置', 'notice' => '模板消息设置',
                            'trade'    => '交易设置', 'pay' => '支付方式设置', 'template' => '模板设置', 'member' => '会员设置',
                            'category' => '分类层级设置', 'contact' => '联系方式设置','activity'=>'活动设置'
                        ), 'save' => array(
                            'text'     => '修改', 'shop' => '修改商城设置-log', 'follow' => '修改引导及分享设置-log',
                            'notice'   => '修改模板消息设置-log', 'trade' => '修改交易设置-log', 'pay' => '修改支付方式设置-log',
                            'template' => '模板设置-log', 'member' => '会员设置-log', 'category' => '分类层级设置-log',
                            'contact'  => '联系方式设置-log','activity'=>'活动设置-log'
                        ),
                    ),
                ),
            );
            $dephp_1 = m('plugin') -> getAll();
            foreach ($dephp_1 as $dephp_2){
                $dephp_3 = p($dephp_2['identity']);
                if ($dephp_3){
                    if (method_exists($dephp_3, 'perms')){
                        $dephp_4 = $dephp_3 -> perms();
                        $dephp_0 = array_merge($dephp_0, $dephp_4);
                    }
                }
            }
            return $dephp_0;
        }
        public function isopen($dephp_5 = ''){
            if (empty($dephp_5)){
                return false;
            }
            $dephp_1 = m('plugin') -> getAll();
            foreach ($dephp_1 as $dephp_2){
                if ($dephp_2['identity'] == strtolower($dephp_5)){
                    if (empty($dephp_2['status'])){
                        return false;
                    }
                }
            }
            return true;
        }
        public function check_edit($dephp_6 = '', $dephp_7 = array()){
            if (empty($dephp_6)){
                return false;
            }
            if (!$this -> check_perm($dephp_6)){
                return false;
            }
            if (empty($dephp_7['id'])){
                $dephp_8 = $dephp_6 . '.add';
                if (!$this -> check($dephp_8)){
                    return false;
                }
                return true;
            }else{
                $dephp_9 = $dephp_6 . '.edit';
                if (!$this -> check($dephp_9)){
                    return false;
                }
                return true;
            }
        }
        public function check_perm($dephp_10 = ''){
            global $_W;
            $dephp_11 = true;
            if (empty($dephp_10)){
                return false;
            }
            if (!strexists($dephp_10, '&') && !strexists($dephp_10, '|')){
                $dephp_11 = $this -> check($dephp_10);
            }else if (strexists($dephp_10, '&')){
                $dephp_12 = explode('&', $dephp_10);
                foreach ($dephp_12 as $dephp_13){
                    $dephp_11 = $this -> check($dephp_13);
                    if (!$dephp_11){
                        break;
                    }
                }
            }else if (strexists($dephp_10, '|')){
                $dephp_12 = explode('|', $dephp_10);
                foreach ($dephp_12 as $dephp_13){
                    $dephp_11 = $this -> check($dephp_13);
                    if ($dephp_11){
                        break;
                    }
                }
            }
            return $dephp_11;
        }
        private function check($dephp_6 = ''){
            global $_W, $_GPC;
            if ($_W['role'] == 'manager' || $_W['role'] == 'founder'){
                return true;
            }
            $dephp_14 = $_W['uid'];
            if (empty($dephp_6)){
                return false;
            }
            $dephp_15 = pdo_fetch('select u.status as userstatus,r.status as rolestatus,u.perms as userperms,r.perms as roleperms,u.roleid from ' . tablename('manor_shop_perm_user') . ' u ' . ' left join ' . tablename('manor_shop_perm_role') . ' r on u.roleid = r.id ' . ' where uid=:uid limit 1 ', array(':uid' => $dephp_14));
            if (empty($dephp_15) || empty($dephp_15['userstatus'])){
                return false;
            }
            if(!empty($dephp_15['role']) && empty($dephp_15['rolestatus'])){
                return true;
            }
            $dephp_16 = explode(',', $dephp_15['roleperms']);
            $dephp_17 = explode(',', $dephp_15['userperms']);
            $dephp_0 = array_merge($dephp_16, $dephp_17);
            if (empty($dephp_0)){
                return false;
            }
            $dephp_18 = explode('.', $dephp_6);
            if (!in_array($dephp_18[0], $dephp_0)){
                return false;
            }
            if (isset($dephp_18[1]) && !in_array($dephp_18[0] . '.' . $dephp_18[1], $dephp_0)){
                return false;
            }
            if (isset($dephp_18[2]) && !in_array($dephp_18[0] . '.' . $dephp_18[1] . '.' . $dephp_18[2], $dephp_0)){
                return false;
            }
            return true;
        }
        function check_plugin($dephp_5 = ''){
            global $_W, $_GPC;
            $dephp_19 = m('cache') -> getString('permset', 'global');
            if(empty($dephp_19)){
                return true;
            }
            if ($_W['role'] == 'founder'){
                return true;
            }
            $dephp_20 = $this -> isopen($dephp_5);
            if (!$dephp_20){
                return false;
            }
            $dephp_21 = true;
            $dephp_22 = pdo_fetchcolumn('SELECT acid FROM ' . tablename('account_wechats') . ' WHERE `uniacid`=:uniacid LIMIT 1', array(':uniacid' => $_W['uniacid']));
            $dephp_23 = pdo_fetch('select  plugins from ' . tablename('manor_shop_perm_plugin') . ' where acid=:acid limit 1', array(':acid' => $dephp_22));
            if (!empty($dephp_23)){
                $dephp_24 = explode(',', $dephp_23['plugins']);
                if (!in_array($dephp_5, $dephp_24)){
                    $dephp_21 = false;
                }
            }else{
                load() -> model('account');
                $dephp_25 = uni_owned($_W['founder']);
                if(in_array($_W['uniacid'], array_keys($dephp_25))){
                    $dephp_21 = true;
                }else{
                    $dephp_21 = false;
                }
            }
            if (!$dephp_21){
                return false;
            }
            return $this -> check($dephp_5);
        }
        public function getLogName($dephp_26 = '', $dephp_27 = null){
            if (!$dephp_27){
                $dephp_27 = $this -> getLogTypes();
            }
            foreach ($dephp_27 as $dephp_28){
                if ($dephp_28['value'] == $dephp_26){
                    return $dephp_28['text'];
                }
            }
            return '';
        }
        public function getLogTypes(){
            $dephp_29 = array();
            $dephp_0 = $this -> allPerms();
            foreach ($dephp_0 as $dephp_30 => $dephp_31){
                if (isset($dephp_31['child'])){
                    foreach ($dephp_31['child'] as $dephp_32 => $dephp_33){
                        foreach ($dephp_33 as $dephp_34 => $dephp_35){
                            if (strexists($dephp_35, '-log')){
                                $dephp_36 = str_replace('-log', "", $dephp_31['text'] . '-' . $dephp_33['text'] . '-' . $dephp_35);
                                if ($dephp_34 == 'text'){
                                    $dephp_36 = str_replace('-log', "", $dephp_31['text'] . '-' . $dephp_33['text']);
                                }
                                $dephp_29[] = array('text' => $dephp_36, 'value' => str_replace('.text', "", $dephp_30 . '.' . $dephp_32 . '.' . $dephp_34));
                            }
                        }
                    }
                }else{
                    foreach ($dephp_31 as $dephp_34 => $dephp_35){
                        if (strexists($dephp_35, '-log')){
                            $dephp_36 = str_replace('-log', "", $dephp_31['text'] . '-' . $dephp_35);
                            if ($dephp_34 == 'text'){
                                $dephp_36 = str_replace('-log', "", $dephp_31['text']);
                            }
                            $dephp_29[] = array('text' => $dephp_36, 'value' => str_replace('.text', "", $dephp_30 . '.' . $dephp_34));
                        }
                    }
                }
            }
            return $dephp_29;
        }
        public function log($dephp_26 = '', $dephp_37 = ''){
            global $_W;
            static $dephp_38;
            if (!$dephp_38){
                $dephp_38 = $this -> getLogTypes();
            }
            $dephp_39 = array('uniacid' => $_W['uniacid'], 'uid' => $_W['uid'], 'name' => $this -> getLogName($dephp_26, $dephp_38), 'type' => $dephp_26, 'op' => $dephp_37, 'ip' => CLIENT_IP, 'createtime' => time());
            pdo_insert('manor_shop_perm_log', $dephp_39);
        }
        public function perms(){
            return array('perm' => array('text' => $this -> getName(), 'isplugin' => true, 'child' => array('set' => array('text' => '基础设置'), 'role' => array('text' => '角色', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log'), 'user' => array('text' => '操作员', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log'), 'log' => array('text' => '操作日志', 'view' => '浏览', 'delete' => '删除-log', 'clear' => '清除-log'),)));
        }
    }
}
