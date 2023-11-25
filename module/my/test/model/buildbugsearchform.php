#!/usr/bin/env php
<?php
declare(strict_types=1);
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/my.class.php';

zdTable('user')->gen('1');

su('admin');

/**

title=测试 myModel->buildBugSearchForm();
cid=1
pid=1

*/

$my = new myTest();

$queryID   = array(0, 1);
$actionURL = array('actionURL1', 'actionURL2');
$config1 = $my->buildBugSearchFormTest($queryID[0], $actionURL[0]);
$config2 = $my->buildBugSearchFormTest($queryID[1], $actionURL[1]);
r($config1) && p('module,queryID,actionURL') && e('bugBug,0,actionURL1'); // 测试获取queryID 1 actionURL actionURL1 的搜索表单
r($config2) && p('module,queryID,actionURL') && e('bugBug,1,actionURL2'); // 测试获取queryID 0 actionURL actionURL2 的搜索表单
