<?php
 if(!defined('IN_IA')){
    exit('Access Denied');
}
define('manor_shop_DEBUG', true );
!defined('manor_shop_PATH') && define('manor_shop_PATH', IA_ROOT . '/addons/manor_shop/');
!defined('manor_shop_CORE') && define('manor_shop_CORE', manor_shop_PATH . 'core/');
!defined('manor_shop_PLUGIN') && define('manor_shop_PLUGIN', manor_shop_PATH . 'plugin/');
!defined('manor_shop_INC') && define('manor_shop_INC', manor_shop_CORE . 'inc/');
!defined('manor_shop_URL') && define('manor_shop_URL', $_W['siteroot'] . 'addons/manor_shop/');
!defined('manor_shop_STATIC') && define('manor_shop_STATIC', manor_shop_URL . 'static/');
!defined('manor_shop_PREFIX') && define('manor_shop_PREFIX', 'manor_shop_');
