#!/usr/bin/env php
<?php

/**

title=测试 fileModel->setFileWebAndRealPaths();
cid=0

- 检查 id=1 的真实路径 @1
- 检查 id=1 的网络路径 @1
- 检查 id=5 的真实路径 @1
- 检查 id=5 的网络路径 @1

*/
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/file.unittest.class.php';
su('admin');

$file = zenData('file');
$file->pathname->range('202305/0414225006610005,202305/0414225006610006,202305/0414225006610007,202305/0414225006610008,202305/0414225006610009,202305/0414225006610010,202305/0414225006610011');
$file->gen(20);

$file = new fileTest();

$file1 = $file->setFileWebAndRealPathsTest(1);
$file5 = $file->setFileWebAndRealPathsTest(5);
r(strpos($file1->realPath, 'data/upload/1/202305/0414225006610005') !== false) && p() && e('1'); //检查 id=1 的真实路径
r(strpos($file1->webPath,  'data/upload/1/202305/0414225006610005') !== false) && p() && e('1'); //检查 id=1 的网络路径
r(strpos($file5->realPath, 'data/course/202305/0414225006610009') !== false)   && p() && e('1'); //检查 id=5 的真实路径
r(strpos($file5->webPath,  'data/course/202305/0414225006610009') !== false)   && p() && e('1'); //检查 id=5 的网络路径
