<?php
declare(strict_types=1);
/**
 * The create file of bug module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yuting Wang<wangyuting@easycorp.ltd>
 * @package     bug
 * @link        http://www.zentao.net
 */
namespace zin;

jsVar('oldProjectID', $bug->projectID);
jsVar('oldProductID', $bug->productID);
jsVar('moduleID', $bug->moduleID);
jsVar('tab', $this->app->tab);
if($this->app->tab == 'execution') jsVar('objectID', zget($bug->execution, 'id', ''));
if($this->app->tab == 'project')   jsVar('objectID', $bug->projectID);

foreach(explode(',', $config->bug->create->requiredFields) as $field)
{
    if($field and strpos($showFields, $field) === false) $showFields .= ',' . $field;
}

$showExecution        = strpos(",$showFields,", ',execution,')        !== false;
$showDeadline         = strpos(",$showFields,", ',deadline,')         !== false;
$showNoticefeedbackBy = strpos(",$showFields,", ',noticefeedbackBy,') !== false;
$showOS               = strpos(",$showFields,", ',os,')               !== false;
$showBrowser          = strpos(",$showFields,", ',browser,')          !== false;
$showSeverity         = strpos(",$showFields,", ',severity,')         !== false;
$showPri              = strpos(",$showFields,", ',pri,')              !== false;
$showStory            = strpos(",$showFields,", ',story,')            !== false;
$showTask             = strpos(",$showFields,", ',task,')             !== false;
$showMailto           = strpos(",$showFields,", ',mailto,')           !== false;
$showKeywords         = strpos(",$showFields,", ',keywords,')         !== false;

