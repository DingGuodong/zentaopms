<?php
declare(strict_types=1);
/**
 * The ajaxGetTestStories view file of task module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian<tianshujie@easycorp.ltd>
 * @package     task
 * @link        https://www.zentao.net
 */
namespace zin;

$requiredFields = array();
foreach(explode(',', $config->task->create->requiredFields) as $field)
{
    if($field) $requiredFields[$field] = '';
}

$taskTR = array();
$i      = 0;
foreach($testStories as $storyID => $storyTitle)
{
    $taskTR[] = h::tr
        (
            h::td
            (
                select
                (
                    set::id("testStory{$i}"),
                    set::name("testStory[$i]"),
                    set::value($storyID),
                    set::items(array($storyID => $storyTitle)),
                ),
            ),
            h::td
            (
                select
                (
                    set::id("testPri{$i}"),
                    set::name("testPri[$i]"),
                    set::required(true),
                    set::value($task->pri),
                    set::items($lang->task->priList),
                ),
            ),
            h::td
            (
                input
                (
                    set::id("testEstStarted{$i}"),
                    set::name("testEstStarted[$i]"),
                    set::type('date'),
                    set::value($task->estStarted),
                ),
            ),
            h::td
            (
                input
                (
                    set::id("testDeadline{$i}"),
                    set::name("testDeadline[$i]"),
                    set::type('date'),
                    set::value($task->deadline),
                ),
            ),
            h::td
            (
                select
                (
                    set::id("testAssignedTo{$i}"),
                    set::name("testAssignedTo[$i]"),
                    set::value($task->assignedTo),
                    set::items($members),
                ),
            ),
            h::td
            (
                inputControl
                (
                    input
                    (
                        set::id("testEstimate{$i}"),
                        set::name("testEstimate[$i]"),
                    ),
                    to::suffix($lang->task->suffixHour),
                    set::suffixWidth(20),
                ),
            ),
            h::td
            (
                set::class('center'),
                btnGroup
                (
                    set::items(array(
                        array('class' => 'btn btn-link text-gray', 'icon' => 'plus', 'onclick' => 'addItem(this)'),
                        array('class' => 'btn btn-link text-gray', 'icon' => 'trash', 'onclick' => 'removeItem(this)'),
                    ))
                )
            )
        );
}

formGroup
(
    set::label($lang->task->selectTestStory),
    set::labelClass('selectStoryLabel'),
    h::table
    (
        set::class('table table-form'),
        set::id('testTaskTable'),
        h::thead
        (
            h::tr
            (
                h::th($lang->task->storyAB),
                h::th
                (
                    $lang->task->pri,
                    set::width('80px'),
                    setClass(isset($requiredFields['pri']) ? 'required' : '')
                ),
                h::th
                (
                    $lang->task->estStarted,
                    set::width('140px'),
                    setClass(isset($requiredFields['estStarted']) ? 'required' : '')
                ),
                h::th
                (
                    $lang->task->deadline,
                    set::width('140px'),
                    setClass(isset($requiredFields['deadline']) ? 'required' : '')
                ),
                h::th
                (
                    $lang->task->assignedTo,
                    set::width('100px')
                ),
                h::th
                (
                    $lang->task->estimate,
                    set::width('88px'),
                    setClass(isset($requiredFields['estimate']) ? 'required' : '')
                ),
                h::th
                (
                    $lang->actions,
                    set::width('70px')
                ),
            )
        ),
        h::tbody
        (
            $taskTR
        )
    ),
);

/* ====== Render page ====== */
render();
