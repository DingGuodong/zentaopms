#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/testtask.class.php';

zdTable('testrun')->gen(4);
zdTable('case')->config('case')->gen(4);
zdTable('projectcase')->config('projectcase')->gen(16);
zdTable('projectstory')->config('projectstory')->gen(8);

su('admin');

/**

title=测试 testtaskModel->unlinkCase();
cid=1
pid=1

*/

$testtask = new testtaskTest();

r($testtask->unlinkCaseTest(1)) && p('run;cases;action:objectType|objectID|action|extra', '|') && e('0;1,2,3,4,1,2,3,4,2,3,4,2,3,4;case|1|unlinkedfromtesttask|1'); // 从测试单 1 中移除用例 1，同时从项目 3 和项目 4 中移除用例1。
r($testtask->unlinkCaseTest(2)) && p('run;cases;action:objectType|objectID|action|extra', '|') && e('0;1,2,3,4,1,2,3,4,3,4,3,4;case|2|unlinkedfromtesttask|1');     // 从测试单 1 中移除用例 2，同时从项目 3 和项目 4 中移除用例2。
r($testtask->unlinkCaseTest(3)) && p('run;cases;action:objectType|objectID|action|extra', '|') && e('0;1,2,4,1,2,4,3,4,3,4;case|3|unlinkedfromtesttask|1');         // 从测试单 1 中移除用例 3，同时从项目 1 和项目 2 中移除用例3。
r($testtask->unlinkCaseTest(4)) && p('run;cases;action:objectType|objectID|action|extra', '|') && e('0;1,2,1,2,3,4,3,4;case|4|unlinkedfromtesttask|1');             // 从测试单 1 中移除用例 4，同时从项目 1 和项目 2 中移除用例4。

r($testtask->unlinkCaseTest(5)) && p() && e(0); // 测试轮次不存在返回 false。
