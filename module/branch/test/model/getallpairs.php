#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/branch.class.php';
su('admin');

/**

title=测试 branchModel->getAllPairs();
cid=1
pid=1

测试获取全部分支名 >> 167
测试获取全部分支名 >> 166

*/
$params = 'noempty';

$branch = new branchTest();

r($branch->getAllPairsTest())        && p() && e('167'); // 测试获取全部分支名
r($branch->getAllPairsTest($params)) && p() && e('166'); // 测试获取全部分支名