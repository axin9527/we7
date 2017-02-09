<?php

	class Core extends WeModuleSite
	{
		public function _exec($do, $default = '', $web = TRUE)
		{
			global $_GPC;
			$do = strtolower(substr($do, $web ? 5 : 8));
			$p = trim($_GPC['p']);
			empty($p) && $p = $default;
			if($web) {
				$file = IA_ROOT . "/addons/" . MODULE_NAME . "/core/web/" . $do . "/" . $p . ".php";
			} else {
				$this->setFooter();
				$file = IA_ROOT . "/addons/" . MODULE_NAME . "/core/mobile/" . $do . "/" . $p . ".php";
			}
			if(!is_file($file)) {
				message("未找到 控制器文件 {$do}::{$p} : {$file}");
			}
			include $file;
			exit;
		}
		public function template($filename, $type = TEMPLATE_INCLUDEPATH)
		{
			global $_W;
			$name = strtolower($this->modulename);
			if (defined('IN_SYS')) {
				$source  = IA_ROOT . "/web/themes/{$_W['template']}/{$name}/{$filename}.html";
				$compile = IA_ROOT . "/data/tpl/web/{$_W['template']}/{$name}/{$filename}.tpl.php";
				if (!is_file($source)) {
					$source = IA_ROOT . "/web/themes/default/{$name}/{$filename}.html";
				}
				if (!is_file($source)) {
					$source = IA_ROOT . "/addons/{$name}/template/{$filename}.html";
				}
				if (!is_file($source)) {
					$source = IA_ROOT . "/web/themes/{$_W['template']}/{$filename}.html";
				}
				if (!is_file($source)) {
					$source = IA_ROOT . "/web/themes/default/{$filename}.html";
				}
				if (!is_file($source)) {
					$explode = explode('/', $filename);
					$temp    = array_slice($explode, 1);
					$source  = IA_ROOT . "/addons/{$name}/plugin/" . $explode[0] . "/template/" . implode('/', $temp) . ".html";
				}
			} else {
				$template = m('cache')->getString('template_shop');
				if (empty($template)) {
					$template = "default";
				}
				if (!is_dir(IA_ROOT . '/addons/'.MODULE_NAME.'/template/mobile/' . $template)) {
					$template = "default";
				}
				$compile = IA_ROOT . "/data/tpl/app/".MODULE_NAME."/{$template}/mobile/{$filename}.tpl.php";
				$source  = IA_ROOT . "/addons/{$name}/template/mobile/{$template}/{$filename}.html";
				if (!is_file($source)) {
					$source = IA_ROOT . "/addons/{$name}/template/mobile/default/{$filename}.html";
				}
				if (!is_file($source)) {
					$names      = explode('/', $filename);
					$pluginname = $names[0];
					$ptemplate  = m('cache')->getString('template_' . $pluginname);
					if (empty($ptemplate)) {
						$ptemplate = "default";
					}
					if (!is_dir(IA_ROOT . '/addons/'.MODULE_NAME.'/plugin/' . $pluginname . "/template/mobile/" . $ptemplate)) {
						$ptemplate = "default";
					}
					$pfilename = $names[1];
					$source    = IA_ROOT . "/addons/".MODULE_NAME."/plugin/" . $pluginname . "/template/mobile/" . $ptemplate . "/{$pfilename}.html";
				}
				if (!is_file($source)) {
					$source = IA_ROOT . "/app/themes/{$_W['template']}/{$filename}.html";
				}
				if (!is_file($source)) {
					$source = IA_ROOT . "/app/themes/default/{$filename}.html";
				}
			}
			if (!is_file($source)) {
				exit("Error: template source '{$filename}' is not exist!");
			}
			if (DEVELOPMENT || !is_file($compile) || filemtime($source) > filemtime($compile)) {
				shop_template_compile($source, $compile, true);
			}
			return $compile;
		}
	}

