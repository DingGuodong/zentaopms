#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/program.unittest.class.php';

zenData('project')->loadYaml('program')->gen(40);
su('admin');

/**
title=测试programTao::getRootProgramList();
timteout=0
cid=1

*/

$tester = new programTest();
$programList = $tester->program->getRootProgramList();

r(count($programList)) && p('') && e('10');
