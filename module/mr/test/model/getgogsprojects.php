#!/usr/bin/env php
<?php

/**

title=测试 mrModel::getGogsProjects();
timeout=0
cid=1

- 服务器ID正确
 - 第easycorp/unittest条的id属性 @1
 - 第easycorp/unittest条的name属性 @unittest
- 服务器ID为空 @0
- 服务器ID不存在 @0

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/mr.unittest.class.php';

zenData('pipeline')->gen(5);
su('admin');

$mrTester = new mrTest();

r($mrTester->getGogsProjectsTester(5))   && p('easycorp/unittest:id,name') && e('1,unittest'); // 服务器ID正确
r($mrTester->getGogsProjectsTester(0))   && p()                            && e('0');          // 服务器ID为空
r($mrTester->getGogsProjectsTester(100)) && p()                            && e('0');          // 服务器ID不存在