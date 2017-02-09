<?php
	/**
	 * 操作类名称: PhpStorm.
	 * 作者名称: alan
	 * 创建时间: 16/8/5 13:59
	 */
	global $_GPC;
	$op = $_GPC['op'];
	$url = '';
	if($op == 'about') {
		$url = 'help/about';
	} elseif($op == 'distribution') {
		$url = 'help/distribution';
	}elseif($op == 'payment') {
		$url = 'help/payment';
	}elseif($op == 'returns') {
		$url = 'help/returns';
	}
	include $this -> template($url);