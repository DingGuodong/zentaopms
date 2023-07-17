<?php
declare(strict_types=1);
/**
 * The createcase view file of caselib module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Mengyi Liu <liumengyi@easycorp.ltd>
 * @package     caselib
 * @link        https://www.zentao.net
 */
namespace zin;

formPanel
(
    // on::click('#refresh', 'clickRefresh'),
    formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::label($lang->testcase->lib),
            inputGroup
            (
                picker
                (
                    set::name('lib'),
                    set::items($libraries),
                    set::value($libID)
                ),
            )
        ),
        formGroup
        (
            set::width('1/2'),
            set::label($lang->testcase->module),
            inputGroup
            (
                set('id', 'moduleBox'),
                picker
                (
                    set::name('module'),
                    set::items($moduleOptionMenu),
                    set::value($currentModuleID)
                ),
                count($moduleOptionMenu) == 1 ? span
                (
                    set('class', 'input-group-addon'),
                    a
                    (
                        set('class', 'mr-2'),
                        set('href', $this->createLink('tree', 'browse', "rootID={$libID}&view=caselib&currentModuleID=0")),
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
    ),
    formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::label($lang->testcase->type),
            picker
            (
                set::name('type'),
                set::items($lang->testcase->typeList),
                set::value($type)
            ),
        ),
        formGroup
        (
            set::width('1/2'),
            set::label($lang->testcase->stage),
            inputGroup
            (
                set('id', 'stageBox'),
                picker
                (
                    set::name('stage[]'),
                    set::multiple(true),
                    set::items($lang->testcase->stageList),
                    set::value($stage)
                )
            )
        )
    ),
    formRow
    (
        formGroup
        (
            set::label($lang->testcase->title),
            inputControl
            (
                input
                (
                    set::name('title'),
                    set::value($caseTitle)
                ),
                set::suffixWidth('icon'),
                to::suffix
                (
                    colorPicker
                    (
                        set::name('color'),
                        set::value(''),
                        set::syncColor('#title')
                    )
                )
            )
        ),
        formGroup
        (
            set::width('180px'),
            set::label($lang->testcase->pri),
            set::control(array('type' => 'priPicker', 'items' => $lang->testcase->priList)),
            set::name('pri'),
            set::value()
        ),
    ),
    formRow
    (
        formGroup
        (
            set::label($lang->testcase->precondition),
            set::control(array('type' => 'textarea', 'rows' => 2)),
            set::name('precondition'),
            set::value($precondition)
        )
    ),
    formRow
    (

        formGroup
        (
            set::label($lang->testcase->steps),
            printStepsTable()
        )
    ),
    formRow
    (
        formGroup
        (
            set::label($lang->testcase->keywords),
            set::name('keywords'),
            set::value($keywords)
        )
    ),
    formRow
    (
        formGroup
        (
            set::label($lang->testcase->files),
            upload()
        )
    )
);

render();

function printStepsTable()
{
    global $lang;

    $stepsTR = array();
    for($i = 1; $i <= 3; $i ++)
    {
        $stepsTR[] = h::tr
        (
            h::td
            (
                set::class('center'),
                $i
            ),
            h::td
            (
                inputGroup
                (
                    textarea
                    (
                        set::rows(1),
                        set::name("steps[$i]")
                    ),
                    span
                    (
                        set('class', 'input-group-addon'),
                        checkbox
                        (
                            set::name("stepType[$i]"),
                            set::text($lang->testcase->group),
                        )
                    )
                )
            ),
            h::td
            (
                textarea
                (
                    set::rows(1),
                    set::name("expects[$i]")
                )
            ),
            h::td
            (
                set::class('center'),
                btnGroup
                (
                    set::items(array(
                        array('icon' => 'plus'),
                        array('icon' => 'trash'),
                        array('icon' => 'move')
                    ))
                )
            )
        );
    }
    return h::table
    (
        set::class('w-full'),
        h::thead
        (
            h::tr
            (
                h::th($lang->testcase->stepID),
                h::th($lang->testcase->stepDesc),
                h::th($lang->testcase->stepExpect),
                h::th($lang->actions)
            )
        ),
        h::tbody
        (
            $stepsTR
        )
    );
}
