#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . "/test/lib/init.php";
include dirname(__FILE__, 2) . '/job.class.php';
su('admin');

/**

title=jobModel->getSonarqubeByRepo();
cid=1
pid=1

根据repo查询sonarqube >> 这是一个Job1

*/

$repoIDList = array('1', '2');

$job = new jobTest();
r($job->getSonarqubeByRepoTest($repoIDList)) && p('1:name') && e('这是一个Job1');  // 根据repo查询sonarqube
