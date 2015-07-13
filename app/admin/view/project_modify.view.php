<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hive - 项目</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <link href="static/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
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
                修改项目
                <small>系统管理</small>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-primary">
                        <!-- form start -->
                        <form id="frm_project" role="form" method="POST" action="index.php?controller=project&action=dosave">
                            <input type="hidden" id="id" name="id" value="<?php echo $prj_info['prj_id']?>">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="name">项目名</label>
                                    <input type="text" placeholder="填写项目名" id="name" name="prj_name" value="<?php echo htmlspecialchars($prj_info['prj_name'])?>" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="url">访问url</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">http://</span>
                                        <input type="text" placeholder="填写URL" id="url" name="url" value="<?php echo htmlspecialchars($prj_info['url'])?>" class="form-control">
                                    </div>

                                </div>
                                <div class="form-group">
                                    <label for="desc">描述</label>
                                    <textarea placeholder="填写 ..." rows="3" id="desc" name="desc" value="<?php echo htmlspecialchars($prj_info['desc'])?>" class="form-control"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="svn">svn地址</label>
                                    <input type="text" placeholder="填写SVN地址" id="svn" name="svn_url" value="<?php echo htmlspecialchars($prj_info['svn_url'])?>" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="svn">svn账号</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <span class="input-group-addon">@</span>
                                                <input type="text" placeholder="Username" id="svn_username" name="svn_username" value="<?php echo htmlspecialchars($prj_info['svn_username'])?>" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                                <input type="password" placeholder="Password" id="svn_pwd" name="svn_pwd" value="<?php echo htmlspecialchars($prj_info['svn_pwd'])?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="test_ip">测试服务器</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-laptop"></i>
                                                </div>
                                                <input type="text" id="test_ip" name="test_ip" value="<?php echo htmlspecialchars($prj_info['test_server_ip'])?>" class="form-control" data-inputmask="'alias': 'ip'" data-mask/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <span class="input-group-addon">Path</span>
                                                <input type="text" placeholder="input root path" id="test_path" name="test_path" value="<?php echo htmlspecialchars($prj_info['test_server_path'])?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="test_ip">生产服务器</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-laptop"></i>
                                                </div>
                                                <input type="text" id="product_ip" name="product_ip" value="<?php echo htmlspecialchars($prj_info['product_server_ip'])?>" class="form-control" data-inputmask="'alias': 'ip'" data-mask/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <span class="input-group-addon">Path</span>
                                                <input type="text" placeholder="input root path" id="product_path" value="<?php echo htmlspecialchars($prj_info['product_server_path'])?>" name="product_path" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>参与人员</label>
                                    <div class="btn-group pull-right">
                                        <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#addDialog">添加</button>
                                        <button type="button" class="btn btn-default btn-xs">删除</button>
                                    </div>
                                    <table class="table table-condensed">
                                        <tbody><tr>
                                            <th style="width: 10px">#</th>
                                            <th>姓名</th>
                                            <th>权限</th>
                                        </tr>
                                        <tr>
                                            <td><label><input type="checkbox" name="users[]" value=""/></label></td>
                                            <td>zhangy</td>
                                            <td><a href="" data="">项目管理员、测试人员、开发人员</a></td>
                                        </tr>
                                        </tbody></table>
                                </div>
                            </div><!-- /.box-body -->

                            <div class="box-footer">
                                <button class="btn btn-primary" type="submit">提交</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal" id="addDialog" tabindex="-1" role="dialog" aria-labelledby="addDialogLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="addDialogLabel">添加参与人员</h4>
                        </div>
                        <div class="modal-body">
                            <form method="get" action="#">

                                <div class="form-group input-group input-group-sm">
                                    <input type="text" placeholder="输入姓名..." class="form-control" name="ver">
                                    <span class="input-group-btn">
                                      <button type="button" class="btn btn-info btn-flat" name="search"><i class="fa fa-search"></i></button>
                                    </span>
                                </div>

                                <div class="form-group">
                                    <table class="table table-condensed">
                                        <tbody><tr>
                                            <th style="width: 10px">#</th>
                                            <th>姓名</th>
                                        </tr>
                                        <tr>
                                            <td><label><input type="checkbox"></label></td>
                                            <td>zhangy</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" class="flat-green"/>
                                        项目管理员
                                    </label>
                                    <label>
                                        <input type="checkbox" class="flat-green"/>
                                        测试人员
                                    </label>
                                    <label>
                                        <input type="checkbox" class="flat-green"/>
                                        开发人员
                                    </label>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default pull-left" type="button">Close</button>
                            <button class="btn btn-primary" type="button">OK</button>
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
        //Flat red color scheme for iCheck
        $('input[type="checkbox"].flat-green').iCheck({
            checkboxClass: 'icheckbox_minimal-blue'
        });

        $("#frm_project").submit(function(){
            $("#frm_project").ajaxSubmit({
                dataType:"json",
                beforeSubmit: function(){

                },
                success: function(data) {
                    alert(data.msg);
                },
                error: function (error) {

                }
            });
            return false;
        });
    });
</script>
</body>
</html>