#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/execution.class.php';

$execution = zdTable('project');
$execution->id->range('1-5');
$execution->name->range('项目1,项目2,迭代1,迭代2,迭代3');
$execution->type->range('project{2},sprint,waterfall,kanban');
$execution->status->range('doing{3},closed,doing');
$execution->parent->range('0,0,1,1,2');
$execution->grade->range('2{2},1{3}');
$execution->path->range('1,2,`1,3`,`1,4`,`2,5`')->prefix(',')->postfix(',');
$execution->begin->range('20230102 000000:0')->type('timestamp')->format('YY/MM/DD');
$execution->end->range('20230212 000000:0')->type('timestamp')->format('YY/MM/DD');
$execution->gen(5);

$projectstory = zdTable('projectcase');
$projectstory->project->range('3-5');
$projectstory->product->range('1,43,68');
$projectstory->case->range('4,324,364');
$projectstory->gen(3);

$product = zdTable('product');
$product->id->range('1,43,68');
$product->name->range('1-3')->prefix('产品');
$product->code->range('1-3')->prefix('product');
$product->type->range('normal');
$product->status->range('normal');
$product->gen(3);

$stroy = zdTable('story');
$stroy->id->range('2,170,270');
$stroy->title->range('1-3')->prefix('需求');
$stroy->type->range('story');
$stroy->status->range('active');
$stroy->gen(3);

su('admin');

/**

title=测试executionModel->linkCasesTest();
cid=1
pid=1

敏捷执行关联用例     >> 3,1,1
瀑布执行关联用例     >> 4,43,169
看板执行关联用例     >> 5,68,269
敏捷执行关联用例统计 >> 8
瀑布执行关联用例统计 >> 4
看板执行关联用例统计 >> 4

*/

$executionIDList = array('3', '4', '5');
$products        = array('1', '43', '68');
$stories         = array('2', '170', '270');
$count           = array('0','1');

$execution = new executionTest();
r($execution->linkCasesTest($executionIDList[0], $count[0], $products[0], $stories[0])) && p('0:project,product,case') && e('3,1,1');    // 敏捷执行关联用例
r($execution->linkCasesTest($executionIDList[1], $count[0], $products[1], $stories[1])) && p('0:project,product,case') && e('4,43,169'); // 瀑布执行关联用例
r($execution->linkCasesTest($executionIDList[2], $count[0], $products[2], $stories[2])) && p('0:project,product,case') && e('5,68,269'); // 看板执行关联用例
r($execution->linkCasesTest($executionIDList[0], $count[1], $products[0], $stories[0])) && p()                         && e('8');        // 敏捷执行关联用例统计
r($execution->linkCasesTest($executionIDList[1], $count[1], $products[1], $stories[1])) && p()                         && e('4');        // 瀑布执行关联用例统计
r($execution->linkCasesTest($executionIDList[2], $count[1], $products[2], $stories[2])) && p()                         && e('4');        // 看板执行关联用例统计
