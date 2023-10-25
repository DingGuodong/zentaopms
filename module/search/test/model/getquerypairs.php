#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/search.class.php';
su('admin');

zdTable('userquery')->config('userquery')->gen(50);

/**

title=测试 searchModel->getQueryPairs();
cid=1
pid=1

查询module为task的键值对 >> 这是搜索条件名称1
查询module为executionStory的键值对 >> 这是搜索条件名称2
查询module为user的键值对 >> 这是搜索条件名称3
查询module为projectBuild的键值对 >> 这是搜索条件名称4
查询module为executionBuild的键值对 >> 这是搜索条件名称5
查询module为design的键值对 >> 这是搜索条件名称6
查询module不存在的情况 >> 这是搜索条件名称7

*/

$search = new searchTest();

$queryIDList = array('task','executionStory','user','projectBuild','executionBuild','design','nullValue');

r($search->getQueryPairsTest($queryIDList[0])) && p('1')   && e('这是搜索条件名称1'); //查询module为task的键值对
r($search->getQueryPairsTest($queryIDList[1])) && p('2')   && e('这是搜索条件名称2'); //查询module为executionStory的键值对
r($search->getQueryPairsTest($queryIDList[2])) && p('3')   && e('这是搜索条件名称3'); //查询module为user的键值对
r($search->getQueryPairsTest($queryIDList[3])) && p('4')   && e('这是搜索条件名称4'); //查询module为projectBuild的键值对
r($search->getQueryPairsTest($queryIDList[4])) && p('5')   && e('这是搜索条件名称5'); //查询module为executionBuild的键值对
r($search->getQueryPairsTest($queryIDList[5])) && p('6')   && e('这是搜索条件名称6'); //查询module为design的键值对

$result = $search->getQueryPairsTest($queryIDList[6]);
reset($result);
$result['-1'] = current($result);
r($result) && p('-1')  && e('我的查询');                                              //查询module不存在的情况
