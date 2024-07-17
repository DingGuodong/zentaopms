#!/usr/bin/env php
<?php
declare(strict_types=1);

/**

title=测试 myModel->getActions();
timeout=0
cid=1

- 正常查询action @96,64,32,95,63

- 正常查询action统计 @5

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/my.unittest.class.php';

zendata('action')->gen('100');
zendata('doc')->gen('0');
zendata('api')->gen('0');
zendata('doclib')->gen('0');
zendata('project')->gen('0');
zendata('product')->gen('0');
zenData('user')->gen('1');

su('admin');

global $lang, $app;
$lang->SRCommon = '研发需求';
$lang->URCommon = '用户需求';
$app->loadLang('action');

$my = new myTest();

$actions = $my->getActionsTest();
r(implode(',', $actions)) && p() && e('93,62,31,92,61'); // 正常查询action
r(count($actions))        && p() && e('5');              // 正常查询action统计
