#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/testsuite.unittest.class.php';

zenData('case')->gen(20);
zenData('suitecase')->gen(3);
zenData('testsuite')->gen(1);
zenData('user')->gen(1);

su('admin');

/**

title=测试 testsuiteModel->deleteCaseBySuiteIDTest();
cid=1
pid=1

检查当前套件的关联用例                >> 2
测试suiteiD值为1,cases值为array(1, 2) >> 2
检查当前套件的关联用例数量            >> 0

*/
$suiteID    = 1;
$cases = array(1, 2);

$testsuite = new testsuiteTest();

r(count($testsuite->getLinkedCasesTest($suiteID)))       && p() && e('2');        //检查当前关联的用例数量
r($testsuite->deleteCaseBySuiteIDTest($cases, $suiteID)) && p() && e('2');        //测试suiteID值为1
unset(dao::$cache[TABLE_CASE]);
r(count($testsuite->getLinkedCasesTest($suiteID)))       && p() && e('0');        //检查当前关联的用例数量
