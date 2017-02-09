<?php
    if(!defined('IN_IA')) {
        exit('Access Denied');
    }
    define('ALAN_NAME', 'alan_qrcode');
    !defined('ALAN_PATH') && define('ALAN_PATH', IA_ROOT.'/addons/alan_qrcode/');
    !defined('ALAN_CORE') && define('ALAN_CORE', ALAN_PATH.'core/');
    !defined('ALAN_APP') && define('ALAN_APP', ALAN_PATH.'app/');
    !defined('ALAN_WEB') && define('ALAN_WEB', ALAN_PATH.'web/');
    !defined('ALAN_DATA') && define('ALAN_DATA', ALAN_PATH.'data/');

    !defined('ALAN_URL') && define('ALAN_URL', $_W['siteroot'].'addons/alan_qrcode/');
    !defined('ALAN_URL_APP') && define('ALAN_URL_APP', ALAN_URL.'app/');
    !defined('ALAN_URL_WEB') && define('ALAN_URL_WEB', ALAN_URL.'web/');