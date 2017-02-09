<?php
/**
 * 全局函数
 * Author：Alan
 * [TangShengMonar System]Copyright (c) 2016 fresh.tangshengmanor.com
 * Time:2016-12-19 10
 */
defined('IN_IA') or exit('Access Denied');
function ticket_template($filename,$flag='') {
    global $_W;
    $name = 'alan_ticket';
    if (defined('IN_SYS')) {
        $template = $_W['tgsetting']['webview'];
        if (empty($template)) {
            $template = "default";
        }
        $source = IA_ROOT . "/addons/{$name}/web/view/{$template}/{$filename}.html";
        $compile = IA_ROOT . "/data/tpl/web/{$name}/web/view/{$template}/{$filename}.tpl.php";
        if (!is_file($source)) {
            $source = IA_ROOT . "/addons/{$name}/web/view/default/{$filename}.html";
        }
    } else {
        $template = $_W['atsetting']['appview'];
        if (empty($template)) {
            $template = "default";
        }
        $source = IA_ROOT . "/addons/{$name}/app/view/{$template}/{$filename}.html";
        $compile = IA_ROOT . "/data/tpl/app/{$name}/app/view/{$template}/{$filename}.tpl.php";
        if (!is_file($source)) {
            $source = IA_ROOT . "/addons/{$name}/app/view/default/{$filename}.html";
        }
    }
    if (!is_file($source)) {
        exit("Error: template source '{$filename}' is not exist!!!");
    }
    if (!is_file($compile) || filemtime($source) > filemtime($compile)) {
        ticket_template_compile($source, $compile, true);

    }
    if($flag==TEMPLATE_FETCH){
        extract($GLOBALS, EXTR_SKIP);
        ob_end_flush();
        ob_clean();
        ob_start();
        include $compile;
        $contents = ob_get_contents();
        ob_clean();
        return $contents;
    }
    return $compile;
}
function ticket_template_compile($from, $to, $inmodule = false) {
    $path = dirname($to);
    if (!is_dir($path)) {
        load()->func('file');
        mkdirs($path);
    }
    $content = ticket_template_parse(file_get_contents($from), $inmodule);
    if(IMS_FAMILY == 'x' && !preg_match('/(footer|header)+/', $from)) {
        $content = str_replace('微擎', '系统', $content);
    }
    file_put_contents($to, $content);
}

function ticket_template_parse($str, $inmodule = false) {
    $str = preg_replace('/<!--{(.+?)}-->/s', '{$1}', $str);
    $str = preg_replace('/{template\s+(.+?)}/', '<?php (!empty($this) && $this instanceof WeModuleSite || '.intval($inmodule).') ? (include $this->template($1, TEMPLATE_INCLUDEPATH)) : (include template($1, TEMPLATE_INCLUDEPATH));?>', $str);

    $str = preg_replace('/{php\s+(.+?)}/', '<?php $1?>', $str);
    $str = preg_replace('/{if\s+(.+?)}/', '<?php if($1) { ?>', $str);
    $str = preg_replace('/{else}/', '<?php } else { ?>', $str);
    $str = preg_replace('/{else ?if\s+(.+?)}/', '<?php } else if($1) { ?>', $str);
    $str = preg_replace('/{\/if}/', '<?php } ?>', $str);
    $str = preg_replace('/{loop\s+(\S+)\s+(\S+)}/', '<?php if(is_array($1)) { foreach($1 as $2) { ?>', $str);
    $str = preg_replace('/{loop\s+(\S+)\s+(\S+)\s+(\S+)}/', '<?php if(is_array($1)) { foreach($1 as $2 => $3) { ?>', $str);
    $str = preg_replace('/{\/loop}/', '<?php } } ?>', $str);
    $str = preg_replace('/{(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)}/', '<?php echo $1;?>', $str);
    $str = preg_replace('/{(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff\[\]\'\"\$]*)}/', '<?php echo $1;?>', $str);
    $str = preg_replace('/{url\s+(\S+)}/', '<?php echo url($1);?>', $str);
    $str = preg_replace('/{url\s+(\S+)\s+(array\(.+?\))}/', '<?php echo url($1, $2);?>', $str);
    $str = preg_replace_callback('/<\?php([^\?]+)\?>/s', "template_addquote", $str);
    $str = preg_replace('/{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)}/s', '<?php echo $1;?>', $str);
    $str = str_replace('{##', '{', $str);
    $str = str_replace('##}', '}', $str);
    $str = "<?php defined('IN_IA') or exit('Access Denied');?>" . $str;
    return $str;
}

