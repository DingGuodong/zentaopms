#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . "/test/lib/init.php";
include dirname(__FILE__, 2) . '/bug.class.php';
su('admin');

/**

title=bugModel->create();
timeout=0
cid=1

- 执行bug模块的createOb方法，参数是$bug_codeerror属性title @bug_codeerror

- 执行bug模块的createOb方法，参数是$bug_project属性project @1

- 执行bug模块的createOb方法，参数是$bug_execution属性execution @2

- 执行bug模块的createOb方法，参数是$bug_priseverity
 - 属性pri @2
 - 属性severity @2

- 执行bug模块的createOb方法，参数是$bug_steps属性steps @i wish

- 执行bug模块的createOb方法，参数是$bug_deadline属性deadline @2023-04-23

- 执行bug模块的createOb方法，参数是$bug_assign属性assignedTo @admin

- 执行bug模块的createOb方法，参数是$bug_notitle第title条的0属性 @『Bug标题』不能为空。

- 执行bug模块的createOb方法，参数是$bug_nobuild第openedBuild条的0属性 @『影响版本』不能为空。

- 执行bug模块的createOb方法，参数是$bug_erremail第notifyEmail条的0属性 @『通知邮箱』应当为合法的EMAIL。



*/

$bug_codeerror    = array('title' => 'bug_codeerror', 'type' => 'codeerror');
$bug_project      = array('title' => 'bug_project', 'project' => 1);
$bug_execution    = array('title' => 'bug_execution', 'execution' => 2);
$bug_priseverity  = array('title' => 'bug_pri', 'pri' => 2, 'severity' => 2);
$bug_steps        = array('title' => 'bug_steps', 'steps' => 'i wish');
$bug_deadline     = array('title' => 'bug_deadline', 'deadline' => '2023-04-23');
$bug_assign       = array('title' => 'bug_assign', 'assignedTo' => 'admin');
$bug_notitle      = array('title' => '');
$bug_nobuild      = array('title' => 'bug_nobuild', 'openedBuild' => '');
$bug_erremail     = array('title' => 'bug_erremail', 'notifyEmail' => '123456');


$bug = new bugTest();
r($bug->createObject($bug_codeerror))    && p('title')         && e('bug_codeerror');                   // 测试bug的标题
r($bug->createObject($bug_project))      && p('project')       && e('1');                               // 测试bug的项目
r($bug->createObject($bug_execution))    && p('execution')     && e('2');                               // 测试bug的执行
r($bug->createObject($bug_priseverity))  && p('pri,severity')  && e('2,2');                             // 测试bug的优先级和严重程度
r($bug->createObject($bug_steps))        && p('steps')         && e('i wish');                          // 测试bug的步骤
r($bug->createObject($bug_deadline))     && p('deadline')      && e('2023-04-23');                      // 测试bug的截止日期
r($bug->createObject($bug_assign))       && p('assignedTo')    && e('admin');                           // 测试bug的指派人
r($bug->createObject($bug_notitle))      && p('title:0')       && e('『Bug标题』不能为空。');           // 测试bug标题为空
r($bug->createObject($bug_nobuild))      && p('openedBuild:0') && e('『影响版本』不能为空。');          // 测试版本为空
r($bug->createObject($bug_erremail))     && p('notifyEmail:0') && e('『通知邮箱』应当为合法的EMAIL。'); // 测试异常的email输入
