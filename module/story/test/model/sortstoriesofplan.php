#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/story.class.php';
su('admin');

zdTable('product')->gen(100);
$projectstory = zdTable('projectstory');
$projectstory->project->range('11{50},36{50}');
$projectstory->product->range('1');
$projectstory->story->range('1-50');
$projectstory->gen(100);

$story = zdTable('story');
$story->product->range('1');
$story->gen(50);

$planstory = zdTable('planstory');
$planstory->plan->range('1{20},2{20},3{20}');
$planstory->gen(50);

$project = zdTable('project');
$project->type->range('project{25},sprint{25}');
$project->gen(50);

/**

title=测试 storyModel->sortStoriesOfPlan();
cid=1
pid=1

*/

global $tester;
$tester->loadModel('story');
$beforeStories = $tester->story->getPlanStories(1);
$tester->story->sortStoriesOfPlan(1, array(3, 1, 4, 2));
$afterStories  = $tester->story->getPlanStories(1);

r($beforeStories) && p('1:order') && e('5');  //获取更新排序之前的需求在计划下的排序
r($beforeStories) && p('2:order') && e('10'); //获取更新排序之前的需求在计划下的排序
r($beforeStories) && p('3:order') && e('15'); //获取更新排序之前的需求在计划下的排序
r($beforeStories) && p('4:order') && e('20'); //获取更新排序之前的需求在计划下的排序
r($afterStories)  && p('1:order') && e('2');  //获取更新排序之后的需求在计划下的排序
r($afterStories)  && p('2:order') && e('4');  //获取更新排序之后的需求在计划下的排序
r($afterStories)  && p('3:order') && e('1');  //获取更新排序之后的需求在计划下的排序
r($afterStories)  && p('4:order') && e('3');  //获取更新排序之后的需求在计划下的排序
