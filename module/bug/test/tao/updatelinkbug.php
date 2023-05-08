#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . "/test/lib/init.php";
include dirname(__FILE__, 2) . '/bug.class.php';
su('admin');

function initData()
{
    $data = zdTable('bug');
    $data->id->range('1-5');
    $data->product->range('1-5');
    $data->branch->range('0-1');
    $data->project->range('0-5');
    $data->execution->range('0-5');
    $data->title->prefix("BUG")->range('1-5');
    $data->openedBuild->range('1-5');
    $data->type->range("[codeerror]");
    $data->status->range("[active]");
    $data->pri->range("[3]");
    $data->severity->range("[3]");

    $data->gen(5);
}

/**

title=bugModel->updateLinkBug();
timeout=0
cid=1

- 测试关联bug2的关联bug同步更新为1属性2 @1

- 测试关联bug2的关联bug同步更新为空，bug3的关联bug为1
 - 属性2 @~~
 - 属性3 @1

- 测试关联bug2的关联bug同步更新为1属性2 @1

- 测试关联bug3的关联bug同步更新为1,2属性3 @1,2



*/

initData();

$bugIDList = array(1, 2, 3);

$bug = new bugTest();
r($bug->updateLinkBugTest($bugIDList[0], '2', ''))    && p('2', ';')   && e('1');    //测试关联bug2的关联bug同步更新为1
r($bug->updateLinkBugTest($bugIDList[0], '3', '2'))   && p('2;3', ';') && e('~~;1'); //测试关联bug2的关联bug同步更新为空，bug3的关联bug为1
r($bug->updateLinkBugTest($bugIDList[0], '2,3', '3')) && p('2')        && e('1');    //测试关联bug2的关联bug同步更新为1
r($bug->updateLinkBugTest($bugIDList[1], '1,3', '1')) && p('3', ';')   && e('1,2');  //测试关联bug3的关联bug同步更新为1,2