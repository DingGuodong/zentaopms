#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/projectrelease.class.php';

zdTable('release')->gen(20);
zdTable('user')->gen(1);

su('admin');

/**

title=测试 projectreleaseModel->getLast();
cid=1
pid=1

*/

$projectID = array(11, 12, 0, 1000);

$projectrelease = new projectreleaseTest();

r($projectrelease->getLastTest($projectID[0])) && p('id') && e('9');  // 测试获取项目 11 的最后一次发布
r($projectrelease->getLastTest($projectID[1])) && p('id') && e('10'); // 测试获取项目 12 的最后一次发布
r($projectrelease->getLastTest($projectID[2])) && p('id') && e('8');  // 测试获取项目 空 的最后一次发布
r($projectrelease->getLastTest($projectID[3])) && p()     && e('0');  // 测试获取项目 不存在 的最后一次发布
