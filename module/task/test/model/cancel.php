#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/task.class.php';
su('admin');

/**

title=taskModel->cancel();
timeout=0
cid=1

*/

$task = zdTable('task');
$task->id->range('1-6');
$task->execution->range('2');
$task->name->prefix("任务")->range('1-6');
$task->left->range('0-5');
$task->story->range('0{4},1{2}');
$task->estStarted->range('2022\-01\-01');
$task->assignedTo->prefix("old")->range('1-6');
$task->status->range("wait,doing,done,pause,cancel,closed");
$task->gen(6);

zdTable('story')->gen(1);
zdTable('product')->gen(1);
zdTable('kanbanlane')->config('kanbanlane', true)->gen(10);
zdTable('kanbancolumn')->config('kanbancolumn', true)->gen(18);
zdTable('kanbancell')->config('kanbancell', true)->gen(18);

$taskIDlist = array(1, 2, 3, 4, 5, 6);

$task = new taskTest();
r($task->cancelTest($taskIDlist[0], array('status' => 'cancel', 'comment' => '取消备注1'))) && p('id,name,status') && e('1,任务1,cancel'); // wait状态任务取消
r($task->cancelTest($taskIDlist[1], array('status' => 'cancel', 'comment' => '取消备注2'))) && p('id,name,status') && e('2,任务2,cancel'); // doing状态任务取消
r($task->cancelTest($taskIDlist[2], array('status' => 'cancel', 'comment' => '取消备注3'))) && p('id,name,status') && e('3,任务3,cancel'); // done状态任务取消
r($task->cancelTest($taskIDlist[3], array('status' => 'cancel', 'comment' => '取消备注4'))) && p('id,name,status') && e('4,任务4,cancel'); // pause状态任务取消
r($task->cancelTest($taskIDlist[4], array('status' => 'cancel', 'comment' => '取消备注5'))) && p('id,name,status') && e('5,任务5,cancel'); // cancel状态任务取消
r($task->cancelTest($taskIDlist[5], array('status' => 'cancel', 'comment' => '取消备注6'))) && p('id,name,status') && e('6,任务6,cancel'); // closed状态任务取消
