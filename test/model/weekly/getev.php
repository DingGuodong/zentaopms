#!/usr/bin/env php
<?php
include dirname(dirname(dirname(__FILE__))) . '/lib/init.php';
include dirname(dirname(dirname(__FILE__))) . '/class/weekly.class.php';
su('admin');

/**

title=测试 weeklyModel->getEV();
cid=1
pid=1

*/

$weekly = new weeklyTest();

r($weekly->getEVTest()) && p() && e();