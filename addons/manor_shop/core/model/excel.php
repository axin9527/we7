<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
class Ewei_DShop_Excel{
    protected function column_str($dephp_0){
        $dephp_1 = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ', 'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ', 'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ', 'EA', 'EB', 'EC', 'ED', 'EE', 'EF', 'EG', 'EH', 'EI', 'EJ', 'EK', 'EL', 'EM', 'EN', 'EO', 'EP', 'EQ', 'ER', 'ES', 'ET', 'EU', 'EV', 'EW', 'EX', 'EY', 'EZ');
        return $dephp_1[$dephp_0];
    }
    protected function column($dephp_0, $dephp_2 = 1){
        return $this -> column_str($dephp_0) . $dephp_2;
    }
    function export($dephp_3, $dephp_4 = array()){
        if (PHP_SAPI == 'cli'){
            die('This example should only be run from a Web Browser');
        }
        require_once IA_ROOT . '/framework/library/phpexcel/PHPExcel.php';
        $dephp_5 = new PHPExcel();
        $dephp_5 -> getProperties() -> setCreator('人人商城') -> setLastModifiedBy('人人商城') -> setTitle('Office 2007 XLSX Test Document') -> setSubject('Office 2007 XLSX Test Document') -> setDescription('Test document for Office 2007 XLSX, generated using PHP classes.') -> setKeywords('office 2007 openxml php') -> setCategory('report file');
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
    public function import($dephp_15){
        global $_W;
        require_once IA_ROOT . '/framework/library/phpexcel/PHPExcel.php';
        require_once IA_ROOT . '/framework/library/phpexcel/PHPExcel/IOFactory.php';
        require_once IA_ROOT . '/framework/library/phpexcel/PHPExcel/Reader/Excel5.php';
        $dephp_16 = IA_ROOT . '/addons/manor_shop/data/tmp/';
        if(!is_dir($dephp_16)){
            load() -> func('file');
            mkdirs($dephp_16, '0777');
        }
        $dephp_13 = $_FILES[$dephp_15]['name'];
        $dephp_17 = $_FILES[$dephp_15]['tmp_name'];
        if(empty($dephp_17)){
            message('请选择要上传的Excel文件!', '', 'error');
        }
        $dephp_18 = strtolower(pathinfo($dephp_13, PATHINFO_EXTENSION));
        if($dephp_18 != 'xlsx' && $dephp_18 != 'xls'){
            message('请上传 xls 或 xlsx 格式的Excel文件!', '', 'error');
        }
        $dephp_19 = time() . $_W['uniacid'] . '.' . $dephp_18;
        $dephp_20 = $dephp_16 . $dephp_19;
        $dephp_21 = move_uploaded_file($dephp_17, $dephp_20);
        if(!$dephp_21){
            message('上传Excel 文件失败, 请重新上传!', '', 'error');
        }
        $dephp_22 = PHPExcel_IOFactory :: createReader($dephp_18 == 'xls'?'Excel5':'Excel2007');
        $dephp_5 = $dephp_22 -> load($dephp_20);
        $dephp_6 = $dephp_5 -> getActiveSheet();
        $dephp_23 = $dephp_6 -> getHighestRow();
        $dephp_24 = $dephp_6 -> getHighestColumn();
        $dephp_25 = PHPExcel_Cell :: columnIndexFromString($dephp_24);
        $dephp_26 = array();
        for ($dephp_10 = 2;$dephp_10 <= $dephp_23;$dephp_10++){
            $dephp_27 = array();
            for ($dephp_28 = 0;$dephp_28 < $dephp_25;$dephp_28++){
                $dephp_27[] = $dephp_6 -> getCellByColumnAndRow($dephp_28, $dephp_10) -> getValue();
            }
            $dephp_26[] = $dephp_27;
        }
        return $dephp_26;
    }
}
