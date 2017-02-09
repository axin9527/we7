<?php
/**
 * 扫码送余额模块微站定义
 *
 * @author alan51
 * @url 
 */
defined('IN_IA') or exit('Access Denied');
include IA_ROOT.'/addons/alan_balance/core/functions.php';
class Alan_balanceModuleSite extends WeModuleSite {

	public function doWebBalancetimes() {
		//这个操作被定义用来呈现 管理中心导航菜单
        $this->doWebtimes();
	}
	public function doWebtimes() {
		//这个操作被定义用来呈现 管理中心导航菜单
        global $_GPC,$_W;
        $uniacid = $_W['uniacid'];
        $op = $_GPC['op'] ? $_GPC['op'] : 'display';
        $acid = intval($_W['acid']);
        $where = array('uniacid'=>$uniacid);
        if($op == 'display') {
            if($_GPC['keyword']) {
                $where['name like'] = '%'.trim($_GPC['keyword']).'%';
            }
            $list = pdo_getall('alan_scan_balance_time', $where);
            include $this->template('times');
        } else if($op == 'create') {
            $id = $_GPC['id'];
            if($id) {
                $row = pdo_get('alan_scan_balance_time', array('id'=>$id));
            }
            if(checksubmit('submit')) {
                if($id) {
                    pdo_update('alan_scan_balance_time', array('name'=>trim($_GPC['name'])), array('id'=>$id));
                    message('修改成功', 'referer', 'success');
                }
                load()->func('file');
                if(!is_dir(IA_ROOT.'/attachment/alan_balance/'.date('Y').'/'.date('m').'/'.date('d'))) {
                    mkdirs(IA_ROOT.'/attachment/alan_balance/'.date('Y').'/'.date('m').'/'.date('d'));
                }
                $times_data = array(
                    'uniacid'=>$uniacid,
                    'name'=>trim($_GPC['name']),
                    'total'=>intval($_GPC['total']),
                    'price'=>intval($_GPC['price']),
                    'effective_time'=>TIMESTAMP + 604800,
                    'create_time'=>TIMESTAMP,
                    'valid_num'=>intval($_GPC['total'])
                );
                pdo_insert('alan_scan_balance_time', $times_data);
                $times_id = pdo_insertid();
                if(!$times_data['name']) {
                    message('请输入名称', '', 'error');
                }
                if(!is_numeric($times_data['total']) || $times_data['total'] <= 0) {
                    message('请输入正确的数量', '', 'error');
                }
                $uniacccount = WeAccount::create($acid);
                $fail = false;
                for($i=0;$i<$times_data['total'];$i++) {
                    $insert_data = array(
                        'uniacid'=>$uniacid,
                        'title'=>$times_data['name'],
                        'rand_str'=>generate_code(32),
                        'model'=>1,
                        'expire'=>TIMESTAMP + 604800,
                        'create_time'=>TIMESTAMP,
                        'status'=>1,
                        'time_id'=>$times_id,
                        'time_name'=>$times_data['name']
                    );
                    $barcode = array(
                        'expire_seconds' => 604800,
                        'action_name' => 'QR_SCENE',
                        'action_info' => array(
                            'scene' => array(
                                'scene_str'=>'',
                                'scene_id'=>$insert_data['rand_str']
                            ),
                        ),
                    );
                    $path = IA_ROOT.'/attachment/alan_balance/'.date('Y').'/'.date('m').'/'.date('d').'/'.(time()+$i).'.png';
                    $result = $uniacccount->barCodeCreateFixed($barcode);
                    if(!is_error($result)) {
                        $_wx_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$result['ticket'];
                        $insert_data['ticket'] = $result['ticket'];
                        $insert_data['wx_url'] = $_wx_url;
                        $save_path = qrcode_logo($path, $result['url']);
                    } else {
                        $fail = true;
                        $save_path = '';
                    }
                    $insert_data['path'] = $save_path;
                    $insert_data['wx_data'] = json_encode($result);
                    if($fail) {
                        pdo_delete('alan_scan_balance_time', array('id'=>$insert_data['time_id']));
                        message('生成失败', '', 'error');
                        die;
                    }
                    pdo_insert('alan_scan_balance', $insert_data);
                }
                message('生成成功', 'referer', 'success');
            }
            include $this->template('create');
        } else if($op == 'del'){
            $id = intval($_GPC['id']);
            if(!$id) {
                message('参数错误', '', 'error');
            }
            load()->func('file');
            pdo_delete('alan_scan_balance_time', array('id'=>$id));
            $d = pdo_getall('alan_scan_balance', array('time_id'=>$id, 'uniacid'=>$uniacid));
            if($d) {
                foreach($d as $item) {
                    file_delete(realpath($item['path']));
                }
            }
            pdo_delete('alan_scan_balance', array('time_id'=>$id, 'uniacid'=>$uniacid));
            message('删除成功', 'referer', 'success');
        } elseif($op == 'download') {
            $id = intval($_GPC['id']);
            if(!$id) {
                message('参数错误', '', 'error');
            }
            $image = array();
            $ims = pdo_getall('alan_scan_balance', array('time_id'=>$id, 'uniacid'=>$uniacid, 'status'=>1));
            if($ims) {
                foreach($ims as $im) {
                    $image[] = array(
                        'image_src'=>$im['path'],
                        'image_name'=>$im['time_name'].'-'.$im['id'].'.png'
                    );
                }
                $dfile =  tempnam('/tmp', 'tmp');//产生一个临时文件，用于缓存下载文件
                $zip = new zipfile();
                $filename = 'image.zip'; //下载的默认文件名
                foreach($image as $v){
                    $zip->add_file(file_get_contents($v['image_src']),  $v['image_name']);
                    // 添加打包的图片，第一个参数是图片内容，第二个参数是压缩包里面的显示的名称, 可包含路径
                    // 或是想打包整个目录 用 $zip->add_path($image_path);
                }
                //----------------------
                $zip->output($dfile);
                // 下载文件
                ob_clean();
                header('Pragma: public');
                header('Last-Modified:'.gmdate('D, d M Y H:i:s') . 'GMT');
                header('Cache-Control:no-store, no-cache, must-revalidate');
                header('Cache-Control:pre-check=0, post-check=0, max-age=0');
                header('Content-Transfer-Encoding:binary');
                header('Content-Encoding:none');
                header('Content-type:multipart/form-data');
                header('Content-Disposition:attachment; filename="'.$filename.'"'); //设置下载的默认文件名
                header('Content-length:'. filesize($dfile));
                $fp = fopen($dfile, 'r');
                while(connection_status() == 0 && $buf = @fread($fp, 8192)){
                    echo $buf;
                }
                fclose($fp);
                @unlink($dfile);
                @flush();
                @ob_flush();
                exit();
            }
        }

	}
	public function doWebBalancerecord() {
		//这个操作被定义用来呈现 管理中心导航菜单
        $this->doWebrecord();
	}

