<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
require IA_ROOT . '/addons/manor_shop/version.php';
require IA_ROOT . '/addons/manor_shop/defines.php';
require manor_shop_INC . 'functions.php';
require manor_shop_INC . 'processor.php';
require manor_shop_INC . 'plugin/plugin_model.php';
class manor_shopModuleProcessor extends Processor{
    public function respond(){
        return parent :: respond();
    }
}
