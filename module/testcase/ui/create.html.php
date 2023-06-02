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
jsVar('executionID', $executionID);
jsVar('tab', $this->app->tab);
if($app->tab == 'execution') jsVar('objectID', $executionID);
if($app->tab == 'project')   jsVar('objectID', $projectID);

formPanel
(
    on::change('#product',   'changeProduct'),
    on::change('#branch',    'loadBranchNew'),
    on::click('#refresh',    'loadProductModulesNew'),
    to::headingActions(icon('cog-outline')),
    formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::class($hiddenProduct ? 'hidden' : ''),
            set::label($lang->testcase->product),
            inputGroup
            (
                select
                (
                    set::name('product'),
                    set::items($products),
                    set::value($productID)
                ),
                isset($product->type) && $product->type != 'normal' ? select
                (
                    set::width('100px'),
                    set::name('branch'),
                    set::items($branches),
                    set::value($branch)
                ) : null
            )
        ),
        formGroup
        (
            set::width('1/2'),
            set::label($lang->testcase->module),
            inputGroup
            (
                set('id', 'moduleBox'),
                select
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
                        set('href', $this->createLink('tree', 'browse', "rootID=$productID&view=bug&currentModuleID=0&branch={$branch}")),
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
            set::label($lang->testcase->scene),
            inputGroup
            (
                set('id', 'sceneBox'),
                select
                (
                    set::name('scene'),
                    set::items($sceneOptionMenu),
                    set::value($currentSceneID)
                )
            )
        )
    ),
    formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::label($lang->testcase->type),
            inputGroup
            (
                select
                (
                    set::name('type'),
                    set::items($lang->testcase->typeList),
                    set::value($type)
                ),
                span
                (
                    set('class', 'input-group-addon'),
                    checkbox
                    (
                        set::name('auto'),
                        set::text($lang->testcase->showAutoCase),
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
            set::label($lang->testcase->scene),
            inputGroup
            (
                set('id', 'stageBox'),
                select
                (
                    set::name('stage'),
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
            set::width('1/2'),
            set::label($lang->testcase->lblStory),
            inputGroup
            (
                set('id', 'storyBox'),
                select
                (
                    set::name('story'),
                    set::items($stories),
                    set::value($storyID)
                )
            )
        )
    ),
    formRow
    (
        formGroup
        (
            set::label($lang->testcase->title),
            set::name('title'),
            set::value($caseTitle)
        ),
        formGroup
        (
            set::width('180px'),
            set::class(),
            set::label($lang->testcase->pri),
            set::control(array('type' => 'select', 'items' => $lang->testcase->priList)),
            set::name('pri'),
            set::value()
        ),
        formGroup
        (
            set::width('150px'),
            set::class(),
            set::label('是否评审'),
            set::control(array('type' => 'select', 'items' => $lang->testcase->reviewList)),
            set::name('forceNotReview'),
            set::value('1')
        )
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
            set::name('files[]'),
            set::control('file')
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
                            set::text($lang->testcase->showAutoCase),
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
