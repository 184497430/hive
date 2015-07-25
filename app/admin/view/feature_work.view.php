<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hive - 处理中</title>
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
                处理中
                <small><?php echo $prj_info['prj_name']?></small>
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
                                </div>
                            </div>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover">
                            <tbody><tr>
                                <th>ID</th>
                                <th>功能名</th>
                                <th>日期</th>
                                <th>进度</th>
                                <th>相关人员</th>
                            </tr>
                            <?php foreach($features['list'] as $each){ ?>
                            <tr>
                                <td><?php echo $each['feature_id']?></td>
                                <td><a href="index.php?controller=feature&action=detail&id=1"><?php echo $each['feature_name']?></a></td>
                                <td><?php echo date('Y-m-d H:i:s', $each['ctime'])?></td>
                                <td><?php echo $status_tpls[$each['status']];?></td>
                                <td>zhangy</td>
                            </tr>
                            <?php } ?>
                            </tbody></table>
                    </div>
                    <div class="box-footer clearfix">
                        <?php if($features['page']['count']>1) {?>
                            <?php echo $page_tool ?>
                        <?php } ?>
                    </div>
                </div>
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
</body>
</html>