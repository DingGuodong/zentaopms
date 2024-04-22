#!/usr/bin/env php
<?php

/**

title=测试 storyModel->batchChangeBranch();
cid=0

- 批量修改需求的所属分支，判断被修改分支需求的数量 @3
- 批量修改需求的所属分支，判断需求2修改后的分支ID第2条的branch属性 @2
- 批量修改需求的所属分支，判断需求4修改后的分支ID第4条的branch属性 @2
- 批量修改需求的所属分支，判断需求6修改后的分支ID第6条的branch属性 @2
- 批量修改需求的所属分支，并同步修改计划，判断被修改分支需求的数量 @3
- 批量修改需求的所属分支，并同步修改计划，判断需求2修改后的分支ID和计划
 - 属性branch @3
 - 属性plan @2,3
- 批量修改需求的所属分支，并同步修改计划，判断需求4修改后的分支ID第4条的branch属性 @3
- 批量修改需求的所属分支，并同步修改计划，判断需求6修改后的分支ID第6条的branch属性 @3

*/
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/story.unittest.class.php';
su('admin');

$story = zenData('story');
$story->product->range('1{3},2{3}');
$story->branch->range('0{3},0-4');
$story->plan->range('``{3},2,``,3');
$story->status->range('draft,active,closed');
$story->version->range('1');
$story->gen(6);
zenData('storyspec')->gen(20);
zenData('product')->gen(20);

$storyIdList = array(2, 4, 6);
$plans[2][1] = new stdclass();
$plans[2][1]->branch = 2;
$plans[2][2] = new stdclass();
$plans[2][2]->branch = 3;
$plans[2][3] = new stdclass();
$plans[2][3]->branch = 3;

$story    = new storyTest();
$stories1 = $story->batchChangeBranchTest($storyIdList, 2);
$stories2 = $story->batchChangeBranchTest($storyIdList, 3, 'yes', $plans);

r(count($stories1)) && p()           && e('3'); // 批量修改需求的所属分支，判断被修改分支需求的数量
r($stories1)        && p('2:branch') && e('2'); // 批量修改需求的所属分支，判断需求2修改后的分支ID
r($stories1)        && p('4:branch') && e('2'); // 批量修改需求的所属分支，判断需求4修改后的分支ID
r($stories1)        && p('6:branch') && e('2'); // 批量修改需求的所属分支，判断需求6修改后的分支ID

r(count($stories2)) && p()                   && e('3');     // 批量修改需求的所属分支，并同步修改计划，判断被修改分支需求的数量
r($stories2[2])     && p('branch|plan', '|') && e('3|2,3'); // 批量修改需求的所属分支，并同步修改计划，判断需求2修改后的分支ID和计划
r($stories2)        && p('4:branch')         && e('3');     // 批量修改需求的所属分支，并同步修改计划，判断需求4修改后的分支ID
r($stories2)        && p('6:branch')         && e('3');     // 批量修改需求的所属分支，并同步修改计划，判断需求6修改后的分支ID
