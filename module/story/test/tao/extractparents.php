#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . "/test/lib/init.php";

/**

title=测试 storyModel->extractParents();
cid=1
pid=1

*/

global $tester;
$storyModel = $tester->loadModel('story');
r($storyModel->extractParents(array())) && p() && e('0');

$stories[2] = new stdclass();
$stories[2]->id = 2;
$stories[2]->parent = 0;
$stories[3] = new stdclass();
$stories[3]->id = 3;
$stories[3]->parent = -1;
$stories[4] = new stdclass();
$stories[4]->id = 4;
$stories[4]->parent = 8;
r($storyModel->extractParents($stories)) && p('0,1') && e('3,8');
