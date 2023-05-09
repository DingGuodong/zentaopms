#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
su('admin');

$project = zdTable('project')->config('project')->gen(11);

/**

title=测试 projectModel->getProgramMaxEnd();
timeout=0
cid=1
pid=1

- 获取二级项目集下唯一项目最大结束时间属性maxEnd @2023-06-12

- 获取一级项目集下项目最大结束时间属性maxEnd @2023-07-21

- 获取二级项目集下多个项目最大结束时间属性maxEnd @2023-05-20

*/

global $tester;

$tester->loadModel('project');
$project1 = $tester->project->getProgramMaxEnd(1);
$project2 = $tester->project->getProgramMaxEnd(4);
$project3 = $tester->project->getProgramMaxEnd(8);

r($project1) && p('maxEnd')   && e('2023-06-12'); //获取二级项目集下唯一项目最大结束时间
r($project2) && p('maxEnd')   && e('2023-07-21'); //获取一级项目集下项目最大结束时间
r($project3) && p('maxEnd')   && e('2023-05-20'); //获取二级项目集下多个项目最大结束时间
