#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/gitlab.class.php';
su('admin');

/**

title=测试gitlabModel->gitList();
cid=1
pid=1

获取GiLlab列表   >> 1

*/

$gitlab = new gitlabTest();

$orderBy = 'id_desc';
r($gitlab->getList($orderBy)) && p('id') && e('1');    // 获取GitLab列表

system("./ztest init");
