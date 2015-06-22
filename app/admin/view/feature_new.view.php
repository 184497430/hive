<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hive - 新任务</title>
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
    <link href="static/plugins/iCheck/flat/blue.css" rel="stylesheet" type="text/css" />
    <!-- jvectormap -->
    <link href="static/plugins/jvectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
    <!-- Date Picker -->
    <link href="static/plugins/datepicker/datepicker3.css" rel="stylesheet" type="text/css" />
    <!-- Daterange picker -->
    <link href="static/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
    <!-- bootstrap wysihtml5 - text editor -->
    <link href="static/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="static/plugins/html5shiv/html5shiv.min.js"></script>
    <script src="static/plugins/respond/respond.min.js"></script>
    <![endif]-->
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
                新特征
                <small>项目A</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 项目A</a></li>
                <li class="active">发布</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-primary">
                    <!-- form start -->
                    <form role="form">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="name">功能名</label>
                                <input type="text" placeholder="填写功能名" id="name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="desc">描述</label>
                                <textarea id="desc" placeholder="填写 ..." rows="3" class="form-control"></textarea>
                            </div>
                            <div class="form-group">
                                <label>文件列表</label>
                                <div class="btn-group pull-right">
                                    <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#addDialog">添加</button>
                                    <button type="button" class="btn btn-default btn-xs">删除</button>
                                </div>
                                <table class="table table-condensed">
                                    <tbody><tr>
                                        <th style="width: 10px">#</th>
                                        <th>file</th>
                                        <th>版本号</th>
                                        <th>作者</th>
                                    </tr>
                                    <tr>
                                        <td><label><input type="checkbox"/></label></td>
                                        <td>Update software</td>
                                        <td><span class="label label-success">2014</span></td>
                                        <td>zhangy</td>
                                    </tr>
                                    <tr>
                                        <td><label><input type="checkbox"/></label></td>
                                        <td>Clean database</td>
                                        <td><span class="label label-danger">冲突</span></td>
                                        <td>--</td>
                                    </tr>
                                    </tbody></table>
                            </div>
                        </div><!-- /.box-body -->

                        <div class="box-footer">
                            <button class="btn btn-primary" type="submit">Submit</button>
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
                            <h4 class="modal-title" id="addDialogLabel">添加文件</h4>
                        </div>
                        <div class="modal-body">
                            <form method="get" action="#">

                                <div class="form-group input-group input-group-sm">
                                    <input type="text" placeholder="输入版本号..." class="form-control" name="ver">
                                    <span class="input-group-btn">
                                      <button type="button" class="btn btn-info btn-flat" name="search"><i class="fa fa-search"></i></button>
                                    </span>
                                </div>

                                <div class="form-group">
                                    <table class="table table-condensed">
                                        <tbody><tr>
                                            <th style="width: 10px">#</th>
                                            <th>file</th>
                                            <th>版本号</th>
                                            <th>作者</th>
                                        </tr>
                                        <tr>
                                            <td><label><input type="checkbox"></label></td>
                                            <td>Update software</td>
                                            <td><span class="label label-success">2014</span></td>
                                            <td>zhangy</td>
                                        </tr>
                                        <tr>
                                            <td><label><input type="checkbox"></label></td>
                                            <td>Clean database</td>
                                            <td><span class="label label-danger">冲突</span></td>
                                            <td>--</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="form-group">
                                    <div class="callout callout-danger">
                                        <h4>文件冲突！</h4>
                                        <p>/app/controller/filea.php已被zhangy在“登陆权限控制”功能中添加</p>
                                    </div>
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
<!-- jQuery UI 1.11.2 -->
<script src="http://code.jquery.com/ui/1.11.2/jquery-ui.min.js" type="text/javascript"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.2 JS -->
<script src="static/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<!-- Sparkline -->
<script src="static/plugins/sparkline/jquery.sparkline.min.js" type="text/javascript"></script>
<!-- jvectormap -->
<script src="static/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js" type="text/javascript"></script>
<script src="static/plugins/jvectormap/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script>
<!-- jQuery Knob Chart -->
<script src="static/plugins/knob/jquery.knob.js" type="text/javascript"></script>
<!-- daterangepicker -->
<script src="static/plugins/moment/moment.min.js" type="text/javascript"></script>
<script src="static/plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
<!-- datepicker -->
<script src="static/plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="static/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
<!-- Slimscroll -->
<script src="static/plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<!-- FastClick -->
<script src='static/plugins/fastclick/fastclick.min.js'></script>
<!-- AdminLTE App -->
<script src="static/dist/js/app.min.js" type="text/javascript"></script>

<!-- AdminLTE for demo purposes -->
<script src="static/dist/js/demo.js" type="text/javascript"></script>
</body>
</html>