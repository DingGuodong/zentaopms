#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/action.class.php';
su('admin');

zdTable('action')->config('action')->gen(90);
zdTable('doclib')->gen(1);
zdTable('doc')->gen(1);
zdTable('product')->gen(1);
zdTable('userquery')->config('userquery')->gen(1);

/**

title=测试 actionModel->getDynamicBySearch();
cid=1
pid=1

获取排序为date倒序的所有动态       >> 64,testsuite;32,testcase
获取排序为date正序的所有动态       >> 65,caselib;1,product
获取排序为date倒序的今年之后的动态 >> 64,testsuite;32,testcase
获取排序为date倒序的今天之前的动态 >> 63,module;31,bug
获取排序为date倒序的今年之后的动态 >> 1,product

*/

$queryID       = array(0, 1);
$orderByList   = array('date_desc', 'date_asc');
$pager         = null;
$dateList      = array('', 'today');
$directionList = array('next', 'pre');

$action = new actionTest();

r($action->getDynamicBySearchTest($queryID[0], $orderByList[0], $pager, $dateList[0], $directionList[0])) && p('0:id,objectType;1:id,objectType') && e('64,testsuite;32,testcase');  // 获取排序为date倒序的所有动态
r($action->getDynamicBySearchTest($queryID[0], $orderByList[1], $pager, $dateList[0], $directionList[0])) && p('0:id,objectType;1:id,objectType') && e('65,caselib;1,product');      // 获取排序为date正序的所有动态
r($action->getDynamicBySearchTest($queryID[0], $orderByList[0], $pager, $dateList[0], $directionList[1])) && p('0:id,objectType;1:id,objectType') && e('64,testsuite;32,testcase');  // 获取排序为date倒序的今年之后的动态
r($action->getDynamicBySearchTest($queryID[0], $orderByList[0], $pager, $dateList[1], $directionList[0])) && p('0:id,objectType;1:id,objectType') && e('63,module;31,bug');          // 获取排序为date倒序的今天之前的动态
r($action->getDynamicBySearchTest($queryID[1], $orderByList[0], $pager, $dateList[0], $directionList[1])) && p('0:id,objectType')                 && e('1,product');                 // 获取排序为date倒序的今年之后的动态
