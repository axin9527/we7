<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
class Plugin extends Core{
    public $pluginname;
    public $model;
    public function __construct($dephp_0 = ''){
        parent :: __construct();
        $this -> modulename = 'manor_shop';
        $this -> pluginname = $dephp_0;
        $this -> loadModel();
        if (strexists($_SERVER['REQUEST_URI'], '/web/')){
            cpa($this -> pluginname);
        }else if (strexists($_SERVER['REQUEST_URI'], '/app/')){
            $this -> setFooter();
        }
        $this -> module['title'] = pdo_fetchcolumn('select title from ' . tablename('modules') . ' where name=\'manor_shop\' limit 1');
    }
    private function loadModel(){
        $dephp_1 = IA_ROOT . '/addons/' . $this -> modulename . '/plugin/' . $this -> pluginname . '/model.php';
        if (is_file($dephp_1)){
            $dephp_2 = ucfirst($this -> pluginname) . 'Model';
            require $dephp_1;
            $this -> model = new $dephp_2($this -> pluginname);
        }
    }
    public function getSet(){
        return $this -> model -> getSet();
    }
    public function updateSet($dephp_3 = array()){
        $this -> model -> updateSet($dephp_3);
    }
    public function template($dephp_4, $dephp_5 = TEMPLATE_INCLUDEPATH){
        global $_W;
        $dephp_6 = IA_ROOT . '/addons/manor_shop/';
        if (defined('IN_SYS')){
            $dephp_7 = IA_ROOT . '/addons/manor_shop/plugin/' . $this -> pluginname . "/template/{$dephp_4}.html";
            $dephp_8 = IA_ROOT . "/data/tpl/web/{$_W['template']}/manor_shop/plugin/" . $this -> pluginname . "/{$dephp_4}.tpl.php";
            if (!is_file($dephp_7)){
                $dephp_7 = IA_ROOT . "/addons/manor_shop/template/{$dephp_4}.html";
                $dephp_8 = IA_ROOT . "/data/tpl/web/{$_W['template']}/manor_shop/{$dephp_4}.tpl.php";
            }
            if (!is_file($dephp_7)){
                $dephp_7 = IA_ROOT . "/web/themes/{$_W['template']}/{$dephp_4}.html";
                $dephp_8 = IA_ROOT . "/data/tpl/web/{$_W['template']}/{$dephp_4}.tpl.php";
            }
            if (!is_file($dephp_7)){
                $dephp_7 = IA_ROOT . "/web/themes/default/{$dephp_4}.html";
                $dephp_8 = IA_ROOT . "/data/tpl/web/default/{$dephp_4}.tpl.php";
            }
        }else{
            $dephp_9 = m('cache') -> getString('template_shop');
            if (empty($dephp_9)){
                $dephp_9 = 'default';
            }
            if (!is_dir(IA_ROOT . '/addons/manor_shop/template/mobile/' . $dephp_9)){
                $dephp_9 = 'default';
            }
            $dephp_9 = 'tangsheng';
            $dephp_10 = m('cache') -> getString('template_' . $this -> pluginname);
            if (empty($dephp_10)){
                $dephp_10 = 'default';
            }
            if (!is_dir(IA_ROOT . '/addons/manor_shop/plugin/' . $this -> pluginname . '/template/mobile/' . $dephp_10)){
                $dephp_10 = 'default';
            }
            $dephp_8 = IA_ROOT . '/data/app/manor_shop/plugin/' . $this -> pluginname . "/{$dephp_10}/mobile/{$dephp_4}.tpl.php";
            $dephp_7 = $dephp_6 . '/plugin/' . $this -> pluginname . "/template/mobile/{$dephp_10}/{$dephp_4}.html";
            if (!is_file($dephp_7)){
                $dephp_7 = $dephp_6 . '/plugin/' . $this -> pluginname . "/template/mobile/default/{$dephp_4}.html";
                $dephp_8 = IA_ROOT . '/data/app/manor_shop/plugin/' . $this -> pluginname . "/default/mobile/{$dephp_4}.tpl.php";
            }
            if (!is_file($dephp_7)){
                $dephp_7 = $dephp_6 . "/template/mobile/{$dephp_9}/{$dephp_4}.html";
                $dephp_8 = IA_ROOT . "/data/app/manor_shop/{$dephp_9}/{$dephp_4}.tpl.php";
            }
            if (!is_file($dephp_7)){
                $dephp_7 = $dephp_6 . "/template/mobile/default/{$dephp_4}.html";
                $dephp_8 = IA_ROOT . "/data/app/manor_shop/default/{$dephp_4}.tpl.php";
            }
            if (!is_file($dephp_7)){
                $dephp_7 = $dephp_6 . "/template/mobile/{$dephp_4}.html";
                $dephp_8 = IA_ROOT . "/data/app/manor_shop/{$dephp_4}.tpl.php";
            }
            if (!is_file($dephp_7)){
                $dephp_11 = explode('/', $dephp_4);
                $dephp_12 = $dephp_11[0];
                $dephp_13 = m('cache') -> getString('template_' . $dephp_12);
                if (empty($dephp_13)){
                    $dephp_13 = 'default';
                }
                if (!is_dir(IA_ROOT . '/addons/manor_shop/plugin/' . $dephp_12 . '/template/mobile/' . $dephp_13)){
                    $dephp_13 = 'default';
                }
                $dephp_14 = $dephp_11[1];
                $dephp_7 = IA_ROOT . '/addons/manor_shop/plugin/' . $dephp_12 . '/template/mobile/' . $dephp_13 . "/{$dephp_14}.html";
            }
        }
        if (!is_file($dephp_7)){
            exit("Error: template source '{$dephp_4}' is not exist!");
        }
        if (DEVELOPMENT || !is_file($dephp_8) || filemtime($dephp_7) > filemtime($dephp_8)){
            shop_template_compile($dephp_7, $dephp_8, true);
        }
        return $dephp_8;
    }
    public function _exec_plugin($dephp_15, $dephp_16 = true){
        global $_GPC;
        if ($dephp_16){
            $dephp_17 = IA_ROOT . '/addons/manor_shop/plugin/' . $this -> pluginname . '/core/web/' . $dephp_15 . '.php';
        }else{
            $dephp_17 = IA_ROOT . '/addons/manor_shop/plugin/' . $this -> pluginname . '/core/mobile/' . $dephp_15 . '.php';
        }
        if (!is_file($dephp_17)){
            message("未找到控制器文件 : {$dephp_17}");
        }
        include $dephp_17;
        exit;
    }
}
