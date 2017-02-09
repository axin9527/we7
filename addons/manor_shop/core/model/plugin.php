<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
class Ewei_DShop_Plugin{
    public function getSet($dephp_0 = '', $dephp_1 = '', $dephp_2 = 0){
        global $_W, $_GPC;
        if (empty($dephp_2)){
            $dephp_2 = $_W['uniacid'];
        }
        $dephp_3 = m('cache') -> getArray('sysset', $dephp_2);
        if(empty($dephp_3)){
            $dephp_3 = pdo_fetch('select * from ' . tablename('manor_shop_sysset') . ' where uniacid=:uniacid limit 1', array(':uniacid' => $dephp_2));
        }
        if (empty($dephp_3)){
            return array();
        }
        $dephp_4 = unserialize($dephp_3['sets']);
        if (empty($dephp_1)){
            return $dephp_4;
        }
        return $dephp_4[$dephp_1];
    }
    public function exists($dephp_5 = ''){
        $dephp_6 = pdo_fetchall('select * from ' . tablename('manor_shop_plugin') . ' where identity=:identyty limit  1', array(':identity' => $dephp_5));
        if(empty($dephp_6)){
            return false;
        }
        return true;
    }
    public function getAll(){
        global $_W;
        //$dephp_7 = m('cache') -> getArray('plugins', 'global');
        if(empty($dephp_7)){
            $dephp_7 = pdo_fetchall('select * from ' . tablename('manor_shop_plugin') . ' order by displayorder asc');
            m('cache') -> set('plugins', $dephp_7, 'global');
        }
        return $dephp_7;
    }
    public function getCategory(){
        return array('biz' => array('name' => '业务类'), 'sale' => array('name' => '营销类'), 'tool' => array('name' => '工具类'), 'help' => array('name' => '辅助类'));
    }
}
