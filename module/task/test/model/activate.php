#!/usr/bin/env php
<?php

/**

title=taskModel->activate();
cid=0

*/
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/task.unittest.class.php';

zenData('user')->gen(5);
su('admin');

zenData('effort')->gen(0);
zenData('project')->loadYaml('project')->gen(6);
zenData('task')->loadYaml('task')->gen(9);
zenData('taskteam')->loadYaml('taskteam')->gen(6);
zenData('kanbanregion')->loadYaml('kanbanregion')->gen(1);
zenData('kanbanlane')->loadYaml('kanbanlane')->gen(1);
zenData('kanbancolumn')->loadYaml('kanbancolumn')->gen(7);
zenData('kanbancell')->loadYaml('kanbancell')->gen(7);

$accountList      = array('admin', 'user1', 'user2', 'user3');
$teamEstimateList = array(1, 2, 3, 4);
$teamConsumedList = array(4, 3, 2, 1);
$teamLeftList     = array(1, 1, 1, 1);
$teamSourceList   = array('admin', 'user1', 'user2', 'user3');

$teamData = new stdclass();
$teamData->team         = $accountList;
$teamData->teamLeft     = $teamLeftList;
$teamData->teamSource   = $teamSourceList;
$teamData->teamEstimate = $teamEstimateList;
$teamData->teamConsumed = $teamConsumedList;

$emptyTeamData = new stdclass();

$drag = array('fromColID' => 1, 'toColID' => 2, 'fromLaneID' => 1, 'toLaneID' => 1);

$taskIDList = range(1, 9);

$task = new taskTest();
r($task->activateTest($taskIDList[0], '', $emptyTeamData)) && p('0:field,old,new') && e('status,wait,doing');   // wait状态任务激活
r($task->activateTest($taskIDList[1], '', $emptyTeamData)) && p('0:field,old,new') && e('left,1,3');            // doing状态任务激活
r($task->activateTest($taskIDList[2], '', $emptyTeamData)) && p('0:field,old,new') && e('status,done,doing');   // done状态任务激活
r($task->activateTest($taskIDList[3], '', $emptyTeamData)) && p('0:field,old,new') && e('status,cancel,doing'); // cancel状态任务激活
r($task->activateTest($taskIDList[4], '', $emptyTeamData)) && p('0:field,old,new') && e('status,closed,doing'); // closed状态任务激活
r($task->activateTest($taskIDList[7], '', $teamData))      && p('0:field,old,new') && e('status,wait,doing');   // wait状态串行任务激活
r($task->activateTest($taskIDList[8], '', $teamData))      && p('0:field,old,new') && e('left,8,4');            // doing状态并行任务激活

zenData('task')->loadYaml('task')->gen(9);
zenData('taskteam')->loadYaml('taskteam')->gen(6);

r($task->activateTest($taskIDList[0], '', $emptyTeamData, $drag)) && p('0:field,old,new') && e('status,wait,doing');   // wait状态任务激活
r($task->activateTest($taskIDList[1], '', $emptyTeamData, $drag)) && p('0:field,old,new') && e('left,1,3');            // doing状态任务激活
r($task->activateTest($taskIDList[2], '', $emptyTeamData, $drag)) && p('0:field,old,new') && e('status,done,doing');   // done状态任务激活
r($task->activateTest($taskIDList[3], '', $emptyTeamData, $drag)) && p('0:field,old,new') && e('status,cancel,doing'); // cancel状态任务激活
r($task->activateTest($taskIDList[4], '', $emptyTeamData, $drag)) && p('0:field,old,new') && e('status,closed,doing'); // closed状态任务激活
r($task->activateTest($taskIDList[7], '', $teamData,      $drag)) && p('0:field,old,new') && e('status,wait,doing');   // wait状态串行任务激活
r($task->activateTest($taskIDList[8], '', $teamData,      $drag)) && p('0:field,old,new') && e('left,8,4');            // doing状态并行任务激活


