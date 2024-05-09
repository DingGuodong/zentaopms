#!/usr/bin/env php
<?php

/**

title=测试 programplanTao->computeNewParenAndAction();
cid=0

- 返回状态为wait属性status @wait
- 返回状态为closed属性status @closed
- 返回状态为suspended属性status @suspended
- 返回状态为doing属性status @doing

*/
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/programplan.unittest.class.php';
su('admin');

zenData('project')->loadYaml('project')->gen(13);
zenData('task')->loadYaml('task')->gen(13);

global $tester;
$programplan = new programplanTest();
$tester->loadModel('programplan')->programplanTao;

$project1 = $programplan->getByIdTest(1);
$project  = $programplan->getByIdTest(2);
$result   = $tester->programplan->getNewParentAndAction(array('wait' => 1), $project, 0, 'edit', $project1);
$parent1  = $result['newParent'];

$project = $programplan->getByIdTest(5);
$result  = $tester->programplan->getNewParentAndAction(array('closed' => 1), $project, 0, 'edit', $project1);
$parent2 = $result['newParent'];

$project = $programplan->getByIdTest(8);
$result  = $tester->programplan->getNewParentAndAction(array('suspended' => 1), $project, 0, 'edit', $project1);
$parent3 = $result['newParent'];

$project = $programplan->getByIdTest(11);
$result  = $tester->programplan->getNewParentAndAction(array('wait' => 2, 'closed' => 1), $project, 0, 'edit', $project1);
$parent4 = $result['newParent'];

r($parent1)        && p('status') && e('wait');      // 返回状态为wait
r($parent2)        && p('status') && e('closed');    // 返回状态为closed
r($parent3)        && p('status') && e('suspended'); // 返回状态为suspended
r($parent4)        && p('status') && e('doing');     // 返回状态为doing
