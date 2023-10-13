#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/story.class.php';
su('admin');

zdTable('story')->gen(0);
zdTable('storyspec')->gen(0);

/**

title=测试 storyModel->batchCreate();
cid=1
pid=1

*/

$now   = helper::now();
$story = new storyTest();
$stories = array();
$stories['title']      = array(1 => '测试需求1', 2 => '测试需求2', 3 => '测试需求2');
$stories['pri']        = array(1 => 1, 2 => 2, 3 => 3);
$stories['spec']       = array(1 => '测试需求描述1', 2 => '测试需求描述2', 3 => '测试需求描述3');
$stories['verify']     = array(1 => '测试需求验收标准1', 2 => '测试需求验收标准2', 3 => '测试需求验收标准3');
$stories['estimate']   = array(1 => 1, 2 => 2, 3 => 3);
$stories['module']     = array(1 => 2221, 2 => 2222, 3 => 2223);
$stories['plan']       = array(1 => 1, 2 => 2, 3 => 3);
$stories['source']     = array(1 => '', 2 => '', 3 => '');
$stories['sourceNote'] = array(1 => '', 2 => '', 3 => '');
$stories['keywords']   = array(1 => '', 2 => '', 3 => '');
$stories['color']      = array(1 => '', 2 => '', 3 => '');
$stories['openedBy']   = array(1 => 'admin', 2 => 'admin', 3 => 'admin');
$stories['openedDate'] = array(1 => $now, 2 => $now, 3 => $now);

$result1 = $story->batchCreateTest(1, 0, 'story', $stories);
$result2 = $story->batchCreateTest(2, 0, 'requirement', $stories);

r(count($result1)) && p() && e('3'); // 插入两条软件需求，判断返回的需求总量
r(count($result2)) && p() && e('3'); // 插入两条用户需求，判断返回的需求总量
r($result1) && p('1:title,type,pri,spec,estimate,stage,module') && e('测试需求1,story,1,测试需求描述1,1,planned,2221');       // 插入两条软件需求，判断返回的title、type等信息
r($result2) && p('5:title,type,pri,spec,estimate,stage,module') && e('测试需求2,requirement,2,测试需求描述2,2,planned,2222'); // 插入两条用户需求，判断返回的title、type等信息
