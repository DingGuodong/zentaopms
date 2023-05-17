#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . "/test/lib/init.php";
include dirname(__FILE__, 2) . '/bug.class.php';

zdTable('bug')->config('bug_assign')->gen(6);
zdTable('user')->gen(1);
zdTable('product')->gen(10);

su('admin');

/**

title=bugModel->assign();
cid=1
pid=1

*/

$now = helper::now();

$bug1 = new stdclass();
$bug1->id             = 1;
$bug1->assignedTo     = 'user2';
$bug1->assignedDate   = $now;
$bug1->lastEditedBy   = 'admin';
$bug1->lastEditedDate = $now;
$bug1->mailto         = 'user1,user3';

$bug2 = new stdclass();
$bug2->id             = 2;
$bug2->assignedTo     = 'user2';
$bug2->assignedDate   = $now;
$bug2->lastEditedBy   = 'admin';
$bug2->lastEditedDate = $now;
$bug2->mailto         = 'user2';

$bug3 = new stdclass();
$bug3->id             = 3;
$bug3->assignedTo     = 'user2';
$bug3->assignedDate   = $now;
$bug3->lastEditedBy   = 'admin';
$bug3->lastEditedDate = $now;
$bug3->mailto         = 'user3';

$bug4 = new stdclass();
$bug4->id             = 4;
$bug4->assignedTo     = 'user1';
$bug4->assignedDate   = $now;
$bug4->lastEditedBy   = 'admin';
$bug4->lastEditedDate = $now;
$bug4->mailto         = 'user1';

$bug5 = new stdclass();
$bug5->id             = 5;
$bug5->assignedTo     = 'user1';
$bug5->assignedDate   = $now;
$bug5->lastEditedBy   = 'admin';
$bug5->lastEditedDate = $now;
$bug5->mailto         = 'user2';

$bug6 = new stdclass();
$bug6->id             = 6;
$bug6->assignedTo     = 'user1';
$bug6->assignedDate   = $now;
$bug6->lastEditedBy   = 'admin';
$bug6->lastEditedDate = $now;
$bug6->mailto         = 'user3';

$bug = new bugTest();
r($bug->assignTest($bug1))  && p('assignedTo;mailto', ';') && e('user2;user1,user3;'); // 指派bug状态为激活的bug 更改指派人
r($bug->assignTest($bug2))  && p('assignedTo;mailto', ';') && e('user2;user2;');       // 指派bug状态为解决的bug 更改指派人
r($bug->assignTest($bug3))  && p('assignedTo;mailto', ';') && e('user1;admin');        // 指派bug状态为关闭的bug 更改指派人
r($bug->assignTest($bug4))  && p('assignedTo;mailto', ';') && e('user1;user1;');       // 指派bug状态为激活的bug 不更改指派人
r($bug->assignTest($bug5))  && p('assignedTo;mailto', ';') && e('user1;user2;');       // 指派bug状态为解决的bug 不更改指派人
r($bug->assignTest($bug6))  && p('assignedTo;mailto', ';') && e('user1;admin');        // 指派bug状态为关闭的bug 不更改指派人
