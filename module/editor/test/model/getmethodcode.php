#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/editor.class.php';
su('admin');

/**

title=测试 editorModel::getMethodCode();
cid=1
pid=1

获取todo模块的create方法的参数 >> 1

*/

$editor = new editorTest();
r($editor->getMethodCodeTest()) && p() && e(1);    //获取todo模块的create方法的参数
