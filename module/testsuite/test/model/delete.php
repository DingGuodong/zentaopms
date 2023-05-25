#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/testsuite.class.php';
su('admin');

/**

title=测试 testsuiteModel->delete();
cid=1
pid=1

测试suiteID值为1 >> 1
测试suiteID值为1000 >> 1
测试suiteID值为0 >> 1

*/
$suiteID = array(1, 1000, 0);

$testsuite = new testsuiteTest();

r($testsuite->deleteTest($suiteID[0])) && p() && e('1');  //测试suiteID值为1
r($testsuite->deleteTest($suiteID[1])) && p() && e('1');  //测试suiteID值为1000
r($testsuite->deleteTest($suiteID[2])) && p() && e('1');  //测试suiteID值为0
