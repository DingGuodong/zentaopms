#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/task.class.php';
su('admin');

zdTable('task')->config('task', true)->gen(30);

/**

title=taskModel->updateOrderByGantt();
timeout=0
cid=1

*/

$taskIdList[0] = array(1, 2, 3, 4, 5);
$taskIdList[1] = array(2, 3, 1, 4, 5);
$taskIdList[2] = array(4, 2, 3, 1, 5);

$taskTester = new taskTest();

r($taskTester->updateOrderByGanttTest(3, 1, $taskIdList[0])) && p() && e('1,2,3,4,5'); // 测试更新开发任务11的顺序
r($taskTester->updateOrderByGanttTest(3, 1, $taskIdList[1])) && p() && e('3,1,2,4,5'); // 测试更新开发任务11的顺序
r($taskTester->updateOrderByGanttTest(3, 4, $taskIdList[2])) && p() && e('4,2,3,1,5'); // 测试更新开发任务14的顺序
