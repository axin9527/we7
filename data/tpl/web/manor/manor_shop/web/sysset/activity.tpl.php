<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/_header', TEMPLATE_INCLUDEPATH)) : (include template('web/_header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/sysset/tabs', TEMPLATE_INCLUDEPATH)) : (include template('web/sysset/tabs', TEMPLATE_INCLUDEPATH));?>
<div class="main">
    <form id="dataform"    <?php if(cv('sale.recharge.save')) { ?>action="" method="post"<?php  } ?> class="form-horizontal form">
    <div class="panel panel-default">
        <ul class="nav nav-tabs">
            <li <?php  if($_GPC['pp']=='haier') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('sysset',array('op'=>'activity', 'pp'=>'haier'))?>">海尔优惠劵设置</a></li>
             <li <?php  if($_GPC['pp']=='ayimabang') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('sysset',array('op'=>'activity', 'pp'=>'ayimabang'))?>">阿姨帮活动设置</a></li>
             <!-- <li <?php  if($_GPC['pp']=='shuangshiyi') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('sysset',array('op'=>'activity', 'pp'=>'shuangshiyi'))?>">双十一设置</a></li>-->
             <li <?php  if($_GPC['pp']=='send_grapefruit') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('sysset',array('op'=>'activity', 'pp'=>'send_grapefruit'))?>">0元送柚子设置</a></li>
        </ul>
        <?php  if($_GPC['pp'] == 'haier') { ?>
        <div class="panel-heading">
            海尔优惠劵设置
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">海尔优惠劵开关</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sale.enough.save')) { ?>
                    <label class="radio-inline">
                        <input type="radio" name="shop[haier_power]" value='1' <?php  if($set['shop']['haier_power']==1) { ?>checked<?php  } ?> /> 开启
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="shop[haier_power]" value='0' <?php  if(empty($set['shop']['haier_power'])) { ?>checked<?php  } ?> /> 关闭
                    </label>
                    <span class='help-block'>开启海尔优惠劵开关, 可在领取页面进行优惠劵设置的领取进行领取</span>
                    <?php  } else { ?>
                    <div class='form-control-static'><?php  if($set['shop']['haier_power']==1) { ?>开启<?php  } else { ?>关闭<?php  } ?></div>
                    <?php  } ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">赠送优惠劵列表</label>
                <div class="col-sm-4">
                    <div class='recharge-items'>
                        <?php  if(is_array($set['shop']['haier_coupon'])) { foreach($set['shop']['haier_coupon'] as $item) { ?>
                        <div class="input-group recharge-item" style="margin-top:5px">
                            <select class="form-control" name="haier_coupon[]" >
                                <option selected value="">请选择</option>
                                <?php  if(is_array($coupon)) { foreach($coupon as $k => $v) { ?>
                                <option <?php  if($v['id'] == $item) { ?> selected<?php  } ?> value="<?php  echo $v['id'];?>"><?php  echo $v['couponname'];?></option>
                                <?php  } } ?>
                            </select>
                            <div class='input-group-btn'>
                                <button class='btn btn-danger' type='button' onclick="removeRechargeItem(this)"><i class='fa fa-remove'></i></button>
                            </div>

                        </div>
                        <?php  } } ?>
                    </div>

                    <div style="margin-top:5px">
                        <button type='button' class="btn btn-default" onclick='addRechargeItem()' style="margin-bottom:5px"><i class='fa fa-plus'></i> 增加优卷</button>
                    </div>
                </div>
            </div>


            <?php if(cv('sale.recharge.save')) { ?>

            <?php  } ?>
        </div>
        <?php  } else if($_GPC['pp'] == 'ayimabang') { ?>
        <div class="panel-heading">
            阿姨帮设置
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">阿姨帮活动开关</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sale.enough.save')) { ?>
                    <label class="radio-inline">
                        <input type="radio" name="shop[yimabang_power]" value='1' <?php  if($set['shop']['yimabang_power']==1) { ?>checked<?php  } ?> /> 开启
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="shop[yimabang_power]" value='0' <?php  if(empty($set['shop']['yimabang_power'])) { ?>checked<?php  } ?> /> 关闭
                    </label>
                    <span class='help-block'>开启阿姨帮优惠劵开关, 可在领取页面进行优惠劵设置的领取进行领取</span>
                    <?php  } else { ?>
                    <div class='form-control-static'><?php  if($set['shop']['yimabang_power']==1) { ?>开启<?php  } else { ?>关闭<?php  } ?></div>
                    <?php  } ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">赠送优惠劵组</label>
                <div class="col-sm-4">
                    <div class="input-group recharge-item">
                        <select class="form-control" style="min-width: 600px" name="shop[ayibang_coupon]" >
                            <option selected value="">请选择</option>
                            <?php  if(is_array($coupon_group)) { foreach($coupon_group as $k => $v) { ?>
                            <option <?php  if($v['id'] == $set['shop']['yimabang_coupon']) { ?> selected<?php  } ?> value="<?php  echo $v['id'];?>"><?php  echo $v['name'];?></option>
                            <?php  } } ?>
                        </select>
                    </div>
                    <span class='help-block'>优惠卷劵组包包含一组特定优惠卷，发放将对劵组包内所以优惠卷一同发放，除非存在无库存，建议设置无限制数量达到统一劵码数量，劵组数量以本设置为主，例如下面设置了发放100组，那将会对应发放100组优惠卷，所以建议优惠卷统一设置为无限制数量</span>
                    <!-- <div style="margin-top:5px">
                        <button type='button' class="btn btn-default" onclick='addRechargeItem()' style="margin-bottom:5px"><i class='fa fa-plus'></i> 增加优卷</button>
                    </div>-->
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">选择二维码</label>
                <div class="col-sm-4">
                    <div class="input-group recharge-item">
                        <select class="form-control" style="min-width: 600px" name="shop[ayibang_qr]" >
                            <option selected value="">请选择</option>
                            <?php  if(is_array($qr)) { foreach($qr as $k => $v) { ?>
                            <option <?php  if($v['id'] == $set['shop']['ayibang_qr']) { ?> selected<?php  } ?> value="<?php  echo $v['id'];?>"><?php  echo $v['name'];?></option>
                            <?php  } } ?>
                        </select>
                    </div>
                    <span class='help-block'>选择阿姨帮对应的二维码</span>
                </div>
            </div>
        </div>
        <?php  } else if($_GPC['pp'] == 'shuangshiyi') { ?>
        <div class="panel-heading">
            双十一设置
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">活动开关</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sale.enough.save')) { ?>
                    <label class="radio-inline">
                        <input type="radio" name="shop[shuangshiyi_power]" value='1' <?php  if($set['shop']['shuangshiyi_power']==1) { ?>checked<?php  } ?> /> 开启
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="shop[shuangshiyi_power]" value='0' <?php  if(empty($set['shop']['shuangshiyi_power'])) { ?>checked<?php  } ?> /> 关闭
                    </label>
                    <span class='help-block'>开启该活动开关, 可进行生成图片操作</span>
                    <?php  } else { ?>
                    <div class='form-control-static'><?php  if($set['shop']['shuangshiyi_power']==1) { ?>开启<?php  } else { ?>关闭<?php  } ?></div>
                    <?php  } ?>
                </div>
            </div>
            <!-- <div class="form-group" id="tuijian" <?php  if($set['shop']['shuangshiyi'] === 0) { ?>style="display:none"<?php  } ?>>
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">上传生成图</label>
            <div class="col-sm-9 col-xs-12">
                <?php if(cv('sysset.save.activity')) { ?>
                <?php  echo tpl_form_field_multi_image('shop[shuangshiyi]', $set['shop']['shuangshiyi'])?>
                <span class='help-block'>设置默认图片</span>
                <?php  } else { ?>
                <input type="hidden" name="shop[shuangshiyi]" value="<?php  echo $set['shop']['shuangshiyi'];?>" />
                <?php  if(!empty($set['shop']['shuangshiyi'])) { ?>
                <a href='<?php  echo tomedia($set['shop']['shuangshiyi'])?>' target='_blank'>
                <img src="<?php  echo tomedia($set['shop']['shuangshiyi'])?>" style='width:200px;border:1px solid #ccc;padding:1px' />
                </a>
                <?php  } ?>
                <?php  } ?>
            </div>
        </div>-->
        </div>
    <?php  } else if($_GPC['pp'] == 'send_grapefruit') { ?>
        <!-- <div class="panel-heading">
            0元抽送柚子参与列表
        </div>-->
        <div class="panel-body">
            <form action="" method="get" class='form form-horizontal'>
                <div class="panel panel-info">
                    <div class="panel-heading">筛选</div>
                    <div class="panel-body">
                        <form action="./index.php" method="get" class="form-horizontal" plugins="form">
                            <input type="hidden" name="c" value="site" />
                            <input type="hidden" name="a" value="entry" />
                            <input type="hidden" name="m" value="manor_shop" />
                            <input type="hidden" name="do" value="shop" />
                            <input type="hidden" name="pp"  value="send_grapefruit" />
                            <input type="hidden" name="op" value="activity" />
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">关键词</label>
                                <div class="col-xs-12 col-sm-8 col-lg-9">
                                    <input class="form-control" name="keyword" id="" type="text" value="<?php  echo $_GPC['keyword'];?>" placeholder="可搜索姓名/手机号">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">按时间</label>
                                <div class="col-sm-2">
                                    <select name='searchtime' class='form-control'>
                                        <option value='' <?php  if(empty($_GPC['searchtime'])) { ?>selected<?php  } ?>>不搜索</option>
                                        <option value='1' <?php  if($_GPC['searchtime']==1) { ?>selected<?php  } ?> >搜索</option>
                                    </select>
                                </div>
                                <div class="col-sm-7 col-lg-9 col-xs-12">
                                    <?php  echo tpl_form_field_daterange('time', array('starttime'=>date('Y-m-d H:i', $starttime),'endtime'=>date('Y-m-d  H:i', $endtime)),true);?>
                                </div>

                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label"> </label>
                                <div class="col-xs-12 col-sm-2 col-lg-2">
                                    <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

                <div class='panel panel-default'>
                    <div class='panel-heading' >
                        参与列表 (数量: <?php  echo $total;?>  条)

                    </div>
                    <div class='panel-body'>

                        <table class="table">
                            <thead>
                            <tr>
                                <th style='width:250px;'>用户</th>
                                <th style='width:200px;'>手机号</th>
                                <th style='width:150px;'>截图</th>
                                <th style='width:300px;' >收获地址</th>
                                <th style='width:100px;'>状态</th>
                                <th  style='width:150px;'>提交时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php  if($list) { ?>
                            <?php  if(is_array($list)) { foreach($list as $row) { ?>
                            <tr>
                                <td>
                                    <img src="<?php  echo $row['avastar'];?>" style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;border-radius: 16px;">
                                    昵称：<?php  echo $row['nickname'];?>  姓名:<?php  echo $row['real_name'];?>

                                </td>
                                <td><?php  echo $row['mobile'];?></td>
                                <td style="color:#ff6600">
                                    <a href="<?php  echo tomedia($row['thumb'])?>" target="_blank"><img title="点击查看完整图片" class="caption" src="<?php  echo tomedia($row['thumb'])?>" style="width: 50px; height: 50px;border-radius: 30px;border:1px solid #ccc;padding:1px;"></a>
                                </td>
                                <td><?php  echo $row['address'];?></td>
                                <td>
                                    <?php  if($row['status'] == 1) { ?> 已抽中<?php  } else { ?>未抽中<?php  } ?>
                                </td>
                                <td ><?php  echo date('Y-m-d H:i:s', $row['createtime'])?></td>
                                <td>
                                    <a class='btn btn-default'  href="<?php  echo $this->createWebUrl('sysset', array('op' => 'activity','pp'=>'send_grapefruit', 'act'=>'set', 'id' => $row['id']))?>" title='设置中奖'><i class="fa fa-cog"></i></a>
                                    <a class='btn btn-default'  href="<?php  echo $this->createWebUrl('sysset', array('op' => 'activity','pp'=>'send_grapefruit','act'=>'del', 'id' => $row['id']))?>" title='删除记录'><i class="fa fa-times"></i></a>
                                </td>
                            </tr>
                            <?php  } } ?>
                            <?php  } else { ?>
                            <tr style="text-align: center">
                                <td colspan="7">暂无数据</td>
                            </tr>
                            <?php  } ?>
                            </tbody>
                        </table>
                        <?php  echo $pager;?>

                    </div>
                </div>
            </form>
            <!-- <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">活动开关</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sale.enough.save')) { ?>
                    <label class="radio-inline">
                        <input type="radio" name="shop[grapefruit_power]" value='1' <?php  if($set['shop']['grapefruit_power']==1) { ?>checked<?php  } ?> /> 开启
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="shop[grapefruit_power]" value='0' <?php  if(empty($set['shop']['grapefruit_power'])) { ?>checked<?php  } ?> /> 关闭
                    </label>
                    <span class='help-block'>是否开启该活动开关</span>
                    <?php  } else { ?>
                    <div class='form-control-static'><?php  if($set['shop']['grapefruit_power']==1) { ?>开启<?php  } else { ?>关闭<?php  } ?></div>
                    <?php  } ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">赠送优惠劵组</label>
                <div class="col-sm-9">
                    <div class='recharge-items'>
                        <div class="input-group recharge-item">
                            <select class="form-control" style="min-width: 600px" name="shop[grapefruit_coupon]" >
                                <option selected value="">请选择</option>
                                <?php  if(is_array($coupon_group)) { foreach($coupon_group as $k => $v) { ?>
                                <option <?php  if($v['id'] == $set['shop']['grapefruit_coupon']) { ?> selected<?php  } ?> value="<?php  echo $v['id'];?>"><?php  echo $v['name'];?></option>
                                <?php  } } ?>
                            </select>
                        </div>
                        <span class='help-block'>优惠卷劵组包包含一组特定优惠卷，发放将对劵组包内所以优惠卷一同发放，除非存在无库存，建议设置无限制数量达到统一劵码数量，劵组数量以本设置为主，例如下面设置了发放100组，那将会对应发放100组优惠卷，所以建议优惠卷统一设置为无限制数量</span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">劵组数量</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if(cv('sysset.save.activity')) { ?>
                    <input type="number" name="shop[grapefruit_num]" class="form-control" value="<?php  echo $set['shop']['grapefruit_num'];?>" />
                    <?php  } else { ?>
                    <input type="hidden" name="shop[grapefruit_num]" value="<?php  echo $set['shop']['grapefruit_num'];?>" />
                    <div class='form-control-static'><?php  echo $set['shop']['grapefruit_num'];?></div>
                    <?php  } ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">已发放</label>
                <div class="col-sm-9 col-xs-12">
                    <div class='form-control-static'><?php  echo $set['shop']['grapefruit_use_num'];?></div>
                </div>
            </div>-->
        </div>
        <?php  } ?>
    </div>
    <?php  if($_GPC['pp'] != 'send_grapefruit') { ?>
    <div class="form-group"></div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
        <div class="col-sm-9 col-xs-12">

            <input type="submit" name="submit"  value="保存设置" class="btn btn-primary"/>
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
        </div>
    </div>
    <?php  } ?>
    </form>
</div>
<script language='javascript'>
    var coupon = '<?php  echo $_coupon;?>';
    function addRechargeItem(){
        var eval_goods = eval("("+coupon+")");
        var option = '<option>请选择</option>';
        $.each(eval_goods,function (k,v) {
            option += "<option value='"+v.id+"'>"+v.couponname+"</option>";
        });
        var html= '<div class="input-group recharge-item"  style="margin-top:5px">';
        html += '<select class="form-control" name="haier_coupon[]" >';
        html += option;
        html += '</select>';
        html+='<div class="input-group-btn"><button type="button" class="btn btn-danger" onclick="removeRechargeItem(this)"><i class="fa fa-remove"></i></button></div>';
        html+='</div>';
        $('.recharge-items').append(html);
    }
    function removeRechargeItem(obj){
        $(obj).closest('.recharge-item').remove();
    }

</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/_footer', TEMPLATE_INCLUDEPATH)) : (include template('web/_footer', TEMPLATE_INCLUDEPATH));?>
