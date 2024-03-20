#!/usr/bin/env php
<?php

/**

title=测试 storyModel->setStageToClosed();
cid=0

- 不传入任何数据。 @0

*/
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/story.class.php';
su('admin');

$product = zdTable('product');
$product->type->range('normal,branch');
$product->gen(2);
zdTable('storystage')->gen(0);
$story = zdTable('story');
$story->product->range('1,2,2');
$story->branch->range('0,0,1');
$story->gen(3);
global $tester;
$storyModel = $tester->loadModel('story');

$stages    = array();
$stages[0] = 'developed';
$stages[1] = 'testing';
r($storyModel->updateStage(0, $stages)) && p() && e('0'); //不传入任何数据。

$storyTest = new storyTest();
r($storyTest->updateStageTest(1, $stages)) && p('stage') && r('developed'); //传入不是多分支产品的需求，检查需求的阶段。
r($storyTest->updateStageTest(3, $stages)) && p('stage') && r('developed'); //传入从属多分支的需求，检查需求的阶段。

$story = $storyTest->updateStageTest(2, $stages);
r($story) && p('stage') && r('developed');                             //传入属于主干需求，检查需求的阶段。
r(count($story->stages)) && p() && r('2');                             //传入属于主干需求，检查 storystage 记录数。
r($story->stages[0]) && p('story,branch,stage') && r('2,0,developed'); //传入属于主干需求，检查分支为 0 的 storystage 记录。
r($story->stages[1]) && p('story,branch,stage') && r('2,1,testing');   //传入属于主干需求，检查分支为 1 的 storystage 记录。

$oldStages = array();
$oldStages[1] = new stdclass();
$oldStages[1]->story    = 2;
$oldStages[1]->branch   = 1;
$oldStages[1]->stage    = 'developing';
$oldStages[1]->stagedBy = 'admin';

$story = $storyTest->updateStageTest(2, $stages, $oldStages);
r($story) && p('stage') && r('developing');                             //传入属于主干需求，并传入 oldStages 数据，检查需求的阶段。
r(count($story->stages)) && p() && r('2');                              //传入属于主干需求，并传入 oldStages 数据，检查 storystage 记录数。
r($story->stages[0]) && p('story,branch,stage') && r('2,0,developed');  //传入属于主干需求，并传入 oldStages 数据，检查分支为 0 的 storystage 记录。
r($story->stages[1]) && p('story,branch,stage') && r('2,1,developing'); //传入属于主干需求，并传入 oldStages 数据，检查分支为 1 的 storystage 记录。
