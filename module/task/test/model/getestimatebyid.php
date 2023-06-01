#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/task.class.php';
su('admin');
zdTable('effort')->gen(11);

/**

title=taskModel->getEstimateByID();
cid=1

*/

$estimateIdList = array('0', '1', '11', '50');

$task = new taskTest();
r($task->getEstimateByIdTest($estimateIdList[0])) && p()              && e('0');                // 查询日志为空的情况
r($task->getEstimateByIdTest($estimateIdList[1])) && p('work,isLast') && e('这是工作内容1,0');  // 查询不是最后一次添加的日志
r($task->getEstimateByIdTest($estimateIdList[2])) && p('work,isLast') && e('这是工作内容11,1'); // 查询最后添加的日志
r($task->getEstimateByIdTest($estimateIdList[3])) && p()              && e('0');                // 查询不存在的日志