	public function doWebrecord(){
	    global $_GPC, $_W;
        $op = $_GPC['op'] ? $_GPC['op'] : 'display';
        $where = array('uniacid'=>$_W['uniacid']);
        if($op  == 'display') {
            $times = pdo_getall('alan_scan_balance_time', $where);
            if($_GPC['keyword']) {
                $where['time_name like'] = '%'.trim($_GPC['keyword']).'%';
            }
            if($_GPC['time_id']) {
                $where['time_id'] = intval($_GPC['time_id']);
            }
            if($_GPC['status'] && in_array($_GPC['status'], array(1, 2, 3))) {
                $where['status'] = intval($_GPC['status']);
            }
            $list = pdo_getall('alan_scan_balance', $where);
        } elseif($op == 'del') {
            $id = intval($_GPC['id']);
            if(!$id) {
                message('参数错误', '', 'error');
            }
            $row = pdo_get('alan_scan_balance', array('id'=>$id));
            if($row){
                if($row['status'] == 1) {
                    pdo_update('alan_scan_balance_time', array('valid_num -='=>1), array('id'=>$row['time_id']));
                }
                if($row['status'] == 2) {
                    pdo_update('alan_scan_balance_time', array('expired_num -='=>1), array('id'=>$row['time_id']));
                }
                if($row['status'] == 3) {
                    pdo_update('alan_scan_balance_time', array('failure_num -='=>1), array('id'=>$row['time_id']));
                }
                load()->func('file');
                file_delete($row['path']);
                pdo_delete('alan_scan_balance', array('id'=>$id));
                message('删除成功', 'referer', 'success');
            } else {
                message('参数错误', '', 'error');
            }
        }
        include $this->template('record');

    }

}





    class zipfile {
        var $datasec = array ();
        var $ctrl_dir = array ();
        var $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00";
        var $old_offset = 0;

        function unix2_dostime($unixtime = 0){
            $timearray = ($unixtime == 0) ? getdate () : getdate($unixtime);
            if ($timearray ['year'] < 1980){
                $timearray ['year'] = 1980;
                $timearray ['mon'] = 1;
                $timearray ['mday'] = 1;
                $timearray ['hours'] = 0;
                $timearray ['minutes'] = 0;
                $timearray ['seconds'] = 0;
            }
            return (($timearray ['year'] - 1980) << 25) | ($timearray ['mon'] << 21) | ($timearray ['mday'] << 16) | ($timearray ['hours'] << 11) | ($timearray ['minutes'] << 5) | ($timearray ['seconds'] >> 1);
        }
        function add_file($data, $name, $time = 0){
            $name = str_replace('\\', '/', $name);

            $dtime = dechex($this->unix2_dostime($time));
            $hexdtime = '\x' . $dtime [6] . $dtime [7] . '\x' . $dtime [4] . $dtime [5] . '\x' . $dtime [2] . $dtime [3] . '\x' . $dtime [0] . $dtime [1];
            eval('$hexdtime = "' . $hexdtime . '";');

            $fr = "\x50\x4b\x03\x04";
            $fr .= "\x14\x00";
            $fr .= "\x00\x00";
            $fr .= "\x08\x00";
            $fr .= $hexdtime;

            $unc_len = strlen($data);
            $crc = crc32($data);
            $zdata = gzcompress($data);
            $zdata = substr(substr($zdata, 0, strlen($zdata)- 4), 2);
            $c_len = strlen($zdata);
            $fr .= pack('V', $crc);
            $fr .= pack('V', $c_len);
            $fr .= pack('V', $unc_len);
            $fr .= pack('v', strlen($name));
            $fr .= pack('v', 0);
            $fr .= $name;

            $fr .= $zdata;
            $fr .= pack('V', $crc);
            $fr .= pack('V', $c_len);
            $fr .= pack('V', $unc_len);

            $this->datasec [] = $fr;

            $cdrec = "\x50\x4b\x01\x02";
            $cdrec .= "\x00\x00";
            $cdrec .= "\x14\x00";
            $cdrec .= "\x00\x00";
            $cdrec .= "\x08\x00";
            $cdrec .= $hexdtime;
            $cdrec .= pack('V', $crc);
            $cdrec .= pack('V', $c_len);
            $cdrec .= pack('V', $unc_len);
            $cdrec .= pack('v', strlen($name));
            $cdrec .= pack('v', 0);
            $cdrec .= pack('v', 0);
            $cdrec .= pack('v', 0);
            $cdrec .= pack('v', 0);
            $cdrec .= pack('V', 32);

            $cdrec .= pack('V', $this->old_offset);
            $this->old_offset += strlen($fr);

            $cdrec .= $name;

            $this->ctrl_dir[] = $cdrec;
        }
        function add_path($path, $l = 0){
            $d = @opendir($path);
            $l = $l > 0 ? $l : strlen($path) + 1;
            while($v = @readdir($d)){
                if($v == '.' || $v == '..'){
                    continue;
                }
                $v = $path . '/' . $v;
                if(is_dir($v)){
                    $this->add_path($v, $l);
                } else {
                    $this->add_file(file_get_contents($v), substr($v, $l));
                }
            }
        }
        function file(){
            $data = implode('', $this->datasec);
            $ctrldir = implode('', $this->ctrl_dir);
            return $data . $ctrldir . $this->eof_ctrl_dir . pack('v', sizeof($this->ctrl_dir)) . pack('v', sizeof($this->ctrl_dir)) . pack('V', strlen($ctrldir)) . pack('V', strlen($data)) . "\x00\x00";
        }

        function add_files($files){
            foreach($files as $file){
                if (is_file($file)){
                    $data = implode("", file($file));
                    $this->add_file($data, $file);
                }
            }
        }
        function output($file){
            $fp = fopen($file, "w");
            fwrite($fp, $this->file ());
            fclose($fp);
        }
    }