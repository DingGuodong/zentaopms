#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';

$action = zdTable('action');
$action->product->range('`,1,`');
$action->action->range('reviewed');
$action->objectType->range('story');
$action->execution->range('0');
$action->objectID->range('1-3');
$action->extra->range('Revert');
$action->gen(10);

/**

title=测试 storyModel->getRevertStoryIdList();
cid=1
pid=1

*/

global $tester;
$storyModel = $tester->loadModel('story');
r($storyModel->getRevertStoryIdList(0)) && p()  && e('0'); //不传入数据。
r($storyModel->getRevertStoryIdList(1)) && p(1) && e('1'); //传入存在数据的产品 ID。
r($storyModel->getRevertStoryIdList(2)) && p()  && e('0'); //传入不存在数据的产品 ID。
