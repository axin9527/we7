<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

define('TICKET_DEBUG', false);
define('TICKET_NAME', 'alan_ticket');

!defined('TICKET_PATH') && define('TICKET_PATH', IA_ROOT . '/addons/alan_ticket/');
!defined('TICKET_CORE') && define('TICKET_CORE', TICKET_PATH . 'core/');
!defined('TICKET_APP') && define('TICKET_APP', TICKET_PATH . 'app/');
!defined('TICKET_WEB') && define('TICKET_WEB', TICKET_PATH . 'web/');
!defined('TICKET_DATA') && define('TICKET_DATA', TICKET_PATH . 'data/');

!defined('TICKET_URL') && define('TICKET_URL', $_W['siteroot'] . 'addons/alan_ticket/');
!defined('TICKET_URL_APP') && define('TICKET_URL_APP', TICKET_URL . 'app/');
!defined('TICKET_URL_WEB') && define('TICKET_URL_WEB', TICKET_URL . 'web/');
!defined('TICKET_URL_ARES') && define('TICKET_URL_ARES', TICKET_URL . 'app/resource/');
!defined('TICKET_URL_WRES') && define('TICKET_URL_WRES', TICKET_URL . 'web/resource/');

!defined('IMAGE_PIXEL') && define('IMAGE_PIXEL', TICKET_URL . 'web/resource/images/pixel.gif');
!defined('IMAGE_NOPIC_SMALL') && define('IMAGE_NOPIC_SMALL', TICKET_URL . 'web/resource/images/nopic-small.jpg');
!defined('IMAGE_LOADING') && define('IMAGE_LOADING', TICKET_URL . 'web/resource/images/loading.gif');
