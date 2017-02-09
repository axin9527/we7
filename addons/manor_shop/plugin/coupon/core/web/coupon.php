<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$type = intval($_GPC['type']);
if ($operation == 'display'){
    ca('coupon.coupon.view');
    if (!empty($_GPC['displayorder'])){
        ca('coupon.coupon.edit');
        foreach ($_GPC['displayorder'] as $id => $displayorder){
            pdo_update('manor_shop_coupon', array('displayorder' => $displayorder), array('id' => $id));
        }
        plog('coupon.coupon.edit', '批量修改排序');
        message('分类排序更新成功！', $this -> createPluginWebUrl('coupon', array('op' => 'display')), 'success');
    }
    $pindex = max(1, intval($_GPC['page']));
    $psize = 20;
    $condition = ' uniacid = :uniacid';
    $params = array(':uniacid' => $_W['uniacid']);
    if (!empty($_GPC['keyword'])){
        $_GPC['keyword'] = trim($_GPC['keyword']);
        $condition .= ' AND couponname LIKE :couponname';
        $params[':couponname'] = '%' . trim($_GPC['keyword']) . '%';
    }
    if (empty($starttime) || empty($endtime)){
        $starttime = strtotime('-1 month');
        $endtime = time();
    }
    if (!empty($_GPC['searchtime'])){
        $starttime = strtotime($_GPC['time']['start']);
        $endtime = strtotime($_GPC['time']['end']);
        if ($_GPC['searchtime'] == '1'){
            $condition .= ' AND createtime >= :starttime AND createtime <= :endtime ';
            $params[':starttime'] = $starttime;
            $params[':endtime'] = $endtime;
        }
    }
    if ($_GPC['type'] != ''){
        $condition .= ' AND coupontype = :coupontype';
        $params[':coupontype'] = intval($_GPC['type']);
    }
    $sql = 'SELECT * FROM ' . tablename('manor_shop_coupon') . ' ' . " where  1 and {$condition} ORDER BY displayorder DESC,id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
    $list = pdo_fetchall($sql, $params);
    foreach ($list as & $row){
        $row['gettotal'] = pdo_fetchcolumn('select count(*) from ' . tablename('manor_shop_coupon_data') . ' where couponid=:couponid and uniacid=:uniacid limit 1', array(':couponid' => $row['id'], ':uniacid' => $_W['uniacid']));
        $row['usetotal'] = pdo_fetchcolumn('select count(*) from ' . tablename('manor_shop_coupon_data') . ' where used = 1 and couponid=:couponid and uniacid=:uniacid limit 1', array(':couponid' => $row['id'], ':uniacid' => $_W['uniacid']));
        $row['pwdjoins'] = pdo_fetchcolumn('select count(*) from ' . tablename('manor_shop_coupon_guess') . ' where couponid=:couponid and uniacid=:uniacid limit 1', array(':couponid' => $row['id'], ':uniacid' => $_W['uniacid']));
        $row['pwdoks'] = pdo_fetchcolumn('select count(*) from ' . tablename('manor_shop_coupon_guess') . ' where couponid=:couponid and uniacid=:uniacid and ok=1 limit 1', array(':couponid' => $row['id'], ':uniacid' => $_W['uniacid']));
    }
    unset($row);
    $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('manor_shop_coupon') . " where 1 and {$condition}", $params);
    $pager = pagination($total, $pindex, $psize);
}elseif ($operation == 'post'){
    $id = intval($_GPC['id']);
    if (empty($id)){
        ca('coupon.coupon.add');
    }else{
        ca('coupon.coupon.view|coupon.coupon.edit');
    }
    if (checksubmit('submit')){
        $data = array('uniacid' => $_W['uniacid'], 'couponname' => trim($_GPC['couponname']), 'coupontype' => intval($_GPC['type']), 'catid' => intval($_GPC['catid']), 'timelimit' => intval($_GPC['timelimit']), 'usetype' => intval($_GPC['usetype']), 'returntype' => intval($_GPC['returntype']), 'enough' => trim($_GPC['enough']), 'timedays' => intval($_GPC['timedays']), 'timestart' => strtotime($_GPC['time']['start']), 'timeend' => strtotime($_GPC['time']['end']), 'backtype' => intval($_GPC['backtype']), 'deduct' => trim($_GPC['deduct']), 'discount' => trim($_GPC['discount']), 'backmoney' => trim($_GPC['backmoney']), 'backcredit' => trim($_GPC['backcredit']), 'backredpack' => trim($_GPC['backredpack']), 'backwhen' => intval($_GPC['backwhen']), 'gettype' => intval($_GPC['gettype']), 'getmax' => intval($_GPC['getmax']), 'credit' => intval($_GPC['credit']), 'money' => trim($_GPC['money']), 'usecredit2' => intval($_GPC['usecredit2']), 'total' => intval($_GPC['total']), 'bgcolor' => trim($_GPC['bgcolor']), 'thumb' => save_media($_GPC['thumb']), 'remark' => trim($_GPC['remark']), 'desc' => htmlspecialchars_decode($_GPC['desc']), 'descnoset' => intval($_GPC['descnoset']), 'status' => intval($_GPC['status']), 'resptitle' => trim($_GPC['resptitle']), 'respthumb' => save_media($_GPC['respthumb']), 'respdesc' => trim($_GPC['respdesc']), 'respurl' => trim($_GPC['respurl']), 'pwdkey' => trim($_GPC['pwdkey']), 'pwdwords' => trim($_GPC['pwdwords']), 'pwdask' => trim($_GPC['pwdask']), 'pwdsuc' => trim($_GPC['pwdsuc']), 'pwdfail' => trim($_GPC['pwdfail']), 'pwdfull' => trim($_GPC['pwdfull']), 'pwdurl' => trim($_GPC['pwdurl']), 'pwdtimes' => intval($_GPC['pwdtimes']), 'pwdopen' => intval($_GPC['pwdopen']), 'pwdown' => trim($_GPC['pwdown']), 'pwdexit' => trim($_GPC['pwdexit']), 'pwdexitstr' => trim($_GPC['pwdexitstr']));
        if($type == 2) {
            $data['sys_gen'] = intval($_GPC['sys_gen']);
            $data['sys_gen_text'] = $_GPC['sys_gen_text'];
            $data['gen_times'] = intval($_GPC['gen_times']);
            if($data['sys_gen'] == 1 && !$id) {
                if($data['gen_times']<=0 || !is_numeric($data['gen_times'])) {
                    message('优惠劵数量错误', '', 'error');
                }
            }
        }
        if (!empty($id)){
            if(!empty($data['pwdkey'])){
                $pwdkey = pdo_fetchcolumn('SELECT pwdkey FROM ' . tablename('manor_shop_coupon') . ' WHERE id=:id and uniacid=:uniacid limit 1 ', array(':id' => $id, ':uniacid' => $_W['uniacid']));
                if($pwdkey != $data['pwdkey']){
                    $keyword = pdo_fetch('SELECT * FROM ' . tablename('rule_keyword') . ' WHERE content=:content and uniacid=:uniacid and id<>:id  limit 1 ', array(':content' => $data['pwdkey'], ':uniacid' => $_W['uniacid'], ':id' => $id));
                    if(!empty($keyword)){
                        message('口令关键词已存在!', '', 'error');
                    }
                }
            }
            $coupon_info = pdo_get('manor_shop_coupon', array('uniacid'=>$_W['uniacid'], 'id'=>$id));
            if($_GPC['goods_ids']) {
                $ids = '';
                $goods_arr = array_filter($_GPC['goods_ids']);

                $coupon_goods_arr = pdo_getall('manor_shop_coupon_goods', array('uniacid'=>$_W['uniacid'], 'coupon_id'=>$id));
                $has = array_column($coupon_goods_arr, 'goods_id');
                $add = array_diff($goods_arr, $has);
                $reduce = array_diff($has, $goods_arr);
                if($add) {
                    foreach($add as $item) {
                        pdo_insert('manor_shop_coupon_goods', array('uniacid'=>$_W['uniacid'], 'goods_id'=>$item, 'create_time'=>time(), 'coupon_id'=>$id));
                        $ids .= pdo_insertid().',';
                    }
                }
                if($reduce) {
                    foreach($reduce as $item) {
                        pdo_delete('manor_shop_coupon_goods', array('uniacid'=>$_W['uniacid'], 'goods_id'=>$item, 'coupon_id'=>$id));
                        $ids .= pdo_insertid().',';
                    }
                }
                if(!$add && !$reduce && $has) {
                    foreach($goods_arr as $item) {
                        $_coupon_goods_id = pdo_get('manor_shop_coupon_goods', array('coupon_id'=>$id, 'uniacid'=>$_W['uniacid'],'goods_id'=>$item), array('id'));
                        pdo_update('manor_shop_coupon_goods', array('goods_id'=>$item), array('id'=>$_coupon_goods_id['id'], 'uniacid'=>$_W['uniacid']));
                        $ids .= $_coupon_goods_id['id'].',';
                    }
                }
                $data['coupon_goods_id']= rtrim($ids, ",");
            }
            pdo_update('manor_shop_coupon', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
            if($type == 2) {
                //优惠码创建
                gen_times($coupon_info, intval($_GPC['sys_gen']), $_GPC['sys_gen_text'], intval($_GPC['gen_times']), 2);
            }
            plog('coupon.coupon.edit', "编辑优惠券 ID: {$id} <br/>优惠券名称: {$data['couponname']}");
        }else{
            if(!empty($data['pwdkey'])){
                $keyword = pdo_fetch('SELECT * FROM ' . tablename('rule_keyword') . ' WHERE content=:content and uniacid=:uniacid limit 1 ', array(':content' => $data['pwdkey'], ':uniacid' => $_W['uniacid']));
                if(!empty($keyword)){
                    message('口令关键词已存在!', '', 'error');
                }
            }
            $data['createtime'] = time();
            $data['coupon_goods_id'] = '';
            pdo_insert('manor_shop_coupon', $data);
            $id = pdo_insertid();
            $data['id'] = $id;
            //单品操作
            $ids = '';
            if($_GPC['goods_ids'][0]) {
                $goods_arr = array_filter($_GPC['goods_ids']);
                if(!$goods_arr) {
                    message('请选择正确的单品商品!', '', 'error');
                }
                foreach($goods_arr as $item) {
                    pdo_insert('manor_shop_coupon_goods', array('uniacid'=>$_W['uniacid'], 'goods_id'=>$item, 'create_time'=>time(), 'coupon_id'=>$id));
                    $ids .= pdo_insertid().',';
                }
                $coupon_goods_id = rtrim($ids, ","  );
                pdo_update('manor_shop_coupon', array('coupon_goods_id'=>$ids), array('id'=>$id, 'uniacid'=>$_W['uniacid']));
            }
            //单品操作结束
            if($type == 2) {
                //优惠码创建
                gen_times($data, intval($_GPC['sys_gen']), $_GPC['sys_gen_text'], intval($_GPC['gen_times']), 1);
            }
            plog('coupon.coupon.add', "添加优惠券 ID: {$id}  <br/>优惠券名称: {$data['couponname']}");
        }
        $key = 'manor_shop:coupon:' . $id;
        $rule = pdo_fetch('select * from ' . tablename('rule') . ' where uniacid=:uniacid and module=:module and name=:name  limit 1', array(':uniacid' => $_W['uniacid'], ':module' => 'manor_shop', ':name' => $key));
        if(!empty($data['pwdkey'])){
            if (empty($rule)){
                $rule_data = array('uniacid' => $_W['uniacid'], 'name' => $key, 'module' => 'manor_shop', 'displayorder' => 0, 'status' => $data['pwdopen']);
                pdo_insert('rule', $rule_data);
                $rid = pdo_insertid();
                $keyword_data = array('uniacid' => $_W['uniacid'], 'rid' => $rid, 'module' => 'manor_shop', 'content' => $data['pwdkey'], 'type' => 1, 'displayorder' => 0, 'status' => $data['pwdopen']);
                pdo_insert('rule_keyword', $keyword_data);
            }else{
                pdo_update('rule_keyword', array('content' => $data['pwdkey'], 'status' => $data['pwdopen']), array('rid' => $rule['id']));
            }
        }else{
            if(!empty($rule)){
                pdo_delete('rule_keyword', array('rid' => $rule['id']));
                pdo_delete('rule', array('id' => $rule['id']));
            }
        }
        message('更新优惠券成功！', $this -> createPluginWebUrl('coupon/coupon'), 'success');
    }
    $item = pdo_fetch('SELECT * FROM ' . tablename('manor_shop_coupon') . ' WHERE id =:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
    if (empty($item)){
        $starttime = time();
        $endtime = strtotime(date('Y-m-d H:i:s', $starttime) . '+7 days');
    }else{
        $type = $item['coupontype'];
        $starttime = $item['timestart'];
        $endtime = $item['timeend'];
    }
    if($item['coupon_goods_id']) {
        $goods_arr = pdo_getall('manor_shop_coupon_goods', array('coupon_id'=>$id, 'uniacid'=>$_W['uniacid']), array('goods_id'));
        if($goods_arr) {
            $item['goods_id'] = array_column($goods_arr, 'goods_id');
        }else {
            $item['goods_id'] = false;
        }
    }
    $goods = pdo_getall('manor_shop_goods', array('deleted'=>0, 'uniacid'=>$_W['uniacid']), array('id', 'title'));
}elseif ($operation == 'delete'){
    ca('coupon.coupon.delete');
    $id = intval($_GPC['id']);
    $item = pdo_fetch('SELECT id,couponname,coupontype FROM ' . tablename('manor_shop_coupon') . ' WHERE id =:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
    if (empty($item)){
        message('抱歉，优惠券不存在或是已经被删除！', $this -> createPluginWebUrl('coupon/coupon', array('op' => 'display')), 'error');
    }
    pdo_delete('manor_shop_coupon', array('id' => $id, 'uniacid' => $_W['uniacid']));
    $couponids = pdo_fetchall('select id from ' . tablename('manor_shop_coupon') . ' where uniacid=:uniacid', array(':uniacid' => $_W['uniacid']), 'id');
    if(!empty($couponids)){
        pdo_query('delete from ' . tablename('manor_shop_coupon_data') . ' where couponid not in (' . implode(',', array_keys($couponids)) . ') and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
    }
    pdo_delete('manor_shop_coupon_goods', array('coupon_id' => $id, 'uniacid' => $_W['uniacid']));
    pdo_delete('manor_shop_coupon_data', array('couponid' => $id, 'uniacid' => $_W['uniacid']));
    plog('coupon.coupon.delete', "删除优惠券 ID: {$id}  <br/>优惠券名称: {$item['couponname']} ");

    if($item['coupontype'] == 2) {
        del_gen($item['id']);
    }
    if($item['coupontype'] ==2 ) {
        pdo_delete('manor_shop_coupon_gen', array('uniacid'=>$_W['uniacid'], 'coupon_id'=>$item['id']));
    }
    message('优惠券删除成功！', $this -> createPluginWebUrl('coupon/coupon', array('op' => 'display')), 'success');
}else if ($operation == 'query'){
    $kwd = trim($_GPC['keyword']);
    $params = array();
    $params[':uniacid'] = $_W['uniacid'];
    $condition = ' and uniacid=:uniacid';
    if (!empty($kwd)){
        $condition .= ' AND couponname like :couponname';
        $params[':couponname'] = "%{$kwd}%";
    }
    $time = time();
    $ds = pdo_fetchall('SELECT * FROM ' . tablename('manor_shop_coupon') . "  WHERE 1 {$condition} ORDER BY id asc", $params);
    foreach ($ds as & $d){
        $d = $this -> model -> setCoupon($d, $time, false);
        $d['last'] = $this -> model -> get_last_count($d['id']);
        if ($d['last'] == -1){
            $d['last'] = '不限';
        }
    }
    unset($d);
    include $this -> template('coupon/query');
    exit;
}else if ($operation=='ticket'){

    $id = intval($_GPC['id']);
    //echo $id;die;
    if (empty($id)){
        ca('coupon.coupon.add');
    }else{   
        ca('coupon.coupon.edit');
    }

    $coupon = pdo_getall('manor_shop_coupon', array( 'uniacid'=>$_W['uniacid']), array('id', 'couponname'));

    $_coupon = json_encode($coupon);
   
    if (checksubmit('submit')){

         if($_GPC['average']==0){
              
             $catid=$_GPC['catid']; 
             $couponname = pdo_get('manor_shop_coupon', array( 'uniacid'=>$_W['uniacid'],'id'=>$_GPC['catid']), array('couponname'));
             $couponname=$couponname['couponname'];

         }else{

             $catid=$_GPC['catids'];
             $couponname= pdo_get('manor_shop_coupon_category',array( 'uniacid'=>$_W['uniacid'],'id'=>$_GPC['catids']), array('name'));
             $couponname=$couponname['name'];
         }
   
        $data = array('uniacid' => $_W['uniacid'], 'fissionname' => trim($_GPC['fissionname']),'average'=>intval($_GPC['average']),'total'=>intval($_GPC['total']),'createtime'=>time(),'status'=>intval($_GPC['status']),'catid'=>intval($catid),'orderprice'=>$_GPC['orderprice'],'orders'=>'0','grant'=>'0','couponname'=>$couponname,'effective'=>$_GPC['effective']);

        if($id==0){

             pdo_insert('manor_shop_coupon_fission', $data);

             message('裂变券添加成功', $this -> createPluginWebUrl('coupon/coupon', array('op' => 'fission')), 'success');
        }else{
         
              pdo_update('manor_shop_coupon_fission',$data,array('id'=>$id));
               message('裂变券更新成功', $this -> createPluginWebUrl('coupon/coupon', array('op' => 'fission')), 'success');
        }

       

    }

    $item = pdo_fetch('SELECT * FROM ' . tablename('manor_shop_coupon_fission') . ' WHERE id =:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
  


}else if($operation=='fission'){

    if (!empty($_GPC['displayorder'])){

        ca('coupon.coupon.edit');
        foreach ($_GPC['displayorder'] as $id => $displayorder){
            
            pdo_update('manor_shop_coupon_fission', array('orders' => $displayorder), array('id' => $id));
        }

        plog('coupon.coupon.edit', '批量修改排序');
        message('分类排序更新成功！', $this -> createPluginWebUrl('coupon/coupon', array('op' => 'fission')), 'success');
    }

    $fission=pdo_fetchall('select * from ' . tablename('manor_shop_coupon_fission') . ' where uniacid=:uniacid order by orders desc', array(':uniacid' => $_W['uniacid']));
     //$fission = pdo_getall('manor_shop_coupon_fission', array( 'uniacid'=>$_W['uniacid']));
    // print_r($fission);die;

     
}else if($operation=='sharecoupon'){
    
      $share = pdo_getall('manor_shop_coupon_share', array( 'uniacid'=>$_W['uniacid']));
   

}else if($operation==='deletefission'){

    ca('coupon.coupon.delete');
    $id = intval($_GPC['id']);
    $item = pdo_fetch('SELECT id FROM ' . tablename('manor_shop_coupon_fission') . ' WHERE id =:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
    if (empty($item)){
        message('抱歉，优惠券不存在或是已经被删除！', $this -> createPluginWebUrl('coupon/coupon', array('op' => 'fission')), 'error');
    }

    pdo_delete('manor_shop_coupon_fission', array('id' => $id, 'uniacid' => $_W['uniacid']));

    message('优惠券删除成功！', $this -> createPluginWebUrl('coupon/coupon', array('op' => 'fission')), 'success');

}else if($operation=='deleteshare'){
     
     ca('coupon.coupon.delete');
    $id = intval($_GPC['id']);
    $item = pdo_fetch('SELECT id FROM ' . tablename('manor_shop_coupon_share') . ' WHERE id =:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
    if (empty($item)){
        message('抱歉，分享券不存在或是已经被删除！', $this -> createPluginWebUrl('coupon/coupon', array('op' => 'sharecoupon')), 'error');
    }

    pdo_delete('manor_shop_coupon_share', array('id' => $id, 'uniacid' => $_W['uniacid']));

    message('分享券删除成功！', $this -> createPluginWebUrl('coupon/coupon', array('op' => 'sharecoupon')), 'success');
}
$category = pdo_fetchall('select * from ' . tablename('manor_shop_coupon_category') . ' where uniacid=:uniacid order by id desc', array(':uniacid' => $_W['uniacid']), 'id');
load() -> func('tpl');
include $this -> template('coupon');


    /*
     * @author 兰辉
     * @access public
     * @param $coupon_id 优惠劵id
     * @param $sys_gen 是否系统生成
     * @param $sys_gen_text 劵码集合
     * @param $gen_times 生成数量
     * @param $type 类型 1 新增 2 编辑
     * @since  1.0
     */
    function gen_times($coupon, $sys_gen, $sys_gen_text, $gen_times, $type = 1) {
        global  $_GPC, $_W;
        unset($coupon['desc']);
        $gen = array(
            'uniacid'=>$_W['uniacid'],
            'coupon_id'=>$coupon['id'],
            'coupon_data'=>json_encode($coupon),
            'create_time'=>TIMESTAMP,
            'coupon_name'=>$coupon['couponname'],
        );
        if($sys_gen) {
            if($gen_times <=0 && !is_numeric($gen_times)) {
                return true;
            }
            if($type == 1) {
                $pa = 'a';
                $num = $gen_times;
            } else if($type == 2){
                $pa = 'e';
                $num = $coupon['gen_times'] + $gen_times;
            } else {
                $pa = '';
                $num = $gen_times;
            }
            $code = unique_rand(100000, 999999, $gen_times, $pa.$coupon['id']);
            foreach($code as $item) {
                $gen['code'] = $item;
                pdo_insert('manor_shop_coupon_gen', $gen);
            }
        } else {
            $rr = array_filter(explode("\n", $sys_gen_text));
            $rrr = array();
            if($rr) {
                foreach($rr as $item) {
                    $rrr[] = rtrim($item);
                }
            }
            $sys_gen_text_arr = array_unique($rrr);
            $is_exist = false;
            $num = 0;
            if($sys_gen_text_arr) {
                $stt = '';
                foreach($sys_gen_text_arr as $item) {
                    if(!pdo_get('manor_shop_coupon_gen', array('uniacid'=>$_W['uniacid'], 'code'=>$item))) {
                        $num++;
                        $gen['code'] = $item;
                        $stt .= $item.'<br>';
                        pdo_insert('manor_shop_coupon_gen', $gen);
                    };
                }
            }else{
                return true;
            }
            if($type == 2) {
                $stt = $coupon['sys_gen_text'].$stt;
                $num = intval($coupon['gen_times']) + $num;

            }
        }
        pdo_update('manor_shop_coupon', array('gen_times'=>$num,'total'=>$num, 'sys_gen_text'=>$stt), array('id'=>$coupon['id']));
        return $num;
    }
    function del_gen($coupon_id) {
        global $_W,$_GPC;
        pdo_delete('manor_shop_coupon_gen', array('coupon_id'=>$coupon_id, 'uniacid'=>$_W['uniacid']));
        return true;
    }
