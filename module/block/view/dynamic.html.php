<?php if(empty($actions)): ?>
<div class='empty-tip'><?php echo $lang->block->emptyTip;?></div>
<?php else:?>
<style>
.block-dynamic .timeline > li .timeline-text {max-width: 600px; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-height: 20px;}
.block-dynamic .panel-body {padding-top: 0;}
.timeline>li:before {left: -26px;}
.timeline>li+li:after {left: -23px;}
.timeline-text {margin-left: -18px;}
.block-dynamic .label-action {padding: 0 6px;}
.block-dynamic .label-action + a {padding-left: 6px;}
.timeline>li.active:before {left: -30px;}
.timeline>li>div:after {left: -27px;}
</style>
<div class='panel-body scrollbar-hover'>
  <ul class="timeline timeline-tag-left no-margin">
    <?php
    $i = 0;
    foreach($actions as $action)
    {
        $user = zget($users, $action->actor);
        if($action->action == 'login' or $action->action == 'logout') $action->objectName = $action->objectLabel = '';
        if($action->objectType == 'sonarqubeproject') $action->objectName = $action->extra;
        $class = $action->major ? "class='active'" : '';
        echo "<li $class><div>";
        if($action->objectLink) printf($lang->block->dynamicInfo, $action->date, $user, $action->actionLabel, $action->objectLabel, $action->objectLink, $action->objectName, $action->objectName);
        if(!$action->objectLink) printf($lang->block->noLinkDynamic, $action->date, $action->objectName, $user, $action->actionLabel, $action->objectLabel, $action->objectName);
        echo "</div></li>";
        $i++;
    }
    ?>
  </ul>
</div>
<?php endif;?>
