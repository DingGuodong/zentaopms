#!/usr/bin/env php
<?php

/**

title=测试 storyModel->getParentStoryPairs();
cid=0

- 第91个元素和第0个元素的值
 - 属性91 @用户需求91
 -  @~~
- 获取符合条件的需求数 @5
- 测试附加的需求ID1，需求1以数字1结尾属性1 @~f:1$~

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/story.unittest.class.php';
su('admin');

$user = zenData('user');
$user->account->range('admin,user1,user2');
$user->gen(3);

$story = zenData('story');
$story->type->range('requirement{60},story{40}');
$story->assignedTo->range('admin{10},user1{10},user2{80}');
$story->deleted->range('0');
$story->stage->range('wait,closed');
$story->status->range('active,draft');
$story->product->range('1{10},2{5},3{5},4{70},5{10}');
$story->parent->range('0,1');
$story->plan->range('0,1');
$story->gen(100);

$product = zenData('product');
$product->gen(5);

global $tester;
$tester->loadModel('story');
$stories = $tester->story->getParentStoryPairs(5);

r($stories) && p('91,0') && e('用户需求91,~~'); // 第91个元素和第0个元素的值

array_pop($stories); /* remove empty item at array top. */
r(count($stories)) && p() && e(5);  // 获取符合条件的需求数

$storiesWithAppended = $tester->story->getParentStoryPairs(5, 1);
r($storiesWithAppended) && p('1') && e('~f:1$~'); // 测试附加的需求ID1，需求1以数字1结尾
