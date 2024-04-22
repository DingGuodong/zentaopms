#!/usr/bin/env php
<?php

/**

title=测试 storyModel->getAffectedTwins();
cid=0

- 检查需求2孪生需求 @1
- 检查需求28孪生需求 @1

*/
include dirname(__FILE__, 5) . "/test/lib/init.php";
include dirname(__FILE__, 2) . '/lib/story.unittest.class.php';

$story = zenData('story');
$story->product->range(1);
$story->plan->range('0,1,0{100}');
$story->duplicateStory->range('0,4,0{100}');
$story->linkStories->range('0,6,0{100}');
$story->linkRequirements->range('3,0{100}');
$story->childStories->range('0,8,0{100}');
$story->toBug->range('0{9},1,0{100}');
$story->parent->range('0{17},`-1`,0,18,0{100}');
$story->twins->range('``{27},30,``,28');
$story->gen(30);

$storySpec = zenData('storyspec');
$storySpec->story->range('1-30{3}');
$storySpec->version->range('1-3');
$storySpec->gen(90);

zenData('branch')->gen(5);

$story = new storyTest();
$affectedStory2  = $story->getAffectedTwinsTest(2);
$affectedStory28 = $story->getAffectedTwinsTest(28);

r((int)empty($affectedStory2->twins))      && p() && e('1');  //检查需求2孪生需求
r((int)isset($affectedStory28->twins[30])) && p() && e('1');  //检查需求28孪生需求
