<?php
/**
 * 唐代金融发红包模块微站定义
 *
 * @author 诗意的边缘
 * @url http://s.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Td_redenvelopesModuleSite extends WeModuleSite {

	public function doWebIndex() {
		//这个操作被定义用来呈现 管理中心导航菜单
        global $_W,$_GPC;
        $where = array(
            ':uniacid'=>$_W['uniacid']
        );

        $condition = '';
        if (!empty($_GPC['keyword'])){
            $_GPC['keyword'] = trim($_GPC['keyword']);
            $condition .= ' and ( nickname like :keyword or code like :keyword or message like :keyword)';
            $where[':keyword'] = "%{$_GPC['keyword']}%";
        }

        if (!empty($_GPC['searchtime'])){
            $starttime = strtotime($_GPC['time']['start']);
            $endtime = strtotime($_GPC['time']['end']);
            $condition .= " AND createtime >= :starttime AND createtime <= :endtime ";
            $where[':starttime'] = $starttime;
            $where[':endtime'] = $endtime;
        }
        if($_GPC['status']) {
            $condition .= ' and status='.intval($_GPC['status']);
        }
        if (empty($starttime) || empty($endtime)){
            $starttime = strtotime('-1 month');
            $endtime = time();
        }
        if($_GPC['pp'] == 'export') {
            $list = pdo_fetchall('select id,nickname,code,status,message,createtime,updatetime,times,money,mobile from '.tablename('td_redenvelopes_record').' where uniacid=:uniacid  '.$condition.' order by updatetime desc,createtime desc', $where);
            $columns = array(
                array('title' => '序号', 'field' => 'id', 'width' => 12),
                array('title' => '微信昵称', 'field' => 'nickname', 'width' => 12),
                array('title' => '领奖码', 'field' => 'code', 'width' => 12),
                array('title' => '状态', 'field' => 'status', 'width' => 12),
                array('title' => '备注', 'field' => 'message', 'width' => 12),
                array('title' => '领取时间', 'field' => 'createtime', 'width' => 24),
                array('title' => '重新领取时间', 'field' => 'updatetime', 'width' => 24),
                array('title' => '单个红包尝试领取数', 'field' => 'times', 'width' => 24),
                array('title' => '金额', 'field' => 'money', 'width' => 24),
                array('title' => '手机号', 'field' => 'mobile', 'width' => 24),
            );
            if($list) {
                $export_list = array();
                foreach ($list as $k=>$item) {
                    if($item['status'] == 1) {
                        $list[$k]['status'] = '领取成功';
                    } else if($item['status'] == 2){
                        $list[$k]['status'] = '记录成功';
                    } else {
                        $list[$k]['status'] = '领取失败';
                    }
                    $list[$k]['createtime'] = date('Y-m-d H:i:s', $item['createtime']);
                    $list[$k]['updatetime'] = date('Y-m-d H:i:s', $item['updatetime']);
                    $export_list[] = $list[$k];
                }
                $this-> export($list, array('title' => '发放红包数据-' . date('Y-m-d-H-i', time()), 'columns' => $columns));die;
            } else {
                message('暂无数据', '', 'info');
            }

        }
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $sql = "SELECT COUNT(*) FROM " . tablename('td_redenvelopes_record') . " where uniacid=:uniacid  $condition";
        $total = pdo_fetchcolumn($sql, $where);
        $list = pdo_fetchall('select * from '.tablename('td_redenvelopes_record').' where uniacid=:uniacid '.$condition.' order by createtime desc,updatetime desc limit ' . ($pindex - 1) * $psize . ',' . $psize, $where);
        $pager = pagination($total, $pindex, $psize);
        $send_amount = pdo_fetchcolumn('select sum(money) as total_amount from '.tablename('td_redenvelopes_record').' where uniacid=:uniacid and status = 1', array(':uniacid'=>$_W['uniacid']));
        $search_amount = pdo_fetchcolumn('select sum(money) as total_amount from '.tablename('td_redenvelopes_record').' where uniacid=:uniacid '.$condition, $where);
        include $this->template('index');
	}

    public function doWebSend()
    {
        global $_W,$_GPC;
        $id = $_GPC['id'];
        if(!$id) {
            echo json_encode(array('status'=>1,'result'=>'参数错误'));die;
        }
        $log = pdo_get('td_redenvelopes_record', array('id'=>$id, 'uniacid'=>$_W['uniacid']));
        if($log && $log != 1) {
            //发送
           $res = $this->send($log['money'], $log['open_id']);
            if($res['return_code'] == 'SUCCESS') {
                $arr = array(
                    'updatetime'=>TIMESTAMP,
                    'times +='=>1,
                    'message'=>'管理员手动发放',
                    'status'=>1
                );
                pdo_update('td_redenvelopes_record' , $arr, array('id'=>$id, 'uniacid'=>$_W['uniacid']));
                echo json_encode(array('status'=>1, 'result'=>'发送成功'));die;
            } else {
                echo json_encode(array('status'=>-1,'result'=>$log['return_msg']));die;
            }

        } else {
            echo json_encode(array('status'=>-1,'result'=>'参数错误'));die;
        }
	}

    public function doWebFans()
    {
        global $_W,$_GPC;
        $where = array(
            ':uniacid'=>$_W['uniacid']
        );
        $list = pdo_fetchall('select id,avstar,nickname,code,status,message,createtime,updatetime,times,money,open_id from '.tablename('td_redenvelopes_record').' where uniacid=:uniacid and status>=1 and money >0  order by updatetime desc,createtime desc', $where);
        $fans = array();
        $f = array();
        if($list) {
            foreach ($list as $item) {
                if(in_array($item['open_id'], $f)) {
                    $fans[$item['open_id']]['total'] += $item['money'];
                    $fans[$item['open_id']]['times'] += 1;
                    if($item['status'] == 1) {
                        $code = '<b style="color:blue;">(已领)</b>'.$item['code'].'<br/>';
                    } else {
                        $code = '<b style="color:red;">(待发)</b>'.$item['code'].'<br/>';
                    }
                    $fans[$item['open_id']]['code'] .= $code;
                } else {
                    $f[] = $item['open_id'];
                    if($item['status'] == 1) {
                        $code = '<b style="color:blue;">(已领)</b>'.$item['code'].'<br/>';
                    } else {
                        $code = '<b style="color:red;">(待发)</b>'.$item['code'].'<br/>';
                    }
                    $fans[$item['open_id']] = array(
                        'nickname'=>$item['nickname'],
                        'code'=>$code,
                        'times'=>1,
                        'createtime'=>$item['createtime'],
                        'avstar'=>$item['avstar'],
                        'total'=>$item['money']
                    );
                }
            }
        }
        $fans = $this->multi_array_sort($fans, 'total', SORT_DESC);
        include $this->template('fans');
	}

	protected function send($money, $re_openid){
        global $_W;
        $money = $money * 100;
        include_once('hongbao/WxHongBaoHelper.php');
        $commonUtil = new CommonUtil();
        $wxHongBaoHelper = new WxHongBaoHelper();
        $settings = pdo_fetchcolumn("SELECT settings FROM ".tablename('uni_account_modules')." WHERE module = :module AND uniacid = :uniacid", array(':module' => 'td_redenvelopes', ':uniacid' => $_W['uniacid']));
        $config = unserialize($settings);
        $wxHongBaoHelper->setParameter("nonce_str", $this->great_rand());//随机字符串，丌长于 32 位
        $wxHongBaoHelper->setParameter("mch_billno", $config['app_mchid'].date('YmdHis').rand(1000, 9999));//订单号
        $wxHongBaoHelper->setParameter("mch_id", $config['app_mchid']);//商户号
        $wxHongBaoHelper->setParameter("wxappid", $config['app_id']);
        $wxHongBaoHelper->setParameter("nick_name", '唐盛鲜果');//提供方名称
        $wxHongBaoHelper->setParameter("send_name", '唐盛鲜果');//红包发送者名称
        $wxHongBaoHelper->setParameter("re_openid", $re_openid);//相对于医脉互通的openid
        $wxHongBaoHelper->setParameter("total_amount", $money);//付款金额，单位分
        $wxHongBaoHelper->setParameter("min_value", $money);//最小红包金额，单位分
        $wxHongBaoHelper->setParameter("max_value", $money);//最大红包金额，单位分
        $wxHongBaoHelper->setParameter("total_num", 1);//红包収放总人数
        $wxHongBaoHelper->setParameter("wishing", '恭喜您，获得唐盛鲜果红包');//红包祝福诧
        $wxHongBaoHelper->setParameter("client_ip", '127.0.0.1');//调用接口的机器 Ip 地址
        $wxHongBaoHelper->setParameter("act_name", '唐代金融超市');//活劢名称
        $wxHongBaoHelper->setParameter("remark", '快来领！');//备注信息
        $postXml = $wxHongBaoHelper->create_hongbao_xml();
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        $responseXml = $wxHongBaoHelper->curl_post_ssl($url, $postXml, 30, array(), $_W['uniacid']);
        $responseObj = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $res_obj = (array)$responseObj;
        $res_obj['money'] = $money/100;
        return $res_obj;
    }

    public function export($dephp_3, $dephp_4 = array()){
        if (PHP_SAPI == 'cli'){
            die('This example should only be run from a Web Browser');
        }
        require_once IA_ROOT . '/framework/library/phpexcel/PHPExcel.php';
        $dephp_5 = new PHPExcel();
        $dephp_5 -> getProperties() -> setCreator('唐代红包') -> setLastModifiedBy('唐代红包') -> setTitle('Office 2007 XLSX Test Document') -> setSubject('Office 2007 XLSX Test Document') -> setDescription('Test document for Office 2007 XLSX, generated using PHP classes.') -> setKeywords('office 2007 openxml php') -> setCategory('report file');
        $dephp_6 = $dephp_5 -> setActiveSheetIndex(0);
        $dephp_7 = 1;
        foreach ($dephp_4['columns'] as $dephp_0 => $dephp_8){
            $dephp_6 -> setCellValue($this -> column($dephp_0, $dephp_7), $dephp_8['title']);
            if (!empty($dephp_8['width'])){
                $dephp_6 -> getColumnDimension($this -> column_str($dephp_0)) -> setWidth($dephp_8['width']);
            }
        }
        $dephp_7++;
        $dephp_9 = count($dephp_4['columns']);;
        foreach ($dephp_3 as $dephp_10){
            for ($dephp_11 = 0; $dephp_11 < $dephp_9; $dephp_11++){
                $dephp_12 = isset($dephp_10[$dephp_4['columns'][$dephp_11]['field']])?$dephp_10[$dephp_4['columns'][$dephp_11]['field']]:'';
                $dephp_6 -> setCellValue($this -> column($dephp_11, $dephp_7), $dephp_12);
            }
            $dephp_7++;
        }
        $dephp_5 -> getActiveSheet() -> setTitle($dephp_4['title']);
        $dephp_13 = urlencode($dephp_4['title'] . '-' . date('Y-m-d H:i', time()));
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment;filename="' . $dephp_13 . '.xls"');
        header('Cache-Control: max-age=0');
        $dephp_14 = PHPExcel_IOFactory :: createWriter($dephp_5, 'Excel5');
        $dephp_14 -> save('php://output');
        exit;
    }

    protected function column_str($dephp_0){
        $dephp_1 = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ', 'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ', 'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ', 'EA', 'EB', 'EC', 'ED', 'EE', 'EF', 'EG', 'EH', 'EI', 'EJ', 'EK', 'EL', 'EM', 'EN', 'EO', 'EP', 'EQ', 'ER', 'ES', 'ET', 'EU', 'EV', 'EW', 'EX', 'EY', 'EZ');
        return $dephp_1[$dephp_0];
    }
    protected function column($dephp_0, $dephp_2 = 1){
        return $this -> column_str($dephp_0) . $dephp_2;
    }
    public function great_rand(){
        $str = '1234567890abcdefghijklmnopqrstuvwxyz';
        $t1 = "";
        for($i=0;$i<30;$i++){
            $j=rand(0,35);
            $t1 .= $str[$j];
        }
        return $t1;
    }

    public function multi_array_sort($multi_array,$sort_key,$sort=SORT_ASC){
        if(is_array($multi_array)){
            foreach ($multi_array as $row_array){
                if(is_array($row_array)){
                    $key_array[] = $row_array[$sort_key];
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }
        array_multisort($key_array,$sort,$multi_array);
        return $multi_array;
    }

}