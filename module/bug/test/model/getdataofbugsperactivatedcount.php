#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/bug.class.php';
su('admin');

zdTable('bug')->config('activatedcount')->gen(10);

/**

title=bugModel->getDataOfBugsPerActivatedCount();
timeout=0
cid=1

- 获取 bug 激活次数为 0 的数据
 - 第0条的name属性 @激活次数:0
 - 第0条的value属性 @5

- 获取 bug 激活次数为 1 的数据
 - 第1条的name属性 @激活次数:1
 - 第1条的value属性 @3

- 获取 bug 激活次数为 2 的数据
 - 第2条的name属性 @激活次数:2
 - 第2条的value属性 @2

*/

$bug = new bugTest();
r($bug->getDataOfBugsPerActivatedCountTest()) && p('0:name,value') && e('激活次数:0,5'); //获取 bug 激活次数为 0 的数据
r($bug->getDataOfBugsPerActivatedCountTest()) && p('1:name,value') && e('激活次数:1,3'); //获取 bug 激活次数为 1 的数据
r($bug->getDataOfBugsPerActivatedCountTest()) && p('2:name,value') && e('激活次数:2,2'); //获取 bug 激活次数为 2 的数据