<?php
    /**
     * 操作类名称: PhpStorm.
     * 作者名称: alan
     * 创建时间: 2016/9/28 14:51
     */
    global $_W,$_GPC;
    $operation = $_GPC['op'] ? $_GPC['op'] : 'display';
    if($operation == 'display') {
        $_W['page']['title'] = '粉丝分组列表';
        $keywords = trim($_GPC['op']);
        $where  = array(
            'uniacid'=>$_W['uniacid']
        );
        $list = pdo_getall('alan_qrcode_fans_group', $where);
    }
    load()->classs('account');
    $account = WeAccount::create($_W['acid'] ? $_W['acid'] : $_W['uniaccid']);
    if($operation == 'edit') {
        $_W['page']['title'] = '粉丝分组创建';
        if(checksubmit()) {
            if($_GPC['group_id'] && $_GPC['mc_group_id']) {
                if($_GPC['name']) {
                    $data = array('id' => $_GPC['mc_group_id'], 'name' => trim($_GPC['name']));
                    $state = $account->editFansGroupname($data);
                    if(is_error($state)) {
                        message($state['message'], web_url('qrcode_fans_group'), 'error');
                    }
                }
            } else {
                $value = trim($_GPC['name']);
                $state = $account->addFansGroup($value);
                if(is_error($state)) {
                    message($state['message'], web_url('qrcode_fans_group'), 'error');
                }
            }
            $qrcode_group = array(
                'uniacid'=>$_W['uniacid'],
                'name'=>trim($_GPC['name']),
                'group_desc'=>htmlspecialchars($_GPC['group_desc']),
                'mc_group_id'=>$state['group']['id']
            );
            if(!$_GPC['group_id']) {
                $qrcode_group['create_time'] = TIMESTAMP;
                pdo_insert('alan_qrcode_fans_group', $qrcode_group);
            } else {
                pdo_update('alan_qrcode_fans_group', $qrcode_group, array('id'=>$_GPC['group_id']));
            }
            message('保存分组名称成功', web_url('qrcode_fans_group/index'), 'success');
        }
        if($_GPC['id']) {
            $item = pdo_get('alan_qrcode_fans_group', array('uniacid'=>$_W['uniacid'], 'id'=>intval($_GPC['id'])));
        }
    } elseif($operation == 'del') {
        //删除分组
        $groups = pdo_get('alan_qrcode_fans_group', array('id'=>intval($_GPC['id'])));
        if($_GPC['mc_group_id'] > 0) {
            $groupid = intval($_GPC['mc_group_id']);
            $account = WeAccount::create($_W['acid'] ? $_W['acid'] : $_W['uniaccid']);
            $groups = $account->delFansGroup($groupid);
            if(!is_error($groups)) {
                $r = pdo_update('mc_mapping_fans', array('groupid' => 0), array('acid' => $_W['acid'], 'groupid' => $groupid));
            }else {
                $r = FALSE;
            }
        }
        pdo_delete('alan_qrcode_fans_group', array('id' => intval($_GPC['id'])));
        message($groups, '', 'ajax');die;
    }
    if($operation == 'sync') {
        load()->model('mc');
        $groups = mc_fans_groups(true);
        if(empty($groups)) {
            message($groups['message'], '', 'error');
        }
        if($groups) {
            foreach($groups as $group) {
                if($res = pdo_get('alan_qrcode_fans_group', array('name'=>$group['name'], 'uniacid'=>$_W['uniacid']))){
                    $up_data = array(
                        'count'=>$group['count'],
                        'mc_group_id'=>$group['mc_group_id']
                    );
                    pdo_update('alan_qrcode_fans_group', $up_data, array('id'=>$res['id']));
                } else {
                    $inser_data = array(
                        'uniacid'=>$_W['uniacid'],
                        'name'=>$group['name'],
                        'group_desc'=>$group['name'],
                        'mc_group_id'=>$group['id'],
                        'count'=>$group['count'],
                        'scan_count'=>0,
                        'create_time'=>TIMESTAMP
                    );
                    pdo_insert('alan_qrcode_fans_group', $inser_data);
                }
            }
        }
        message('更新成功', web_url('qrcode_fans_group'), 'success');
        die;
    }
    include alan_template('qrcode_fans_group/index');