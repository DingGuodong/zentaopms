#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/compile.class.php';
su('admin');

/**

title=测试 compileModel->getListByJobID();
cid=1
pid=1

检查是否能拿到数据 >> 这是一个Job1
检查传一个不存在的jobid会返回什么 >> 0

*/

$compile = new compileTest();

r($compile->getListByJobIDTest('1')) && p('1:name') && e('这是一个Job1'); //检查是否能拿到数据
r($compile->getListByJobIDTest('3')) && p()         && e('0');            //检查传一个不存在的jobid会返回什么