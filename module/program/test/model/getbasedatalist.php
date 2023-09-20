#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/program.class.php';

zdTable('team')->gen(5);
zdTable('project')->config('program')->gen(40);
su('admin');

/**
title=测试programTao::getBaseDataList();
timeout=0
cid=1

*/

$tester = new programTest();

$programList = $tester->program->getBaseDataList(array(3,13,23,33,44));

r($programList[3]->path)         && p('') && e(',3,');
r($programList[13]->path)        && p('') && e(',3,13,');
r($programList[23]->path)        && p('') && e(',3,13,23,');
r($programList[33]->path)        && p('') && e(',3,13,23,33,');

r(isset($programList[44])) && p('') && e('0');
