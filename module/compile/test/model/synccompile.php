#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';

zenData('job')->loadYaml('job')->gen(6);
zenData('compile')->gen(6);
zenData('pipeline')->gen(6);
su('admin');

/**

title=测试 compileModel->syncCompile();
timeout=0
cid=1

- 调用jenkins接口之前job为1的compile数量。 @1
- 调用jenkins接口之后job为1的compile数量。 @17
- 调用gitlab接口之前的获取不到ID为50的compile。属性50 @~~
- 调用gitlab接口之后的能获取到ID为50的compile。第50条的name属性 @这是一个Job2

*/

$tester->loadModel('compile');
r(count($tester->compile->getListByJobID(1))) && p() && e(1);   //调用jenkins接口之前job为1的compile数量。
$tester->compile->syncCompile(0, 1);
r(count($tester->compile->getListByJobID(1))) && p() && e(17);  //调用jenkins接口之后job为1的compile数量。

r($tester->compile->getListByJobID(2)) && p('50')      && e('~~');            //调用gitlab接口之前的获取不到ID为50的compile。
$tester->compile->syncCompile(0, 2);
r($tester->compile->getListByJobID(2)) && p('50:name') && e('这是一个Job2');  //调用gitlab接口之后的能获取到ID为50的compile。