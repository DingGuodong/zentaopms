#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
su('admin');

$user = zdTable('user');
$user->dept->range('0,1');
$user->role->range(',``,qa');
$user->gen(10);

/**

title=测试 storyModel->checkforcereview();
cid=1
pid=1

*/

global $tester;
$tester->loadModel('story');

$tester->story->app->user->account = 'admin';
$tester->story->config->story->needReview = 0;
$tester->story->config->story->forceReview      = '';
$tester->story->config->story->forceReviewRoles = '';
$tester->story->config->story->forceReviewDepts = '';

r((int)$tester->story->checkForceReview()) && p() && e('0');

$tester->story->config->story->forceReview = 'admin,user1';
r((int)$tester->story->checkForceReview()) && p() && e('1');

$tester->story->config->story->forceReview = '';
$tester->story->config->story->forceReviewRoles = 'qa';
r((int)$tester->story->checkForceReview()) && p() && e('0');

$tester->story->config->story->forceReviewRoles = '';
$tester->story->config->story->forceReviewDepts = '1';
r((int)$tester->story->checkForceReview()) && p() && e('0');








$tester->story->config->story->needReview = 1;
$tester->story->config->story->forceNotReview      = '';
$tester->story->config->story->forceNotReviewRoles = '';
$tester->story->config->story->forceNotReviewDepts = '';

r((int)$tester->story->checkForceReview()) && p() && e('1');

$tester->story->config->story->forceNotReview = 'admin,user1';
r((int)$tester->story->checkForceReview()) && p() && e('0');

$tester->story->config->story->forceNotReview = '';
$tester->story->config->story->forceNotReviewRoles = 'qa';
r((int)$tester->story->checkForceReview()) && p() && e('1');

$tester->story->config->story->forceNotReviewRoles = '';
$tester->story->config->story->forceNotReviewDepts = '1';
r((int)$tester->story->checkForceReview()) && p() && e('1');
