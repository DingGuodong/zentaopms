#!/usr/bin/env php
<?php

/**

title=测试 storyModel->isSuperReviewer();
cid=0

- superReviewers变量中无该账号。 @0
- superReviewers变量中有该账号。 @1

*/
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/story.class.php';
su('admin');

global $tester;
$storyModel = $tester->loadModel('story');

$storyModel->app->user->account = 'admin';
$storyModel->config->story->superReviewers = '';
r((int)$storyModel->isSuperReviewer())  && p() && e('0'); // superReviewers变量中无该账号。

$storyModel->config->story->superReviewers = 'admin';
r((int)$storyModel->isSuperReviewer())  && p() && e('1'); // superReviewers变量中有该账号。
