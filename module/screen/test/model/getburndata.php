#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/screen.unittest.class.php';
su('admin');

zenData('project')->gen(0);
zenData('project')->loadYaml('project')->gen(1, false ,false);
zenData('project')->loadYaml('execution_burn')->gen(30, false, false);

/**

title=测试 screenModel->getBurnData();
timeout=0
cid=1

- 测试生成的数据条数 @16
- 测试生成的数据是否正确第104条的name属性 @项目集1--项目集4

*/

$screen = new screenTest();

$data = $screen->getBurnDataTest();

r(count($data))          && p('')         && e(16);                    //测试生成的数据条数
r($data)                 && p('104:name') && e('项目集1--项目集4');   //测试生成的数据是否正确