#!/usr/bin/env php
<?php

/**

title=测试 mrModel::apiCloseMR();
timeout=0
cid=0

- 不存在的主机 @0
- 重新打开并关闭Gitlab合并请求
 - 属性title @test
 - 属性state @closed

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/mr.unittest.class.php';

zenData('pipeline')->gen(5);
su('admin');

$mrModel = new mrTest();

$hostID = array
(
    'gitlab' => 1,
    'error'  => 10
);

$projectID = array
(
    'gitlab' => 3,
);

$mrID = array
(
    'gitlab' => 114,
);

r($mrModel->apiCloseMrTester($hostID['error'], $projectID['gitlab'], $mrID['gitlab'])) && p() && e('0'); // 不存在的主机
r($mrModel->apiCloseMrTester($hostID['gitlab'], $projectID['gitlab'], $mrID['gitlab'])) && p('title,state') && e('test,closed'); // 重新打开并关闭Gitlab合并请求