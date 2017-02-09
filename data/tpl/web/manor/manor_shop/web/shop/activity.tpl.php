<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/_header', TEMPLATE_INCLUDEPATH)) : (include template('web/_header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/shop/tabs', TEMPLATE_INCLUDEPATH)) : (include template('web/shop/tabs', TEMPLATE_INCLUDEPATH));?>
<style>
    .group_list{border: 1px dotted #ccc;padding: 2px 3px;font-size: 12px;cursor: pointer;background: #ccc;}
</style>
<link href="../addons/manor_shop/plugin/designer/template/imgsrc/designer.css" rel="stylesheet">
<script type="text/javascript" src="../addons/manor_shop/plugin/designer/template/imgsrc/angular.min.js"></script>
<?php  if($operation == 'post') { ?>
<div class="main">
    
    <form  <?php if( ce('shop.activity' ,$item) ) { ?>action="" method="post"<?php  } ?> class="form-horizontal form" enctype="multipart/form-data" >
    
        <div class="panel panel-default">
            <div class="panel-heading">
                商品活动
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                    <div class="col-sm-9 col-xs-12">
                        <?php if( ce('shop.activity' ,$item) ) { ?>
                           <input type="text" name="displayorder" class="form-control" value="<?php  echo $item['displayorder'];?>" />
                        <?php  } else { ?>
                           <div class='form-control-static'><?php  echo $item['displayorder'];?></div>
                        <?php  } ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>活动标题</label>
                    <div class="col-sm-9 col-xs-12">
                         <?php if( ce('shop.activity' ,$item) ) { ?>
                        <input type="text" name="catename" class="form-control" value="<?php  echo $item['name'];?>" />
                           <?php  } else { ?>
                           <div class='form-control-static'><?php  echo $item['name'];?></div>
                        <?php  } ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">活动副标题</label>
                    <div class="col-sm-9 col-xs-12">
                         <?php if( ce('shop.activity' ,$item) ) { ?>
                        <input type="text" name="subtitle" class="form-control" value="<?php  echo $item['subtitle'];?>" />
                           <?php  } else { ?>
                           <div class='form-control-static'><?php  echo $item['subtitle'];?></div>
                        <?php  } ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">活动分组</label>
                    <div class="col-sm-2 col-xs-2">
                        <?php if( ce('shop.activity' ,$item) ) { ?>
                        <input type="text" name="act_group" class="form-control" value="<?php  echo $item['act_group'];?>" />
                        <?php  } else { ?>
                        <div class='form-control-static'><?php  echo $item['act_group'];?></div>
                        <?php  } ?>
                    </div>
                    <div class='form-control-static'>
                        <?php  if($act_group_list) { ?>
                        系统预设:
                        <?php  if(is_array($act_group_list)) { foreach($act_group_list as $k => $group) { ?>
                        <?php  if($k <= 8) { ?>
                        <span class="group_list"><?php  echo $group['name'];?></span>
                        <?php  } ?>
                        <?php  } } ?>
                        <?php  } ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">活动图片</label>
                    <div class="col-sm-9 col-xs-12">
                      <?php if( ce('shop.activity' ,$item) ) { ?>
                             <?php  echo tpl_form_field_image('thumb', $item['thumb'])?>
                            <span class="help-block">建议尺寸: 100*100，或正方型图片 </span>
                        <?php  } else { ?>
                            <?php  if(!empty($item['thumb'])) { ?>
                                  <a href='<?php  echo tomedia($item['thumb'])?>' target='_blank'>
                                <img src="<?php  echo tomedia($item['thumb'])?>" style='width:100px;border:1px solid #ccc;padding:1px' />
                                  </a>
                            <?php  } ?>
                        <?php  } ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">活动页背景图</label>
                    <div class="col-sm-9 col-xs-12">
                        <?php if( ce('shop.activity' ,$item) ) { ?>
                        <?php  echo tpl_form_field_image('background_img', $item['background_img'])?>
                        <span class="help-block"> </span>
                        <?php  } else { ?>
                        <?php  if(!empty($item['background_img'])) { ?>
                        <a href='<?php  echo tomedia($item['background_img'])?>' target='_blank'>
                        <img src="<?php  echo tomedia($item['background_img'])?>" style='width:100px;border:1px solid #ccc;padding:1px' />
                        </a>
                        <?php  } ?>
                        <?php  } ?>
                        <span class='help-block show_h'>可以不设置,不设置背景默认为白色。</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">活动描述</label>
                    <div class="col-sm-9 col-xs-12">
                        <?php if( ce('shop.activity' ,$item) ) { ?>
                        <textarea name="description" class="form-control" cols="70"><?php  echo $item['description'];?></textarea>
                        <?php  } else { ?>
                         <div class='form-control-static'><?php  echo $item['description'];?></div>
                        <?php  } ?>
                        
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">活动广告图</label>
                    <div class="col-sm-9 col-xs-12">
                        <?php if( ce('shop.activity' ,$item) ) { ?>
                        <?php  echo tpl_form_field_image('advimg', $item['advimg'])?>
                        <span class="help-block">建议尺寸: 320*640</span>
                         <?php  } else { ?>
                           <?php  if(!empty($item['advimg'])) { ?>
                                 <a href='<?php  echo tomedia($item['advimg'])?>' target='_blank'>
                                <img src="<?php  echo tomedia($item['advimg'])?>" style='width:100px;border:1px solid #ccc;padding:1px' />
                                 </a>
                           <?php  } ?>
                        <?php  } ?>
                    </div>
                </div>
                 <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">来源</label>
                    <div class="col-sm-9 col-xs-12">
                         <?php if( ce('shop.activity' ,$item) ) { ?>
                        <div class="input-group">
                            <input type="text" name="advurl" class="form-control" value="<?php  echo $item['advurl'];?>"/>
                            <span class="input-group-btn">
				                <button class="btn btn-default" type="button" id="chooseUrl">系统链接</button>
			                </span>
                        </div>
                        <?php  } else { ?>
                         <div class='form-control-static'><?php  echo $item['advurl'];?></div>
                         <?php  } ?>
                        <span class='help-block show_h'>此处可以为空,填写后将不显示下面的详情,直接跳转本链接,不填则显示详情信息</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否推荐</label>
                    <div class="col-sm-9 col-xs-12">
                            <?php if( ce('shop.activity' ,$item) ) { ?>
                        <label class='radio-inline'>
                            <input type='radio' name='isrecommand' value=1' <?php  if($item['isrecommand']==1) { ?>checked<?php  } ?> /> 是
                        </label>
                        <label class='radio-inline'>
                            <input type='radio' name='isrecommand' value=0' <?php  if($item['isrecommand']==0) { ?>checked<?php  } ?> /> 否
                        </label>
                            <?php  } else { ?>
                           <div class='form-control-static'><?php  if(empty($item['isrecommand'])) { ?>否<?php  } else { ?>是<?php  } ?></div>
                           <?php  } ?>
                    </div> 
                </div>
               <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">首页推荐</label>
                    <div class="col-sm-9 col-xs-12">
                            <?php if( ce('shop.activity' ,$item) ) { ?>
                        <label class='radio-inline'>
                            <input type='radio' name='ishome' value=1' <?php  if($item['ishome']==1) { ?>checked<?php  } ?> /> 是
                        </label>
                        <label class='radio-inline'>
                            <input type='radio' name='ishome' value=0' <?php  if($item['ishome']==0) { ?>checked<?php  } ?> /> 否
                        </label>
                           <?php  } else { ?>
                           <div class='form-control-static'><?php  if(empty($item['ishome'])) { ?>否<?php  } else { ?>是<?php  } ?></div>
                           <?php  } ?>
                    </div> 
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否显示</label>
                    <div class="col-sm-9 col-xs-12">
                        <?php if( ce('shop.activity' ,$item) ) { ?>
                        <label class='radio-inline'>
                            <input type='radio' name='enabled' value=1 <?php  if($item['enabled']==1) { ?>checked<?php  } ?> /> 是
                        </label>
                        <label class='radio-inline'>
                            <input type='radio' name='enabled' value=0 <?php  if($item['enabled']==0) { ?>checked<?php  } ?> /> 否
                        </label>
                          <?php  } else { ?>
                           <div class='form-control-static'><?php  if(empty($item['enabled'])) { ?>否<?php  } else { ?>是<?php  } ?></div>
                           <?php  } ?>
                    </div>
                </div>
                <!-- <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否自提</label>
                    <div class="col-sm-9 col-xs-12">
                        <?php if( ce('shop.activity' ,$item) ) { ?>
                        <label class='radio-inline'>
                            <input type='radio' name='is_mention' class="is_mention" value=1 <?php  if($item['is_mention']==1) { ?>checked<?php  } ?> /> 是
                        </label>
                        <label class='radio-inline'>
                            <input type='radio' name='is_mention' class="is_mention" value=0 <?php  if($item['is_mention']==0) { ?>checked<?php  } ?> /> 否
                        </label>
                        <?php  } else { ?>
                        <div class='form-control-static'><?php  if(empty($item['is_mention'])) { ?>否<?php  } else { ?>是<?php  } ?></div>
                        <?php  } ?>
                    </div>
                </div>-->
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">活动详情</label>
                    <div class="col-sm-9 col-xs-12">
                        <?php  echo tpl_ueditor('content',htmlspecialchars_decode($item['content']))?>
                        <textarea id='detail' style='display:none'><?php  echo htmlspecialchars_decode($item['content'])?></textarea>
                        <span class='help-block show_h'>此处的详情是放在活动页面的尾部显示的,默认可以为空</span>
                    </div>
                </div>
                <div class="form-group tuijian">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">显示活动商品</label>
                    <div class="col-sm-9 col-xs-12">
                        <?php if( ce('shop.activity' ,$item) ) { ?>
                        <label class='radio-inline'>
                            <input type='radio' name='is_activity' value=1 <?php  if($item['is_activity']==1) { ?>checked<?php  } ?> /> 是
                        </label>
                        <label class='radio-inline'>
                            <input type='radio' name='is_activity' value=0 <?php  if($item['is_activity']==0) { ?>checked<?php  } ?> /> 否
                        </label>
                        <?php  } else { ?>
                        <div class='form-control-static'><?php  if(empty($item['is_activity'])) { ?>否<?php  } else { ?>是<?php  } ?></div>
                        <?php  } ?>
                        <span class='help-block show_h'>是否在活动底部显示推荐的商品。</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">活动商品排列方式</label>
                    <div class="col-sm-9 col-xs-12">
                        <?php if( ce('shop.activity' ,$item) ) { ?>
                        <label class='radio-inline'>
                            <input type='radio' name='goods_sort_type' value=1 <?php  if($item['goods_sort_type']==1 || !$item['goods_sort_type']) { ?>checked<?php  } ?> /> 横排固定
                        </label>
                        <label class='radio-inline'>
                            <input type='radio' name='goods_sort_type' value=2 <?php  if($item['goods_sort_type']==2) { ?>checked<?php  } ?> /> 横排滑动
                        </label>
                        <label class='radio-inline'>
                            <input type='radio' name='goods_sort_type' value=3 <?php  if($item['goods_sort_type']==3) { ?>checked<?php  } ?> /> 竖排显示
                        </label>
                        <?php  } else { ?>
                        <div class='form-control-static'><?php  if(empty($item['is_activity'])) { ?>否<?php  } else { ?>是<?php  } ?></div>
                        <?php  } ?>
                        <span class='help-block show_h'>活动商品的展现方式，只在首页有效，具体请参考首页设计稿。横排固定就是商品在首页展示的时候是固定横排展示，横排滑动就是如果有多个商品横排展示显示滑动效果。竖排按照商品一行一个商品显示。</span>
                    </div>
                </div>
                <div class='form-group' <?php  if(!$item['is_activity']) { ?>style="display:none"<?php  } ?> id="activity">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">活动商品</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="recharge_items_for_coupon">
                        <?php  if(is_array($item['act_goods'])) { foreach($item['act_goods'] as $act_good) { ?>
                        <div class="input-group recharge-item" style="margin-top:5px">
                            <span class="input-group-addon">选择商品</span>
                            <select class="form-control" style="width: 300px" name="act_goods[]">
                                <option>请选择</option>
                                <?php  if(is_array($goods)) { foreach($goods as $good) { ?>
                                <option <?php  if($good['id'] == $act_good['id']) { ?>selected<?php  } ?> value="<?php  echo $good['id'];?>"><?php  echo $good['title'];?></option>
                                <?php  } } ?>
                            </select>
                            <div class="input-group ">
                                <input type="hidden" value="<?php  echo $act_good['index_img'];?>" name="goods_img[]">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" onclick="showImageDialog(this);" >选择图片</button>
                                </span>
                            </div>
                            <div class="input-group " style="position: absolute;left: 53rem;top:6px;">
                                <img src="<?php  echo tomedia($act_good['index_img'])?>" style="width: 26px;margin-left: 87px">
                            </div>
                            <div class="input-group-btn">
                                <a class="btn btn-danger" onclick="removeConsumeItem(this)">
                                    <i class="fa fa-remove"></i>
                                </a>
                            </div>
                        </div>
                        <?php  } } ?>
                        </div>
                        <div style="margin-top:5px">
                            <button type='button' class="btn btn-default" onclick='addConsumeItem_for_activity()' style="margin-bottom:5px"><i class='fa fa-plus'></i> 添加活动商品</button>
                        </div>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="form-group tuijian">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">显示推荐商品</label>
                    <div class="col-sm-9 col-xs-12">
                        <?php if( ce('shop.activity' ,$item) ) { ?>
                        <label class='radio-inline'>
                            <input type='radio' name='is_tui' value=1 <?php  if($item['is_tui']==1) { ?>checked<?php  } ?> /> 是
                        </label>
                        <label class='radio-inline'>
                            <input type='radio' name='is_tui' value=0 <?php  if($item['is_tui']==0) { ?>checked<?php  } ?> /> 否
                        </label>
                        <?php  } else { ?>
                        <div class='form-control-static'><?php  if(empty($item['is_tui'])) { ?>否<?php  } else { ?>是<?php  } ?></div>
                        <?php  } ?>
                        <span class='help-block show_h'>是否在活动底部显示推荐的商品。</span>
                    </div>
                </div>
                <div class="form-group" id="is_tui"  <?php  if($item['is_tui']==0) { ?>style="display: none;"<?php  } ?> >
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">选择推荐商品</label>
                    <div class="col-sm-9 col-xs-12">
                        <?php if( ce('shop.activity' ,$item) ) { ?>
                        <div class="input-group">
                            <select class="form-control" name="tui_goods[]" id="" size="10"  multiple="multiple">
                                <?php  if(is_array($goods)) { foreach($goods as $value) { ?>
                                <?php  if($item['tui_goods']) { ?>
                                <?php  if(in_array($value['id'], $item['tui_goods'])) { ?>
                                <option selected value="<?php  echo $value['id'];?>"><?php  echo $value['title'];?></option>
                                <?php  } else { ?>
                                <option value="<?php  echo $value['id'];?>"><?php  echo $value['title'];?></option>
                                <?php  } ?>
                                <?php  } else { ?>
                                <option value="<?php  echo $value['id'];?>"><?php  echo $value['title'];?></option>
                                <?php  } ?>
                                <?php  } } ?>
                            </select>
                        </div>
                        <?php  } else { ?>
                        <div class='form-control-static'><?php  echo $item['advurl'];?></div>
                        <?php  } ?>
                        <span class='help-block show_h'>鼠标多选,或者按住ctr+选择进行多选。</span>
                    </div>
                </div>
                
                 <div class="form-group"></div>
            <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                           <?php if( ce('shop.activity' ,$item) ) { ?>
                            <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" onclick="return formcheck()" />
                            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
                        <?php  } ?>
                       <input type="button" name="back" onclick='history.back()' <?php if(cv('shop.activity.add|shop.activity.edit')) { ?>style='margin-left:10px;'<?php  } ?> value="返回列表" class="btn btn-default col-lg-1" />
                    </div>
            </div>
                
            </div>
        </div>
      
    </form>
</div>
<script language='javascript'>
    var goods = '<?php  echo json_encode($goods)?>';
    var base_goods = JSON.parse(goods);
    console.log(base_goods);
    function removeConsumeItem(obj){
        $(obj).closest('.recharge-item').remove();
    }
    function addConsumeItem_for_activity(){
        var html= '<div class="input-group recharge-item"  style="margin-top:5px">';
        html+='<span class="input-group-addon">选择商品</span>';
        var option = '<option>请选择</option>';
        $.each(base_goods,function (k,v) {
            option += "<option value='"+v.id+"'>"+v.title+"</option>";
        });
        html += '<select class="form-control" style="width: 300px" name="act_goods[]" >';
        html += option;
        html += '</select>';
        html+='<div class="input-group "><input type="hidden" name="goods_img[]"><span class="input-group-btn"><button class="btn btn-default" type="button" onclick="showImageDialog(this);" data-original-title="" title="">选择图片</button></span></div>';
        html += '<div class="input-group " style="position: absolute;left: 53rem;top:6px;"><img src="" style="width: 26px;margin-left: 87px"></div>';
        html+='<div class="input-group-btn"><a class="btn btn-danger" onclick="removeConsumeItem(this)"><i class="fa fa-remove"></i></a></div>';
        html+='</div>';
        $('.recharge_items_for_coupon').append(html);
    }
    require(['util'], function (u) {
        $('#cp').each(function () {
            u.clip(this, $(this).text());
        });
    })
    $('form').submit(function(){
        if($(':input[name=catename]').isEmpty()){
            Tip.focus(':input[name=catename]','请输入活动名称!');
            return false;
        }
        /* if($(':input[name=advimg]').isEmpty()){
            Tip.focus(':input[name=advimg]','请选择活动广告图!');
            return false;
        }*/
        return true;
    });
    $(function () {
        $('#chooseUrl').on('click', function () {
            $('#floating-link').modal();
        });
        $(':input[name=is_tui]').on('click', function () {
            var is_tui = $(this).val();
            console.log(is_tui);
            if(is_tui == 1) {
                $('#is_tui').show();
            } else {
                $('#is_tui').hide();
            }
        });
        $(':input[name=is_activity]').on('click', function () {
            var is_activity = $(this).val();
            if(is_activity == 1) {
                $('#activity').show();
            } else {
                $('#activity').hide();
            }
        });
        $('.group_list').on('click', function () {
            $(':input[name=act_group]').val($(this).html());
        });

        window.optionchanged = false;
        $('#myTab a').click(function (e) {
            e.preventDefault();//阻止a链接的跳转行为
            $(this).tab('show');//显示当前选中的链接及关联的content
        });
        //默认获取
        var goods = '<?php  echo $_goods;?>';
        var act_goods = '<?php  echo $act_goods;?>';
        var id = '<?php  echo $_GPC["id"];?>';
        var activity_goods_obj = $('#activity_goods');
        var html = '';
        if(goods){
            $.each(eval("("+goods+")"), function (k,v) {
                if(act_goods && act_goods.indexOf(v.id) != -1) {
                    html += '<option selected value="'+v.id+'">'+v.title+'</option>';
                } else {
                    html += '<option value="' + v.id + '">' + v.title + '</option>';
                }
            });
        }
        activity_goods_obj.html(html);
        $(".is_mention").on('click', function () {
            var value = $(this).val();
            if(value == 1) {
                $('.tuijian').hide();
            } else {
                $('.tuijian').show();
            }
            $.ajax("<?php  echo $this->createWebUrl('shop/post', array('op'=>'util','p'=>'activity'))?>", {
                type:'get',
                data:{is_mention:value},
                dataType:'json',
                success: function (res) {
                    var html = '';
                    if(res.status == 1) {
                        if(res.result){
                            $.each(res.result, function (k,v) {
                                if(act_goods && act_goods.indexOf(v.id) != -1) {
                                    html += '<option selected value="'+v.id+'">'+v.title+'</option>';
                                } else {
                                    html += '<option value="' + v.id + '">' + v.title + '</option>';
                                }
                            });
                        }
                    }
                    activity_goods_obj.html(html);
                }
            });
        });
    })
</script>
<?php  } else if($operation == 'display') { ?>
<script language="javascript" src="../addons/manor_shop/static/js/dist/nestable/jquery.nestable.js"></script>
<link rel="stylesheet" type="text/css" href="../addons/manor_shop/static/js/dist/nestable/nestable.css" />
<style type='text/css'>
    .dd-handle { height: 40px; line-height: 30px}
</style>
<div class="main">
    <div class="activity">
        <form action="" method="post">
            <div class="panel panel-default">
                <div class="panel panel-title" style="padding: 10px;">
                    活动列表
                </div>
                <div class="panel-body table-responsive">

                        <div class="dd" id="div_nestable" style="max-width: 100%">
                            <ol class="dd-list">

                               <?php  if(is_array($activity)) { foreach($activity as $row) { ?>
                                 <?php  if(empty($row['parentid'])) { ?>
                                <li class="dd-item" data-id="<?php  echo $row['id'];?>">

                                    <div class="dd-handle"  style='width:100%;'>
                                        <img src="<?php  echo tomedia($row['advimg']);?>" width='30' height="30" onerror="$(this).remove()" style='padding:1px;border: 1px solid #ccc;float:left;' /> &nbsp; <!--[ID: <?php  echo $row['id'];?>]--> <?php  echo $row['name'];?>
                                        <span class="pull-right">
                                           <!-- <?php if(cv('shop.activity.add')) { ?><a class='btn btn-default btn-sm' href="<?php  echo $this->createWebUrl('shop/activity', array('parentid' => $row['id'], 'op' => 'post'))?>" title='添加子活动' ><i class="fa fa-plus"></i></a><?php  } ?>-->
                                            <?php if(cv('shop.activity.edit|shop.activity.view')) { ?>
                                             <a class='btn btn-default btn-sm' href="<?php  echo $this->createWebUrl('shop/activity', array('id' => $row['id'], 'op' => 'post'))?>" title="<?php if(cv('shop.activity.edit')) { ?>修改<?php  } else { ?>查看<?php  } ?>" ><i class="fa fa-edit"></i></a>
                                            <?php  } ?>
                                            <?php if(cv('shop.activity.delete')) { ?><a class='btn btn-default btn-sm' href="<?php  echo $this->createWebUrl('shop/activity', array('id' => $row['id'], 'op' => 'delete'))?>" title='删除' onclick="return confirm('确认删除此活动吗？');return false;"><i class="fa fa-remove"></i></a><?php  } ?>
                                        </span>
                                    </div>
                                    <?php  if(count($children[$row['id']])>0) { ?>
                                    
                                    <ol class="dd-list"  style='width:100%;'>
                                        <?php  if(is_array($children[$row['id']])) { foreach($children[$row['id']] as $child) { ?>
                                        <li class="dd-item" data-id="<?php  echo $child['id'];?>">
                                            <div class="dd-handle">
                                                <img src="<?php  echo tomedia($child['thumb']);?>" width='30' height="30" onerror="$(this).remove()" style='padding:1px;border: 1px solid #ccc;float:left;' /> &nbsp;
                                                <!--[ID: <?php  echo $child['id'];?>]--> <?php  echo $child['name'];?>
                                                <span class="pull-right">
                                                    <?php  if(intval($shopset['catlevel'])==3) { ?>
                                                     <?php if(cv('shop.activity.add')) { ?><a class='btn btn-default btn-sm' href="<?php  echo $this->createWebUrl('shop/activity', array('parentid' => $child['id'], 'op' => 'post'))?>" title='添加子活动' ><i class="fa fa-plus"></i></a><?php  } ?>
                                                     <?php  } ?>
                                                      <?php if(cv('shop.activity.edit|shop.activity.view')) { ?><a class='btn btn-default btn-sm' href="<?php  echo $this->createWebUrl('shop/activity', array('id' => $child['id'], 'op' => 'post'))?>" title="<?php if(cv('shop.activity.edit')) { ?>修改<?php  } else { ?>查看<?php  } ?>" ><i class="fa fa-edit"></i></a><?php  } ?>
                                                      <?php if(cv('shop.activity.delete')) { ?> <a class='btn btn-default btn-sm' href="<?php  echo $this->createWebUrl('shop/activity', array('id' => $child['id'], 'op' => 'delete'))?>" title='删除' onclick="return confirm('确认删除此活动吗？');return false;"><i class="fa fa-remove"></i></a><?php  } ?>
                                                </span>
                                            </div>
                                                  <?php  if(count($children[$child['id']])>0 && intval($shopset['catlevel'])==3) { ?>

                                                    <ol class="dd-list"  style='width:100%;'>
                                                        <?php  if(is_array($children[$child['id']])) { foreach($children[$child['id']] as $third) { ?>
                                                        <li class="dd-item" data-id="<?php  echo $third['id'];?>">
                                                            <div class="dd-handle">
                                                                <img src="<?php  echo tomedia($third['thumb']);?>" width='30' height="30" onerror="$(this).remove()" style='padding:1px;border: 1px solid #ccc;float:left;' /> &nbsp;
                                                                [ID: <?php  echo $third['id'];?>] <?php  echo $third['name'];?>
                                                                <span class="pull-right">
                                                                        <?php if(cv('shop.activity.edit|shop.activity.view')) { ?><a class='btn btn-default btn-sm' href="<?php  echo $this->createWebUrl('shop/activity', array('id' => $third['id'], 'op' => 'post'))?>" title="<?php if(cv('shop.activity.edit')) { ?>修改<?php  } else { ?>查看<?php  } ?>" ><i class="fa fa-edit"></i></a><?php  } ?>
                                                                      <?php if(cv('shop.activity.delete')) { ?><a class='btn btn-default btn-sm' href="<?php  echo $this->createWebUrl('shop/activity', array('id' => $third['id'], 'op' => 'delete'))?>" title='删除' onclick="return confirm('确认删除此活动吗？');return false;"><i class="fa fa-remove"></i></a><?php  } ?>
                                                                </span>
                                                            </div>
                                                        </li>
                                                        <?php  } } ?>
                                                    </ol>
                                                    <?php  } ?>
                                        </li>
                                        <?php  } } ?>
                                    </ol>
                                    <?php  } ?>
                                    
                                </li>
                                <?php  } ?>
                              <?php  } } ?>
                                
                            </ol>
                            <table class='table' style="margin-top: 15px">
                                <tr>
                                <td>
                                    <?php if(cv('shop.activity.add')) { ?>
                                    <a href="<?php  echo $this->createWebUrl('shop/activity',array('op' => 'post'))?>" class="btn btn-default"><i class="fa fa-plus"></i> 添加新活动</a>
                                    <?php  } ?>
                                    <?php if(cv('shop.activity.edit')) { ?>
                                    <input id="save_activity" type="button" class="btn btn-primary" value="保存活动修改">
                                    <?php  } ?>
                                    <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
                                    <input type="hidden" name="datas" value="" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
</div>
    <script language='javascript'>
    $(function(){
      var depth = <?php  echo intval($shopset['catlevel'])?>;
      if(depth<=0) {
          depth =2;
      }
      $('#div_nestable').nestable({maxDepth: depth });
         
        $(".dd-handle a,dd-handle embed,dd-handle div").mousedown(function (e) {
            e.stopPropagation();
        }); 
        var $expand = false;
        $('#nestableMenu').on('click', function(e)
        {
            if ($expand) {
                $expand = false;
                $('.dd').nestable('expandAll');
            }else {
                $expand = true;
                $('.dd').nestable('collapseAll');
            }
        });
        
        $("#save_activity").click(function(){
             var json = window.JSON.stringify($('#div_nestable').nestable("serialize"));
             $(':input[name=datas]').val(json);
             $('form').submit();
        });
    });
    </script>
 
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/_footer', TEMPLATE_INCLUDEPATH)) : (include template('web/_footer', TEMPLATE_INCLUDEPATH));?>

