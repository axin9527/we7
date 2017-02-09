<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
function m($dephp_0 = ''){
    static $dephp_1 = array();
    if (isset($dephp_1[$dephp_0])){
        return $dephp_1[$dephp_0];
    }
    $dephp_2 = manor_shop_CORE . 'model/' . strtolower($dephp_0) . '.php';
    if (!is_file($dephp_2)){
        die(' Model ' . $dephp_0 . ' Not Found!');
    }
    require $dephp_2;
    $dephp_3 = 'Ewei_Dshop_' . ucfirst($dephp_0);
    $dephp_1[$dephp_0] = new $dephp_3();
    return $dephp_1[$dephp_0];
}
function p($dephp_0 = ''){
    if($dephp_0 != 'perm' && !IN_MOBILE){
        static $dephp_4;
        if(!$dephp_4){
            $dephp_5 = manor_shop_PLUGIN . 'perm/model.php';
            if (is_file($dephp_5)){
                require $dephp_5;
                $dephp_6 = 'PermModel';
                $dephp_4 = new $dephp_6('perm');
            }
        }
        if($dephp_4){
            if(!$dephp_4 -> check_plugin($dephp_0)){
                return false;
            }
        }
    }
    static $dephp_7 = array();
    if (isset($dephp_7[$dephp_0])){
        return $dephp_7[$dephp_0];
    }
    $dephp_2 = manor_shop_PLUGIN . strtolower($dephp_0) . '/model.php';
    if (!is_file($dephp_2)){
        return false;
    }
    require $dephp_2;
    $dephp_3 = ucfirst($dephp_0) . 'Model';
    $dephp_7[$dephp_0] = new $dephp_3($dephp_0);
    return $dephp_7[$dephp_0];
}
function byte_format($dephp_8, $dephp_9 = 0){
    $dephp_10 = array(' B', 'K', 'M', 'G', 'T');
    $dephp_11 = round($dephp_8, $dephp_9);
    $dephp_12 = 0;
    while ($dephp_11 > 1024){
        $dephp_11 /= 1024;
        $dephp_12++;
    }
    $dephp_13 = round($dephp_11, $dephp_9) . $dephp_10[$dephp_12];
    return $dephp_13;
}
function save_media($dephp_14){
    load() -> func('file');
    $dephp_15 = array('qiniu' => false);
    $dephp_16 = p('qiniu');
    if ($dephp_16){
        $dephp_15 = $dephp_16 -> getConfig();
        if ($dephp_15){
            if (strexists($dephp_14, $dephp_15['url'])){
                return $dephp_14;
            }
            $dephp_17 = $dephp_16 -> save(tomedia($dephp_14), $dephp_15);
            if (empty($dephp_17)){
                return $dephp_14;
            }
            return $dephp_17;
        }
        return $dephp_14;
    }
    return $dephp_14;
}
function save_remote($dephp_14){
}
function is_array2($dephp_18){
    if (is_array($dephp_18)){
        foreach ($dephp_18 as $dephp_19 => $dephp_20){
            return is_array($dephp_20);
        }
        return false;
    }
    return false;
}
function set_medias($dephp_21 = array(), $dephp_22 = null){
    if (empty($dephp_22)){
        foreach ($dephp_21 as & $dephp_23){
            $dephp_23 = tomedia($dephp_23);
        }
        return $dephp_21;
    }
    if (!is_array($dephp_22)){
        $dephp_22 = explode(',', $dephp_22);
    }
    if (is_array2($dephp_21)){
        foreach ($dephp_21 as $dephp_24 => & $dephp_11){
            foreach ($dephp_22 as $dephp_25){
                if (isset($dephp_21[$dephp_25])){
                    $dephp_21[$dephp_25] = tomedia($dephp_21[$dephp_25]);
                }
                if (is_array($dephp_11) && isset($dephp_11[$dephp_25])){
                    $dephp_11[$dephp_25] = tomedia($dephp_11[$dephp_25]);
                }
            }
        }
        return $dephp_21;
    }else{
        foreach ($dephp_22 as $dephp_25){
            if (isset($dephp_21[$dephp_25])){
                $dephp_21[$dephp_25] = tomedia($dephp_21[$dephp_25]);
            }
        }
        return $dephp_21;
    }
}
function get_last_day($dephp_26, $dephp_27){
    return date('t', strtotime("{$dephp_26}-{$dephp_27} -1"));
}
function show_message($dephp_28 = '', $dephp_14 = '', $dephp_29 = 'success'){
    $dephp_30 = '<script language=\'javascript\'>require([\'core\'],function(core){ core.message(\'' . $dephp_28 . '\',\'' . $dephp_14 . '\',\'' . $dephp_29 . '\')})</script>';
    die($dephp_30);
}
function show_json($dephp_31 = 1, $dephp_32 = null){
    $dephp_33 = array('status' => $dephp_31);
    if ($dephp_32){
        $dephp_33['result'] = $dephp_32;
    }
    die(json_encode($dephp_33));
}
function is_weixin(){
    if (empty($_SERVER['HTTP_USER_AGENT']) || strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'Windows Phone') === false){
        return false;
    }
    return true;
}
function b64_encode($dephp_34){
    if (is_array($dephp_34)){
        return urlencode(base64_encode(json_encode($dephp_34)));
    }
    return urlencode(base64_encode($dephp_34));
}
function b64_decode($dephp_35, $dephp_36 = true){
    $dephp_35 = base64_decode(urldecode($dephp_35));
    if ($dephp_36){
        return json_decode($dephp_35, true);
    }
    return $dephp_35;
}
function create_image($dephp_37){
    $dephp_38 = strtolower(substr($dephp_37, strrpos($dephp_37, '.')));
    if ($dephp_38 == '.png'){
        $dephp_39 = imagecreatefrompng($dephp_37);
    }else if ($dephp_38 == '.gif'){
        $dephp_39 = imagecreatefromgif($dephp_37);
    }else{
        $dephp_39 = imagecreatefromjpeg($dephp_37);
    }
    return $dephp_39;
}
function get_authcode(){
    $dephp_40 = get_auth();
    return empty($dephp_40['code']) ? '' : $dephp_40['code'];
}
function get_auth(){
    global $_W;
    $dephp_41 = pdo_fetch('select sets from ' . tablename('manor_shop_sysset') . ' order by id asc limit 1');
    $dephp_42 = iunserializer($dephp_41['sets']);
    if (is_array($dephp_42)){
        return is_array($dephp_42['auth']) ? $dephp_42['auth'] : array();
    }
    return array();
}
function check_shop_auth($dephp_14 = '', $dephp_29 = 's'){
    global $_W, $_GPC;
    if ($_W['ispost'] && $_GPC['do'] != 'auth'){
        $dephp_40 = get_auth();
        load() -> func('communication');
        $dephp_43 = $_SERVER['HTTP_HOST'];
        $dephp_44 = gethostbyname($dephp_43);
        $dephp_45 = setting_load('site');
        $dephp_46 = isset($dephp_45['site']['key']) ? $dephp_45['site']['key'] : '0';
        if(empty($dephp_29) || $dephp_29 == 's'){
            $dephp_47 = array('type' => $dephp_29, 'ip' => $dephp_44, 'id' => $dephp_46, 'code' => $dephp_40['code'], 'domain' => $dephp_43);
        }else{
            $dephp_47 = array('type' => 'm', 'm' => $dephp_29, 'ip' => $dephp_44, 'id' => $dephp_46, 'code' => $dephp_40['code'], 'domain' => $dephp_43);
        }
        $dephp_48 = ihttp_post($dephp_14, $dephp_47);
        $dephp_31 = $dephp_48['content'];
        if ($dephp_31 != '1'){
            message(base64_decode('57O757uf5q2j5Zyo57u05oqk77yM6K+35oKo56iN5ZCO5YaN6K+V77yM5pyJ55aR6Zeu6K+36IGU57O757O757uf566h55CG5ZGYIQ=='), '', 'error');
        }
    }
}
$my_scenfiles = array();
function my_scandir($dephp_49){
    global $my_scenfiles;
    if ($dephp_50 = opendir($dephp_49)){
        while (($dephp_51 = readdir($dephp_50)) !== false){
            if ($dephp_51 != '..' && $dephp_51 != '.'){
                if (is_dir($dephp_49 . '/' . $dephp_51)){
                    my_scandir($dephp_49 . '/' . $dephp_51);
                }else{
                    $my_scenfiles[] = $dephp_49 . '/' . $dephp_51;
                }
            }
        }
        closedir($dephp_50);
    }
}
function shop_template_compile($dephp_52, $dephp_53, $dephp_54 = false){
    $dephp_55 = dirname($dephp_53);
    if (!is_dir($dephp_55)){
        load() -> func('file');
        mkdirs($dephp_55);
    }
    $dephp_56 = shop_template_parse(file_get_contents($dephp_52), $dephp_54);
    if (IMS_FAMILY == 'x' && !preg_match('/(footer|header|account\/welcome|login|register)+/', $dephp_52)){
        $dephp_56 = str_replace('微擎', '系统', $dephp_56);
    }
    file_put_contents($dephp_53, $dephp_56);
}
function shop_template_parse($dephp_35, $dephp_54 = false){
    $dephp_35 = template_parse($dephp_35, $dephp_54);
    $dephp_35 = preg_replace('/{ifp\s+(.+?)}/', '<?php if(cv($1)) { ?>', $dephp_35);
    $dephp_35 = preg_replace('/{ifpp\s+(.+?)}/', '<?php if(cp($1)) { ?>', $dephp_35);
    $dephp_35 = preg_replace('/{ife\s+(\S+)\s+(\S+)}/', '<?php if( ce($1 ,$2) ) { ?>', $dephp_35);
    return $dephp_35;
}
function ce($dephp_57 = '', $dephp_58 = null){
    $dephp_59 = p('perm');
    if ($dephp_59){
        return $dephp_59 -> check_edit($dephp_57, $dephp_58);
    }
    return true;
}
function cv($dephp_60 = ''){
    $dephp_59 = p('perm');
    if ($dephp_59){
        return $dephp_59 -> check_perm($dephp_60);
    }
    return true;
}
function ca($dephp_60 = ''){
    if(!cv($dephp_60)){
        message('您没有权限操作，请联系管理员!', '', 'error');
    }
}
function cp($dephp_61 = ''){
    $dephp_59 = p('perm');
    if ($dephp_59){
        return $dephp_59 -> check_plugin($dephp_61);
    }
    return true;
}
function cpa($dephp_61 = ''){
    if(!cp($dephp_61)){
        message('您没有权限操作，请联系管理员!', '', 'error');
    }
}
function plog($dephp_29 = '', $dephp_62 = ''){
    $dephp_59 = p('perm');
    if ($dephp_59){
        $dephp_59 -> log($dephp_29, $dephp_62);
    }
}
function tpl_form_field_category_3level($dephp_0, $dephp_63, $dephp_64, $dephp_65, $dephp_66, $dephp_67){
    $dephp_68 = '
<script type="text/javascript">
	window._' . $dephp_0 . ' = ' . json_encode($dephp_64) . ';
</script>';
    if (!defined('TPL_INIT_CATEGORY_THIRD')){
        $dephp_68 .= '	
<script type="text/javascript">
	function renderCategoryThird(obj, name){
		var index = obj.options[obj.selectedIndex].value;
		require([\'jquery\', \'util\'], function($, u){
			$selectChild = $(\'#\'+name+\'_child\');
                                                      $selectThird = $(\'#\'+name+\'_third\');
			var html = \'<option value="0">请选择二级分类</option>\';
                                                      var html1 = \'<option value="0">请选择三级分类</option>\';
			if (!window[\'_\'+name] || !window[\'_\'+name][index]) {
				$selectChild.html(html); 
                                                                        $selectThird.html(html1);
				return false;
			}
			for(var i=0; i< window[\'_\'+name][index].length; i++){
				html += \'<option value="\'+window[\'_\'+name][index][i][\'id\']+\'">\'+window[\'_\'+name][index][i][\'name\']+\'</option>\';
			}
			$selectChild.html(html);
                                                    $selectThird.html(html1);
		});
	}
        function renderCategoryThird1(obj, name){
		var index = obj.options[obj.selectedIndex].value;
		require([\'jquery\', \'util\'], function($, u){
			$selectChild = $(\'#\'+name+\'_third\');
			var html = \'<option value="0">请选择三级分类</option>\';
			if (!window[\'_\'+name] || !window[\'_\'+name][index]) {
				$selectChild.html(html);
				return false;
			}
			for(var i=0; i< window[\'_\'+name][index].length; i++){
				html += \'<option value="\'+window[\'_\'+name][index][i][\'id\']+\'">\'+window[\'_\'+name][index][i][\'name\']+\'</option>\';
			}
			$selectChild.html(html);
		});
	}
</script>
			';
        define('TPL_INIT_CATEGORY_THIRD', true);
    }
    $dephp_68 .= '<div class="row row-fix tpl-category-container">
	<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
		<select class="form-control tpl-category-parent" id="' . $dephp_0 . '_parent" name="' . $dephp_0 . '[parentid]" onchange="renderCategoryThird(this,\'' . $dephp_0 . '\')">
			<option value="0">请选择一级分类</option>';
    $dephp_69 = '';
    foreach ($dephp_63 as $dephp_23){
        $dephp_68 .= '
			<option value="' . $dephp_23['id'] . '" ' . (($dephp_23['id'] == $dephp_65) ? 'selected="selected"' : '') . '>' . $dephp_23['name'] . '</option>';
    }
    $dephp_68 .= '
		</select>
	</div>
	<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
		<select class="form-control tpl-category-child" id="' . $dephp_0 . '_child" name="' . $dephp_0 . '[childid]" onchange="renderCategoryThird1(this,\'' . $dephp_0 . '\')">
			<option value="0">请选择二级分类</option>';
    if (!empty($dephp_65) && !empty($dephp_64[$dephp_65])){
        foreach ($dephp_64[$dephp_65] as $dephp_23){
            $dephp_68 .= '
			<option value="' . $dephp_23['id'] . '"' . (($dephp_23['id'] == $dephp_66) ? 'selected="selected"' : '') . '>' . $dephp_23['name'] . '</option>';
        }
    }
    $dephp_68 .= '
		</select> 
	</div> 
                  <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
		<select class="form-control tpl-category-child" id="' . $dephp_0 . '_third" name="' . $dephp_0 . '[thirdid]">
			<option value="0">请选择三级分类</option>';
    if (!empty($dephp_66) && !empty($dephp_64[$dephp_66])){
        foreach ($dephp_64[$dephp_66] as $dephp_23){
            $dephp_68 .= '
			<option value="' . $dephp_23['id'] . '"' . (($dephp_23['id'] == $dephp_67) ? 'selected="selected"' : '') . '>' . $dephp_23['name'] . '</option>';
        }
    }
    $dephp_68 .= '</select>
	</div>
</div>';
    return $dephp_68;
}

	/**
	 * 格式化打印
	 * @author 兰辉
	 * @access public
	 * @param        $mix_var
	 * @param bool   $die
	 * @param string $name
	 * @since  1.0
	 */
	function dd($mix_var,$die = false,$name = '')
	{
		if(is_string($die))
		{
			$name = $die;
			$die = false;
		}
		if(!empty($name))
		{
			$name = "[$name]";
		}
		echo "<div style='border:solid 2px #FF6600;background-color:#FFFEFC;padding:1em 0.5em;'><strong style='color:red;font-weight:bold;'>$name</strong><pre style='margin-left:1em;color:#243F61;'>",var_dump($mix_var),"</pre></div>";
		if($die)
		{
			die();
		}
	}

    function list_sort_by($list, $field, $sortby = 'asc')
    {
        if(is_array($list)) {
            $refer = $resultSet = array();
            foreach($list as $i => $data)
                $refer[ $i ] = &$data[ $field ];
            switch ($sortby) {
                case 'asc': // 正向排序
                    asort($refer);
                    break;
                case 'desc':// 逆向排序
                    arsort($refer);
                    break;
                case 'nat': // 自然排序
                    natcasesort($refer);
                    break;
            }
            foreach($refer as $key => $val)
                $resultSet[] = &$list[ $key ];

            return $resultSet;
        }

        return FALSE;
    }

    /*
    * array unique_rand( int $min, int $max, int $num )
    * 生成一定数量的不重复随机数
    * $min 和 $max: 指定随机数的范围
    * $num: 指定生成数量
    */
    function unique_rand($min, $max, $num, $str)
    {
        $count = 0;
        $return = array();
        while($count<$num) {
            $return[] = $str.mt_rand($min, $max);
            $return = array_flip(array_flip($return));
            $count = count($return);
        }
        shuffle($return);

        return $return;
    }