#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/execution.unittest.class.php';

$execution = zenData('project');
$execution->id->range('1-5');
$execution->name->range('项目1,项目2,迭代1,迭代2,迭代3');
$execution->project->range('0,0,1,1,2');
$execution->type->range('project{2},sprint,waterfall,kanban');
$execution->status->range('doing{3},closed,doing');
$execution->parent->range('0,0,1,1,2');
$execution->grade->range('2{2},1{3}');
$execution->path->range('1,2,`1,3`,`1,4`,`2,5`')->prefix(',')->postfix(',');
$execution->begin->range('20230102 000000:0')->type('timestamp')->format('YY/MM/DD');
$execution->end->range('20230212 000000:0')->type('timestamp')->format('YY/MM/DD');
$execution->storyType->range('story');
$execution->gen(5);

$projectstory = zenData('projectstory');
$projectstory->project->range('3-5');
$projectstory->product->range('1-3');
$projectstory->story->range('4,324,364');
$projectstory->gen(3);

$product = zenData('product');
$product->id->range('1-3');
$product->name->range('1-3')->prefix('产品');
$product->code->range('1-3')->prefix('product');
$product->type->range('normal');
$product->status->range('normal');
$product->gen(3);

$stroy = zenData('story');
$stroy->id->range('4,324,364');
$stroy->title->range('1-3')->prefix('需求');
$stroy->type->range('story');
$stroy->status->range('active');
$stroy->gen(3);

$cell = zenData('kanbancell');
$cell->id->range('1');
$cell->kanban->range('5');
$cell->gen(1);

su('admin');

/**

title=测试executionModel->unlinkStoryTest();
timeout=0
cid=1

*/

$executionIDList = array(3, 4, 5);
$stories         = array(4, 324, 364);
$count           = array(0, 1);

$execution = new executionTest();
r($execution->unlinkStoryTest($executionIDList[0], $stories[0], $stories, $count[0])) && p('0:project,product,story') && e('3,1,324'); // 敏捷执行解除关联需求，移除迭代3中的需求4
r($execution->unlinkStoryTest($executionIDList[1], $stories[1], $stories, $count[0])) && p('0:project,product,story') && e('4,1,4');   // 瀑布执行解除关联需求，移除迭代4中的需求324
r($execution->unlinkStoryTest($executionIDList[2], $stories[2], $stories, $count[0])) && p('0:project,product,story') && e('5,1,4');   // 看板执行解除关联需求，移除迭代5中的需求364
r($execution->unlinkStoryTest($executionIDList[0], $stories[0], $stories, $count[1])) && p()                          && e('2');       // 敏捷执行关联需求统计
r($execution->unlinkStoryTest($executionIDList[1], $stories[1], $stories, $count[1])) && p()                          && e('2');       // 瀑布执行关联需求统计
r($execution->unlinkStoryTest($executionIDList[2], $stories[2], $stories, $count[1])) && p()                          && e('2');       // 看板执行关联需求统计
