<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hive - 新任务</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <link href="static/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="static/plugins/JQueryUI/jquery-ui-1.10.3.custom.css" rel="stylesheet" type="text/css" />
    <!-- FontAwesome 4.3.0 -->
    <link href="static/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons 2.0.0 -->
    <link href="static/plugins/ionicons/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="static/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link href="static/dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
    <!-- iCheck -->
    <link href="static/plugins/iCheck/minimal/blue.css" rel="stylesheet" type="text/css" />
    <!-- bootstrap wysihtml5 - text editor -->
    <link href="static/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
    <style>
        .button-row button{margin:0px 5px;}
    </style>
</head>
<body class="skin-blue sidebar-mini">
<div class="wrapper">

    <?php include("header.view.php"); ?>
    <?php include("menu.view.php"); ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                送测单
                <small><?php echo $prj_info['prj_name'] ?></small>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-primary">
                    <!-- form start -->
                    <form id="frm_prj" role="form">
                        <input type="hidden" name="prj_id" value="<?php echo $prj_id?>">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="name">功能名</label>
                                <input type="text" placeholder="填写功能名" id="name" value="<?php echo h($feature_info['feature_name']) ?>" class="form-control" disabled>
                            </div>
                            <div class="form-group">
                                <label>状态</label>
                                <input type="text" placeholder="填写功能名" id="name" class="form-control" value="<?php echo h($status_dict[$feature_info['status']])?>" disabled>
                            </div>
                            <div class="form-group">
                                <label for="desc">描述</label>
                                <textarea id="desc" placeholder="填写 ..." rows="3" name="desc" class="form-control"><?php echo h($feature_info['desc'])?></textarea>
                            </div>
                            <div class="form-group">
                                <label>文件列表</label>
                                <div class="btn-group pull-right">
                                    <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#addDialog">添加</button>
                                    <button type="button" class="btn btn-default btn-xs btn-del">删除</button>
                                </div>
                                <table class="table table-condensed">
                                    <tbody><tr>
                                        <th style="width: 10px">#</th>
                                        <th>file</th>
                                        <th>版本号</th>
                                        <th>作者</th>
                                    </tr>
                                    </tbody></table>
                            </div>

                            <div class="form-group">
                                <label>操作</label>
                                <div class="button-row">
                                    <?php switch($feature_info['status']){
                                        case FeatureModel::STATUS_NEW:
                                            echo '<button type="button" class="btn btn-default btn-md">保存</button>';
                                            echo '<button type="button" class="btn btn-info btn-md">送测</button>';
                                            echo '<button type="button" class="btn btn-danger btn-md">关闭</button>';
                                            break;
                                        case FeatureModel::STATUS_TEST:
                                            echo '<button type="button" class="btn btn-warning btn-md">打回</button>';
                                            echo '<button type="button" class="btn btn-success btn-md">通过</button>';
                                            echo '<button type="button" class="btn btn-danger btn-md">关闭</button>';
                                            break;
                                        case FeatureModel::STATUS_FAIL:
                                            echo '<button type="button" class="btn btn-default btn-md">保存</button>';
                                            echo '<button type="button" class="btn btn-info btn-md">送测</button>';
                                            echo '<button type="button" class="btn btn-danger btn-md">关闭</button>';
                                            break;
                                        case FeatureModel::STATUS_PASS:
                                            echo '<button type="button" class="btn btn-primary btn-md">部署</button>';
                                            echo '<button type="button" class="btn btn-danger btn-md">关闭</button>';
                                            break;
                                        case FeatureModel::STATUS_CLOSE:
                                            break;
                                        case FeatureModel::STATUS_DONE:
                                            echo '<button type="button" class="btn btn-danger btn-md">回滚</button>';
                                            break;
                                    }?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>历史记录</label>
                                <ul class="products-list product-list-in-box">
                                    <li class="item">
                                        <a class="product-title" href="javascript::;">Samsung TV <span class="label label-warning pull-right">$1800</span></a>
                                        <span class="product-description">
                                          Samsung 32" 1080p 60Hz LED Smart HDTV.
                                        </span>
                                    </li><!-- /.item -->
                                    <li class="item">
                                            <a class="product-title" href="javascript::;">Bicycle <span class="label label-info pull-right">$700</span></a>
                                            <span class="product-description">
                                              26" Mongoose Dolomite Men's 7-speed, Navy Blue.
                                            </span>
                                    </li><!-- /.item -->

                                </ul>
                            </div>
                        </div><!-- /.box-body -->
                    </form>
                </div>
                </div>
            </div>

            <div class="modal" id="addDialog" tabindex="-1" role="dialog" aria-labelledby="addDialogLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="addDialogLabel">添加文件</h4>
                        </div>
                        <div class="modal-body">

                            <form id="frm_search" method="get" action="index.php?controller=feature&action=ajaxFiles&id=<?php echo $feature_info['feature_id']; ?>">
                                <div class="form-group input-group input-group-sm">
                                    <input type="text" placeholder="输入版本号..." class="form-control" name="rev" id="rev">
                                    <span class="input-group-btn">
                                      <button type="submit" class="btn btn-info btn-flat" name="search"><i class="fa fa-search"></i></button>
                                    </span>
                                </div>
                            </form>

                            <form id="frm_files" method="get" action="#">

                            </form>
                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default pull-left" type="button">Close</button>
                            <button class="btn btn-primary btn-ok" type="button">OK</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div>
            </div>
        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->

    <?php include('footer.view.php');?>
    <?php include("sidebar.view.php"); ?>
