#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . "/test/lib/init.php";
include dirname(__FILE__, 2) . '/editor.class.php';
su('admin');

/**

title=测试 editorModel::getModuleFiles();
cid=1
pid=1

获取todo模块的文件列表 >> 1

*/

$editor = new editorTest();
r($editor->getModuleFilesTest('todo')) && p() && e(1);    //获取todo模块的文件列表
