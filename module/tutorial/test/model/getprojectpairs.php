#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/tutorial.class.php';
su('admin');

/**

title=测试 tutorialModel->getProjectPairs();
cid=1
pid=1

测试是否能拿到数据 >> Test Project

*/

$tutorial = new tutorialTest();

r($tutorial->getProjectPairsTest()) && p('2') && e('Test Project'); //测试是否能拿到数据