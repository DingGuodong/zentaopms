#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/story.class.php';
su('admin');

zdTable('story')->gen(20);
zdTable('product')->gen(1);
$story = zdTable('story');
$story->product->range(1);
$story->version->range(1);
$story->gen(20);

$storySpec = zdTable('storyspec');
$storySpec->story->range('1-20{2}');
$storySpec->version->range('1-2');
$storySpec->gen(40);
zdTable('storyspec')->gen(20);
$relation = zdTable('relation');
$relation->AID->range('1,10,2,11,3,12,4,13,5,14,6,15,7,16,8,17');
$relation->AVersion->range('1');
$relation->BID->range('10,1,11,2,12,3,13,4,14,5,15,6,16,7,17,8');
$relation->BVersion->range('1');
$relation->gen(16);

/**

title=测试 storyModel->updateStoryVersion();
cid=1
pid=1

*/

$changes = array();
$changes[] = array('field' => 'pri', 'old' => '3', 'new' => '5', 'diff' => '');
$changes[] = array('field' => 'estimate', 'old' => '10', 'new' => '100', 'diff' => '');

$story = new storyTest();
$twins = $story->syncTwinsTest(5, '6,7', $changes);

r($twins[0]) && p('id,pri,estimate') && e('6,5,100');
r($twins[1]) && p('id,pri,estimate') && e('7,5,100');
