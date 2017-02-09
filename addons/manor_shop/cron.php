<?php
    /**
     * 操作类名称: 本模块定时任务-用于收集用户行为进行必要统计操作.
     * Author：Alan
     * [TangShengMonar System]Copyright (c) 2016 fresh.tangshengmanor.com
     * Time:2016-12-02 15
     */
    defined('IN_IA') or exit('Access Denied');
    class manor_shopModuleCron extends WeModuleCron {
        public function doCronTest() {
            global $_W,$_GPC;
            pdo_insert('log', array('cont'=>var_export('定时任务', true)));
        }
    }