formPanel
(
    on::change('#product',   'changeProduct'),
    on::change('#branch',    'changeBranch'),
    on::change('#project',   'changeProject'),
    on::change('#execution', 'changeExecution'),
    on::change('#module',    'changeModule'),
    on::click('#refresh',    'clickRefresh'),
    on::click('#allBuilds',  'loadAllBuilds'),
    on::click('#allUsers',   'loadAllUsers'),
    to::headingActions(icon('cog-outline')),
    formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::class($product->shadow ? 'hidden' : ''),
            set::label($lang->bug->product),
            inputGroup
            (
                select
                (
                    set::name('product'),
                    set::items($products),
                    set::value($bug->productID)
                ),
                $product->type != 'normal' && isset($products[$bug->productID]) ? select
                (
                    set::width('100px'),
                    set::name('branch'),
                    set::items($branches),
                    set::value($bug->branch)
                ) : null
            )
        ),
        formGroup
        (
            set::width('1/2'),
            set::label($lang->bug->project),
            inputGroup
            (
                set('id', 'projectBox'),
                select
                (
                    set::name('project'),
                    set::items($projects),
                    set::value($bug->projectID)
                )
            )
        )
    ),
    formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::label($lang->bug->module),
            inputGroup
            (
                set('id', 'moduleBox'),
                select
                (
                    set::name('module'),
                    set::items($moduleOptionMenu),
                    set::value($bug->moduleID)
                ),
                count($moduleOptionMenu) == 1 ? span
                (
                    set('class', 'input-group-addon'),
                    a
                    (
                        set('class', 'mr-2'),
                        set('href', $this->createLink('tree', 'browse', "rootID=$productID&view=bug&currentModuleID=0&branch={$bug->branch}")),
                        set('data-toggle', 'modal'),
                        $lang->tree->manage
                    ),
                    a
                    (
                        set('id', 'refreshModule'),
                        set('class', 'text-black'),
                        set('href', 'javascript:void(0)'),
                        icon('refresh')
                    )
                ) : null
            )
        ),
        formGroup
        (
            set::class($showExecution ? '' : 'hidden'),
            set::width('1/2'),
            set::label($bug->projectModel == 'kanban' ? $lang->bug->kanban : $lang->bug->execution),
            inputGroup
            (
                set('id', 'executionBox'),
                select
                (
                    set::name('execution'),
                    set::items($executions),
                    set::value(zget($bug->execution, 'id', ''))
                )
            )
        )
    ),
    formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::label($lang->bug->openedBuild),
            inputGroup
            (
                select
                (
                    set::multiple(true),
                    set::name('openedBuild[]'),
                    set::items($bug->builds),
                    set::value(empty($bug->buildID) ? '' : $bug->buildID)
                ),
                span
                (
                    set('class', 'input-group-addon'),
                    a
                    (
                        set('id', 'allBuilds'),
                        set('href', 'javascript:;'),
                        $lang->bug->allBuilds
                    )
                )
            )
        ),
        formGroup
        (
            set::width('1/2'),
            set::label($lang->bug->lblAssignedTo),
            inputGroup
            (
                select
                (
                    set::name('assignedTo'),
                    set::items($productMembers),
                    set::value($bug->assignedTo)
                ),
                span
                (
                    set('class', 'input-group-addon'),
                    a
                    (
                        set('id', 'allUsers'),
                        set('href', 'javascript:;'),
                        $lang->bug->allUsers
                    )
                )
            )
        )
    ),
    formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::class($showDeadline ? '' : 'hidden'),
            set::label($lang->bug->deadline),
            datePicker
            (
                set::name('deadline'),
                set::value($bug->deadline)
            )
        ),
        formGroup
        (
            set::width('1/2'),
            set::class($showNoticefeedbackBy ? '' : 'hidden'),
            set::label($lang->bug->feedbackBy),
            set::name('feedbackBy'),
            set::value(isset($bug->feedbackBy) ? $bug->feedbackBy : '')
        )
    ),
    formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::class($showNoticefeedbackBy ? '' : 'hidden'),
            set::label($lang->bug->notifyEmail),
            set::name('notifyEmail'),
            set::value($bug->notifyEmail)
        ),
        formGroup
        (
            set::width('1/2'),
            set::label($lang->bug->type),
            set::control(array('type' => 'select', 'items' => $lang->bug->typeList)),
            set::name('type'),
            set::value($bug->type)
        )
    ),
    formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::class($showOS ? '' : 'hidden'),
            set::label($lang->bug->os),
            set::control(array('type' => 'select', 'items' => $lang->bug->osList, 'multiple' => true)),
            set::name('os[]'),
            set::value($bug->os)
        ),
        formGroup
        (
            set::width('1/2'),
            set::class($showOS ? '' : 'hidden'),
            set::label($lang->bug->browser),
            set::control(array('type' => 'select', 'items' => $lang->bug->browserList)),
            set::name('browser'),
            set::value($bug->browser)
        )
    ),
    formRow
    (
        formGroup
        (
            set::label($lang->bug->title),
            set::name('title'),
            set::value($bug->title)
        ),
        formGroup
        (
            set::width('180px'),
            set::class($showSeverity ? '' : 'hidden'),
            set::label($lang->bug->severity),
            set::control(array('type' => 'select', 'items' => $lang->bug->severityList)),
            set::name('severity'),
            set::value($bug->severity)
        ),
        formGroup
        (
            set::width('180px'),
            set::class($showPri ? '' : 'hidden'),
            set::label($lang->bug->pri),
            set::control(array('type' => 'select', 'items' => $lang->bug->priList)),
            set::name('pri'),
            set::value($bug->pri)
        )
    ),
    formRow
    (
        formGroup
        (
            set::label($lang->bug->steps),
            editor
            (
                set::name('steps'),
                set::value($bug->steps ? htmlSpecialString($bug->steps) : '')
            )
        ),
    ),
    formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::class($showStory ? '' : 'hidden'),
            set::label($lang->bug->story),
            inputGroup
            (
                set('id', 'storyBox'),
                select
                (
                    set::name('story'),
                    set::items((empty($bug->stories) ? '' : $bug->stories)),
                    set::value($bug->storyID)
                )
            )
        ),
        formGroup
        (
            set::width('1/2'),
            set::class($showTask ? '' : 'hidden'),
            set::label($lang->bug->task),
            set::control(array('type' => 'select', 'items' => '')),
            set::name('task'),
            set::value($bug->taskID)
        )
    ),
    formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::class($showMailto ? '' : 'hidden'),
            set::label($lang->bug->lblMailto),
            set::control(array('type' => 'select', 'items' => $users, 'multiple' => true)),
            set::name('mailto[]'),
            set::value($bug->mailto ? str_replace(' ', '', $bug->mailto) : '')
        ),
        formGroup
        (
            set::width('1/2'),
            set::class($showKeywords ? '' : 'hidden'),
            set::label($lang->bug->keywords),
            set::name('keywords'),
            set::value($bug->keywords)
        )
    ),
    formRow
    (
        formGroup
        (
            set::label($lang->bug->files),
            set::name('files[]'),
            set::control('file')
        ),
    )
);

render();
