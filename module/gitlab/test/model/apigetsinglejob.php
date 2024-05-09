#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/gitlab.unittest.class.php';
su('admin');

/**

title=测试gitlabModel->apiGetSingleJob();
timeout=0
cid=1

- 查询正确的job信息属性stage @deploy
- 使用不存在的gitlabID查询job信息 @0
- 使用不存在的projectID查询job信息属性message @404 Project Not Found
- 使用不存在的jobID查询job信息属性message @404 Not found

*/

zenData('job')->gen(5);

$gitlab = $tester->loadModel('gitlab');

$gitlabID  = 1;
$projectID = 2;
$jobID     = 8;

$job1 = $gitlab->apiGetSingleJob($gitlabID, $projectID, $jobID);
$job2 = $gitlab->apiGetSingleJob(0, $projectID, $jobID);
$job3 = $gitlab->apiGetSingleJob($gitlabID, 0, $jobID);
$job4 = $gitlab->apiGetSingleJob($gitlabID, $projectID, 10001);

r($job1) && p('stage')   && e('deploy');                // 查询正确的job信息
r($job2) && p()          && e('0');                     // 使用不存在的gitlabID查询job信息
r($job3) && p('message') && e('404 Project Not Found'); // 使用不存在的projectID查询job信息
r($job4) && p('message') && e('404 Not found');         // 使用不存在的jobID查询job信息