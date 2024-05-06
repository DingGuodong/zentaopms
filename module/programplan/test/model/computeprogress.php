#!/usr/bin/env php
<?php

/**

title=测试 programplanModel->computeProgress();
cid=0

- 测试参数=项目id直接continue @fail
- 测试阶段status=suspended子阶段status=wait @success
- 测试阶段status:suspended=>wait属性status @wait
- 测试阶段status=suspended子阶段status=closed @success
- 测试阶段status:suspended=>closed属性status @closed
- 测试阶段status=doing子阶段status=suspended @success
- 测试阶段status:doing=>suspended属性status @suspended
- 测试阶段status更新为doing @success
- 测试阶段状态更新为donging属性status @doing

*/
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/programplan.unittest.class.php';
su('admin');

zenData('project')->loadYaml('project')->gen(13);
zenData('task')->loadYaml('task')->gen(13);

$programplan = new programplanTest();

r($programplan->computeProgressTest('1', 'edit', false))   && p() && e('fail');              // 测试参数=项目id直接continue
r($programplan->computeProgressTest('2', 'edit', true))    && p() && e('success');           // 测试阶段status=suspended子阶段status=wait
r($programplan->getByIdTest(2))                            && p('status') && e('wait');      // 测试阶段status:suspended=>wait
r($programplan->computeProgressTest('5', 'edit', true))    && p() && e('success');           // 测试阶段status=suspended子阶段status=closed
r($programplan->getByIdTest(5))                            && p('status') && e('closed');    // 测试阶段status:suspended=>closed
r($programplan->computeProgressTest('8', 'edit', true))    && p() && e('success');           // 测试阶段status=doing子阶段status=suspended
r($programplan->getByIdTest(8))                            && p('status') && e('suspended'); // 测试阶段status:doing=>suspended
r($programplan->computeProgressTest('11', 'edit', true))   && p() && e('success');           // 测试阶段status更新为doing
r($programplan->getByIdTest(11))                           && p('status') && e('doing');     // 测试阶段状态更新为donging