function web_url($segment, $params = array()) {
    global $_W;
    list($do, $ac, $op) = explode('/', $segment);
    $url = $_W['siteroot'] . 'web/index.php?c=site&a=entry&m=alan_ticket&';
    if(!empty($do)) {
        $url .= "do={$do}&";
    }
    if(!empty($ac)) {
        $url .= "ac={$ac}&";
    }
    if(!empty($op)) {
        $url .= "op={$op}&";
    }
    if(!empty($params)) {
        $queryString = http_build_query($params, '', '&');
        $url .= $queryString;
    }
    return $url;
}

/********************app页面跳转************************/
function app_url($segment, $params = array()) {
    global $_W;
    list($do, $ac, $op) = explode('/', $segment);
    $url = $_W['siteroot'] . 'app/index.php?i='.$_W['uniacid'].'&c=entry&m=alan_ticket&';
    if(!empty($do)) {
        $url .= "do={$do}&";
    }
    if(!empty($ac)) {
        $url .= "ac={$ac}&";
    }
    if(!empty($op)) {
        $url .= "op={$op}&";
    }
    if(!empty($params)) {
        $queryString = http_build_query($params, '', '&');
        $url .= $queryString;
    }
    return $url;
}
//获取ticket
function getTicket($user_id, $openid){
    global $_W, $_GPC;
    $acid = $_W['uniacid'] ? $_W['uniacid'] : 6;
    load()->classs('account');
    $access = WeAccount :: create($acid);
    $scene_str = md5("{$_W['uniacid']}:{$openid}:{$user_id}");
    $wx_post_data = '{"action_info":{"scene":{"scene_str":"' . $scene_str . '"} },"action_name":"QR_LIMIT_STR_SCENE"}';
    $access_token = $access -> fetch_token();
    $wx_url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $access_token;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $wx_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $wx_post_data);
    $ret = curl_exec($ch);
    $result = @json_decode($ret, true);
    if(!is_array($result)){
        return false;
    }
    if (!empty($result['errcode'])){
        return error(-1, $result['errmsg']);
    }
    $ticket = $result['ticket'];
    return array('barcode' => json_decode($wx_post_data, true), 'ticket' => $ticket);
}

//获取微信url或者微信图片url
function getUrlByTicket($ticket,$back_jpg=false) {
    global $_W;
    $wx_ticket = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $ticket;
    if(!$back_jpg) {
        return $wx_ticket;
    }
    $path = IA_ROOT . '/addons/alan_ticket/app/data/qrcode/' . $_W['uniacid'] . '/';
    if (!is_dir($path)){
        load() -> func('file');
        @mkdirs($path);
    }
    $qr_name = $_W['openid'].'.jpg';
    file_put_contents($path.$qr_name, file_get_contents($wx_ticket));
    return tomedia('../addons/alan_ticket/app/data/qrcode/' . $_W['uniacid'] . '/'.$qr_name);
}
//获取头像url
function getWxAvatarUrl($wx_head_url) {
    global $_W;
    $path = IA_ROOT . '/addons/alan_ticket/app/data/avatar/' . $_W['uniacid'] . '/';
    if (!is_dir($path)){
        load() -> func('file');
        @mkdirs($path);
    }
    $avatar_name = $_W['openid'].'.jpg';
    file_put_contents($path.$avatar_name, file_get_contents($wx_head_url));
    return tomedia('../addons/alan_ticket/app/data/avatar/' . $_W['uniacid'] . '/'.$avatar_name);
}