#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/repo.unittest.class.php';
su('admin');

/**

title=测试 repoModel->rmClientVersionFile();
timeout=0
cid=1

- 测试删除文件 @1

*/

$repo = new repoTest();

r($repo->rmClientVersionFileTest()) && p() && e('1'); //测试删除文件