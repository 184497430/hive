<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hive - 人员管理</title>
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
                人员列表
                <small>系统管理</small>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"></h3>
                            <div class="box-tools">
                                <div class="input-group">
                                    <input type="text" placeholder="Search" style="width: 150px;" class="form-control input-sm pull-right" name="table_search">
                                    <div class="input-group-btn">
                                        <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                                        <a href="index.php?controller=user&action=new" class="btn btn-sm btn-default">＋新成员</a>
                                        <button class="btn btn-sm btn-default">删除</button>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.box-header -->
                        <div class="box-body table-responsive no-padding">
                            <table class="table table-hover">
                                <tbody><tr>
                                    <th>ID</th>
                                    <th>登录名</th>
                                    <th>姓名</th>
                                    <th>状态</th>
                                    <th>参与项目</th>
                                    <th>参与单子数</th>
                                    <th>打回数</th>
                                    <th>回滚数</th>
                                    <th>注册时间</th>
                                </tr>
                                <?php foreach($users['list'] as $each){ ?>
                                <tr>
                                    <td><?php echo $each['user_id']?></td>
                                    <td><?php echo htmlspecialchars($each['user_name'])?></td>
                                    <td><?php echo htmlspecialchars($each['real_name'])?></td>
                                    <td>
                                        <?php if($each['status']==1){ ?>
                                        <span class="label label-danger">中止</span>
                                        <?php } else { ?>
                                            <span class="label label-success">正常</span>
                                        <?php } ?>
                                    </td>
                                    <td>项目A、项目B</td>
                                    <td><?php echo $each['feature_num']?></td>
                                    <td><?php echo $each['testback_num']?></td>
                                    <td><?php echo $each['rollback_num']?></td>
                                    <td><?php echo $each['ctime']?></td>
                                </tr>
                                <?php } ?>
                                </tbody></table>
                        </div>
                        <div class="box-footer clearfix">
                            <?php if($users['page']['count']>1) {?>
                                <?php echo $page_tool ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title">Modal Default</h4>
                        </div>
                        <div class="modal-body">
                            <p>One fine body…</p>
                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default pull-left" type="button">Close</button>
                            <button class="btn btn-primary" type="button">Save changes</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
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
</body>
</html>