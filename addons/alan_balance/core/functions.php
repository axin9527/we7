<?php
    /**
     * 操作类名称: PhpStorm.
     * 作者名称: alan
     * 创建时间: 2016/10/21 14:21
     */
    function qrcode_logo($path, $_wx_url){
        require_once IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
        $errorCorrectionLevel = 'L';//设置容错级别
        $matrixPointSize = 3;//生成图片大小
        if($matrixPointSize > 10) {
            $matrixPointSize = 20;
        }
        QRcode::png($_wx_url, $path, $errorCorrectionLevel, $matrixPointSize, 1);//生成二维码图片 无logo
        return '..'.str_replace(IA_ROOT, '', $path);
    }

    function generate_code($length = 4) {
        $str = str_replace('.', '', microtime(true));
        if(strlen($str) > $length) {
            return substr($str, 0, -($length-strlen($str)));
        }
        $has = $length - strlen($str);
        $min = 1;
        $max = 9;
        for($i=0;$i < $has-1;$i++) {
            $min .=0;
            $max .=9;
        }
        return $str.mt_rand(intval($min), intval($max));
    }
