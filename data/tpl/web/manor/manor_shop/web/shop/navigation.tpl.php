<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/_header', TEMPLATE_INCLUDEPATH)) : (include template('web/_header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/shop/tabs', TEMPLATE_INCLUDEPATH)) : (include template('web/shop/tabs', TEMPLATE_INCLUDEPATH));?>
<link href="../addons/manor_shop/plugin/designer/template/imgsrc/designer.css" rel="stylesheet">
<script type="text/javascript" src="../addons/manor_shop/plugin/designer/template/imgsrc/angular.min.js"></script>
<?php  if($operation == 'post') { ?>
<div class="main">
    
    <form  <?php if( ce('shop.navigation' ,$item) ) { ?>action="" method="post"<?php  } ?> class="form-horizontal form" enctype="multipart/form-data" >
    
        <div class="panel panel-default">
            <div class="panel-heading">
                商品首页导航
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                    <div class="col-sm-9 col-xs-12">
                        <?php if( ce('shop.navigation' ,$item) ) { ?>
                           <input type="text" name="displayorder" class="form-control" value="<?php  echo $item['displayorder'];?>" />
                        <?php  } else { ?>
                           <div class='form-control-static'><?php  echo $item['displayorder'];?></div>
                        <?php  } ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">首页导航名称</label>
                    <div class="col-sm-9 col-xs-12">
                         <?php if( ce('shop.navigation' ,$item) ) { ?>
                        <input type="text" name="name" class="form-control" value="<?php  echo $item['name'];?>" />
                           <?php  } else { ?>
                           <div class='form-control-static'><?php  echo $item['name'];?></div>
                        <?php  } ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>首页导航小图标</label>
                    <div class="col-sm-9 col-xs-12">
                      <?php if( ce('shop.navigation' ,$item) ) { ?>
                             <?php  echo tpl_form_field_image('thumb', $item['thumb'])?>
                            <span class="help-block">建议尺寸: 200*200，或正方型图片 </span>
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
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">首页导航链接</label>
                    <div class="col-sm-9 col-xs-12">
                         <?php if( ce('shop.navigation' ,$item) ) { ?>
                        <div class="input-group">
                            <input type="text" name="advurl" class="form-control" value="<?php  echo $item['url'];?>"/>
                            <span class="input-group-btn">
				                <button class="btn btn-default" type="button" id="chooseUrl">系统链接</button>
			                </span>
                        </div>
                        <?php  } else { ?>
                         <div class='form-control-static'><?php  echo $item['url'];?></div>
                         <?php  } ?>
                        <span class='help-block show_h'>导航转跳的页面地址</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否显示</label>
                    <div class="col-sm-9 col-xs-12">
                        <?php if( ce('shop.navigation' ,$item) ) { ?>
                        <label class='radio-inline'>
                            <input type='radio' name='enabled' value=1' <?php  if($item['enabled']==1) { ?>checked<?php  } ?> /> 是
                        </label>
                        <label class='radio-inline'>
                            <input type='radio' name='enabled' value=0' <?php  if($item['enabled']==0) { ?>checked<?php  } ?> /> 否
                        </label>
                          <?php  } else { ?>
                           <div class='form-control-static'><?php  if(empty($item['enabled'])) { ?>否<?php  } else { ?>是<?php  } ?></div>
                           <?php  } ?>
                    </div>
                </div>
                 <div class="form-group"></div>
            <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                           <?php if( ce('shop.navigation' ,$item) ) { ?>
                            <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" onclick="return formcheck()" />
                            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
                        <?php  } ?>
                       <input type="button" name="back" onclick='history.back()' <?php if(cv('shop.navigation.add|shop.navigation.edit')) { ?>style='margin-left:10px;'<?php  } ?> value="返回列表" class="btn btn-default col-lg-1" />
                    </div>
            </div>
                
            </div>
        </div>
      
    </form>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/shop/modal', TEMPLATE_INCLUDEPATH)) : (include template('web/shop/modal', TEMPLATE_INCLUDEPATH));?>
<script language='javascript'>
         require(['util'],function(u){
    $('#cp').each(function(){
	u.clip(this, $(this).text());
	});
    })
    $('form').submit(function(){
        /* if($(':input[name=name]').isEmpty()){
            Tip.focus(':input[name=name]','请输入首页导航名称!');
            return false;
        }*/
        if($(':input[name=thumb]').isEmpty()){
            Tip.focus(':input[name=thumb]','请选择首页导航小图标!');
            return false;
        }
        return true;
    });
    $(function () {
        $('#chooseUrl').on('click', function () {
            $('#floating-link').modal();
        })
    })
</script>
<?php  } else if($operation == 'display') { ?>
<script language="javascript" src="../addons/manor_shop/static/js/dist/nestable/jquery.nestable.js"></script>
<link rel="stylesheet" type="text/css" href="../addons/manor_shop/static/js/dist/nestable/nestable.css" />
<style type='text/css'>
    .dd-handle { height: 40px; line-height: 30px}
</style>
<div class="main">
    <div class="navigation">
        <form action="" method="post">
            <div class="panel panel-default">
                <div class="panel panel-title" style="padding: 10px;">
                    首页导航列表
                </div>
                <div class="panel-body table-responsive">

                        <div class="dd" id="div_nestable" style="max-width: 100%">
                            <ol class="dd-list">

                               <?php  if(is_array($navigation)) { foreach($navigation as $row) { ?>
                                 <?php  if(empty($row['parentid'])) { ?>
                                <li class="dd-item" data-id="<?php  echo $row['id'];?>">

                                    <div class="dd-handle"  style='width:100%;'>
                                        <img src="<?php  echo tomedia($row['thumb']);?>" width='30' height="30" onerror="$(this).remove()" style='padding:1px;border: 1px solid #ccc;float:left;' /> &nbsp; <!--[ID: <?php  echo $row['id'];?>]--> <?php  echo $row['name'];?>
                                        <span class="pull-right">
                                           <!-- <?php if(cv('shop.navigation.add')) { ?><a class='btn btn-default btn-sm' href="<?php  echo $this->createWebUrl('shop/navigation', array('parentid' => $row['id'], 'op' => 'post'))?>" title='添加子首页导航' ><i class="fa fa-plus"></i></a><?php  } ?>-->
                                            <?php if(cv('shop.navigation.edit|shop.navigation.view')) { ?>
                                             <a class='btn btn-default btn-sm' href="<?php  echo $this->createWebUrl('shop/navigation', array('id' => $row['id'], 'op' => 'post'))?>" title="<?php if(cv('shop.navigation.edit')) { ?>修改<?php  } else { ?>查看<?php  } ?>" ><i class="fa fa-edit"></i></a>
                                            <?php  } ?>
                                            <?php if(cv('shop.navigation.delete')) { ?><a class='btn btn-default btn-sm' href="<?php  echo $this->createWebUrl('shop/navigation', array('id' => $row['id'], 'op' => 'delete'))?>" title='删除' onclick="return confirm('确认删除此首页导航吗？');return false;"><i class="fa fa-remove"></i></a><?php  } ?>
                                        </span>
                                            <?php  if($row['enabled'] == 1) { ?>
                                            <span style="margin-left:60px;padding: 3px;background: green;border-radius: 4px;color: #fff;">显示</span>
                                            <?php  } else { ?>
                                            <span style="margin-left:60px;padding: 3px;background: red;border-radius: 4px;color: #fff;">隐藏</span>
                                            <?php  } ?>
                                    </div>
                                </li>
                                <?php  } ?>
                              <?php  } } ?>
                                
                            </ol>
                            <table class='table' style="margin-top: 15px">
                                <tr>
                                <td>
                                    <?php if(cv('shop.navigation.add')) { ?>
                                    <a href="<?php  echo $this->createWebUrl('shop/navigation',array('op' => 'post'))?>" class="btn btn-default"><i class="fa fa-plus"></i> 添加新首页导航</a>
                                    <?php  } ?>
                                    <?php if(cv('shop.navigation.edit')) { ?>
                                    <input id="save_navigation" type="button" class="btn btn-primary" value="保存首页导航修改">
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
        
        $("#save_navigation").click(function(){
             var json = window.JSON.stringify($('#div_nestable').nestable("serialize"));
             $(':input[name=datas]').val(json);
             $('form').submit();
        })
        
    })
    </script>
 
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/_footer', TEMPLATE_INCLUDEPATH)) : (include template('web/_footer', TEMPLATE_INCLUDEPATH));?>

