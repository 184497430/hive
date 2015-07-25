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
                新建送测单
                <small><?php echo $prj_info['prj_name']?></small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 项目A</a></li>
                <li class="active">送测</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-primary">
                    <!-- form start -->
                    <form id="frm_prj" role="form" method="post" action="index.php?controller=feature&action=donew">
                        <input type="hidden" name="prj_id" value="<?php echo $prj_id?>">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="name">功能名</label>
                                <input type="text" placeholder="填写功能名" id="name" name="name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="desc">描述</label>
                                <textarea id="desc" placeholder="填写 ..." rows="3" name="desc" class="form-control"></textarea>
                            </div>
                        </div><!-- /.box-body -->

                        <div class="box-footer">
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </form>
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


<script>

    $(document).ready(function(){

        $("#frm_prj").submit(function(){
            $("#frm_prj").ajaxSubmit({
                dataType:"json",
                beforeSubmit: function(){

                },
                success: function(data) {
                    alert(data.msg)
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