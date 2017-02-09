<?php
	/**
	 * 领取裂变券
	 * 操作类名称: sublime.
	 * 作者名称: wangyanqi 
	 * 创建时间: 16/12/4 19:10
	 */
	global $_W, $_GPC;
     $ordersn=$_GPC['ordersn'];
     $fid=$_GPC['fid'];
     $openid = m('user') -> getOpenid();
  
     if($_GPC['op']=='get_coupon'){
    
        $couponid=$_GPC['couponid'];
        $ordersn=$_GPC['ordersn'];

         $qu=pdo_fetch('select id  from ' . tablename('manor_shop_coupon_data') . ' where  uniacid=:uniacid and openid=:openid  and couponid=:couponid and gettype=:gettype', array(':uniacid' => $_W['uniacid'],':openid'=>$openid,':couponid'=>$couponid,':gettype'=>'4'));
         if(empty($qu)){

           $share=pdo_fetch('select receivenum,surplusnum  from ' . tablename('manor_shop_coupon_share') . ' where  uniacid=:uniacid and ordersn=:ordersn', array(':uniacid' => $_W['uniacid'],':ordersn'=>$ordersn));
           $re=pdo_update('manor_shop_coupon_share',array('receivenum'=>$share['receivenum']+1,'surplusnum'=>$share['surplusnum']-1),array('ordersn'=>$ordersn,'uniacid'=>$_W['uniacid']));

           $log = array('uniacid' => $_W['uniacid'], 'openid' => $openid, 'logno' => m('common') -> createNO('coupon_log', 'logno', 'CC'), 'couponid' => $couponid, 'status' => 1, 'paystatus' => -1, 'creditstatus' => -1, 'createtime' => time(), 'getfrom' => 0);
            pdo_insert('manor_shop_coupon_log', $log);

           $data = array('uniacid' => $_W['uniacid'], 'openid' => $openid, 'couponid' => $couponid, 'gettype' => 4, 'gettime' => time());
           $status=pdo_insert('manor_shop_coupon_data', $data);

            if($status){
               show_json(1,'领取成功,点击下方进入商城去狂欢');
             }else{
               show_json(2,'领取失败,可能是网不好');
             }
         }else{

             show_json(0,'请不要重复领取,去商城消费吧 ');
         }

     }else{


      $row=pdo_fetch('select surplusnum  from ' . tablename('manor_shop_coupon_share') . ' where  uniacid=:uniacid and ordersn =:ordersn and fid=:fid', array(':uniacid' => $_W['uniacid'],':ordersn'=>$ordersn,':fid'=>$fid));
       if(empty($row)){

            echo "活动有误";

       }else if($row['surplusnum']<=0){

           echo "券已领完";
           
       }else{

          $fission=pdo_fetch('select average,catid  from ' . tablename('manor_shop_coupon_fission') . ' where  uniacid=:uniacid and id=:fid', array(':uniacid' => $_W['uniacid'],':fid'=>$fid));
          
          if($fission['average']==0){

            $couponname=pdo_fetch('select *  from ' . tablename('manor_shop_coupon') . ' where  uniacid=:uniacid and id=:id', array(':uniacid' => $_W['uniacid'],':id'=>$fission['catid']));


          }else{

             $couponname=pdo_fetch('select *  from ' . tablename('manor_shop_coupon') . ' where  uniacid=:uniacid and catid=:id', array(':uniacid' => $_W['uniacid'],':id'=>$fission['catid']));
                

          }

             include $this->template('activity/fission');

       }

     }
     
 
	
	




