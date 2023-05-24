#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/task.class.php';
su('admin');

zdTable('project')->config('execution')->gen(5);
zdTable('task')->config('task')->gen(5);
zdTable('story')->gen(5);
zdTable('product')->gen(5);

/**

title=taskModel->close();
cid=1
pid=1

wait状态任务关闭 >> status,wait,closed
doing状态任务关闭 >> status,doing,closed
done状态任务关闭 >> status,done,closed
pause状态任务关闭 >> status,pause,closed
cancel状态任务关闭 >> status,cancel,closed

*/

$taskIDList = range(1, 5);

$task = new taskTest();
r($task->closeTest($taskIDList[0])) && p() && e('status-wait-closed');   // wait状态任务关闭
r($task->closeTest($taskIDList[1])) && p() && e('status-doing-closed');  // doing状态任务关闭
r($task->closeTest($taskIDList[2])) && p() && e('status-done-closed');   // done状态任务关闭
r($task->closeTest($taskIDList[3])) && p() && e('status-pause-closed');  // pause状态任务关闭
r($task->closeTest($taskIDList[4])) && p() && e('status-cancel-closed'); // cancel状态任务关闭
