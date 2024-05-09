#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/execution.unittest.class.php';
zenData('user')->gen(5);
su('admin');

$execution = zenData('project');
$execution->id->range('1-5');
$execution->name->range('项目集1,项目1,父阶段1,子阶段1,子阶段2');
$execution->type->range('program,project,stage{3}');
$execution->parent->range('0,1,0,3{2}');
$execution->status->range('wait');
$execution->openedBy->range('admin,user1');
$execution->begin->range('20220112 000000:0')->type('timestamp')->format('YY/MM/DD');
$execution->end->range('20220212 000000:0')->type('timestamp')->format('YY/MM/DD');
$execution->gen(5);

/**

title=测试executionModel->getChildExecutionsTest();
timeout=0
cid=1

*/

$executionID = 3;
$count       = array(0, 1);

$executionTester = new executionTest();
r($executionTester->getChildExecutionsTest($executionID, $count[0])) && p('4:name') && e('子阶段1'); // 查询子阶段
r($executionTester->getChildExecutionsTest($executionID, $count[1])) && p()         && e('2');       // 查询子阶段数量
