#!/usr/bin/env php
<?php

/**

title=测试 docModel->getLinkedProjectData();
cid=1

- 获取开源版系统中所有关联项目的数据属性4 @SELECT id FROM `zt_design` WHERE `project`  = '0' AND  `deleted`  = '0'
- 获取旗舰版系统中所有关联项目的数据属性1 @SELECT id FROM `zt_issue` WHERE `project`  = '0' AND  `deleted`  = '0'
- 获取ipd版系统中所有关联项目的数据属性2 @SELECT id FROM `zt_meeting` WHERE `project`  = '0' AND  `deleted`  = '0'
- 获取开源版系统中所有关联项目ID=11的数据属性4 @SELECT id FROM `zt_design` WHERE `project`  = '11' AND  `deleted`  = '0'
- 获取旗舰版系统中所有关联项目ID=11的数据属性1 @SELECT id FROM `zt_issue` WHERE `project`  = '11' AND  `deleted`  = '0'
- 获取ipd版系统中所有关联项目ID=11的数据属性2 @SELECT id FROM `zt_meeting` WHERE `project`  = '11' AND  `deleted`  = '0'
- 获取开源版系统中所有关联项目ID=60的数据属性4 @SELECT id FROM `zt_design` WHERE `project`  = '60' AND  `deleted`  = '0'
- 获取旗舰版系统中所有关联项目ID=60的数据属性1 @SELECT id FROM `zt_issue` WHERE `project`  = '60' AND  `deleted`  = '0'
- 获取ipd版系统中所有关联项目ID=60的数据属性2 @SELECT id FROM `zt_meeting` WHERE `project`  = '60' AND  `deleted`  = '0'

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/doc.unittest.class.php';

$projectstoryTable = zenData('projectstory');
$projectstoryTable->project->range('11, 60, 61, 100');
$projectstoryTable->gen(20);

$issuetable = zdtable('issue');
$issuetable->project->range('11, 60, 61, 100');
$issuetable->gen(20);

$meetingTable = zenData('meeting');
$meetingTable->project->range('11, 60, 61, 100');
$meetingTable->gen(20);

$reviewTable = zenData('review');
$reviewTable->project->range('11, 60, 61, 100');
$reviewTable->gen(20);

$designTable = zenData('design');
$designTable->project->range('11, 60, 61, 100');
$designTable->gen(20);

$taskTable = zenData('task');
$taskTable->execution->range('101-110');
$taskTable->gen(20);

$buildTable = zenData('build');
$buildTable->execution->range('101-110');
$buildTable->gen(20);

zenData('project')->loadYaml('execution')->gen(10);
zenData('user')->gen(5);
su('admin');

$projects = array(0, 11, 60);
$editions = array('open', 'max', 'ipd');

$docTester = new docTest();
r($docTester->getLinkedProjectDataTest($projects[0], $editions[0])) && p('4') && e("SELECT id FROM `zt_design` WHERE `project`  = '0' AND  `deleted`  = '0'");   // 获取开源版系统中所有关联项目的数据
r($docTester->getLinkedProjectDataTest($projects[0], $editions[1])) && p('1') && e("SELECT id FROM `zt_issue` WHERE `project`  = '0' AND  `deleted`  = '0'");    // 获取旗舰版系统中所有关联项目的数据
r($docTester->getLinkedProjectDataTest($projects[0], $editions[2])) && p('2') && e("SELECT id FROM `zt_meeting` WHERE `project`  = '0' AND  `deleted`  = '0'");  // 获取ipd版系统中所有关联项目的数据
r($docTester->getLinkedProjectDataTest($projects[1], $editions[0])) && p('4') && e("SELECT id FROM `zt_design` WHERE `project`  = '11' AND  `deleted`  = '0'");  // 获取开源版系统中所有关联项目ID=11的数据
r($docTester->getLinkedProjectDataTest($projects[1], $editions[1])) && p('1') && e("SELECT id FROM `zt_issue` WHERE `project`  = '11' AND  `deleted`  = '0'");   // 获取旗舰版系统中所有关联项目ID=11的数据
r($docTester->getLinkedProjectDataTest($projects[1], $editions[2])) && p('2') && e("SELECT id FROM `zt_meeting` WHERE `project`  = '11' AND  `deleted`  = '0'"); // 获取ipd版系统中所有关联项目ID=11的数据
r($docTester->getLinkedProjectDataTest($projects[2], $editions[0])) && p('4') && e("SELECT id FROM `zt_design` WHERE `project`  = '60' AND  `deleted`  = '0'");  // 获取开源版系统中所有关联项目ID=60的数据
r($docTester->getLinkedProjectDataTest($projects[2], $editions[1])) && p('1') && e("SELECT id FROM `zt_issue` WHERE `project`  = '60' AND  `deleted`  = '0'");   // 获取旗舰版系统中所有关联项目ID=60的数据
r($docTester->getLinkedProjectDataTest($projects[2], $editions[2])) && p('2') && e("SELECT id FROM `zt_meeting` WHERE `project`  = '60' AND  `deleted`  = '0'"); // 获取ipd版系统中所有关联项目ID=60的数据
