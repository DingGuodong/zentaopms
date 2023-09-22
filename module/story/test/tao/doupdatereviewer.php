#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/story.class.php';
su('admin');

$story = zdTable('story');
$story->id->range('1-10');
$story->version->range('1');
$story->twins->range('2,1');
$story->gen(2);

$storyReview = zdTable('storyreview');
$storyReview->story->range('1{2},2{2}');
$storyReview->version->range('1');
$storyReview->reviewer->range('admin,user1,admin,user1');
$storyReview->gen(4);

/**

title=测试 storyModel->doUpdateReviewer();
cid=1
pid=1

*/

$reviewers = array('user2', 'user3');

$story = new storyTest();
r($story->doUpdateReviewerTest(0, array()))    && p('0:reviewer') && e('admin'); // 不传入任何数据。
r($story->doUpdateReviewerTest(0, $reviewers)) && p() && e('0'); // 只传入评审人列表。
r($story->doUpdateReviewerTest(1, array()))    && p() && e('0'); // 只传入软件需求 ID。
r($story->doUpdateReviewerTest(1, $reviewers)) && p('0:reviewer') && e('users'); // 只传入软件需求 ID。

$reviewers = $story->objectModel->dao->select('*')->from(TABLE_STORYREVIEW)->where('story')->eq('2')->fetchAll();
r($reviewers[0]) && p('reviewer') && e('user1'); // 传入软件需求 ID 和 评审人列表，查看storyreview表记录的关系。
r($reviewers[1]) && p('reviewer') && e('user2'); // 传入软件需求 ID 和 评审人列表，查看storyreview表记录的关系。
