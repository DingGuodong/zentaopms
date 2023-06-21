<?php
declare(strict_types=1);
/**
 * The edit effort view file of task module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Sun Guangming<sunguangming@easycorp.ltd>
 * @package     task
 * @link        https://www.zentao.net
 */

namespace zin;

jsVar('confirmRecord', $lang->task->confirmRecord);
$readonly = (!empty($task->team) and $work->left == 0);

formPanel
(
    set::title($lang->task->editEffort),
    set::headingClass('status-heading'),
    set::titleClass('form-label .form-grid'),
    set::shadow(!isonlybody()),
    to::headingActions
    (
        entityLabel
        (
            setClass('my-3 gap-x-3'),
            set::level(1),
            set::text($task->name),
            set::entityID($task->id),
            set::reverse(true),
        )
    ),
    formGroup
    (
        set::width('1/4'),
        set::label($lang->task->date),
        set::name('date'),
        set::control('date'),
        set::value($effort->date)
    ),
    formRow
    (
        formGroup
        (
            set::width('1/4'),
            set::label($lang->task->currentConsumed),
            inputControl
            (
                input
                (
                    set::name('consumed'),
                    set::value($effort->consumed),
                ),
                to::suffix($lang->task->suffixHour),
                set::suffixWidth(20),
            ),
        ),
        formGroup
        (
            set::width('1/4'),
            set::label($lang->task->left),
            inputControl
            (
                input
                (
                    set::name('left'),
                    set::value($effort->left),
                    set::readonly($readonly),
                ),
                to::suffix($lang->task->suffixHour),
                set::suffixWidth(20),
            ),
        )
    ),
    formGroup
    (
        set::label($lang->task->work),
        set::control('textarea'),
        set::name('work'),
        set::rows('1'),
    ),
);

render();
