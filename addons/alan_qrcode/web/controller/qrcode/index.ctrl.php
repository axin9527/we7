<?php
    /**
     * 操作类名称: 二维码创建.
     * 作者名称: alan
     * 创建时间: 2016/9/28 14:51
     */
    global $_W,$_GPC;
    $op = trim($_GPC['op']) ? trim($_GPC['op']) : '';
    $fansgroup = pdo_getall('alan_qrcode_fans_group', array('uniacid'=>$_W['uniacid']), array('id', 'name'));
    if(!$op) {
        $_W['page']['title'] = '场景二维码列表';
        $keywords = trim($_GPC['keyword']);
        $where  = array(
            'uniacid'=>$_W['uniacid']
        );
        if($keywords) {
          $where['name like'] = '%'.$keywords.'%';
        }
        if($_GPC['group_id']) {
            $where['group_id'] = intval($_GPC['group_id']);
        }
        $page = !empty($_GPC['page'])?$_GPC['page']:1;
        $size = 10;
        $total = count(pdo_getall('alan_qrcode',$where, array('id')));
        $list = pdo_getslice('alan_qrcode', $where,  "LIMIT " . ($page - 1) * $size . " , " . $size, $total);
        if($list) {
            foreach($list as $k=>$v) {
                $r = pdo_get('alan_qrcode_fans_group', array('id'=>$v['group_id']), array('name'));
                $list[$k]['group_name'] = $r['name'];
            }
        }
        $pager = pagination($total, $page, $size);
    }
    if($op == 'create') {
        if(checksubmit()){
            load()->func('file');
            $path = IA_ROOT.'/attachment/'.ALAN_NAME.'/'.date('Y').'/'.date('m').'/'.date('d').'/'.time().'.png';
            if(!is_dir(IA_ROOT.'/attachment/'.ALAN_NAME.'/'.date('Y').'/'.date('m').'/'.date('d'))) {
                @mkdirs(IA_ROOT.'/attachment/'.ALAN_NAME.'/'.date('Y').'/'.date('m').'/'.date('d'), '0777');
            }
            if(!$_GPC['group_id']) {
                message('请先创建粉丝组', $this->createWebUrl('qrcode_fans_group', array('op'=>'edit')), 'error');
            }
            $id = intval($_GPC['id']);
            $uniacid = $_W['uniacid'];
            load()->func('communication');
            $acid = intval($_W['acid']);
            $insert_data = array(
                'name'=>$_GPC['name'],
                'title'=>$_GPC['name'],
                'keyword'=>$_GPC['name'],
                'group_id'=>$_GPC['group_id'],
            );
            $config = $this->module['config'];
            if(!$config) {
                message('请先配置信息', 'referer', 'error');
            }
            $insert_data['model'] = $config['mode'];
            $info = pdo_fetch( 'SELECT * FROM '.tablename('alan_qrcode').' WHERE uniacid = :uniacid AND id = :id' , array(':uniacid' => $uniacid,':id'=>$id));
            if( $info ){
                pdo_update('alan_qrcode', $insert_data, array('id'=>$id));
            } else {
                $insert_data['uniacid']	=	$uniacid;
                $insert_data['create_time']	=	TIMESTAMP;
                $uniacccount = WeAccount::create($acid);
                $barcode = array(
                    'expire_seconds' => '',
                    'action_name' => '',
                    'action_info' => array(
                        'scene' => array(),
                    ),
                );
                $insert_data['scan_key'] = getRandChar(11);
                $barcode['action_info']['scene']['scene_str'] = $insert_data['scan_key'];
                $barcode['action_name'] = 'QR_LIMIT_STR_SCENE';
                $result = $uniacccount->barCodeCreateFixed($barcode);
                if(!is_error($result)) {
                    $_wx_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$result['ticket'];
                    $insert_data['ticket'] = $result['ticket'];
                    $insert_data['wx_url'] = $_wx_url;
                    $insert_data['expire'] = $result['expire_seconds'];
                    $cont = file_get_contents($_wx_url);
                    if($config['mode'] == 1) {
                        $config['logoqrwidth'] = 5;
                        $save_path = qrcode_logo($config, $path,$result['url'], true);
                    } else {
                        $config['logoqrwidth'] = 20;
                        $save_path = qrcode_logo($config,$path, $result['url']);
                    }

                    if(is_error($result)) {
                        message("生成失败");
                    }
                } else {
                    message("公众平台返回接口错误. <br />错误代码为: {$result['errorcode']} <br />错误信息为: {$result['message']}");
                }
                $insert_data['path'] = $save_path;
                $insert_data['wx_data'] = json_encode($result);
                pdo_insert('alan_qrcode', $insert_data);
                message('保存成功', 'refresh');
            }
        }
        include alan_template('qrcode/create');
        die;
    } elseif($op == 'post') {
        //二维码编辑
        $id = intval($_GPC['id']);
        $row = pdo_get('alan_qrcode', array('id'=>$id));
        if(checksubmit()) {
            $name = trim($_GPC['name']);
            pdo_update('alan_qrcode', array('name'=>$name), array('id'=>$id));
            message('保存成功', 'refresh');
        }
        include alan_template('qrcode/create');
    } elseif($op == 'del') {
        //二维码删除
        load()->func('file');
        $id = intval($_GPC['id']);
        $row = pdo_get('alan_qrcode', array('id'=>$id));
        if($row['source'] && file_exists($row['source'])) {
            file_delete($row['source']);
        }
        if($row['path'] && file_exists($row['path'])) {
            file_delete($row['path']);
        }
        pdo_delete('alan_qrcode', array('id'=>$id));
        if($row['rid']) {
            $rid = $row['rid'];
            load()->model('reply');
            $reply = reply_single($rid);
            if(empty($reply) || $reply['uniacid'] != $_W['uniacid']) {
                //message('抱歉，您操作的规则不在存或是已经被删除！', url('platform/reply', array('m' => $m)), 'error');
            } else {
                if (pdo_delete('rule', array('id' => $rid))) {
                    pdo_delete('rule_keyword', array('rid' => $rid));
                    pdo_delete('stat_rule', array('rid' => $rid));
                    pdo_delete('stat_keyword', array('rid' => $rid));
                }
            }

        }
        message('保存成功', web_url('qrcode'), 'success');
    } elseif($op == 'view_code') {
        $id = intval($_GPC['id']);
        $row = pdo_get('alan_qrcode', array('id'=>$id));
        if(file_exists($row['path'])) {
            echo '<img src="'.$row['path'].'">';
            die;
        } else {
            message('文件不存在,请删除重试', web_url('qrcode'), 'error');
        }
    }/* else if($op = 'test'){
        require IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
        $file = ALAN_DATA.'test.png';
        QRcode::png('12121', $file, $errorCorrectionLevel, 4, 2);//生成二维码图片 无logo

        die;
    }*/
    include alan_template('qrcode/index');