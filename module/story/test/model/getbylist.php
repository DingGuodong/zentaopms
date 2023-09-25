#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/story.class.php';
su('admin');

zdTable('product')->gen(1);
$story = zdTable('story');
$story->product->range(1);
$story->version->range(1);
$story->deleted->range('0{4},1');
$story->gen(20);

zdTable('storyspec')->gen(20);

/**

title=测试 storyModel->getByList();
cid=1
pid=1

根据指定的ID列表，获取需求数量 >> 5
获取软件需求数量 >> 10
获取用户需求数量 >> 10
根据指定的ID列表，获取第一个需求的名称 >> 用户需求1
获取第一个软件需求的名称 >> 软件需求2
获取第一个用户需求的名称 >> 用户需求1
传入空类型，返回空列表 >> 0

*/

$story = new storyTest();
$storyIdList = array(1, 2, 3, 4, 5);

$storyInIdList = $story->getByListTest($storyIdList);
$allStories    = $story->getByListTest($storyIdList, 'all');
$emptyStories  = $story->getByListTest(0);

r(count($storyInIdList)) && p()          && e('4');  //根据指定的ID列表，获取需求数量
r(count($allStories))    && p()          && e('5');  //根据指定的ID列表，获取所有需求数量
r($emptyStories)         && p('')        && e('0');  //传入空类型，返回空列表
