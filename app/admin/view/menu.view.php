<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
              </span>
            </div>
        </form>
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="treeview active">
                <a href="#">
                    <i class="fa fa-th"></i>
                    <span>项目A</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="active"><a href="index.php?controller=feature&action=new&prjid=1"><i class="fa fa-circle-o"></i> 送测</a></li>
                    <li><a href="index.php?controller=feature&action=work&prjid=1"><i class="fa fa-circle-o"></i> 测试中</a></li>
                    <li><a href="index.php?controller=feature&action=history&prjid=1"><i class="fa fa-circle-o"></i> 已部署</a></li>
                    <li><a href="index.php?controller=feature&action=timeline&prjid=1"><i class="fa fa-circle-o"></i> 时间轴</a></li>
                </ul>
            </li>
            <li class="header">系统管理</li>
            <li><a href="index.php?controller=user&action=list"><i class="fa fa-circle-o text-red"></i> <span>人员管理</span></a></li>
            <li><a href="index.php?controller=project&action=list"><i class="fa fa-circle-o text-yellow"></i> <span>项目管理</span></a></li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>