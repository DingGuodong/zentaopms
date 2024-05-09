#!/usr/bin/env php
<?php

/**

title=测试 storyModel->getDataOfStoriesPerModule();
cid=0

- 按照模块分组，获取分组后的需求数量 @6
- 按照模块分组，获取各个模块下的需求数量，查看模块2150下的数据
 - 第1条的name属性 @/这是一个模块1
 - 第1条的value属性 @4
 - 第1条的product属性 @1

*/
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/story.unittest.class.php';

$story = zenData('story');
$story->module->range('1-6');
$story->product->range('1');
$story->gen(20);
$module = zenData('module');
$module->root->range(1);
$module->gen(20);
zenData('product')->gen(1);

su('admin');

global $tester;
$tester->loadModel('story');
$tester->loadModel('report');

$_SESSION['storyOnlyCondition']  = true;
$_SESSION['storyQueryCondition'] = "`id` < 20";

$data = $tester->story->getDataOfStoriesPerModule();

r(count($data)) && p()                       && e('6');               // 按照模块分组，获取分组后的需求数量
r($data)        && p('1:name,value,product') && e('/这是一个模块1,4,1'); // 按照模块分组，获取各个模块下的需求数量，查看模块2150下的数据
