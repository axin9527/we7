<?php
    /**
     * 操作类名称: PhpStorm.
     * 作者名称: alan
     * 创建时间: 2016/9/28 14:51
     */
    global $_W,$_GPC;
    $op = trim($_GPC['op']) ? trim($_GPC['op']) : '';
    $fansgroup = pdo_getall('alan_qrcode_fans_group', array('uniacid'=>$_W['uniacid']), array('id', 'name'));
    $qrcode = pdo_getall('alan_qrcode', array('uniacid'=>$_W['uniacid']), array('id', 'name'));
    if(!$op) {
        $_W['page']['title'] = '粉丝管理';
        $keywords = trim($_GPC['keyword']);
        $where  = array(
            'uniacid'=>$_W['uniacid']
        );
        //昵称
        if($keywords) {
            $where['nickname like'] = '%'.$keywords.'%';
        }
        //分组
        if($_GPC['group_id']) {
            $where['group_id'] = intval($_GPC['group_id']);
        }
        if($_GPC['qrcode_id']) {
            $where['qrcode_id'] = intval($_GPC['qrcode_id']);
        }
        if($_GPC['follow']) {
            $where['follow'] = intval($_GPC['follow']);
        }
        $page = !empty($_GPC['page'])?$_GPC['page']:1;
        $size = 10;
        if($_GPC['act'] == 'export') {
            $_list = pdo_getall('alan_qrcode_fans', $where, array('id', 'nickname', 'sex','group_id', 'qrcode_id', 'follow', 'subscribe_time', 'cancel_sub_time'));
            $columns = array(
                array('title' => '编号', 'field' => 'id', 'width' => 12),
                array('title' => '昵称', 'field' => 'nickname', 'width' => 12),
                array('title' => '性别', 'field' => 'sex', 'width' => 12),
                array('title' => '粉丝组', 'field' => 'group_id', 'width' => 12),
                array('title' => '来源', 'field' => 'qrcode_id', 'width' => 12),
                array('title' => '是否关注', 'field' => 'follow', 'width' => 12),
                array('title' => '关注时间', 'field' => 'subscribe_time', 'width' => 24),
                array('title' => '取关时间', 'field' => 'cancel_sub_time', 'width' => 24),
            );
            if($_list) {
                $export_list = array();
                foreach ($_list as $k => $item) {
                    if ($item['sex'] == 1) {
                        $_list[$k]['sex'] = '男';
                    } else if ($item['sex'] == 2) {
                        $_list[$k]['sex'] = '女';
                    } else {
                        $_list[$k]['sex'] = '未知';
                    }
                    $g = pdo_get('alan_qrcode_fans_group', array('id'=>$item['group_id']), array('name'));
                    $r = pdo_get('alan_qrcode', array('id'=>$item['qrcode_id']), array('name'));
                    $_list[$k]['qrcode_id'] = $r['name'] ? $r['name'] : '未知';
                    $_list[$k]['group_id'] = $g['name'] ? $g['name'] : '未知';
                    if ($item['follow'] == 1) {
                        $_list[$k]['follow'] = '已关注';
                    } else if ($item['follow'] == 2) {
                        $_list[$k]['follow'] = '取消关注';
                    } else {
                        $_list[$k]['follow'] = '自然关注';
                    }

                    $_list[$k]['subscribe_time'] = date('Y-m-d H:i:s', $item['subscribe_time']);
                    $_list[$k]['cancel_sub_time'] = date('Y-m-d H:i:s', $item['cancel_sub_time']);
                    $export_list[] = $_list[$k];
                }
                export($_list, array('title' => '二维码邀请记录数据-' . date('Y-m-d-H-i', time()), 'columns' => $columns));
                die;
            }

        }
        $total = count(pdo_getall('alan_qrcode_fans',$where, array('id')));
        $list = pdo_getslice('alan_qrcode_fans', $where,  "LIMIT " . ($page - 1) * $size . " , " . $size, $total);
        if($list) {
            foreach($list as $k=>$v) {
                $g = pdo_get('alan_qrcode_fans_group', array('id'=>$v['group_id']), array('name'));
                $r = pdo_get('alan_qrcode', array('id'=>$v['qrcode_id']), array('name'));
                $list[$k]['qr_name'] = $r['name'];
                $list[$k]['group_name'] = $g['name'];
            }
        }
        $pager = pagination($total, $page, $size);
    }
    include alan_template('qrcode_fans/index');


function export($dephp_3, $dephp_4 = array()){
    if (PHP_SAPI == 'cli'){
        die('This example should only be run from a Web Browser');
    }
    require_once IA_ROOT . '/framework/library/phpexcel/PHPExcel.php';
    $dephp_5 = new PHPExcel();
    $dephp_5 -> getProperties() -> setCreator('二维码邀请记录') -> setLastModifiedBy('二维码邀请记录') -> setTitle('Office 2007 XLSX Test Document') -> setSubject('Office 2007 XLSX Test Document') -> setDescription('Test document for Office 2007 XLSX, generated using PHP classes.') -> setKeywords('office 2007 openxml php') -> setCategory('report file');
    $dephp_6 = $dephp_5 -> setActiveSheetIndex(0);
    $dephp_7 = 1;
    foreach ($dephp_4['columns'] as $dephp_0 => $dephp_8){
        $dephp_6 -> setCellValue(column($dephp_0, $dephp_7), $dephp_8['title']);
        if (!empty($dephp_8['width'])){
            $dephp_6 -> getColumnDimension(column_str($dephp_0)) -> setWidth($dephp_8['width']);
        }
    }
    $dephp_7++;
    $dephp_9 = count($dephp_4['columns']);;
    foreach ($dephp_3 as $dephp_10){
        for ($dephp_11 = 0; $dephp_11 < $dephp_9; $dephp_11++){
            $dephp_12 = isset($dephp_10[$dephp_4['columns'][$dephp_11]['field']])?$dephp_10[$dephp_4['columns'][$dephp_11]['field']]:'';
            $dephp_6 -> setCellValue(column($dephp_11, $dephp_7), $dephp_12);
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
function column_str($dephp_0){
    $dephp_1 = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ', 'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ', 'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ', 'EA', 'EB', 'EC', 'ED', 'EE', 'EF', 'EG', 'EH', 'EI', 'EJ', 'EK', 'EL', 'EM', 'EN', 'EO', 'EP', 'EQ', 'ER', 'ES', 'ET', 'EU', 'EV', 'EW', 'EX', 'EY', 'EZ');
    return $dephp_1[$dephp_0];
}
function column($dephp_0, $dephp_2 = 1){
    return column_str($dephp_0) . $dephp_2;
}
function great_rand(){
    $str = '1234567890abcdefghijklmnopqrstuvwxyz';
    $t1 = "";
    for($i=0;$i<30;$i++){
        $j=rand(0,35);
        $t1 .= $str[$j];
    }
    return $t1;
}

function multi_array_sort($multi_array,$sort_key,$sort=SORT_ASC){
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