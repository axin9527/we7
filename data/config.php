<?php
defined('IN_IA') or exit('Access Denied');

$config = array();

$config['db']['master']['host'] = '127.0.0.1';
	if($_SERVER['HTTP_HOST'] == 'test.tangshengmanor.com') {
		$config['db']['master']['username'] = 'root';
		$config['db']['master']['password'] = 'TszYp@PwL23O5P0w';
	}else if($_SERVER['HTTP_HOST'] == 'fresh.tangshengmanor.com' || $_SERVER['HTTP_HOST']  == 'guofan.tangshengmanor.com') {
	$config['db']['master']['username'] = 'lanhui';
	$config['db']['master']['password'] = 'b1890b862da';
} else {
	$config['db']['master']['username'] = 'root';
	$config['db']['master']['password'] = '123456';
}
/*$config['db']['master']['username'] = 'root';
$config['db']['master']['password'] = 'TszYp@PwL23O5P0w';*/
$config['db']['master']['port'] = '3306';
$config['db']['master']['database'] = 'we7';
$config['db']['master']['charset'] = 'utf8mb4';
$config['db']['master']['pconnect'] = 0;
$config['db']['master']['tablepre'] = 'ims_';

$config['db']['slave_status'] = false;
$config['db']['slave']['1']['host'] = '';
$config['db']['slave']['1']['username'] = '';
$config['db']['slave']['1']['password'] = '';
$config['db']['slave']['1']['port'] = '3307';
$config['db']['slave']['1']['database'] = '';
$config['db']['slave']['1']['charset'] = 'utf8';
$config['db']['slave']['1']['pconnect'] = 0;
$config['db']['slave']['1']['tablepre'] = 'ims_';
$config['db']['slave']['1']['weight'] = 0;

$config['db']['common']['slave_except_table'] = array('core_sessions');

// --------------------------  CONFIG COOKIE  --------------------------- //
$config['cookie']['pre'] = '7e41_';
$config['cookie']['domain'] = '';
$config['cookie']['path'] = '/';

// --------------------------  CONFIG SETTING  --------------------------- //
$config['setting']['charset'] = 'utf8mb4';
$config['setting']['cache'] = 'mysql';
$config['setting']['timezone'] = 'Asia/Shanghai';
$config['setting']['memory_limit'] = '256M';
$config['setting']['filemode'] = 0644;
$config['setting']['authkey'] = '823d6525';
$config['setting']['founder'] = '1';
$config['setting']['development'] = 0;
$config['setting']['referrer'] = 0;

// --------------------------  CONFIG UPLOAD  --------------------------- //
$config['upload']['image']['extentions'] = array('gif', 'jpg', 'jpeg', 'png');
$config['upload']['image']['limit'] = 5000;
$config['upload']['attachdir'] = 'attachment';
$config['upload']['audio']['extentions'] = array('mp3');
$config['upload']['audio']['limit'] = 5000;

// --------------------------  CONFIG MEMCACHE  --------------------------- //
$config['setting']['memcache']['server'] = '';
$config['setting']['memcache']['port'] = 11211;
$config['setting']['memcache']['pconnect'] = 1;
$config['setting']['memcache']['timeout'] = 30;
$config['setting']['memcache']['session'] = 1;

// --------------------------  CONFIG PROXY  --------------------------- //
$config['setting']['proxy']['host'] = '';
$config['setting']['proxy']['auth'] = '';