</div><!-- ./wrapper -->

<!-- jQuery 2.1.4 -->
<script src="static/plugins/jQuery/jQuery-2.1.4.min.js"></script>
<script src="static/plugins/jQueryUI/jquery-ui-1.10.3.custom.min.js"></script>
<script src="static/plugins/jQueryForm/jquery.form.js"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="static/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="static/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
<!-- iCheck 1.0.1 -->
<script src="static/plugins/iCheck/icheck.min.js" type="text/javascript"></script>
<!-- AdminLTE App -->
<script src="static/dist/js/app.min.js" type="text/javascript"></script>


<script>

    $(document).ready(function(){

        $("#addDialog").on("show.bs.modal", function(){
            $("#frm_search").resetForm();
            $("#frm_files").html("");
        });

        $("#addDialog .btn-ok").on('click', function(){
            $(this).parents(".modal").modal('hide');

            $("#frm_files :checkbox:checked").each(function(){
                var tds = $(this).parents('tr').first().children('td');

                var find = null;
                $('#frm_prj table:eq(0) tr:gt(0)').each(function(){
                    var tds2 = $(this).children('td');

                    if( tds.eq(1).html().trim() == tds2.eq(1).html().trim() ){
                        find = tds2;
                    }
                });

                if(find){
                    console.log(find);
                    find.eq(2).html(tds.eq(3).html());
                    find.eq(3).html(tds.eq(4).html());
                }else{
                    var file = tds.eq(1).html().trim();
                    var rev = tds.eq(3).html().trim();
                    var author = tds.eq(4).html().trim();
                    $('#frm_prj table:eq(0) tbody').append('<tr><td><label><input type="checkbox"/></label></td><td>'
                    +file+'</td><td>'+rev+'</td><td>'+author+'</td></tr>');
                }
            });
        });

        $("#frm_prj .btn-del").on('click', function(){
            $('#frm_prj table:eq(0) :checkbox:checked').each(function(){
                $(this).parents('tr').first().remove();
            })
        });

        $("body").delegate('tr', 'click', null, function(){
            var checkbox = $(this).find(":checkbox:eq(0)");
            //console.log(checkbox.attr('checked'));
            checkbox.selected(!checkbox.is(":checked"));
        });

        $("#frm_search").on('submit', function(){
            $(this).ajaxSubmit({
                dataType:"html",
                beforeSubmit: function(){

                },
                success: function(data) {
                    $("#frm_files").html(data);
                },
                error: function (error) {

                }
            });
            return false;
        });

        var availableTags = ["ActionScript", "AppleScript", "Asp", "BASIC", "C", "C++", "Clojure", "COBOL", "ColdFusion", "Erlang", "Fortran", "Groovy", "Haskell", "Java", "JavaScript", "Lisp", "Perl", "PHP", "Python", "Ruby", "Scala", "Scheme"];

        $("#rev").autocomplete({
            source:function(request, response){
                var matchCount = this.options.items;//返回结果集最大数量
                $.get("index.php?controller=feature&action=ajaxGuess",{"rev":request.term, "id":1, "matchCount":matchCount},function(respData){
                    return response(respData.data);
                }, 'json');
            }

            /*
            formatItem:function(item){
                return item["regionName"]+"("+item["regionNameEn"]+"，"+item["regionShortnameEn"]+") - "+item["regionCode"];
            },
            setValue:function(item){
                return {'data-value':item["regionName"],'real-value':item["regionCode"]};
            }
            */
        });

        $("#frm_files").on('submit', function(){

            return false;
        });
    });
</script>
</body>
</html>