<ul class="pagination pagination-sm no-margin pull-right">
    <?php if(!empty($prior_group)){ ?>
    <li><a href="<?php echo $prior_group['url'] ?>">«</a></li>
    <?php } ?>
    <?php if(!empty($prior)){ ?>
    <li><a href="<?php echo $prior['url'] ?>"><?php echo $prior['no']?></a></li>
    <?php } ?>
    <?php if(!empty($current)){ ?>
    <li><a href="<?php echo $current['url'] ?>"><?php echo $current['no']?></a></li>
    <?php } ?>
    <?php if(!empty($next)){ ?>
    <li><a href="<?php echo $next['url'] ?>"><?php echo $next['no']?></a></li>
    <?php } ?>
    <?php if(!empty($next_group)){ ?>
    <li><a href="<?php echo $next_group['url'] ?>">»</a></li>
    <?php } ?>
</ul>