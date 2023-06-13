<?php
declare(strict_types=1);
/**
 * The bug view file of user module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Wang Yidong <yidong@easycorp.ltd>
 * @package     user
 * @link        https://www.zentao.net
 */
namespace zin;
include './featurebar.html.php';

jsVar('trunkLang', $lang->trunk);

$todoNavs = array();
$that     = zget($lang->user->thirdPerson, $user->gender);
$testtaskNavs['assignedTo'] = array('text' => sprintf($lang->user->testTask2Him, $that), 'url' => inlink('testtask', "userID={$user->id}"), 'active' => true);

$cols = array();
foreach($config->user->defaultFields['testtask'] as $field) $cols[$field] = $config->testtask->dtable->fieldList[$field];
$cols['id']['checkbox'] = false;

$waitCount    = 0;
$testingCount = 0;
$blockedCount = 0;
$doneCount    = 0;
foreach($tasks as $task)
{
    if($task->status == 'wait')    $waitCount ++;
    if($task->status == 'doing')   $testingCount ++;
    if($task->status == 'blocked') $blockedCount ++;
    if($task->status == 'done')    $doneCount ++;

    $task->statusLabel = $this->processStatus('testtask', $task);
}
$summary = sprintf($lang->testtask->allSummary, count($tasks), $waitCount, $testingCount, $blockedCount, $doneCount);

panel
(
    setClass('list'),
    set::title(null),
    set::headingActions(array(nav(set::items($testtaskNavs)))),
    dtable
    (
        set::userMap($users),
        set::bordered(true),
        set::cols($cols),
        set::data(array_values($tasks)),
        set::footPager(usePager()),
        set::onRenderCell(jsRaw('window.renderCell')),
        set::footer(array(array('html' => $summary, 'className' => "text-dark"), 'flex', 'pager')),
    )
);

render();
