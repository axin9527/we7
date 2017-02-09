<?php
//微信商城
if (!defined('IN_IA')) {
    exit('Access Denied');
}
define('SHOP_DEBUG', false);//false
!defined('SHOP_PATH') && define('SHOP_PATH', IA_ROOT . '/addons/tangshow/');
!defined('SHOP_CORE') && define('SHOP_CORE', SHOP_PATH . 'core/');
!defined('SHOP_PLUGIN') && define('SHOP_PLUGIN', SHOP_PATH . 'plugin/');
!defined('SHOP_INC') && define('SHOP_INC', SHOP_CORE . 'inc/');
!defined('SHOP_URL') && define('SHOP_URL', $_W['siteroot'] . 'addons/shop/');
!defined('SHOP_STATIC') && define('SHOP_STATIC', SHOP_URL . 'static/');
!defined('SHOP_PREFIX') && define('SHOP_PREFIX', 'shop_');
!defined('SEND_WALLET') && define('SEND_WALLET', IA_ROOT.'/addons/tangshow/sendWallet');
