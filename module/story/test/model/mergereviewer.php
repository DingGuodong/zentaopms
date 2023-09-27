#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/story.class.php';
su('admin');

$story = zdTable('story');
$story->product->range(1);
$story->plan->range('0,1,0{100}');
$story->duplicateStory->range('0,4,0{100}');
$story->linkStories->range('0,6,0{100}');
$story->linkRequirements->range('3,0{100}');
$story->childStories->range('0,8,0{100}');
$story->toBug->range('0{9},1,0{100}');
$story->parent->range('0{17},`-1`,0,18,0{100}');
$story->twins->range('``{27},30,``,28');
$story->version->range('1');
$story->gen(30);

$storyReview = zdTable('storyreview');
$storyReview->story->range('1-30{3}');
$storyReview->reviewer->range('admin,user1,user2');
$storyReview->result->range('``{2}, pass');
$storyReview->version->range('1');
$storyReview->gen(90);

/**

title=测试 storyModel->mergeReviewer();
cid=1
pid=1

*/

global $tester;
$story = $tester->loadModel('story')->getById(2);
$story = $tester->story->mergeReviewer($story, true);
r(implode('|', $story->reviewer))  && p()  && e('admin|user1|user2'); // 获取需求2的评审人
r(implode('|', $story->notReview)) && p()  && e('admin|user1');       // 获取需求2的待评审人

$story = $tester->loadModel('story')->getById(18);
$story = $tester->story->mergeReviewer($story, true);
r(implode('|', $story->reviewer))  && p()  && e('admin|user1|user2');               // 获取需求18的评审人
r(implode('|', $story->notReview)) && p()  && e('admin|user1');                     // 获取需求18的待评审人
r(implode('|', $story->children[20]->reviewer))  && p()  && e('admin|user1|user2'); // 获取需求18子需求的评审人
r(implode('|', $story->children[20]->notReview)) && p()  && e('admin|user1');       // 获取需求18子需求的待评审人

$stories = $tester->story->mergeReviewer(array(18 => $story));

r(implode('|', $stories[18]->reviewer))  && p()  && e('admin|user1|user2');               // 批量获取需求18的评审人
r(implode('|', $stories[18]->notReview)) && p()  && e('admin|user1');                     // 批量获取需求18的待评审人
r(implode('|', $stories[18]->children[20]->reviewer))  && p()  && e('admin|user1|user2'); // 批量获取需求18子需求的评审人
r(implode('|', $stories[18]->children[20]->notReview)) && p()  && e('admin|user1');       // 批量获取需求18子需求的待评审人
