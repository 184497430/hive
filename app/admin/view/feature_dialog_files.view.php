<?php if($log){ ?>

<div class="form-group">
    <div class="callout callout-info">
        <p>[<?php echo date('Y-m-d H:i:s', strtotime($log['date']))?>]&nbsp;<?php echo $log['msg'] ?></p>
    </div>
</div>

<div class="form-group">
    <table class="table table-condensed">
        <tbody><tr>
            <th style="width: 10px">#</th>
            <th>file</th>
            <th>动作</th>
            <th>版本号</th>
            <th>作者</th>
        </tr>

        <?php foreach($log['paths'] as $each){?>
            <?php if($each['type']=='dir') continue; ?>
        <tr>
            <td><label><input type="checkbox""></label></td>
            <td><?php echo $each['path']?></td>
            <td><?php echo $each['action']?></td>
            <td><span class="label label-success"><?php echo $log['rev'] ?></span></td>
            <td><?php echo $log['author'] ?></td>
        </tr>
        <?php }?>
        <!--
        <tr>
            <td><label><input type="checkbox"></label></td>
            <td>Clean database</td>
            <td><span class="label label-danger">冲突</span></td>
            <td>--</td>
        </tr>
        -->
        </tbody>
    </table>
</div>
    <!--
<div class="form-group">
    <div class="callout callout-danger">
        <h4>文件冲突！</h4>
        <p>/app/controller/filea.php已被zhangy在“登陆权限控制”功能中添加</p>
    </div>
</div>
-->
<?php } ?>