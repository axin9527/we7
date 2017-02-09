<?php
    /**
     * 操作类名称: PhpStorm.
     * 作者名称: alan
     * 创建时间: 2016/9/28 14:51
     */
    global $_W,$_GPC;
    $operation = $_GPC['op'] ? $_GPC['op'] : 'display';
    $keywords = trim($_GPC['op']);
    $where  = array(
        'uniacid'=>$_W['uniacid']
    );
    $_group = pdo_getall('alan_qrcode_fans_group', $where);
    if($_GPC['group_id']) {
        $where['id'] = intval($_GPC['group_id']);
    }
    $group = pdo_getall('alan_qrcode_fans_group', $where);
    $qrcode = pdo_getall('alan_qrcode', array('uniacid'=>$_W['uniacid']), array('id', 'name'));
    $starttime = $_GPC['time']['start'] ? $_GPC['time']['start'] : strtotime(date('Y').'-'.date('m').'-01');
    if($_GPC['searchtime'] != 1) {
        $starttime = strtotime(date('Y').'-'.date('m').'-01');
    }
    $endtime = $_GPC['time']['end'] ? $_GPC['time']['end'] : time();
    if($_GPC['searchtime'] != 1) {
        $endtime = time();
    }
    $s_time = date('Ymd', $starttime);
    $e_time = date('Ymd', $endtime);
    if($operation == 'display') {
        $_W['page']['title'] = '场景二维码数据统计';
        $where = 'uniacid=:uniacid ';
        $where_arr = array(':uniacid'=>$_W['uniacid']);
        if($_GPC['searchtime'] == 1) {
            $where = ' and date >= :s_time and date <= :e_time';
            $where_arr[':s_time'] = $s_time;
            $where_arr[':e_time'] = $e_time;
        }
        if($_GPC['group_id']) {
            $where .= ' and group_id = :group_id';
            $where_arr[':group_id'] = intval($_GPC['group_id']);
        }
        if($_GPC['qrcode_id']) {
            $where .= ' and qrcode_id = :qrcode_id';
            $where_arr[':qrcode_id'] = intval($_GPC['qrcode_id']);
        }
        $sum = pdo_fetch('select sum(scan_count) as scan_count,sum(sub_num) as sub_num,sum(cancel_num) as cancel_num from '.tablename('alan_qrcode_stat').' where '.$where, $where_arr);
        extract($sum);
        $dayarr = array();
        for($i=$s_time; $i <= $e_time; $i++) {
            $dayarr[] = intval($i);
        }
        if($group) {
            foreach($group as $item) {
                if($dayarr) {
                    foreach($dayarr as $day) {
                        $map[':uniacid'] = $_W['uniacid'];
                        $map[':date'] = $day;
                        $map[':group_id'] = $item['id'];
                        $map_str = 'uniacid=:uniacid and date =:date and group_id = :group_id';
                        if($_GPC['qrcode_id']) {
                            $map_str .= ' and qrcode_id = :qrcode_id';
                            $map[':qrcode_id'] = intval($_GPC['qrcode_id']);
                        }
                        $sum_num = pdo_fetch('select sum(sub_num) as sub_num from '.tablename('alan_qrcode_stat').' where '.$map_str, $map);
                        $group_arr[$item['id']][$day] = $sum_num['sub_num']?$sum_num['sub_num']:0;
                    }
                }
                $fansgroup[$item['id']] = $item;
            }
        } else {
            $fansgroup = array(

            );
            foreach($dayarr as $day) {
                $group_arr[0][$day] = array(0);
            }
        }
    }
    elseif($operation == 'top') {
        $_W['page']['title'] = '场景二维码分组排行';
        $list = pdo_fetchall('select * from '.tablename('alan_qrcode').' where uniacid=:uniacid order by subnum desc, cancelnum desc', array(':uniacid'=>$_W['uniacid']));
        include alan_template('qrcode_stat/top');
        die;
    }
    include alan_template('qrcode_stat/index');