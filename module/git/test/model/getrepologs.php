#!/usr/bin/env php
<?php

/**

title=测试gitModel->getRepoLogs();
timeout=0
cid=1

- 分支名为空，返回默认分支的日志
 - 属性revision @b362ea7aa65515dc35ff3a93423478b2143e771d
 - 属性msg @Initial commit
- 分支名为test1，返回空数组属性revision @~~
- 分支名为test1000，返回主干的日志
 - 属性revision @b362ea7aa65515dc35ff3a93423478b2143e771d
 - 属性msg @Initial commit

*/
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/git.unittest.class.php';

zenData('pipeline')->gen(1);
zenData('repo')->loadYaml('repo')->gen(1);
su('admin');

$git = new gitTest();

$repoID = 1;

$branch = '';
$result = $git->getRepoLogs($repoID, $branch);
r(end($result)) && p('revision,msg') && e('b362ea7aa65515dc35ff3a93423478b2143e771d,Initial commit'); // 分支名为空，返回默认分支的日志

$branch = 'test1';
r($git->getRepoLogs($repoID, $branch)) && p('revision') && e('~~'); // 分支名为test1，返回空数组

$branch = 'test1000';
$result = $git->getRepoLogs($repoID, $branch);
r(end($result)) && p('revision,msg') && e('b362ea7aa65515dc35ff3a93423478b2143e771d,Initial commit'); // 分支名为test1000，返回主干的日志