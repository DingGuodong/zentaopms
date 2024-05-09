#!/usr/bin/env php
<?php

/**

title=测试 storyModel->submitReview();
cid=0

- 执行$storyList[28]属性status @reviewing
- 执行$storyList[30]属性status @reviewing
- 执行$storyReviewList[28] @admin|user1|user2
- 执行$storyReviewList[30] @admin|user1|user2

*/
include dirname(__FILE__, 5) . '/test/lib/init.php';
su('admin');

$story = zenData('story');
$story->product->range(1);
$story->plan->range('0,1,0{100}');
$story->duplicateStory->range('0,4,0{100}');
$story->linkStories->range('0,6,0{100}');
$story->linkRequirements->range('3,0{100}');
$story->toBug->range('0{9},1,0{100}');
$story->parent->range('0{17},`-1`,0,18,0{100}');
$story->twins->range('``{27},30,``,28');
$story->version->range('3');
$story->gen(30);

$storySpec = zenData('storyspec');
$storySpec->story->range('1-30{3}');
$storySpec->version->range('1-3');
$storySpec->gen(90);

$storyReview = zenData('storyreview');
$storyReview->story->range('1-30');
$storyReview->reviewer->range('admin');
$storyReview->version->range('3');
$storyReview->gen(30);

global $tester;
$tester->loadModel('story');

$storyData = new stdclass();
$storyData->reviewer   = array('admin', 'user1', 'user2');
$storyData->reviewedBy = '';

$tester->story->submitReview(28, $storyData);
$storyList       = $tester->story->dao->select('*')->from(TABLE_STORY)->where('id')->in('28,30')->fetchAll('id');
$storyReviewList = $tester->story->dao->select('*')->from(TABLE_STORYREVIEW)->where('story')->in('28,30')->fetchGroup('story', 'reviewer');

r($storyList[28]) && p('status') && e('reviewing');
r($storyList[30]) && p('status') && e('reviewing');
r(implode('|', array_keys($storyReviewList[28]))) && p() && e('admin|user1|user2');
r(implode('|', array_keys($storyReviewList[30]))) && p() && e('admin|user1|user2');
