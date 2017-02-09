<?php
/**
 * 投票圆梦计划模块微站定义
 *
 * @author alan51
 * @url 
 */
defined('IN_IA') or exit('Access Denied');
require IA_ROOT. '/addons/alan_ticket/core/common/defines.php';
require TICKET_CORE . 'class/wlloader.class.php';
wl_load()->func('global');
class Alan_ticketModuleSite extends WeModuleSite {
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
}