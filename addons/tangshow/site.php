<?php
	/**
	 * 微网站宣传专题模块微站定义
	 *
	 * @author 诗意的边缘
	 * @url http://bbs.we7.cc/
	 */
	defined('IN_IA') or exit('Access Denied');
	define('MODULE_NAME', 'tangshow');
	require IA_ROOT . '/addons/tangshow/defines.php';
	require IA_ROOT . '/addons/tangshow/core/core.php';
	require IA_ROOT . '/addons/tangshow/core/functions.php';

	class TangshowModuleSite extends Core
	{

		public function doMobileIndex()
		{
			//这个操作被定义用来呈现 功能封面
			$this->_exec(__FUNCTION__, 'index', FALSE);
		}

		public function doMobileActivity()
		{
			//这个操作被定义用来呈现 功能封面
			$this->_exec(__FUNCTION__, 'index', FALSE);
		}

		public function doWebIndex()
		{
			//这个操作被定义用来呈现 规则列表
		}

	}