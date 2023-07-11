<?php
declare(strict_types=1);
/**
 * The edit view file of testtask module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Tingting Dai <daitingting@easycorp.ltd>
 * @package     testtask
 * @link        https://www.zentao.net
 */
namespace zin;

formPanel
(
    on::change('#execution', 'loadExecutionRelated'),
    formGroup
    (
        set::width('1/2'),
        set::label($lang->testtask->execution),
        set::name('execution'),
        set::value($task->execution),
        set::class(((!empty($project) && !$project->multiple) || ($app->tab == 'execution' && $task->execution)) ? 'hidden' : ''),
        set::control(array('type' => 'select', 'items' => $executions))
    ),
    formGroup
    (
        set::width('1/2'),
        set::label($lang->testtask->build),
        set::required(true),
        set::name('build'),
        set::value($task->build),
        set::control(array('type' => 'select', 'items' => $builds))
    ),
    formGroup
    (
        set::width('1/2'),
        set::label($lang->testtask->type),
        set::name('type[]'),
        set::value($task->type),
        set::control(array('type' => 'select', 'items' => $lang->testtask->typeList, 'multiple' => true))
    ),
    formGroup
    (
        set::width('1/2'),
        set::label($lang->testtask->owner),
        set::name('owner'),
        set::value($task->owner),
        set::control(array('type' => 'select', 'items' => $users))
    ),
    formGroup
    (
        set::width('1/2'),
        set::label($lang->testtask->begin),
        set::required(true),
        inputGroup
        (
            input
            (
                set::name('begin'),
                set::type('date'),
                set::required(true),
                set::value($task->begin),
                on::change('suitEndDate'),
            ),
            $lang->testtask->end,
            input
            (
                set::name('end'),
                set::type('date'),
                set::required(true),
                set::value($task->end),
            )
        )
    ),
    formGroup
    (
        set::width('1/2'),
        set::label($lang->testtask->status),
        set::name('status'),
        set::required(true),
        set::value($task->status),
        set::control(array('type' => 'select', 'items' => $lang->testtask->statusList)),
    ),
    formGroup
    (
        set::width('1/2'),
        set::label($lang->testtask->testreport),
        set::name('testreport'),
        set::value($task->testreport),
        set::control(array('type' => 'select', 'items' => $testreports))
    ),
    formGroup
    (
        set::label($lang->testtask->name),
        set::required(true),
        inputGroup
        (
            input
            (
                set::name('name'),
                set::required(true),
                set::value($task->name),
            ),
            $lang->testtask->pri,
            select
            (
                zui::width('80px'),
                set::name('pri'),
                set::items($lang->testtask->priList),
                set::value($task->pri)
            )
        )
    ),
    formGroup
    (
        set::label($lang->testtask->desc),
        editor
        (
            set::name('desc'),
            set::rows(10),
            set::value(htmlSpecialString($task->desc))
        )
    ),
    formGroup
    (
        set::label($lang->comment),
        editor
        (
            set::name('comment'),
            set::rows(5),
        )
    ),
    formGroup
    (
        set::label($lang->testtask->files),
        upload()
    ),
    formGroup
    (
        set::label($lang->testtask->mailto),
        set::control(array('type' => 'select', 'items' => $users, 'multiple' => true)),
        set::name('mailto[]'),
        set::value(str_replace(' ', '', $task->mailto)),
    )
);

render();
