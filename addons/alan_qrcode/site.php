<?php
/**
 * 场景二维码模块微站定义
 *
 * @author 诗意的边缘
 * @url http://www.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
require IA_ROOT. '/addons/alan_qrcode/core/common/defines.php';
require ALAN_CORE . 'class/alanloader.class.php';
    alanload()->func('global');
class Alan_qrcodeModuleSite extends WeModuleSite {

    public function __call($name, $arguments) {
        global $_W;
        $isWeb = stripos($name, 'doWeb') === 0;
        $isMobile = stripos($name, 'doMobile') === 0;
        if($isWeb || $isMobile) {
            $dir = IA_ROOT . '/addons/' . $this->modulename . '/';
            if($isWeb) {
                $dir .= 'web/';
                $controller = strtolower(substr($name, 5));
            }
            if($isMobile) {
                $dir .= 'app/';
                $controller = strtolower(substr($name, 8));
            }
            $file = $dir . 'index.php';
            if(file_exists($file)) {
                require $file;
                exit;
            }
        }
        trigger_error("访问的方法 {$name} 不存在.", E_USER_WARNING);
        return null;
    }

	/*public function doWebQrcode() {
		//这个操作被定义用来呈现 规则列表
        var_dump(1);

	}
	public function doWebQrcode_fans() {
		//这个操作被定义用来呈现 规则列表
        var_dump(333);
	}*/

}