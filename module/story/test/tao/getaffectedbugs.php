#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . "/test/lib/init.php";
include dirname(__FILE__, 2) . '/story.class.php';

$story = zdTable('story');
$story->product->range(1);
$story->plan->range('0,1,0{100}');
$story->duplicateStory->range('0,4,0{100}');
$story->linkStories->range('0,6,0{100}');
$story->linkRequirements->range('3,0{100}');
$story->toBug->range('0{9},1,0{100}');
$story->parent->range('0{17},`-1`,0,18,0{100}');
$story->twins->range('``{27},30,``,28');
$story->gen(30);

$storySpec = zdTable('storyspec');
$storySpec->story->range('1-30{3}');
$storySpec->version->range('1-3');
$storySpec->gen(90);

$bug = zdTable('bug');
$bug->story->range('2-30:2');
$bug->gen(40);

/**

title=测试 storyModel->getAffectedBugs();
cid=1
pid=1

*/

$story = new storyTest();
$affectedStory2  = $story->getAffectedBugsTest(2);
$affectedStory28 = $story->getAffectedBugsTest(28);

r(count($affectedStory2->bugs))  && p() && e('3');  //获取需求2关联bug数
r(count($affectedStory28->bugs)) && p() && e('4');  //获取需求28关联bug数，包含孪生需求
