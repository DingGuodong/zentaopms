#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/action.unittest.class.php';

/**

title=测试 actionModel->getDynamicBySearch();
timeout=0
cid=1

- 获取排序为date倒序的所有动态
 - 第0条的id属性 @64
 - 第0条的objectType属性 @testsuite
 - 第1条的id属性 @32
 - 第1条的objectType属性 @testcase
- 获取排序为date正序的所有动态
 - 第0条的id属性 @65
 - 第0条的objectType属性 @caselib
 - 第1条的id属性 @33
 - 第1条的objectType属性 @case
- 获取排序为date倒序的今年之后的动态
 - 第0条的id属性 @64
 - 第0条的objectType属性 @testsuite
 - 第1条的id属性 @32
 - 第1条的objectType属性 @testcase
- 获取排序为date倒序的今天之前的动态
 - 第0条的id属性 @63
 - 第0条的objectType属性 @module
 - 第1条的id属性 @31
 - 第1条的objectType属性 @bug
- 获取排序为date倒序的今年之后的动态
 - 第0条的id属性 @1
 - 第0条的objectType属性 @product

*/

zenData('action')->loadYaml('action')->gen(90);
zenData('doclib')->gen(1);
zenData('doc')->gen(1);
zenData('lang')->gen(0);
zenData('product')->gen(1);
zenData('userquery')->loadYaml('userquery')->gen(1);

global $lang, $app;
$lang->SRCommon = '研发需求';
$lang->URCommon = '用户需求';
$app->loadLang('action');

su('admin');

$queryID       = array(0, 1);
$orderByList   = array('date_desc,id_desc', 'date_asc,id_desc');
$limit         = 50;
$dateList      = array('', 'today');
$directionList = array('next', 'pre');

$action = new actionTest();

r($action->getDynamicBySearchTest($queryID[0], $orderByList[0], $limit, $dateList[0], $directionList[0])) && p('0:id,objectType;1:id,objectType') && e('62,branch;31,bug');      // 获取排序为date倒序的所有动态
r($action->getDynamicBySearchTest($queryID[0], $orderByList[1], $limit, $dateList[0], $directionList[0])) && p('0:id,objectType;1:id,objectType') && e('63,module;32,testcase'); // 获取排序为date正序的所有动态
r($action->getDynamicBySearchTest($queryID[0], $orderByList[0], $limit, $dateList[0], $directionList[1])) && p('0:id,objectType;1:id,objectType') && e('62,branch;31,bug');      // 获取排序为date倒序的今年之后的动态
r($action->getDynamicBySearchTest($queryID[0], $orderByList[0], $limit, $dateList[1], $directionList[0])) && p('0:id,objectType;1:id,objectType') && e('61,todo;30,build');      // 获取排序为date倒序的今天之前的动态
r($action->getDynamicBySearchTest($queryID[1], $orderByList[0], $limit, $dateList[0], $directionList[1])) && p('0:id,objectType')                 && e('1,product');             // 获取排序为date倒序的今年之后的动态
