#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/editor.class.php';
su('admin');

/**

title=测试 editorModel::extendModel();
cid=1
pid=1

获取todo模块的model扩展内容 >> 1

*/

$editor = new editorTest();
r($editor->extendModelTest()) && p() && e(1);    //获取todo模块的model扩展内容
