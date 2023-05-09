#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/task.class.php';
su('admin');

zdTable('project')->config('project')->gen(5);
zdTable('task')->config('task')->gen(8);
zdTable('kanbanlane')->config('kanbanlane')->gen(10);
zdTable('kanbancolumn')->config('kanbancolumn')->gen(18);
zdTable('kanbancell')->config('kanbancell')->gen(18);

/**

title=taskModel->updateKanbanData();
timeout=0
cid=2

*/

$executionIdList = array(0, 2, 4);
$taskIdList      = array(0, 1, 7);
$laneIdList      = array(0, 1, 4);
$oldColumnIdList = array(0, 1, 4);

$taskTester = new taskTest();
r($taskTester->updateKanbanDataTest($executionIdList[0], $taskIdList[0], $laneIdList[0], $oldColumnIdList[0])) && p()                 && e('0');         // 空数据的情况
r($taskTester->updateKanbanDataTest($executionIdList[1], $taskIdList[0], $laneIdList[0], $oldColumnIdList[0])) && p()                 && e('0');         // 测试迭代下更新看板数据时，任务为空的情况
r($taskTester->updateKanbanDataTest($executionIdList[0], $taskIdList[1], $laneIdList[0], $oldColumnIdList[0])) && p()                 && e('0');         // 测试迭代下更新看板数据时，执行为空的情况
r($taskTester->updateKanbanDataTest($executionIdList[0], $taskIdList[0], $laneIdList[0], $oldColumnIdList[0])) && p()                 && e('0');         // 测试迭代下更新看板数据时，执行跟任务为空的情况
r($taskTester->updateKanbanDataTest($executionIdList[1], $taskIdList[1], $laneIdList[1], $oldColumnIdList[0])) && p('execution,type') && e('1,task');    // 测试迭代下更新看板数据时，看板列ID为空时，获取看板泳道的信息
r($taskTester->updateKanbanDataTest($executionIdList[1], $taskIdList[1], $laneIdList[0], $oldColumnIdList[1])) && p('laneType,type')  && e('task,wait'); // 测试迭代下更新看板数据时，看板泳道ID为空时，获取看板列的信息
r($taskTester->updateKanbanDataTest($executionIdList[1], $taskIdList[1], $laneIdList[1], $oldColumnIdList[1])) && p('laneType,type')  && e('task,wait'); // 测试迭代下更新看板数据时，看板泳道的信息

r($taskTester->updateKanbanDataTest($executionIdList[2], $taskIdList[0], $laneIdList[0], $oldColumnIdList[0])) && p()                 && e('0');          // 测试看板执行下更新看板数据时，任务为空的情况
r($taskTester->updateKanbanDataTest($executionIdList[0], $taskIdList[2], $laneIdList[0], $oldColumnIdList[0])) && p()                 && e('0');          // 测试看板执行下更新看板数据时，执行为空的情况
r($taskTester->updateKanbanDataTest($executionIdList[0], $taskIdList[0], $laneIdList[0], $oldColumnIdList[0])) && p()                 && e('0');          // 测试看板执行下更新看板数据时，执行跟任务为空的情况
r($taskTester->updateKanbanDataTest($executionIdList[2], $taskIdList[2], $laneIdList[2], $oldColumnIdList[0])) && p('execution,type') && e('0,common');   // 测试看板执行下更新看板数据时，看板列ID为空时，获取看板泳道的信息
r($taskTester->updateKanbanDataTest($executionIdList[2], $taskIdList[2], $laneIdList[0], $oldColumnIdList[2])) && p('laneType,type')  && e('task,pause'); // 测试看板执行下更新看板数据时，看板泳道ID为空时，获取看板列的信息
r($taskTester->updateKanbanDataTest($executionIdList[2], $taskIdList[2], $laneIdList[2], $oldColumnIdList[2])) && p('laneType,type')  && e('task,pause'); // 测试看板执行下更新看板数据时，看板泳道的信息
