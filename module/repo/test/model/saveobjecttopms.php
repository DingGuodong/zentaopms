#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/repo.unittest.class.php';
su('admin');

/**

title=测试 repoModel->saveObjectToPms();
timeout=0
cid=1

- 开始任务
 - 第1条的objectID属性 @1
 - 第1条的objectType属性 @task
- 完成任务
 - 第8条的objectID属性 @8
 - 第8条的objectType属性 @task
- 工时计算
 - 第2条的objectID属性 @2
 - 第2条的objectType属性 @task
- 修复bug1
 - 第1条的objectID属性 @1
 - 第1条的objectType属性 @bug
- 修复bug2
 - 第2条的objectID属性 @2
 - 第2条的objectType属性 @bug

*/

zenData('task')->gen(10);
$bug = zenData('bug');
$bug->execution->range('0');
$bug->gen(10);
zenData('repo')->loadYaml('repo')->gen(4);
$dao->exec("delete from zt_action where action='gitcommited'");

$repoID   = 1;
$repoRoot = '';
$scm      = 'gitlab';

$log = new stdclass();
$log->revision  = '61e51cadb1aa21ef3d2b51e3f193be3cc19cfef6';
$log->committer = 'root';
$log->time      = '2023-12-29 10:44:36';
$log->comment   = 'Start Task #1 Cost:1h Left:3h,Effort Task #8 Cost:1h Left:3h,Finish Task #2 Cost:10h';
$log->author    = 'user4';
$log->msg       = 'Start Task #1 Cost:1h Left:3h,Effort Task #8 Cost:1h Left:3h,Finish Task #2 Cost:10h';
$log->date      = '2023-12-29 10:44:36';
$log->files     = array('M' => array('/README.md'));
$log->change    = array('/README.md' => array('action' => 'M', 'kind' => 'file', 'oldPath' => ''));

$action  = new stdclass();
$action->actor  = 'user4';
$action->date   = '2023-12-29 13:14:36';
$action->extra  = $scm == 'svn' ? $log->revision : substr($log->revision, 0, 10);
$action->action = 'gitcommited';

$repo = new repoTest();
$result = $repo->saveObjectToPmsTest($log, $action, $repoID, 'task');
r($result) && p('1:objectID,objectType') && e('1,task'); //开始任务
r($result) && p('8:objectID,objectType') && e('8,task'); //完成任务
r($result) && p('2:objectID,objectType') && e('2,task'); //工时计算

$log->msg = $log->comment = 'Fix bug#1,2';
$result = $repo->saveObjectToPmsTest($log, $action, $repoID, 'bug');
r($result) && p('1:objectID,objectType') && e('1,bug'); //修复bug1
r($result) && p('2:objectID,objectType') && e('2,bug'); //修复bug2