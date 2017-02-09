<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
define('TM_COMMISSION_AGENT_NEW', 'commission_agent_new');
define('TM_COMMISSION_ORDER_PAY', 'commission_order_pay');
define('TM_COMMISSION_ORDER_FINISH', 'commission_order_finish');
define('TM_COMMISSION_APPLY', 'commission_apply');
define('TM_COMMISSION_CHECK', 'commission_check');
define('TM_COMMISSION_PAY', 'commission_pay');
define('TM_COMMISSION_UPGRADE', 'commission_upgrade');
define('TM_COMMISSION_BECOME', 'commission_become');
if (!class_exists('CommissionModel')){
    class CommissionModel extends PluginModel{
        public function getSet($dephp_0 = 0){
            $dephp_1 = parent :: getSet($dephp_0);
            $dephp_1['texts'] = array('agent' => empty($dephp_1['texts']['agent']) ? '分销商' : $dephp_1['texts']['agent'], 'shop' => empty($dephp_1['texts']['shop']) ? '小店' : $dephp_1['texts']['shop'], 'myshop' => empty($dephp_1['texts']['myshop']) ? '我的小店' : $dephp_1['texts']['myshop'], 'center' => empty($dephp_1['texts']['center']) ? '分销中心' : $dephp_1['texts']['center'], 'become' => empty($dephp_1['texts']['become']) ? '成为分销商' : $dephp_1['texts']['become'], 'withdraw' => empty($dephp_1['texts']['withdraw']) ? '提现' : $dephp_1['texts']['withdraw'], 'commission' => empty($dephp_1['texts']['commission']) ? '佣金' : $dephp_1['texts']['commission'], 'commission1' => empty($dephp_1['texts']['commission1']) ? '分销佣金' : $dephp_1['texts']['commission1'], 'commission_total' => empty($dephp_1['texts']['commission_total']) ? '累计佣金' : $dephp_1['texts']['commission_total'], 'commission_ok' => empty($dephp_1['texts']['commission_ok']) ? '可提现佣金' : $dephp_1['texts']['commission_ok'], 'commission_apply' => empty($dephp_1['texts']['commission_apply']) ? '已申请佣金' : $dephp_1['texts']['commission_apply'], 'commission_check' => empty($dephp_1['texts']['commission_check']) ? '待打款佣金' : $dephp_1['texts']['commission_check'], 'commission_lock' => empty($dephp_1['texts']['commission_lock']) ? '未结算佣金' : $dephp_1['texts']['commission_lock'], 'commission_detail' => empty($dephp_1['texts']['commission_detail']) ? '佣金明细' : $dephp_1['texts']['commission_detail'], 'commission_pay' => empty($dephp_1['texts']['commission_pay']) ? '成功提现佣金' : $dephp_1['texts']['commission_pay'], 'order' => empty($dephp_1['texts']['order']) ? '分销订单' : $dephp_1['texts']['order'], 'myteam' => empty($dephp_1['texts']['myteam']) ? '我的团队' : $dephp_1['texts']['myteam'], 'c1' => empty($dephp_1['texts']['c1']) ? '一级' : $dephp_1['texts']['c1'], 'c2' => empty($dephp_1['texts']['c2']) ? '二级' : $dephp_1['texts']['c2'], 'c3' => empty($dephp_1['texts']['c3']) ? '三级' : $dephp_1['texts']['c3'], 'mycustomer' => empty($dephp_1['texts']['mycustomer']) ? '我的客户' : $dephp_1['texts']['mycustomer'],);
            return $dephp_1;
        }
        public function calculate($dephp_2 = 0, $dephp_3 = true){
            global $_W;
            $dephp_1 = $this -> getSet();
            $dephp_4 = $this -> getLevels();
            $dephp_5 = pdo_fetchcolumn('select agentid from ' . tablename('manor_shop_order') . ' where id=:id limit 1', array(':id' => $dephp_2));
            $dephp_6 = pdo_fetchall('select og.id,og.realprice,og.total,g.hascommission,g.nocommission, g.commission1_rate,g.commission1_pay,g.commission2_rate,g.commission2_pay,g.commission3_rate,g.commission3_pay,og.commissions from ' . tablename('manor_shop_order_goods') . '  og ' . ' left join ' . tablename('manor_shop_goods') . ' g on g.id = og.goodsid' . ' where og.orderid=:orderid and og.uniacid=:uniacid', array(':orderid' => $dephp_2, ':uniacid' => $_W['uniacid']));
            if ($dephp_1['level'] > 0){
                foreach ($dephp_6 as & $dephp_7){
                    $dephp_8 = $dephp_7['realprice'];
                    if (empty($dephp_7['nocommission'])){
                        if ($dephp_7['hascommission'] == 1){
                            $dephp_7['commission1'] = array('default' => $dephp_1['level'] >= 1 ? ($dephp_7['commission1_rate'] > 0 ? round($dephp_7['commission1_rate'] * $dephp_8 / 100, 2) . "" : round($dephp_7['commission1_pay'] * $dephp_7['total'], 2)) : 0);
                            $dephp_7['commission2'] = array('default' => $dephp_1['level'] >= 2 ? ($dephp_7['commission2_rate'] > 0 ? round($dephp_7['commission2_rate'] * $dephp_8 / 100, 2) . "" : round($dephp_7['commission2_pay'] * $dephp_7['total'], 2)) : 0);
                            $dephp_7['commission3'] = array('default' => $dephp_1['level'] >= 3 ? ($dephp_7['commission3_rate'] > 0 ? round($dephp_7['commission3_rate'] * $dephp_8 / 100, 2) . "" : round($dephp_7['commission3_pay'] * $dephp_7['total'], 2)) : 0);
                            foreach ($dephp_4 as $dephp_9){
                                $dephp_7['commission1']['level' . $dephp_9['id']] = $dephp_7['commission1_rate'] > 0 ? round($dephp_7['commission1_rate'] * $dephp_8 / 100, 2) . "" : round($dephp_7['commission1_pay'] * $dephp_7['total'], 2);
                                $dephp_7['commission2']['level' . $dephp_9['id']] = $dephp_7['commission2_rate'] > 0 ? round($dephp_7['commission2_rate'] * $dephp_8 / 100, 2) . "" : round($dephp_7['commission2_pay'] * $dephp_7['total'], 2);
                                $dephp_7['commission3']['level' . $dephp_9['id']] = $dephp_7['commission3_rate'] > 0 ? round($dephp_7['commission3_rate'] * $dephp_8 / 100, 2) . "" : round($dephp_7['commission3_pay'] * $dephp_7['total'], 2);
                            }
                        }else{
                            $dephp_7['commission1'] = array('default' => $dephp_1['level'] >= 1 ? round($dephp_1['commission1'] * $dephp_8 / 100, 2) . "" : 0);
                            $dephp_7['commission2'] = array('default' => $dephp_1['level'] >= 2 ? round($dephp_1['commission2'] * $dephp_8 / 100, 2) . "" : 0);
                            $dephp_7['commission3'] = array('default' => $dephp_1['level'] >= 3 ? round($dephp_1['commission3'] * $dephp_8 / 100, 2) . "" : 0);
                            foreach ($dephp_4 as $dephp_9){
                                $dephp_7['commission1']['level' . $dephp_9['id']] = $dephp_1['level'] >= 1 ? round($dephp_9['commission1'] * $dephp_8 / 100, 2) . "" : 0;
                                $dephp_7['commission2']['level' . $dephp_9['id']] = $dephp_1['level'] >= 2 ? round($dephp_9['commission2'] * $dephp_8 / 100, 2) . "" : 0;
                                $dephp_7['commission3']['level' . $dephp_9['id']] = $dephp_1['level'] >= 3 ? round($dephp_9['commission3'] * $dephp_8 / 100, 2) . "" : 0;
                            }
                        }
                    }else{
                        $dephp_7['commission1'] = array('default' => 0);
                        $dephp_7['commission2'] = array('default' => 0);
                        $dephp_7['commission3'] = array('default' => 0);
                        foreach ($dephp_4 as $dephp_9){
                            $dephp_7['commission1']['level' . $dephp_9['id']] = 0;
                            $dephp_7['commission2']['level' . $dephp_9['id']] = 0;
                            $dephp_7['commission3']['level' . $dephp_9['id']] = 0;
                        }
                    }
                    if ($dephp_3){
                        $dephp_10 = array('level1' => 0, 'level2' => 0, 'level3' => 0);
                        if (!empty($dephp_5)){
                            $dephp_11 = m('member') -> getMember($dephp_5);
                            if ($dephp_11['isagent'] == 1 && $dephp_11['status'] == 1){
                                $dephp_12 = $this -> getLevel($dephp_11['openid']);
                                $dephp_10['level1'] = empty($dephp_12) ? round($dephp_7['commission1']['default'], 2) : round($dephp_7['commission1']['level' . $dephp_12['id']], 2);
                                if (!empty($dephp_11['agentid'])){
                                    $dephp_13 = m('member') -> getMember($dephp_11['agentid']);
                                    $dephp_14 = $this -> getLevel($dephp_13['openid']);
                                    $dephp_10['level2'] = empty($dephp_14) ? round($dephp_7['commission2']['default'], 2) : round($dephp_7['commission2']['level' . $dephp_14['id']], 2);
                                    if (!empty($dephp_13['agentid'])){
                                        $dephp_15 = m('member') -> getMember($dephp_13['agentid']);
                                        $dephp_16 = $this -> getLevel($dephp_15['openid']);
                                        $dephp_10['level3'] = empty($dephp_16) ? round($dephp_7['commission3']['default'], 2) : round($dephp_7['commission3']['level' . $dephp_16['id']], 2);
                                    }
                                }
                            }
                        }
                        pdo_update('manor_shop_order_goods', array('commission1' => iserializer($dephp_7['commission1']), 'commission2' => iserializer($dephp_7['commission2']), 'commission3' => iserializer($dephp_7['commission3']), 'commissions' => iserializer($dephp_10), 'nocommission' => $dephp_7['nocommission']), array('id' => $dephp_7['id']));
                    }
                }
                unset($dephp_7);
            }
            return $dephp_6;
        }
        public function getOrderCommissions($dephp_2 = 0, $dephp_17 = 0){
            global $_W;
            $dephp_1 = $this -> getSet();
            $dephp_5 = pdo_fetchcolumn('select agentid from ' . tablename('manor_shop_order') . ' where id=:id limit 1', array(':id' => $dephp_2));
            $dephp_6 = pdo_fetch('select commission1,commission2,commission3 from ' . tablename('manor_shop_order_goods') . ' where id=:id and orderid=:orderid and uniacid=:uniacid and nocommission=0 limit 1', array(':id' => $dephp_17, ':orderid' => $dephp_2, ':uniacid' => $_W['uniacid']));
            $dephp_10 = array('level1' => 0, 'level2' => 0, 'level3' => 0);
            if ($dephp_1['level'] > 0){
                $dephp_18 = iunserializer($dephp_6['commission1']);
                $dephp_19 = iunserializer($dephp_6['commission2']);
                $dephp_20 = iunserializer($dephp_6['commission3']);
                if (!empty($dephp_5)){
                    $dephp_11 = m('member') -> getMember($dephp_5);
                    if ($dephp_11['isagent'] == 1 && $dephp_11['status'] == 1){
                        $dephp_12 = $this -> getLevel($dephp_11['openid']);
                        $dephp_10['level1'] = empty($dephp_12) ? round($dephp_18['default'], 2) : round($dephp_18['level' . $dephp_12['id']], 2);
                        if (!empty($dephp_11['agentid'])){
                            $dephp_13 = m('member') -> getMember($dephp_11['agentid']);
                            $dephp_14 = $this -> getLevel($dephp_13['openid']);
                            $dephp_10['level2'] = empty($dephp_14) ? round($dephp_19['default'], 2) : round($dephp_19['level' . $dephp_14['id']], 2);
                            if (!empty($dephp_13['agentid'])){
                                $dephp_15 = m('member') -> getMember($dephp_13['agentid']);
                                $dephp_16 = $this -> getLevel($dephp_15['openid']);
                                $dephp_10['level3'] = empty($dephp_16) ? round($dephp_20['default'], 2) : round($dephp_20['level' . $dephp_16['id']], 2);
                            }
                        }
                    }
                }
            }
            return $dephp_10;
        }
        public function getInfo($dephp_21, $dephp_22 = null){
            if (empty($dephp_22) || !is_array($dephp_22)){
                $dephp_22 = array();
            }
            global $_W;
            $dephp_1 = $this -> getSet();
            $dephp_9 = intval($dephp_1['level']);
            $dephp_23 = m('member') -> getMember($dephp_21);
            $dephp_24 = $this -> getLevel($dephp_21);
            $dephp_25 = time();
            $dephp_26 = intval($dephp_1['settledays']) * 3600 * 24;
            $dephp_27 = 0;
            $dephp_28 = 0;
            $dephp_29 = 0;
            $dephp_30 = 0;
            $dephp_31 = 0;
            $dephp_32 = 0;
            $dephp_33 = 0;
            $dephp_34 = 0;
            $dephp_35 = 0;
            $dephp_36 = 0;
            $dephp_37 = 0;
            $dephp_38 = 0;
            $dephp_39 = 0;
            $dephp_40 = 0;
            $dephp_41 = 0;
            $dephp_42 = 0;
            $dephp_43 = 0;
            $dephp_44 = 0;
            $dephp_45 = 0;
            $dephp_46 = 0;
            $dephp_47 = 0;
            $dephp_48 = 0;
            $dephp_49 = 0;
            $dephp_50 = 0;
            $dephp_51 = 0;
            $dephp_52 = 0;
            $dephp_53 = 0;
            $dephp_54 = 0;
            if ($dephp_9 >= 1){
                if (in_array('ordercount0', $dephp_22)){
                    $dephp_55 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('manor_shop_order') . ' o ' . ' left join  ' . tablename('manor_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid=:agentid and o.status>=0 and og.status1>=0 and og.nocommission=0 and o.uniacid=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $dephp_23['id']));
                    $dephp_43 += $dephp_55['ordercount'];
                    $dephp_28 += $dephp_55['ordercount'];
                    $dephp_29 += $dephp_55['ordermoney'];
                }
                if (in_array('ordercount', $dephp_22)){
                    $dephp_55 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('manor_shop_order') . ' o ' . ' left join  ' . tablename('manor_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid=:agentid and o.status>=1 and og.status1>=0 and og.nocommission=0 and o.uniacid=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $dephp_23['id']));
                    $dephp_46 += $dephp_55['ordercount'];
                    $dephp_30 += $dephp_55['ordercount'];
                    $dephp_31 += $dephp_55['ordermoney'];
                }
                if (in_array('ordercount3', $dephp_22)){
                    $dephp_56 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('manor_shop_order') . ' o ' . ' left join  ' . tablename('manor_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid=:agentid and o.status>=3 and og.status1>=0 and og.nocommission=0 and o.uniacid=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $dephp_23['id']));
                    $dephp_49 += $dephp_56['ordercount'];
                    $dephp_32 += $dephp_56['ordercount'];
                    $dephp_33 += $dephp_56['ordermoney'];
                    $dephp_52 += $dephp_56['ordermoney'];
                }
                if (in_array('total', $dephp_22)){
                    $dephp_57 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join  ' . tablename('manor_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid=:agentid and o.status>=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':agentid' => $dephp_23['id']));
                    foreach ($dephp_57 as $dephp_58){
                        $dephp_10 = iunserializer($dephp_58['commissions']);
                        $dephp_59 = iunserializer($dephp_58['commission1']);
                        if (empty($dephp_10)){
                            $dephp_34 += isset($dephp_59['level' . $dephp_24['id']]) ? $dephp_59['level' . $dephp_24['id']] : $dephp_59['default'];
                        }else{
                            $dephp_34 += isset($dephp_10['level1']) ? floatval($dephp_10['level1']) : 0;
                        }
                    }
                }
                if (in_array('ok', $dephp_22)){
                    $dephp_57 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join  ' . tablename('manor_shop_order') . ' o on o.id = og.orderid' . " where o.agentid=:agentid and o.status>=3 and og.nocommission=0 and ({$dephp_25} - o.finishtime > {$dephp_26}) and og.status1=0  and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid'], ':agentid' => $dephp_23['id']));
                    foreach ($dephp_57 as $dephp_58){
                        $dephp_10 = iunserializer($dephp_58['commissions']);
                        $dephp_59 = iunserializer($dephp_58['commission1']);
                        if (empty($dephp_10)){
                            $dephp_35 += isset($dephp_59['level' . $dephp_24['id']]) ? $dephp_59['level' . $dephp_24['id']] : $dephp_59['default'];
                        }else{
                            $dephp_35 += isset($dephp_10['level1']) ? $dephp_10['level1'] : 0;
                        }
                    }
                }
                if (in_array('lock', $dephp_22)){
                    $dephp_60 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join  ' . tablename('manor_shop_order') . ' o on o.id = og.orderid' . " where o.agentid=:agentid and o.status>=3 and og.nocommission=0 and ({$dephp_25} - o.finishtime <= {$dephp_26})  and og.status1=0  and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid'], ':agentid' => $dephp_23['id']));
                    foreach ($dephp_60 as $dephp_58){
                        $dephp_10 = iunserializer($dephp_58['commissions']);
                        $dephp_59 = iunserializer($dephp_58['commission1']);
                        if (empty($dephp_10)){
                            $dephp_38 += isset($dephp_59['level' . $dephp_24['id']]) ? $dephp_59['level' . $dephp_24['id']] : $dephp_59['default'];
                        }else{
                            $dephp_38 += isset($dephp_10['level1']) ? $dephp_10['level1'] : 0;
                        }
                    }
                }
                if (in_array('apply', $dephp_22)){
                    $dephp_61 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join  ' . tablename('manor_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid=:agentid and o.status>=3 and og.status1=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':agentid' => $dephp_23['id']));
                    foreach ($dephp_61 as $dephp_58){
                        $dephp_10 = iunserializer($dephp_58['commissions']);
                        $dephp_59 = iunserializer($dephp_58['commission1']);
                        if (empty($dephp_10)){
                            $dephp_36 += isset($dephp_59['level' . $dephp_24['id']]) ? $dephp_59['level' . $dephp_24['id']] : $dephp_59['default'];
                        }else{
                            $dephp_36 += isset($dephp_10['level1']) ? $dephp_10['level1'] : 0;
                        }
                    }
                }
                if (in_array('check', $dephp_22)){
                    $dephp_61 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join  ' . tablename('manor_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid=:agentid and o.status>=3 and og.status1=2 and og.nocommission=0 and o.uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':agentid' => $dephp_23['id']));
                    foreach ($dephp_61 as $dephp_58){
                        $dephp_10 = iunserializer($dephp_58['commissions']);
                        $dephp_59 = iunserializer($dephp_58['commission1']);
                        if (empty($dephp_10)){
                            $dephp_37 += isset($dephp_59['level' . $dephp_24['id']]) ? $dephp_59['level' . $dephp_24['id']] : $dephp_59['default'];
                        }else{
                            $dephp_37 += isset($dephp_10['level1']) ? $dephp_10['level1'] : 0;
                        }
                    }
                }
                if (in_array('pay', $dephp_22)){
                    $dephp_61 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join  ' . tablename('manor_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid=:agentid and o.status>=3 and og.status1=3 and og.nocommission=0 and o.uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':agentid' => $dephp_23['id']));
                    foreach ($dephp_61 as $dephp_58){
                        $dephp_10 = iunserializer($dephp_58['commissions']);
                        $dephp_59 = iunserializer($dephp_58['commission1']);
                        if (empty($dephp_10)){
                            $dephp_39 += isset($dephp_59['level' . $dephp_24['id']]) ? $dephp_59['level' . $dephp_24['id']] : $dephp_59['default'];
                        }else{
                            $dephp_39 += isset($dephp_10['level1']) ? $dephp_10['level1'] : 0;
                        }
                    }
                }
                $dephp_62 = pdo_fetchall('select id from ' . tablename('manor_shop_member') . ' where agentid=:agentid and isagent=1 and status=1 and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':agentid' => $dephp_23['id']), 'id');
                $dephp_40 = count($dephp_62);
                $dephp_27 += $dephp_40;
            }
            if ($dephp_9 >= 2){
                if ($dephp_40 > 0){
                    if (in_array('ordercount0', $dephp_22)){
                        $dephp_63 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('manor_shop_order') . ' o ' . ' left join  ' . tablename('manor_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($dephp_62)) . ')  and o.status>=0 and og.status2>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
                        $dephp_44 += $dephp_63['ordercount'];
                        $dephp_28 += $dephp_63['ordercount'];
                        $dephp_29 += $dephp_63['ordermoney'];
                    }
                    if (in_array('ordercount', $dephp_22)){
                        $dephp_63 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('manor_shop_order') . ' o ' . ' left join  ' . tablename('manor_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($dephp_62)) . ')  and o.status>=1 and og.status2>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
                        $dephp_47 += $dephp_63['ordercount'];
                        $dephp_30 += $dephp_63['ordercount'];
                        $dephp_31 += $dephp_63['ordermoney'];
                    }
                    if (in_array('ordercount3', $dephp_22)){
                        $dephp_64 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('manor_shop_order') . ' o ' . ' left join  ' . tablename('manor_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($dephp_62)) . ')  and o.status>=3 and og.status2>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
                        $dephp_50 += $dephp_64['ordercount'];
                        $dephp_32 += $dephp_64['ordercount'];
                        $dephp_33 += $dephp_64['ordermoney'];
                        $dephp_53 += $dephp_64['ordermoney'];
                    }
                    if (in_array('total', $dephp_22)){
                        $dephp_65 = pdo_fetchall('select og.commission2,og.commissions from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join  ' . tablename('manor_shop_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($dephp_62)) . ')  and o.status>=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
                        foreach ($dephp_65 as $dephp_58){
                            $dephp_10 = iunserializer($dephp_58['commissions']);
                            $dephp_59 = iunserializer($dephp_58['commission2']);
                            if (empty($dephp_10)){
                                $dephp_34 += isset($dephp_59['level' . $dephp_24['id']]) ? $dephp_59['level' . $dephp_24['id']] : $dephp_59['default'];
                            }else{
                                $dephp_34 += isset($dephp_10['level2']) ? $dephp_10['level2'] : 0;
                            }
                        }
                    }
                    if (in_array('ok', $dephp_22)){
                        $dephp_65 = pdo_fetchall('select og.commission2,og.commissions  from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join  ' . tablename('manor_shop_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($dephp_62)) . ")  and ({$dephp_25} - o.finishtime > {$dephp_26}) and o.status>=3 and og.status2=0 and og.nocommission=0  and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
                        foreach ($dephp_65 as $dephp_58){
                            $dephp_10 = iunserializer($dephp_58['commissions']);
                            $dephp_59 = iunserializer($dephp_58['commission2']);
                            if (empty($dephp_10)){
                                $dephp_35 += isset($dephp_59['level' . $dephp_24['id']]) ? $dephp_59['level' . $dephp_24['id']] : $dephp_59['default'];
                            }else{
                                $dephp_35 += isset($dephp_10['level2']) ? $dephp_10['level2'] : 0;
                            }
                        }
                    }
                    if (in_array('lock', $dephp_22)){
                        $dephp_66 = pdo_fetchall('select og.commission2,og.commissions  from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join  ' . tablename('manor_shop_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($dephp_62)) . ")  and ({$dephp_25} - o.finishtime <= {$dephp_26}) and og.status2=0 and o.status>=3 and og.nocommission=0 and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
                        foreach ($dephp_66 as $dephp_58){
                            $dephp_10 = iunserializer($dephp_58['commissions']);
                            $dephp_59 = iunserializer($dephp_58['commission2']);
                            if (empty($dephp_10)){
                                $dephp_38 += isset($dephp_59['level' . $dephp_24['id']]) ? $dephp_59['level' . $dephp_24['id']] : $dephp_59['default'];
                            }else{
                                $dephp_38 += isset($dephp_10['level2']) ? $dephp_10['level2'] : 0;
                            }
                        }
                    }
                    if (in_array('apply', $dephp_22)){
                        $dephp_67 = pdo_fetchall('select og.commission2,og.commissions  from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join  ' . tablename('manor_shop_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($dephp_62)) . ')  and o.status>=3 and og.status2=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
                        foreach ($dephp_67 as $dephp_58){
                            $dephp_10 = iunserializer($dephp_58['commissions']);
                            $dephp_59 = iunserializer($dephp_58['commission2']);
                            if (empty($dephp_10)){
                                $dephp_36 += isset($dephp_59['level' . $dephp_24['id']]) ? $dephp_59['level' . $dephp_24['id']] : $dephp_59['default'];
                            }else{
                                $dephp_36 += isset($dephp_10['level2']) ? $dephp_10['level2'] : 0;
                            }
                        }
                    }
                    if (in_array('check', $dephp_22)){
                        $dephp_68 = pdo_fetchall('select og.commission2,og.commissions  from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join  ' . tablename('manor_shop_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($dephp_62)) . ')  and o.status>=3 and og.status2=2 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
                        foreach ($dephp_68 as $dephp_58){
                            $dephp_10 = iunserializer($dephp_58['commissions']);
                            $dephp_59 = iunserializer($dephp_58['commission2']);
                            if (empty($dephp_10)){
                                $dephp_37 += isset($dephp_59['level' . $dephp_24['id']]) ? $dephp_59['level' . $dephp_24['id']] : $dephp_59['default'];
                            }else{
                                $dephp_37 += isset($dephp_10['level2']) ? $dephp_10['level2'] : 0;
                            }
                        }
                    }
                    if (in_array('pay', $dephp_22)){
                        $dephp_68 = pdo_fetchall('select og.commission2,og.commissions  from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join  ' . tablename('manor_shop_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($dephp_62)) . ')  and o.status>=3 and og.status2=3 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
                        foreach ($dephp_68 as $dephp_58){
                            $dephp_10 = iunserializer($dephp_58['commissions']);
                            $dephp_59 = iunserializer($dephp_58['commission2']);
                            if (empty($dephp_10)){
                                $dephp_39 += isset($dephp_59['level' . $dephp_24['id']]) ? $dephp_59['level' . $dephp_24['id']] : $dephp_59['default'];
                            }else{
                                $dephp_39 += isset($dephp_10['level2']) ? $dephp_10['level2'] : 0;
                            }
                        }
                    }
                    $dephp_69 = pdo_fetchall('select id from ' . tablename('manor_shop_member') . ' where agentid in( ' . implode(',', array_keys($dephp_62)) . ') and isagent=1 and status=1 and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']), 'id');
                    $dephp_41 = count($dephp_69);
                    $dephp_27 += $dephp_41;
                }
            }
            if ($dephp_9 >= 3){
                if ($dephp_41 > 0){
                    if (in_array('ordercount0', $dephp_22)){
                        $dephp_70 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('manor_shop_order') . ' o ' . ' left join  ' . tablename('manor_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($dephp_69)) . ')  and o.status>=0 and og.status3>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
                        $dephp_45 += $dephp_70['ordercount'];
                        $dephp_28 += $dephp_70['ordercount'];
                        $dephp_29 += $dephp_70['ordermoney'];
                    }
                    if (in_array('ordercount', $dephp_22)){
                        $dephp_70 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('manor_shop_order') . ' o ' . ' left join  ' . tablename('manor_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($dephp_69)) . ')  and o.status>=1 and og.status3>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
                        $dephp_48 += $dephp_70['ordercount'];
                        $dephp_30 += $dephp_70['ordercount'];
                        $dephp_31 += $dephp_70['ordermoney'];
                    }
                    if (in_array('ordercount3', $dephp_22)){
                        $dephp_71 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('manor_shop_order') . ' o ' . ' left join  ' . tablename('manor_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($dephp_69)) . ')  and o.status>=3 and og.status3>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
                        $dephp_51 += $dephp_71['ordercount'];
                        $dephp_32 += $dephp_71['ordercount'];
                        $dephp_33 += $dephp_71['ordermoney'];
                        $dephp_54 += $dephp_70['ordermoney'];
                    }
                    if (in_array('total', $dephp_22)){
                        $dephp_72 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join  ' . tablename('manor_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($dephp_69)) . ')  and o.status>=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
                        foreach ($dephp_72 as $dephp_58){
                            $dephp_10 = iunserializer($dephp_58['commissions']);
                            $dephp_59 = iunserializer($dephp_58['commission3']);
                            if (empty($dephp_10)){
                                $dephp_34 += isset($dephp_59['level' . $dephp_24['id']]) ? $dephp_59['level' . $dephp_24['id']] : $dephp_59['default'];
                            }else{
                                $dephp_34 += isset($dephp_10['level3']) ? $dephp_10['level3'] : 0;
                            }
                        }
                    }
                    if (in_array('ok', $dephp_22)){
                        $dephp_72 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join  ' . tablename('manor_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($dephp_69)) . ")  and ({$dephp_25} - o.finishtime > {$dephp_26}) and o.status>=3 and og.status3=0  and og.nocommission=0 and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
                        foreach ($dephp_72 as $dephp_58){
                            $dephp_10 = iunserializer($dephp_58['commissions']);
                            $dephp_59 = iunserializer($dephp_58['commission3']);
                            if (empty($dephp_10)){
                                $dephp_35 += isset($dephp_59['level' . $dephp_24['id']]) ? $dephp_59['level' . $dephp_24['id']] : $dephp_59['default'];
                            }else{
                                $dephp_35 += isset($dephp_10['level3']) ? $dephp_10['level3'] : 0;
                            }
                        }
                    }
                    if (in_array('lock', $dephp_22)){
                        $dephp_73 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join  ' . tablename('manor_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($dephp_69)) . ")  and o.status>=3 and ({$dephp_25} - o.finishtime > {$dephp_26}) and og.status3=0  and og.nocommission=0 and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
                        foreach ($dephp_73 as $dephp_58){
                            $dephp_10 = iunserializer($dephp_58['commissions']);
                            $dephp_59 = iunserializer($dephp_58['commission3']);
                            if (empty($dephp_10)){
                                $dephp_38 += isset($dephp_59['level' . $dephp_24['id']]) ? $dephp_59['level' . $dephp_24['id']] : $dephp_59['default'];
                            }else{
                                $dephp_38 += isset($dephp_10['level3']) ? $dephp_10['level3'] : 0;
                            }
                        }
                    }
                    if (in_array('apply', $dephp_22)){
                        $dephp_74 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join  ' . tablename('manor_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($dephp_69)) . ')  and o.status>=3 and og.status3=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
                        foreach ($dephp_74 as $dephp_58){
                            $dephp_10 = iunserializer($dephp_58['commissions']);
                            $dephp_59 = iunserializer($dephp_58['commission3']);
                            if (empty($dephp_10)){
                                $dephp_36 += isset($dephp_59['level' . $dephp_24['id']]) ? $dephp_59['level' . $dephp_24['id']] : $dephp_59['default'];
                            }else{
                                $dephp_36 += isset($dephp_10['level3']) ? $dephp_10['level3'] : 0;
                            }
                        }
                    }
                    if (in_array('check', $dephp_22)){
                        $dephp_75 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join  ' . tablename('manor_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($dephp_69)) . ')  and o.status>=3 and og.status3=2 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
                        foreach ($dephp_75 as $dephp_58){
                            $dephp_10 = iunserializer($dephp_58['commissions']);
                            $dephp_59 = iunserializer($dephp_58['commission3']);
                            if (empty($dephp_10)){
                                $dephp_37 += isset($dephp_59['level' . $dephp_24['id']]) ? $dephp_59['level' . $dephp_24['id']] : $dephp_59['default'];
                            }else{
                                $dephp_37 += isset($dephp_10['level3']) ? $dephp_10['level3'] : 0;
                            }
                        }
                    }
                    if (in_array('pay', $dephp_22)){
                        $dephp_75 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join  ' . tablename('manor_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($dephp_69)) . ')  and o.status>=3 and og.status3=3 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
                        foreach ($dephp_75 as $dephp_58){
                            $dephp_10 = iunserializer($dephp_58['commissions']);
                            $dephp_59 = iunserializer($dephp_58['commission3']);
                            if (empty($dephp_10)){
                                $dephp_39 += isset($dephp_59['level' . $dephp_24['id']]) ? $dephp_59['level' . $dephp_24['id']] : $dephp_59['default'];
                            }else{
                                $dephp_39 += isset($dephp_10['level3']) ? $dephp_10['level3'] : 0;
                            }
                        }
                    }
                    $dephp_76 = pdo_fetchall('select id from ' . tablename('manor_shop_member') . ' where uniacid=:uniacid and agentid in( ' . implode(',', array_keys($dephp_69)) . ') and isagent=1 and status=1', array(':uniacid' => $_W['uniacid']), 'id');
                    $dephp_42 = count($dephp_76);
                    $dephp_27 += $dephp_42;
                }
            }
            $dephp_23['agentcount'] = $dephp_27;
            $dephp_23['ordercount'] = $dephp_30;
            $dephp_23['ordermoney'] = $dephp_31;
            $dephp_23['order1'] = $dephp_46;
            $dephp_23['order2'] = $dephp_47;
            $dephp_23['order3'] = $dephp_48;
            $dephp_23['ordercount3'] = $dephp_32;
            $dephp_23['ordermoney3'] = $dephp_33;
            $dephp_23['order13'] = $dephp_49;
            $dephp_23['order23'] = $dephp_50;
            $dephp_23['order33'] = $dephp_51;
            $dephp_23['order13money'] = $dephp_52;
            $dephp_23['order23money'] = $dephp_53;
            $dephp_23['order33money'] = $dephp_54;
            $dephp_23['ordercount0'] = $dephp_28;
            $dephp_23['ordermoney0'] = $dephp_29;
            $dephp_23['order10'] = $dephp_43;
            $dephp_23['order20'] = $dephp_44;
            $dephp_23['order30'] = $dephp_45;
            $dephp_23['commission_total'] = round($dephp_34, 2);
            $dephp_23['commission_ok'] = round($dephp_35, 2);
            $dephp_23['commission_lock'] = round($dephp_38, 2);
            $dephp_23['commission_apply'] = round($dephp_36, 2);
            $dephp_23['commission_check'] = round($dephp_37, 2);
            $dephp_23['commission_pay'] = round($dephp_39, 2);
            $dephp_23['level1'] = $dephp_40;
            $dephp_23['level1_agentids'] = $dephp_62;
            $dephp_23['level2'] = $dephp_41;
            $dephp_23['level2_agentids'] = $dephp_69;
            $dephp_23['level3'] = $dephp_42;
            $dephp_23['level3_agentids'] = $dephp_76;
            $dephp_23['agenttime'] = date('Y-m-d H:i', $dephp_23['agenttime']);
            return $dephp_23;
        }
        public function getAgents($dephp_2 = 0){
            global $_W, $_GPC;
            $dephp_77 = array();
            $dephp_78 = pdo_fetch('select id,agentid,openid from ' . tablename('manor_shop_order') . ' where id=:id and uniacid=:uniacid limit 1' , array(':id' => $dephp_2, ':uniacid' => $_W['uniacid']));
            if (empty($dephp_78)){
                return $dephp_77;
            }
            $dephp_11 = m('member') -> getMember($dephp_78['agentid']);
            if (!empty($dephp_11) && $dephp_11['isagent'] == 1 && $dephp_11['status'] == 1){
                $dephp_77[] = $dephp_11;
                if (!empty($dephp_11['agentid'])){
                    $dephp_13 = m('member') -> getMember($dephp_11['agentid']);
                    if (!empty($dephp_13) && $dephp_13['isagent'] == 1 && $dephp_13['status'] == 1){
                        $dephp_77[] = $dephp_13;
                        if (!empty($dephp_13['agentid'])){
                            $dephp_15 = m('member') -> getMember($dephp_13['agentid']);
                            if (!empty($dephp_15) && $dephp_15['isagent'] == 1 && $dephp_15['status'] == 1){
                                $dephp_77[] = $dephp_15;
                            }
                        }
                    }
                }
            }
            return $dephp_77;
        }
        public function isAgent($dephp_21){
            if (empty($dephp_21)){
                return false;
            }
            if (is_array($dephp_21)){
                return $dephp_21['isagent'] == 1 && $dephp_21['status'] == 1;
            }
            $dephp_23 = m('member') -> getMember($dephp_21);
            return $dephp_23['isagent'] == 1 && $dephp_23['status'] == 1;
        }
        public function getCommission($dephp_6){
            global $_W;
            $dephp_1 = $this -> getSet();
            $dephp_59 = 0;
            if ($dephp_6['hascommission'] == 1){
                $dephp_59 = $dephp_1['level'] >= 1 ? ($dephp_6['commission1_rate'] > 0 ? ($dephp_6['commission1_rate'] * $dephp_6['marketprice'] / 100) : $dephp_6['commission1_pay']) : 0;
            }else{
                $dephp_21 = m('user') -> getOpenid();
                $dephp_9 = $this -> getLevel($dephp_21);
                if (!empty($dephp_9)){
                    $dephp_59 = $dephp_1['level'] >= 1 ? round($dephp_9['commission1'] * $dephp_6['marketprice'] / 100, 2) : 0;
                }else{
                    $dephp_59 = $dephp_1['level'] >= 1 ? round($dephp_1['commission1'] * $dephp_6['marketprice'] / 100, 2) : 0;
                }
            }
            return $dephp_59;
        }
        public function createMyShopQrcode($dephp_79 = 0, $dephp_80 = 0){
            global $_W;
            $dephp_81 = IA_ROOT . '/addons/manor_shop/data/qrcode/' . $_W['uniacid'];
            if (!is_dir($dephp_81)){
                load() -> func('file');
                mkdirs($dephp_81);
            }
            $dephp_82 = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=manor_shop&do=plugin&p=commission&method=myshop&mid=' . $dephp_79;
            if (!empty($dephp_80)){
                $dephp_82 .= '&posterid=' . $dephp_80;
            }
            $dephp_83 = 'myshop_' . $dephp_80 . '_' . $dephp_79 . '.png';
            $dephp_84 = $dephp_81 . '/' . $dephp_83;
            if (!is_file($dephp_84)){
                require IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
                QRcode :: png($dephp_82, $dephp_84, QR_ECLEVEL_H, 4);
            }
            return $_W['siteroot'] . 'addons/manor_shop/data/qrcode/' . $_W['uniacid'] . '/' . $dephp_83;
        }
        private function createImage($dephp_82){
            load() -> func('communication');
            $dephp_85 = ihttp_request($dephp_82);
            return imagecreatefromstring($dephp_85['content']);
        }
        public function createGoodsImage($dephp_6, $dephp_86){
            global $_W, $_GPC;
            $dephp_6 = set_medias($dephp_6, 'thumb');
            $dephp_21 = m('user') -> getOpenid();
            $dephp_87 = m('member') -> getMember($dephp_21);
            if ($dephp_87['isagent'] == 1 && $dephp_87['status'] == 1){
                $dephp_88 = $dephp_87;
            }else{
                $dephp_79 = intval($_GPC['mid']);
                if (!empty($dephp_79)){
                    $dephp_88 = m('member') -> getMember($dephp_79);
                }
            }
            $dephp_81 = IA_ROOT . '/addons/manor_shop/data/poster/' . $_W['uniacid'] . '/';
            if (!is_dir($dephp_81)){
                load() -> func('file');
                mkdirs($dephp_81);
            }
            $dephp_89 = empty($dephp_6['commission_thumb']) ? $dephp_6['thumb'] : tomedia($dephp_6['commission_thumb']);
            $dephp_90 = md5(json_encode(array('id' => $dephp_6['id'], 'marketprice' => $dephp_6['marketprice'], 'productprice' => $dephp_6['productprice'], 'img' => $dephp_89, 'openid' => $dephp_21, 'version' => 4)));
            $dephp_83 = $dephp_90 . '.jpg';
            if (!is_file($dephp_81 . $dephp_83)){
                set_time_limit(0);
                $dephp_91 = IA_ROOT . '/addons/manor_shop/static/fonts/msyh.ttf';
                $dephp_92 = imagecreatetruecolor(640, 1225);
                $dephp_93 = imagecreatefromjpeg(IA_ROOT . '/addons/manor_shop/plugin/commission/images/poster.jpg');
                imagecopy($dephp_92, $dephp_93, 0, 0, 0, 0, 640, 1225);
                imagedestroy($dephp_93);
                $dephp_94 = preg_replace('/\/0$/i', '/96', $dephp_88['avatar']);
                $dephp_95 = $this -> createImage($dephp_94);
                $dephp_96 = imagesx($dephp_95);
                $dephp_97 = imagesy($dephp_95);
                imagecopyresized($dephp_92, $dephp_95, 24, 32, 0, 0, 88, 88, $dephp_96, $dephp_97);
                imagedestroy($dephp_95);
                $dephp_98 = $this -> createImage($dephp_89);
                $dephp_96 = imagesx($dephp_98);
                $dephp_97 = imagesy($dephp_98);
                imagecopyresized($dephp_92, $dephp_98, 0, 160, 0, 0, 640, 640, $dephp_96, $dephp_97);
                imagedestroy($dephp_98);
                $dephp_99 = imagecreatetruecolor(640, 127);
                imagealphablending($dephp_99, false);
                imagesavealpha($dephp_99, true);
                $dephp_100 = imagecolorallocatealpha($dephp_99, 0, 0, 0, 25);
                imagefill($dephp_99, 0, 0, $dephp_100);
                imagecopy($dephp_92, $dephp_99, 0, 678, 0, 0, 640, 127);
                imagedestroy($dephp_99);
                $dephp_101 = tomedia(m('qrcode') -> createGoodsQrcode($dephp_88['id'], $dephp_6['id']));
                $dephp_102 = $this -> createImage($dephp_101);
                $dephp_96 = imagesx($dephp_102);
                $dephp_97 = imagesy($dephp_102);
                imagecopyresized($dephp_92, $dephp_102, 50, 835, 0, 0, 250, 250, $dephp_96, $dephp_97);
                imagedestroy($dephp_102);
                $dephp_103 = imagecolorallocate($dephp_92, 0, 3, 51);
                $dephp_104 = imagecolorallocate($dephp_92, 240, 102, 0);
                $dephp_105 = imagecolorallocate($dephp_92, 255, 255, 255);
                $dephp_106 = imagecolorallocate($dephp_92, 255, 255, 0);
                $dephp_107 = '我是';
                imagettftext($dephp_92, 20, 0, 150, 70, $dephp_103, $dephp_91, $dephp_107);
                imagettftext($dephp_92, 20, 0, 210, 70, $dephp_104, $dephp_91, $dephp_88['nickname']);
                $dephp_108 = '我要为';
                imagettftext($dephp_92, 20, 0, 150, 105, $dephp_103, $dephp_91, $dephp_108);
                $dephp_109 = $dephp_86['name'];
                imagettftext($dephp_92, 20, 0, 240, 105, $dephp_104, $dephp_91, $dephp_109);
                $dephp_110 = imagettfbbox(20, 0, $dephp_91, $dephp_109);
                $dephp_111 = $dephp_110[4] - $dephp_110[6];
                $dephp_112 = '代言';
                imagettftext($dephp_92, 20, 0, 240 + $dephp_111 + 10, 105, $dephp_103, $dephp_91, $dephp_112);
                $dephp_113 = mb_substr($dephp_6['title'], 0, 50, 'utf-8');
                imagettftext($dephp_92, 20, 0, 30, 730, $dephp_105, $dephp_91, $dephp_113);
                $dephp_114 = '￥' . number_format($dephp_6['marketprice'], 2);
                imagettftext($dephp_92, 25, 0, 25, 780, $dephp_106, $dephp_91, $dephp_114);
                $dephp_110 = imagettfbbox(26, 0, $dephp_91, $dephp_114);
                $dephp_111 = $dephp_110[4] - $dephp_110[6];
                if ($dephp_6['productprice'] > 0){
                    $dephp_115 = '￥' . number_format($dephp_6['productprice'], 2);
                    imagettftext($dephp_92, 22, 0, 25 + $dephp_111 + 10, 780, $dephp_105, $dephp_91, $dephp_115);
                    $dephp_116 = 25 + $dephp_111 + 10;
                    $dephp_110 = imagettfbbox(22, 0, $dephp_91, $dephp_115);
                    $dephp_111 = $dephp_110[4] - $dephp_110[6];
                    imageline($dephp_92, $dephp_116, 770, $dephp_116 + $dephp_111 + 20, 770, $dephp_105);
                    imageline($dephp_92, $dephp_116, 771.5, $dephp_116 + $dephp_111 + 20, 771, $dephp_105);
                }
                imagejpeg($dephp_92, $dephp_81 . $dephp_83);
                imagedestroy($dephp_92);
            }
            return $_W['siteroot'] . 'addons/manor_shop/data/poster/' . $_W['uniacid'] . '/' . $dephp_83;
        }
        public function createShopImage($dephp_86){
            global $_W, $_GPC;
            $dephp_86 = set_medias($dephp_86, 'signimg');
            $dephp_81 = IA_ROOT . '/addons/manor_shop/data/poster/' . $_W['uniacid'] . '/';
            if (!is_dir($dephp_81)){
                load() -> func('file');
                mkdirs($dephp_81);
            }
            $dephp_79 = intval($_GPC['mid']);
            $dephp_21 = m('user') -> getOpenid();
            $dephp_87 = m('member') -> getMember($dephp_21);
            if ($dephp_87['isagent'] == 1 && $dephp_87['status'] == 1){
                $dephp_88 = $dephp_87;
            }else{
                $dephp_79 = intval($_GPC['mid']);
                if (!empty($dephp_79)){
                    $dephp_88 = m('member') -> getMember($dephp_79);
                }
            }
            $dephp_90 = md5(json_encode(array('openid' => $dephp_21, 'signimg' => $dephp_86['signimg'], 'version' => 4)));
            $dephp_83 = $dephp_90 . '.jpg';
            if (!is_file($dephp_81 . $dephp_83)){
                set_time_limit(0);
                @ini_set('memory_limit', '256M');
                $dephp_91 = IA_ROOT . '/addons/manor_shop/static/fonts/msyh.ttf';
                $dephp_92 = imagecreatetruecolor(640, 1225);
                $dephp_103 = imagecolorallocate($dephp_92, 0, 3, 51);
                $dephp_104 = imagecolorallocate($dephp_92, 240, 102, 0);
                $dephp_105 = imagecolorallocate($dephp_92, 255, 255, 255);
                $dephp_106 = imagecolorallocate($dephp_92, 255, 255, 0);
                $dephp_93 = imagecreatefromjpeg(IA_ROOT . '/addons/manor_shop/plugin/commission/images/poster.jpg');
                imagecopy($dephp_92, $dephp_93, 0, 0, 0, 0, 640, 1225);
                imagedestroy($dephp_93);
                $dephp_94 = preg_replace('/\/0$/i', '/96', $dephp_88['avatar']);
                $dephp_95 = $this -> createImage($dephp_94);
                $dephp_96 = imagesx($dephp_95);
                $dephp_97 = imagesy($dephp_95);
                imagecopyresized($dephp_92, $dephp_95, 24, 32, 0, 0, 88, 88, $dephp_96, $dephp_97);
                imagedestroy($dephp_95);
                $dephp_98 = $this -> createImage($dephp_86['signimg']);
                $dephp_96 = imagesx($dephp_98);
                $dephp_97 = imagesy($dephp_98);
                imagecopyresized($dephp_92, $dephp_98, 0, 160, 0, 0, 640, 640, $dephp_96, $dephp_97);
                imagedestroy($dephp_98);
                $dephp_117 = tomedia($this -> createMyShopQrcode($dephp_88['id']));
                $dephp_102 = $this -> createImage($dephp_117);
                $dephp_96 = imagesx($dephp_102);
                $dephp_97 = imagesy($dephp_102);
                imagecopyresized($dephp_92, $dephp_102, 50, 835, 0, 0, 250, 250, $dephp_96, $dephp_97);
                imagedestroy($dephp_102);
                $dephp_107 = '我是';
                imagettftext($dephp_92, 20, 0, 150, 70, $dephp_103, $dephp_91, $dephp_107);
                imagettftext($dephp_92, 20, 0, 210, 70, $dephp_104, $dephp_91, $dephp_88['nickname']);
                $dephp_108 = '我要为';
                imagettftext($dephp_92, 20, 0, 150, 105, $dephp_103, $dephp_91, $dephp_108);
                $dephp_109 = $dephp_86['name'];
                imagettftext($dephp_92, 20, 0, 240, 105, $dephp_104, $dephp_91, $dephp_109);
                $dephp_110 = imagettfbbox(20, 0, $dephp_91, $dephp_109);
                $dephp_111 = $dephp_110[4] - $dephp_110[6];
                $dephp_112 = '代言';
                imagettftext($dephp_92, 20, 0, 240 + $dephp_111 + 10, 105, $dephp_103, $dephp_91, $dephp_112);
                imagejpeg($dephp_92, $dephp_81 . $dephp_83);
                imagedestroy($dephp_92);
            }
            return $_W['siteroot'] . 'addons/manor_shop/data/poster/' . $_W['uniacid'] . '/' . $dephp_83;
        }
        public function checkAgent(){
            global $_W, $_GPC;
            $dephp_1 = $this -> getSet();
            if (empty($dephp_1['level'])){
                return;
            }
            $dephp_21 = m('user') -> getOpenid();
            if (empty($dephp_21)){
                return;
            }
            $dephp_23 = m('member') -> getMember($dephp_21);
            if (empty($dephp_23)){
                return;
            }
            $dephp_118 = false;
            $dephp_79 = intval($_GPC['mid']);
            if (!empty($dephp_79)){
                $dephp_118 = m('member') -> getMember($dephp_79);
            }
            $dephp_119 = !empty($dephp_118) && $dephp_118['isagent'] == 1 && $dephp_118['status'] == 1;
            if ($dephp_119){
                if ($dephp_118['openid'] != $dephp_21){
                    $dephp_120 = pdo_fetchcolumn('select count(*) from ' . tablename('manor_shop_commission_clickcount') . ' where uniacid=:uniacid and openid=:openid and from_openid=:from_openid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $dephp_21, ':from_openid' => $dephp_118['openid']));
                    if ($dephp_120 <= 0){
                        $dephp_121 = array('uniacid' => $_W['uniacid'], 'openid' => $dephp_21, 'from_openid' => $dephp_118['openid'], 'clicktime' => time());
                        pdo_insert('manor_shop_commission_clickcount', $dephp_121);
                        pdo_update('manor_shop_member', array('clickcount' => $dephp_118['clickcount'] + 1), array('uniacid' => $_W['uniacid'], 'id' => $dephp_118['id']));
                    }
                }
            }
            if ($dephp_23['isagent'] == 1){
                return;
            }
            if ($dephp_122 == 0){
                $dephp_123 = pdo_fetchcolumn('select count(*) from ' . tablename('manor_shop_member') . ' where id<>:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $dephp_23['id']));
                if ($dephp_123 <= 0){
                    pdo_update('manor_shop_member', array('isagent' => 1, 'status' => 1, 'agenttime' => time(), 'agentblack' => 0), array('uniacid' => $_W['uniacid'], 'id' => $dephp_23['id']));
                    return;
                }
            }
            $dephp_25 = time();
            $dephp_124 = intval($dephp_1['become_child']);
            if ($dephp_119 && empty($dephp_23['agentid'])){
                if ($dephp_23['id'] != $dephp_118['id']){
                    if (empty($dephp_124)){
                        if (empty($dephp_23['fixagentid'])){
                            pdo_update('manor_shop_member', array('agentid' => $dephp_118['id'], 'childtime' => $dephp_25), array('uniacid' => $_W['uniacid'], 'id' => $dephp_23['id']));
                            $this -> sendMessage($dephp_118['openid'], array('nickname' => $dephp_23['nickname'], 'childtime' => $dephp_25), TM_COMMISSION_AGENT_NEW);
                            $this -> upgradeLevelByAgent($dephp_118['id']);
                        }
                    }else{
                        pdo_update('manor_shop_member', array('inviter' => $dephp_118['id']), array('uniacid' => $_W['uniacid'], 'id' => $dephp_23['id']));
                    }
                }
            }
            $dephp_125 = intval($dephp_1['become_check']);
            if (empty($dephp_1['become'])){
                if (empty($dephp_23['agentblack'])){
                    pdo_update('manor_shop_member', array('isagent' => 1, 'status' => $dephp_125, 'agenttime' => $dephp_125 == 1 ? $dephp_25 : 0), array('uniacid' => $_W['uniacid'], 'id' => $dephp_23['id']));
                    if ($dephp_125 == 1){
                        $this -> sendMessage($dephp_21, array('nickname' => $dephp_23['nickname'], 'agenttime' => $dephp_25), TM_COMMISSION_BECOME);
                        if ($dephp_119){
                            $this -> upgradeLevelByAgent($dephp_118['id']);
                        }
                    }
                }
            }
        }
        public function checkOrderConfirm($dephp_2 = '0'){
            global $_W, $_GPC;
            if (empty($dephp_2)){
                return;
            }
            $dephp_1 = $this -> getSet();
            if (empty($dephp_1['level'])){
                return;
            }
            $dephp_78 = pdo_fetch('select id,openid,ordersn,goodsprice,agentid,paytime from ' . tablename('manor_shop_order') . ' where id=:id and status>=0 and uniacid=:uniacid limit 1', array(':id' => $dephp_2, ':uniacid' => $_W['uniacid']));
            if (empty($dephp_78)){
                return;
            }
            $dephp_21 = $dephp_78['openid'];
            $dephp_23 = m('member') -> getMember($dephp_21);
            if (empty($dephp_23)){
                return;
            }
            $dephp_124 = intval($dephp_1['become_child']);
            $dephp_118 = false;
            if (empty($dephp_124)){
                $dephp_118 = m('member') -> getMember($dephp_23['agentid']);
            }else{
                $dephp_118 = m('member') -> getMember($dephp_23['inviter']);
            }
            $dephp_119 = !empty($dephp_118) && $dephp_118['isagent'] == 1 && $dephp_118['status'] == 1;
            $dephp_25 = time();
            $dephp_124 = intval($dephp_1['become_child']);
            if ($dephp_119){
                if ($dephp_124 == 1){
                    if (empty($dephp_23['agentid']) && $dephp_23['id'] != $dephp_118['id']){
                        if (empty($dephp_23['fixagentid'])){
                            $dephp_23['agentid'] = $dephp_118['id'];
                            pdo_update('manor_shop_member', array('agentid' => $dephp_118['id'], 'childtime' => $dephp_25), array('uniacid' => $_W['uniacid'], 'id' => $dephp_23['id']));
                            $this -> sendMessage($dephp_118['openid'], array('nickname' => $dephp_23['nickname'], 'childtime' => $dephp_25), TM_COMMISSION_AGENT_NEW);
                            $this -> upgradeLevelByAgent($dephp_118['id']);
                        }
                    }
                }
            }
            $dephp_5 = $dephp_23['agentid'];
            if ($dephp_23['isagent'] == 1 && $dephp_23['status'] == 1){
                if (!empty($dephp_1['selfbuy'])){
                    $dephp_5 = $dephp_23['id'];
                }
            }
            if (!empty($dephp_5)){
                pdo_update('manor_shop_order', array('agentid' => $dephp_5), array('id' => $dephp_2));
            }
            $this -> calculate($dephp_2);
        }
        public function checkOrderPay($dephp_2 = '0'){
            global $_W, $_GPC;
            if (empty($dephp_2)){
                return;
            }
            $dephp_1 = $this -> getSet();
            if (empty($dephp_1['level'])){
                return;
            }
            $dephp_78 = pdo_fetch('select id,openid,ordersn,goodsprice,agentid,paytime from ' . tablename('manor_shop_order') . ' where id=:id and status>=1 and uniacid=:uniacid limit 1', array(':id' => $dephp_2, ':uniacid' => $_W['uniacid']));
            if (empty($dephp_78)){
                return;
            }
            $dephp_21 = $dephp_78['openid'];
            $dephp_23 = m('member') -> getMember($dephp_21);
            if (empty($dephp_23)){
                return;
            }
            $dephp_124 = intval($dephp_1['become_child']);
            $dephp_118 = false;
            if (empty($dephp_124)){
                $dephp_118 = m('member') -> getMember($dephp_23['agentid']);
            }else{
                $dephp_118 = m('member') -> getMember($dephp_23['inviter']);
            }
            $dephp_119 = !empty($dephp_118) && $dephp_118['isagent'] == 1 && $dephp_118['status'] == 1;
            $dephp_25 = time();
            $dephp_124 = intval($dephp_1['become_child']);
            if ($dephp_119){
                if ($dephp_124 == 2){
                    if (empty($dephp_23['agentid']) && $dephp_23['id'] != $dephp_118['id']){
                        if (empty($dephp_23['fixagentid'])){
                            $dephp_23['agentid'] = $dephp_118['id'];
                            pdo_update('manor_shop_member', array('agentid' => $dephp_118['id'], 'childtime' => $dephp_25), array('uniacid' => $_W['uniacid'], 'id' => $dephp_23['id']));
                            $this -> sendMessage($dephp_118['openid'], array('nickname' => $dephp_23['nickname'], 'childtime' => $dephp_25), TM_COMMISSION_AGENT_NEW);
                            $this -> upgradeLevelByAgent($dephp_118['id']);
                            if (empty($dephp_78['agentid'])){
                                $dephp_78['agentid'] = $dephp_118['id'];
                                pdo_update('manor_shop_order', array('agentid' => $dephp_118['id']), array('id' => $dephp_2));
                                $this -> calculate($dephp_2);
                            }
                        }
                    }
                }
            }
            $dephp_126 = $dephp_23['isagent'] == 1 && $dephp_23['status'] == 1;
            if (!$dephp_126){
                if (intval($dephp_1['become']) == 4 && !empty($dephp_1['become_goodsid'])){
                    $dephp_127 = pdo_fetchall('select goodsid from ' . tablename('manor_shop_order_goods') . ' where orderid=:orderid and uniacid=:uniacid  ', array(':uniacid' => $_W['uniacid'], ':orderid' => $dephp_78['id']), 'goodsid');
                    if (in_array($dephp_1['become_goodsid'], array_keys($dephp_127))){
                        if (empty($dephp_23['agentblack'])){
                            pdo_update('manor_shop_member', array('status' => 1, 'isagent' => 1, 'agenttime' => $dephp_25), array('uniacid' => $_W['uniacid'], 'id' => $dephp_23['id']));
                            $this -> sendMessage($dephp_21, array('nickname' => $dephp_23['nickname'], 'agenttime' => $dephp_25), TM_COMMISSION_BECOME);
                            if (!empty($dephp_118)){
                                $this -> upgradeLevelByAgent($dephp_118['id']);
                            }
                        }
                    }
                }else if ($dephp_1['become'] == 2 || $dephp_1['become'] == 3){
                    if (empty($dephp_1['become_order'])){
                        $dephp_25 = time();
                        if ($dephp_1['become'] == 2 || $dephp_1['become'] == 3){
                            $dephp_128 = true;
                            if (!empty($dephp_23['agentid'])){
                                $dephp_118 = m('member') -> getMember($dephp_23['agentid']);
                                if (empty($dephp_118) || $dephp_118['isagent'] != 1 || $dephp_118['status'] != 1){
                                    $dephp_128 = false;
                                }
                            }
                            if ($dephp_128){
                                $dephp_129 = false;
                                if ($dephp_1['become'] == '2'){
                                    $dephp_30 = pdo_fetchcolumn('select count(*) from ' . tablename('manor_shop_order') . ' where openid=:openid and status>=1 and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $dephp_21));
                                    $dephp_129 = $dephp_30 >= intval($dephp_1['become_ordercount']);
                                }else if ($dephp_1['become'] == '3'){
                                    $dephp_130 = pdo_fetchcolumn('select sum(og.realprice) from ' . tablename('manor_shop_order_goods') . ' og left join ' . tablename('manor_shop_order') . ' o on og.orderid=o.id  where o.openid=:openid and o.status>=1 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $dephp_21));
                                    $dephp_129 = $dephp_130 >= floatval($dephp_1['become_moneycount']);
                                }
                                if ($dephp_129){
                                    if (empty($dephp_23['agentblack'])){
                                        $dephp_125 = intval($dephp_1['become_check']);
                                        pdo_update('manor_shop_member', array('status' => $dephp_125, 'isagent' => 1, 'agenttime' => $dephp_25), array('uniacid' => $_W['uniacid'], 'id' => $dephp_23['id']));
                                        if ($dephp_125 == 1){
                                            $this -> sendMessage($dephp_21, array('nickname' => $dephp_23['nickname'], 'agenttime' => $dephp_25), TM_COMMISSION_BECOME);
                                            if ($dephp_128){
                                                $this -> upgradeLevelByAgent($dephp_118['id']);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if (!empty($dephp_23['agentid'])){
                $dephp_118 = m('member') -> getMember($dephp_23['agentid']);
                if (!empty($dephp_118) && $dephp_118['isagent'] == 1 && $dephp_118['status'] == 1){
                    if ($dephp_78['agentid'] == $dephp_118['id']){
                        $dephp_127 = pdo_fetchall('select g.id,g.title,og.total,og.price,og.realprice, og.optionname as optiontitle,g.noticeopenid,g.noticetype,og.commission1 from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join ' . tablename('manor_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.orderid=:orderid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $dephp_78['id']));
                        $dephp_6 = '';
                        $dephp_9 = $dephp_118['agentlevel'];
                        $dephp_34 = 0;
                        $dephp_131 = 0;
                        foreach ($dephp_127 as $dephp_132){
                            $dephp_6 .= "" . $dephp_132['title'] . '( ';
                            if (!empty($dephp_132['optiontitle'])){
                                $dephp_6 .= ' 规格: ' . $dephp_132['optiontitle'];
                            }
                            $dephp_6 .= ' 单价: ' . ($dephp_132['realprice'] / $dephp_132['total']) . ' 数量: ' . $dephp_132['total'] . ' 总价: ' . $dephp_132['realprice'] . '); ';
                            $dephp_59 = iunserializer($dephp_132['commission1']);
                            $dephp_34 += isset($dephp_59['level' . $dephp_9]) ? $dephp_59['level' . $dephp_9] : $dephp_59['default'];
                            $dephp_131 += $dephp_132['realprice'];
                        }
                        $this -> sendMessage($dephp_118['openid'], array('nickname' => $dephp_23['nickname'], 'ordersn' => $dephp_78['ordersn'], 'price' => $dephp_131, 'goods' => $dephp_6, 'commission' => $dephp_34, 'paytime' => $dephp_78['paytime'],), TM_COMMISSION_ORDER_PAY);
                    }
                }
            }
        }
        public function checkOrderFinish($dephp_2 = ''){
            global $_W, $_GPC;
            if (empty($dephp_2)){
                return;
            }
            $dephp_78 = pdo_fetch('select id,openid, ordersn,goodsprice,agentid,finishtime from ' . tablename('manor_shop_order') . ' where id=:id and status>=3 and uniacid=:uniacid limit 1', array(':id' => $dephp_2, ':uniacid' => $_W['uniacid']));
            if (empty($dephp_78)){
                return;
            }
            $dephp_1 = $this -> getSet();
            if (empty($dephp_1['level'])){
                return;
            }
            $dephp_21 = $dephp_78['openid'];
            $dephp_23 = m('member') -> getMember($dephp_21);
            if (empty($dephp_23)){
                return;
            }
            $dephp_25 = time();
            $dephp_126 = $dephp_23['isagent'] == 1 && $dephp_23['status'] == 1;
            if (!$dephp_126 && $dephp_1['become_order'] == 1){
                if ($dephp_1['become'] == 2 || $dephp_1['become'] == 3){
                    $dephp_128 = true;
                    if (!empty($dephp_23['agentid'])){
                        $dephp_118 = m('member') -> getMember($dephp_23['agentid']);
                        if (empty($dephp_118) || $dephp_118['isagent'] != 1 || $dephp_118['status'] != 1){
                            $dephp_128 = false;
                        }
                    }
                    if ($dephp_128){
                        $dephp_129 = false;
                        if ($dephp_1['become'] == '2'){
                            $dephp_30 = pdo_fetchcolumn('select count(*) from ' . tablename('manor_shop_order') . ' where openid=:openid and status>=3 and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $dephp_21));
                            $dephp_129 = $dephp_30 >= intval($dephp_1['become_ordercount']);
                        }else if ($dephp_1['become'] == '3'){
                            $dephp_130 = pdo_fetchcolumn('select sum(goodsprice) from ' . tablename('manor_shop_order') . ' where openid=:openid and status>=3 and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $dephp_21));
                            $dephp_129 = $dephp_130 >= floatval($dephp_1['become_moneycount']);
                        }
                        if ($dephp_129){
                            if (empty($dephp_23['agentblack'])){
                                $dephp_125 = intval($dephp_1['become_check']);
                                pdo_update('manor_shop_member', array('status' => $dephp_125, 'isagent' => 1, 'agenttime' => $dephp_25), array('uniacid' => $_W['uniacid'], 'id' => $dephp_23['id']));
                                if ($dephp_125 == 1){
                                    $this -> sendMessage($dephp_23['openid'], array('nickname' => $dephp_23['nickname'], 'agenttime' => $dephp_25), TM_COMMISSION_BECOME);
                                    if ($dephp_128){
                                        $this -> upgradeLevelByAgent($dephp_118['id']);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if (!empty($dephp_23['agentid'])){
                $dephp_118 = m('member') -> getMember($dephp_23['agentid']);
                if (!empty($dephp_118) && $dephp_118['isagent'] == 1 && $dephp_118['status'] == 1){
                    if ($dephp_78['agentid'] == $dephp_118['id']){
                        $dephp_127 = pdo_fetchall('select g.id,g.title,og.total,og.realprice,og.price,og.optionname as optiontitle,g.noticeopenid,g.noticetype,og.commission1 from ' . tablename('manor_shop_order_goods') . ' og ' . ' left join ' . tablename('manor_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.orderid=:orderid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $dephp_78['id']));
                        $dephp_6 = '';
                        $dephp_9 = $dephp_118['agentlevel'];
                        $dephp_34 = 0;
                        $dephp_131 = 0;
                        foreach ($dephp_127 as $dephp_132){
                            $dephp_6 .= "" . $dephp_132['title'] . '( ';
                            if (!empty($dephp_132['optiontitle'])){
                                $dephp_6 .= ' 规格: ' . $dephp_132['optiontitle'];
                            }
                            $dephp_6 .= ' 单价: ' . ($dephp_132['realprice'] / $dephp_132['total']) . ' 数量: ' . $dephp_132['total'] . ' 总价: ' . $dephp_132['realprice'] . '); ';
                            $dephp_59 = iunserializer($dephp_132['commission1']);
                            $dephp_34 += isset($dephp_59['level' . $dephp_9]) ? $dephp_59['level' . $dephp_9] : $dephp_59['default'];
                            $dephp_131 += $dephp_132['realprice'];
                        }
                        $this -> sendMessage($dephp_118['openid'], array('nickname' => $dephp_23['nickname'], 'ordersn' => $dephp_78['ordersn'], 'price' => $dephp_131, 'goods' => $dephp_6, 'commission' => $dephp_34, 'finishtime' => $dephp_78['finishtime'],), TM_COMMISSION_ORDER_FINISH);
                    }
                }
            }
            $this -> upgradeLevelByOrder($dephp_21);
        }
        function getShop($dephp_133){
            global $_W;
            $dephp_23 = m('member') -> getMember($dephp_133);
            $dephp_134 = pdo_fetch('select * from ' . tablename('manor_shop_commission_shop') . ' where uniacid=:uniacid and mid=:mid limit 1' , array(':uniacid' => $_W['uniacid'], ':mid' => $dephp_23['id']));
            $dephp_135 = m('common') -> getSysset(array('shop', 'share'));
            $dephp_1 = $dephp_135['shop'];
            $dephp_136 = $dephp_135['share'];
            $dephp_137 = $dephp_136['desc'];
            if (empty($dephp_137)){
                $dephp_137 = $dephp_1['description'];
            }
            if (empty($dephp_137)){
                $dephp_137 = $dephp_1['name'];
            }
            $dephp_138 = $this -> getSet();
            if (empty($dephp_134)){
                $dephp_134 = array('name' => $dephp_23['nickname'] . '的' . $dephp_138['texts']['shop'], 'logo' => $dephp_23['avatar'], 'desc' => $dephp_137, 'img' => tomedia($dephp_1['img']),);
            }else{
                if (empty($dephp_134['name'])){
                    $dephp_134['name'] = $dephp_23['nickname'] . '的' . $dephp_138['texts']['shop'];
                }
                if (empty($dephp_134['logo'])){
                    $dephp_134['logo'] = tomedia($dephp_23['avatar']);
                }
                if (empty($dephp_134['img'])){
                    $dephp_134['img'] = tomedia($dephp_1['img']);
                }
                if (empty($dephp_134['desc'])){
                    $dephp_134['desc'] = $dephp_137;
                }
            }
            return $dephp_134;
        }
        function getLevels($dephp_139 = true){
            global $_W;
            if ($dephp_139){
                return pdo_fetchall('select * from ' . tablename('manor_shop_commission_level') . ' where uniacid=:uniacid order by commission1 asc', array(':uniacid' => $_W['uniacid']));
            }else{
                return pdo_fetchall('select * from ' . tablename('manor_shop_commission_level') . ' where uniacid=:uniacid and (ordermoney>0 or commissionmoney>0) order by commission1 asc', array(':uniacid' => $_W['uniacid']));
            }
        }
        function getLevel($dephp_21){
            global $_W;
            if (empty($dephp_21)){
                return false;
            }
            $dephp_23 = m('member') -> getMember($dephp_21);
            if (empty($dephp_23['agentlevel'])){
                return false;
            }
            $dephp_9 = pdo_fetch('select * from ' . tablename('manor_shop_commission_level') . ' where uniacid=:uniacid and id=:id limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $dephp_23['agentlevel']));
            return $dephp_9;
        }
        function upgradeLevelByOrder($dephp_21){
            global $_W;
            if (empty($dephp_21)){
                return false;
            }
            $dephp_1 = $this -> getSet();
            if (empty($dephp_1['level'])){
                return false;
            }
            $dephp_133 = m('member') -> getMember($dephp_21);
            if (empty($dephp_133)){
                return;
            }
            $dephp_140 = intval($dephp_1['leveltype']);
            if ($dephp_140 == 4 || $dephp_140 == 5){
                if (!empty($dephp_133['agentnotupgrade'])){
                    return;
                }
                $dephp_141 = $this -> getLevel($dephp_133['openid']);
                if (empty($dephp_141['id'])){
                    $dephp_141 = array('levelname' => empty($dephp_1['levelname']) ? '普通等级' : $dephp_1['levelname'], 'commission1' => $dephp_1['commission1'], 'commission2' => $dephp_1['commission2'], 'commission3' => $dephp_1['commission3']);
                }
                $dephp_142 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('manor_shop_order') . ' o ' . ' left join  ' . tablename('manor_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.openid=:openid and o.status>=3 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $dephp_21));
                $dephp_31 = $dephp_142['ordermoney'];
                $dephp_30 = $dephp_142['ordercount'];
                if ($dephp_140 == 4){
                    $dephp_143 = pdo_fetch('select * from ' . tablename('manor_shop_commission_level') . " where uniacid=:uniacid  and {$dephp_31} >= ordermoney and ordermoney>0  order by ordermoney desc limit 1", array(':uniacid' => $_W['uniacid']));
                    if (empty($dephp_143)){
                        return;
                    }
                    if (!empty($dephp_141['id'])){
                        if ($dephp_141['id'] == $dephp_143['id']){
                            return;
                        }
                        if ($dephp_141['ordermoney'] > $dephp_143['ordermoney']){
                            return;
                        }
                    }
                }else if ($dephp_140 == 5){
                    $dephp_143 = pdo_fetch('select * from ' . tablename('manor_shop_commission_level') . " where uniacid=:uniacid  and {$dephp_30} >= ordercount and ordercount>0  order by ordercount desc limit 1", array(':uniacid' => $_W['uniacid']));
                    if (empty($dephp_143)){
                        return;
                    }
                    if (!empty($dephp_141['id'])){
                        if ($dephp_141['id'] == $dephp_143['id']){
                            return;
                        }
                        if ($dephp_141['ordercount'] > $dephp_143['ordercount']){
                            return;
                        }
                    }
                }
                pdo_update('manor_shop_member', array('agentlevel' => $dephp_143['id']), array('id' => $dephp_133['id']));
                $this -> sendMessage($dephp_133['openid'], array('nickname' => $dephp_133['nickname'], 'oldlevel' => $dephp_141, 'newlevel' => $dephp_143,), TM_COMMISSION_UPGRADE);
            }else if ($dephp_140 >= 0 && $dephp_140 <= 3){
                $dephp_77 = array();
                if (!empty($dephp_1['selfbuy'])){
                    $dephp_77[] = $dephp_133;
                }
                if (!empty($dephp_133['agentid'])){
                    $dephp_11 = m('member') -> getMember($dephp_133['agentid']);
                    if (!empty($dephp_11)){
                        $dephp_77[] = $dephp_11;
                        if (!empty($dephp_11['agentid']) && $dephp_11['isagent'] == 1 && $dephp_11['status'] == 1){
                            $dephp_13 = m('member') -> getMember($dephp_11['agentid']);
                            if (!empty($dephp_13) && $dephp_13['isagent'] == 1 && $dephp_13['status'] == 1){
                                $dephp_77[] = $dephp_13;
                                if (empty($dephp_1['selfbuy'])){
                                    if (!empty($dephp_13['agentid']) && $dephp_13['isagent'] == 1 && $dephp_13['status'] == 1){
                                        $dephp_15 = m('member') -> getMember($dephp_13['agentid']);
                                        if (!empty($dephp_15) && $dephp_15['isagent'] == 1 && $dephp_15['status'] == 1){
                                            $dephp_77[] = $dephp_15;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if (empty($dephp_77)){
                    return;
                }
                foreach ($dephp_77 as $dephp_144){
                    $dephp_145 = $this -> getInfo($dephp_144['id'], array('ordercount3', 'ordermoney3', 'order13money', 'order13'));
                    if (!empty($dephp_145['agentnotupgrade'])){
                        continue;
                    }
                    $dephp_141 = $this -> getLevel($dephp_144['openid']);
                    if (empty($dephp_141['id'])){
                        $dephp_141 = array('levelname' => empty($dephp_1['levelname']) ? '普通等级' : $dephp_1['levelname'], 'commission1' => $dephp_1['commission1'], 'commission2' => $dephp_1['commission2'], 'commission3' => $dephp_1['commission3']);
                    }
                    if ($dephp_140 == 0){
                        $dephp_31 = $dephp_145['ordermoney3'];
                        $dephp_143 = pdo_fetch('select * from ' . tablename('manor_shop_commission_level') . " where uniacid=:uniacid and {$dephp_31} >= ordermoney and ordermoney>0  order by ordermoney desc limit 1", array(':uniacid' => $_W['uniacid']));
                        if (empty($dephp_143)){
                            continue;
                        }
                        if (!empty($dephp_141['id'])){
                            if ($dephp_141['id'] == $dephp_143['id']){
                                continue;
                            }
                            if ($dephp_141['ordermoney'] > $dephp_143['ordermoney']){
                                continue;
                            }
                        }
                    }else if ($dephp_140 == 1){
                        $dephp_31 = $dephp_145['order13money'];
                        $dephp_143 = pdo_fetch('select * from ' . tablename('manor_shop_commission_level') . " where uniacid=:uniacid and {$dephp_31} >= ordermoney and ordermoney>0  order by ordermoney desc limit 1", array(':uniacid' => $_W['uniacid']));
                        if (empty($dephp_143)){
                            continue;
                        }
                        if (!empty($dephp_141['id'])){
                            if ($dephp_141['id'] == $dephp_143['id']){
                                continue;
                            }
                            if ($dephp_141['ordermoney'] > $dephp_143['ordermoney']){
                                continue;
                            }
                        }
                    }else if ($dephp_140 == 2){
                        $dephp_30 = $dephp_145['ordercount3'];
                        $dephp_143 = pdo_fetch('select * from ' . tablename('manor_shop_commission_level') . " where uniacid=:uniacid  and {$dephp_30} >= ordercount and ordercount>0  order by ordercount desc limit 1", array(':uniacid' => $_W['uniacid']));
                        if (empty($dephp_143)){
                            continue;
                        }
                        if (!empty($dephp_141['id'])){
                            if ($dephp_141['id'] == $dephp_143['id']){
                                continue;
                            }
                            if ($dephp_141['ordercount'] > $dephp_143['ordercount']){
                                continue;
                            }
                        }
                    }else if ($dephp_140 == 3){
                        $dephp_30 = $dephp_145['order13'];
                        $dephp_143 = pdo_fetch('select * from ' . tablename('manor_shop_commission_level') . " where uniacid=:uniacid  and {$dephp_30} >= ordercount and ordercount>0  order by ordercount desc limit 1", array(':uniacid' => $_W['uniacid']));
                        if (empty($dephp_143)){
                            continue;
                        }
                        if (!empty($dephp_141['id'])){
                            if ($dephp_141['id'] == $dephp_143['id']){
                                continue;
                            }
                            if ($dephp_141['ordercount'] > $dephp_143['ordercount']){
                                continue;
                            }
                        }
                    }
                    pdo_update('manor_shop_member', array('agentlevel' => $dephp_143['id']), array('id' => $dephp_144['id']));
                    $this -> sendMessage($dephp_144['openid'], array('nickname' => $dephp_144['nickname'], 'oldlevel' => $dephp_141, 'newlevel' => $dephp_143,), TM_COMMISSION_UPGRADE);
                }
            }
        }
        function upgradeLevelByAgent($dephp_21){
            global $_W;
            if (empty($dephp_21)){
                return false;
            }
            $dephp_1 = $this -> getSet();
            if (empty($dephp_1['level'])){
                return false;
            }
            $dephp_133 = m('member') -> getMember($dephp_21);
            if (empty($dephp_133)){
                return;
            }
            $dephp_140 = intval($dephp_1['leveltype']);
            if ($dephp_140 < 6 || $dephp_140 > 9){
                return;
            }
            $dephp_145 = $this -> getInfo($dephp_133['id'], array());
            if ($dephp_140 == 6 || $dephp_140 == 8){
                $dephp_77 = array($dephp_133);
                if (!empty($dephp_133['agentid'])){
                    $dephp_11 = m('member') -> getMember($dephp_133['agentid']);
                    if (!empty($dephp_11)){
                        $dephp_77[] = $dephp_11;
                        if (!empty($dephp_11['agentid']) && $dephp_11['isagent'] == 1 && $dephp_11['status'] == 1){
                            $dephp_13 = m('member') -> getMember($dephp_11['agentid']);
                            if (!empty($dephp_13) && $dephp_13['isagent'] == 1 && $dephp_13['status'] == 1){
                                $dephp_77[] = $dephp_13;
                            }
                        }
                    }
                }
                if (empty($dephp_77)){
                    return;
                }
                foreach ($dephp_77 as $dephp_144){
                    $dephp_145 = $this -> getInfo($dephp_144['id'], array());
                    if (!empty($dephp_145['agentnotupgrade'])){
                        continue;
                    }
                    $dephp_141 = $this -> getLevel($dephp_144['openid']);
                    if (empty($dephp_141['id'])){
                        $dephp_141 = array('levelname' => empty($dephp_1['levelname']) ? '普通等级' : $dephp_1['levelname'], 'commission1' => $dephp_1['commission1'], 'commission2' => $dephp_1['commission2'], 'commission3' => $dephp_1['commission3']);
                    }
                    if ($dephp_140 == 6){
                        $dephp_146 = pdo_fetchall('select id from ' . tablename('manor_shop_member') . ' where agentid=:agentid and uniacid=:uniacid ', array(':agentid' => $dephp_133['id'], ':uniacid' => $_W['uniacid']), 'id');
                        $dephp_147 += count($dephp_146);
                        if (!empty($dephp_146)){
                            $dephp_148 = pdo_fetchall('select id from ' . tablename('manor_shop_member') . ' where agentid in( ' . implode(',', array_keys($dephp_146)) . ') and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']), 'id');
                            $dephp_147 += count($dephp_148);
                            if (!empty($dephp_148)){
                                $dephp_149 = pdo_fetchall('select id from ' . tablename('manor_shop_member') . ' where agentid in( ' . implode(',', array_keys($dephp_148)) . ') and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']), 'id');
                                $dephp_147 += count($dephp_149);
                            }
                        }
                        $dephp_143 = pdo_fetch('select * from ' . tablename('manor_shop_commission_level') . " where uniacid=:uniacid  and {$dephp_147} >= downcount and downcount>0  order by downcount desc limit 1", array(':uniacid' => $_W['uniacid']));
                    }else if ($dephp_140 == 8){
                        $dephp_147 = $dephp_145['level1'] + $dephp_145['level2'] + $dephp_145['level3'];
                        $dephp_143 = pdo_fetch('select * from ' . tablename('manor_shop_commission_level') . " where uniacid=:uniacid  and {$dephp_147} >= downcount and downcount>0  order by downcount desc limit 1", array(':uniacid' => $_W['uniacid']));
                    }
                    if (empty($dephp_143)){
                        continue;
                    }
                    if ($dephp_143['id'] == $dephp_141['id']){
                        continue;
                    }
                    if (!empty($dephp_141['id'])){
                        if ($dephp_141['downcount'] > $dephp_143['downcount']){
                            continue;
                        }
                    }
                    pdo_update('manor_shop_member', array('agentlevel' => $dephp_143['id']), array('id' => $dephp_144['id']));
                    $this -> sendMessage($dephp_144['openid'], array('nickname' => $dephp_144['nickname'], 'oldlevel' => $dephp_141, 'newlevel' => $dephp_143,), TM_COMMISSION_UPGRADE);
                }
            }else{
                if (!empty($dephp_133['agentnotupgrade'])){
                    return;
                }
                $dephp_141 = $this -> getLevel($dephp_133['openid']);
                if (empty($dephp_141['id'])){
                    $dephp_141 = array('levelname' => empty($dephp_1['levelname']) ? '普通等级' : $dephp_1['levelname'], 'commission1' => $dephp_1['commission1'], 'commission2' => $dephp_1['commission2'], 'commission3' => $dephp_1['commission3']);
                }
                if ($dephp_140 == 7){
                    $dephp_147 = pdo_fetchcolumn('select count(*) from ' . tablename('manor_shop_member') . ' where agentid=:agentid and uniacid=:uniacid ', array(':agentid' => $dephp_133['id'], ':uniacid' => $_W['uniacid']));
                    $dephp_143 = pdo_fetch('select * from ' . tablename('manor_shop_commission_level') . " where uniacid=:uniacid  and {$dephp_147} >= downcount and downcount>0  order by downcount desc limit 1", array(':uniacid' => $_W['uniacid']));
                }else if ($dephp_140 == 9){
                    $dephp_147 = $dephp_145['level1'];
                    $dephp_143 = pdo_fetch('select * from ' . tablename('manor_shop_commission_level') . " where uniacid=:uniacid  and {$dephp_147} >= downcount and downcount>0  order by downcount desc limit 1", array(':uniacid' => $_W['uniacid']));
                }
                if (empty($dephp_143)){
                    return;
                }
                if ($dephp_143['id'] == $dephp_141['id']){
                    return;
                }
                if (!empty($dephp_141['id'])){
                    if ($dephp_141['downcount'] > $dephp_143['downcount']){
                        return;
                    }
                }
                pdo_update('manor_shop_member', array('agentlevel' => $dephp_143['id']), array('id' => $dephp_133['id']));
                $this -> sendMessage($dephp_133['openid'], array('nickname' => $dephp_133['nickname'], 'oldlevel' => $dephp_141, 'newlevel' => $dephp_143,), TM_COMMISSION_UPGRADE);
            }
        }
        function upgradeLevelByCommissionOK($dephp_21){
            global $_W;
            if (empty($dephp_21)){
                return false;
            }
            $dephp_1 = $this -> getSet();
            if (empty($dephp_1['level'])){
                return false;
            }
            $dephp_133 = m('member') -> getMember($dephp_21);
            if (empty($dephp_133)){
                return;
            }
            $dephp_140 = intval($dephp_1['leveltype']);
            if ($dephp_140 != 10){
                return;
            }
            if (!empty($dephp_133['agentnotupgrade'])){
                return;
            }
            $dephp_141 = $this -> getLevel($dephp_133['openid']);
            if (empty($dephp_141['id'])){
                $dephp_141 = array('levelname' => empty($dephp_1['levelname']) ? '普通等级' : $dephp_1['levelname'], 'commission1' => $dephp_1['commission1'], 'commission2' => $dephp_1['commission2'], 'commission3' => $dephp_1['commission3']);
            }
            $dephp_145 = $this -> getInfo($dephp_133['id'], array('pay'));
            $dephp_150 = $dephp_145['commission_pay'];
            $dephp_143 = pdo_fetch('select * from ' . tablename('manor_shop_commission_level') . " where uniacid=:uniacid  and {$dephp_150} >= commissionmoney and commissionmoney>0  order by commissionmoney desc limit 1", array(':uniacid' => $_W['uniacid']));
            if (empty($dephp_143)){
                return;
            }
            if ($dephp_141['id'] == $dephp_143['id']){
                return;
            }
            if (!empty($dephp_141['id'])){
                if ($dephp_141['commissionmoney'] > $dephp_143['commissionmoney']){
                    return;
                }
            }
            pdo_update('manor_shop_member', array('agentlevel' => $dephp_143['id']), array('id' => $dephp_133['id']));
            $this -> sendMessage($dephp_133['openid'], array('nickname' => $dephp_133['nickname'], 'oldlevel' => $dephp_141, 'newlevel' => $dephp_143,), TM_COMMISSION_UPGRADE);
        }
        function sendMessage($dephp_21 = '', $dephp_151 = array(), $dephp_152 = ''){
            global $_W, $_GPC;
            $dephp_1 = $this -> getSet();
            $dephp_153 = $dephp_1['tm'];
            $dephp_154 = $dephp_153['templateid'];
            $dephp_23 = m('member') -> getMember($dephp_21);
            $dephp_155 = unserialize($dephp_23['noticeset']);
            if (!is_array($dephp_155)){
                $dephp_155 = array();
            }
            if ($dephp_152 == TM_COMMISSION_AGENT_NEW && !empty($dephp_153['commission_agent_new']) && empty($dephp_155['commission_agent_new'])){
                $dephp_156 = $dephp_153['commission_agent_new'];
                $dephp_156 = str_replace('[昵称]', $dephp_151['nickname'], $dephp_156);
                $dephp_156 = str_replace('[时间]', date('Y-m-d H:i:s', $dephp_151['childtime']), $dephp_156);
                $dephp_157 = array('keyword1' => array('value' => !empty($dephp_153['commission_agent_newtitle']) ? $dephp_153['commission_agent_newtitle'] : '新增下线通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $dephp_156, 'color' => '#73a68d'));
                if (!empty($dephp_154)){
                    m('message') -> sendTplNotice($dephp_21, $dephp_154, $dephp_157);
                }else{
                    m('message') -> sendCustomNotice($dephp_21, $dephp_157);
                }
            }else if ($dephp_152 == TM_COMMISSION_ORDER_PAY && !empty($dephp_153['commission_order_pay']) && empty($dephp_155['commission_order_pay'])){
                $dephp_156 = $dephp_153['commission_order_pay'];
                $dephp_156 = str_replace('[昵称]', $dephp_151['nickname'], $dephp_156);
                $dephp_156 = str_replace('[时间]', date('Y-m-d H:i:s', $dephp_151['paytime']), $dephp_156);
                $dephp_156 = str_replace('[订单编号]', $dephp_151['ordersn'], $dephp_156);
                $dephp_156 = str_replace('[订单金额]', $dephp_151['price'], $dephp_156);
                $dephp_156 = str_replace('[佣金金额]', $dephp_151['commission'], $dephp_156);
                $dephp_156 = str_replace('[商品详情]', $dephp_151['goods'], $dephp_156);
                $dephp_157 = array('keyword1' => array('value' => !empty($dephp_153['commission_order_paytitle']) ? $dephp_153['commission_order_paytitle'] : '下线付款通知'), 'keyword2' => array('value' => $dephp_156));
                if (!empty($dephp_154)){
                    m('message') -> sendTplNotice($dephp_21, $dephp_154, $dephp_157);
                }else{
                    m('message') -> sendCustomNotice($dephp_21, $dephp_157);
                }
            }else if ($dephp_152 == TM_COMMISSION_ORDER_FINISH && !empty($dephp_153['commission_order_finish']) && empty($dephp_155['commission_order_finish'])){
                $dephp_156 = $dephp_153['commission_order_finish'];
                $dephp_156 = str_replace('[昵称]', $dephp_151['nickname'], $dephp_156);
                $dephp_156 = str_replace('[时间]', date('Y-m-d H:i:s', $dephp_151['finishtime']), $dephp_156);
                $dephp_156 = str_replace('[订单编号]', $dephp_151['ordersn'], $dephp_156);
                $dephp_156 = str_replace('[订单金额]', $dephp_151['price'], $dephp_156);
                $dephp_156 = str_replace('[佣金金额]', $dephp_151['commission'], $dephp_156);
                $dephp_156 = str_replace('[商品详情]', $dephp_151['goods'], $dephp_156);
                $dephp_157 = array('keyword1' => array('value' => !empty($dephp_153['commission_order_finishtitle']) ? $dephp_153['commission_order_finishtitle'] : '下线确认收货通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $dephp_156, 'color' => '#73a68d'));
                if (!empty($dephp_154)){
                    m('message') -> sendTplNotice($dephp_21, $dephp_154, $dephp_157);
                }else{
                    m('message') -> sendCustomNotice($dephp_21, $dephp_157);
                }
            }else if ($dephp_152 == TM_COMMISSION_APPLY && !empty($dephp_153['commission_apply']) && empty($dephp_155['commission_apply'])){
                $dephp_156 = $dephp_153['commission_apply'];
                $dephp_156 = str_replace('[昵称]', $dephp_23['nickname'], $dephp_156);
                $dephp_156 = str_replace('[时间]', date('Y-m-d H:i:s', time()), $dephp_156);
                $dephp_156 = str_replace('[金额]', $dephp_151['commission'], $dephp_156);
                $dephp_156 = str_replace('[提现方式]', $dephp_151['type'], $dephp_156);
                $dephp_157 = array('keyword1' => array('value' => !empty($dephp_153['commission_applytitle']) ? $dephp_153['commission_applytitle'] : '提现申请提交成功', 'color' => '#73a68d'), 'keyword2' => array('value' => $dephp_156, 'color' => '#73a68d'));
                if (!empty($dephp_154)){
                    m('message') -> sendTplNotice($dephp_21, $dephp_154, $dephp_157);
                }else{
                    m('message') -> sendCustomNotice($dephp_21, $dephp_157);
                }
            }else if ($dephp_152 == TM_COMMISSION_CHECK && !empty($dephp_153['commission_check']) && empty($dephp_155['commission_check'])){
                $dephp_156 = $dephp_153['commission_check'];
                $dephp_156 = str_replace('[昵称]', $dephp_23['nickname'], $dephp_156);
                $dephp_156 = str_replace('[时间]', date('Y-m-d H:i:s', time()), $dephp_156);
                $dephp_156 = str_replace('[金额]', $dephp_151['commission'], $dephp_156);
                $dephp_156 = str_replace('[提现方式]', $dephp_151['type'], $dephp_156);
                $dephp_157 = array('keyword1' => array('value' => !empty($dephp_153['commission_checktitle']) ? $dephp_153['commission_checktitle'] : '提现申请审核处理完成', 'color' => '#73a68d'), 'keyword2' => array('value' => $dephp_156, 'color' => '#73a68d'));
                if (!empty($dephp_154)){
                    m('message') -> sendTplNotice($dephp_21, $dephp_154, $dephp_157);
                }else{
                    m('message') -> sendCustomNotice($dephp_21, $dephp_157);
                }
            }else if ($dephp_152 == TM_COMMISSION_PAY && !empty($dephp_153['commission_pay']) && empty($dephp_155['commission_pay'])){
                $dephp_156 = $dephp_153['commission_pay'];
                $dephp_156 = str_replace('[昵称]', $dephp_23['nickname'], $dephp_156);
                $dephp_156 = str_replace('[时间]', date('Y-m-d H:i:s', time()), $dephp_156);
                $dephp_156 = str_replace('[金额]', $dephp_151['commission'], $dephp_156);
                $dephp_156 = str_replace('[提现方式]', $dephp_151['type'], $dephp_156);
                $dephp_157 = array('keyword1' => array('value' => !empty($dephp_153['commission_paytitle']) ? $dephp_153['commission_paytitle'] : '佣金打款通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $dephp_156, 'color' => '#73a68d'));
                if (!empty($dephp_154)){
                    m('message') -> sendTplNotice($dephp_21, $dephp_154, $dephp_157);
                }else{
                    m('message') -> sendCustomNotice($dephp_21, $dephp_157);
                }
            }else if ($dephp_152 == TM_COMMISSION_UPGRADE && !empty($dephp_153['commission_upgrade']) && empty($dephp_155['commission_upgrade'])){
                $dephp_156 = $dephp_153['commission_upgrade'];
                $dephp_156 = str_replace('[昵称]', $dephp_23['nickname'], $dephp_156);
                $dephp_156 = str_replace('[时间]', date('Y-m-d H:i:s', time()), $dephp_156);
                $dephp_156 = str_replace('[旧等级]', $dephp_151['oldlevel']['levelname'], $dephp_156);
                $dephp_156 = str_replace('[旧一级分销比例]', $dephp_151['oldlevel']['commission1'] . '%', $dephp_156);
                $dephp_156 = str_replace('[旧二级分销比例]', $dephp_151['oldlevel']['commission2'] . '%', $dephp_156);
                $dephp_156 = str_replace('[旧三级分销比例]', $dephp_151['oldlevel']['commission3'] . '%', $dephp_156);
                $dephp_156 = str_replace('[新等级]', $dephp_151['newlevel']['levelname'], $dephp_156);
                $dephp_156 = str_replace('[新一级分销比例]', $dephp_151['newlevel']['commission1'] . '%', $dephp_156);
                $dephp_156 = str_replace('[新二级分销比例]', $dephp_151['newlevel']['commission2'] . '%', $dephp_156);
                $dephp_156 = str_replace('[新三级分销比例]', $dephp_151['newlevel']['commission3'] . '%', $dephp_156);
                $dephp_157 = array('keyword1' => array('value' => !empty($dephp_153['commission_upgradetitle']) ? $dephp_153['commission_upgradetitle'] : '分销等级升级通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $dephp_156, 'color' => '#73a68d'));
                if (!empty($dephp_154)){
                    m('message') -> sendTplNotice($dephp_21, $dephp_154, $dephp_157);
                }else{
                    m('message') -> sendCustomNotice($dephp_21, $dephp_157);
                }
            }else if ($dephp_152 == TM_COMMISSION_BECOME && !empty($dephp_153['commission_become']) && empty($dephp_155['commission_become'])){
                $dephp_156 = $dephp_153['commission_become'];
                $dephp_156 = str_replace('[昵称]', $dephp_151['nickname'], $dephp_156);
                $dephp_156 = str_replace('[时间]', date('Y-m-d H:i:s', $dephp_151['agenttime']), $dephp_156);
                $dephp_157 = array('keyword1' => array('value' => !empty($dephp_153['commission_becometitle']) ? $dephp_153['commission_becometitle'] : '成为分销商通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $dephp_156, 'color' => '#73a68d'));
                if (!empty($dephp_154)){
                    m('message') -> sendTplNotice($dephp_21, $dephp_154, $dephp_157);
                }else{
                    m('message') -> sendCustomNotice($dephp_21, $dephp_157);
                }
            }
        }
        function perms(){
            return array('commission' => array('text' => $this -> getName(), 'isplugin' => true, 'child' => array('cover' => array('text' => '入口设置'), 'agent' => array('text' => '分销商', 'view' => '浏览', 'check' => '审核-log', 'edit' => '修改-log', 'agentblack' => '黑名单操作-log', 'delete' => '删除-log', 'user' => '查看下线', 'order' => '查看推广订单(还需有订单权限)', 'changeagent' => '设置分销商'), 'level' => array('text' => '分销商等级', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log'), 'apply' => array('text' => '佣金审核', 'view1' => '浏览待审核', 'view2' => '浏览已审核', 'view3' => '浏览已打款', 'view_1' => '浏览无效', 'export1' => '导出待审核-log', 'export2' => '导出已审核-log', 'export3' => '导出已打款-log', 'export_1' => '导出无效-log', 'check' => '审核-log', 'pay' => '打款-log', 'cancel' => '重新审核-log'), 'notice' => array('text' => '通知设置-log'), 'increase' => array('text' => '分销商趋势图'), 'changecommission' => array('text' => '修改佣金-log'), 'set' => array('text' => '基础设置-log'))));
        }
    }
}
