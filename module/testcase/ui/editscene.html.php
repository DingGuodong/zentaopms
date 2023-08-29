<?php
declare(strict_types=1);
/**
 * The editscene view file of testcase module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Mengyi Liu <liumengyi@easycorp.ltd>
 * @package     testcase
 * @link        https://www.zentao.net
 */
namespace zin;

jsVar('tab', $this->app->tab);
jsVar('caseModule', $lang->testcase->module);

formPanel
(
    entityLabel
    (
        to::prefix($lang->testcase->editScene),
        set::entityID($scene->id),
        set::level(1),
        set::text($scene->title),
        set::reverse(true),
    ),
    on::change('#product', 'loadProductRelated'),
    on::change('#branch', 'loadBranchRelated'),
    on::change('#module', 'loadModuleRelated'),
    on::click('.refresh', 'refreshModules'),
    formRow
    (
        formGroup
        (
            set::label($lang->testcase->product),
            set::width('1/2'),
            inputGroup
            (
                picker
                (
                    setID('product'),
                    set::name('product'),
                    set::items($products),
                    set::value($scene->product),
                ),
                isset($product->type) && $product->type != 'normal' ? picker
                (
                    setID('branch'),
                    zui::width('120px'),
                    set::name('branch'),
                    set::items($branchTagOption),
                    set::value($scene->branch)
                ) : null
            )
        ),
        formGroup
        (
            inputGroup
            (
                $lang->testcase->module,
                set::id('moduleIdBox'),
                picker
                (
                    setID('module'),
                    set::name('module'),
                    set::items($moduleOptionMenu),
                    set::value($scene->module),
                    set::required(true),
                ),
                count($moduleOptionMenu) == 1 ? div
                (
                    set::className('input-group-btn flex'),
                    a
                    (
                        $lang->tree->manage,
                        set('href', createLink('tree', 'browse', "rootId=$productID&view=case&currentModuleID=0&branch=$branch")),
                        set('class', 'btn'),
                        set('data-toggle', 'mdoal'),
                    ),
                    a
                    (
                        $lang->refresh,
                        set('href', 'javascript:;'),
                        set('class', 'btn refresh')
                    )
                ) : null
            )
        )
    ),
    formGroup
    (
        set::label($lang->testcase->parentScene),
        set::id('sceneIdBox'),
        picker
        (
            set::name('parent'),
            set::items($sceneOptionMenu),
            set::value($scene->parent),
        )
    ),
    formGroup
    (
        set::label($lang->testcase->sceneTitle),
        set::required(true),
        set::name('title'),
        set::value($scene->title),
    )
);

render();
