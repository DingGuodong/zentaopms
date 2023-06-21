<?php
declare(strict_types=1);
/**
 * The browsetask view file of tree module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Jinyong Zhu<zhujinyong@easycorp.ltd>
 * @package     tree
 * @link        https://www.zentao.net
 */
namespace zin;

$maxOrder = 0;

/* Generate module rows. */
$moduleRows = array();
foreach($sons as $son)
{
    if($son->order > $maxOrder) $maxOrder = $son->order;

    if($newModule and !$productID)
    {
        foreach($products as $product)
        {
            $moduleRows[] = formRow
            (
                formGroup
                (
                    setClass('row-module'),
                    input
                    (
                        setClass('col-module'),
                        set::name("products[id$product->id]"),
                        set::type('input'),
                        set::value($product->name),
                        set::disabled(true),
                    ),
                )
            );
        }
    }

    $moduleRows[] = formRow
    (
        formGroup
        (
            inputGroup
            (
                setClass('row-module'),
                input
                (
                    setClass('col-module'),
                    set::name("modules[id$son->id]"),
                    set::type('input'),
                    set::value($son->name),
                    set::placeholder($lang->tree->name),
                ),
                input
                (
                    setClass('col-short'),
                    set::name("shorts[id$son->id]"),
                    set::type('input'),
                    set::value($son->short),
                    set::placeholder($lang->tree->short),
                ),
                input
                (
                    setClass('hidden'),
                    set::name("order[id$son->id]"),
                    set::value($son->order),
                    set::control('hidden'),
                ),
            ),
        ),
    );
}

for($i = 0; $i < \tree::NEW_CHILD_COUNT; $i ++)
{
    $moduleRows[] = formRow
    (
        formGroup
        (
            inputGroup
            (
                setClass('row-module'),
                input
                (
                    setClass('col-module'),
                    set::name("modules[$i]"),
                    set::type('input'),
                    set::value(''),
                    set::placeholder($lang->tree->name),
                ),
                input
                (
                    setClass('col-short'),
                    set::name("shorts[$i]"),
                    set::type('input'),
                    set::value(''),
                    set::placeholder($lang->tree->short),
                ),
                input
                (
                    setClass('hidden'),
                    set::name("branch[$i]"),
                    set::value(0),
                    set::control('hidden'),
                ),
            ),
            batchActions(),
        ),
    );
}

$parentPath = array();
$parentPath[] = span
(
    a
    (
        setClass('tree-link'),
        set('href', helper::createLink('tree', 'browsetask', "rootID=$root->id&productID=$productID")),
        $root->name,
    ),
    h::i
    (
        setClass('icon icon-angle-right muted align-middle'),
        setStyle('color', '#313C52'),
    )
);
foreach($parentModules as $module)
{
    $parentPath[] = span
    (
        a
        (
            setClass('tree-link'),
            set('href', helper::createLink('tree', 'browsetask', "rootID=$root->id&productID=$productID&module=$module->id")),
            $module->name,
        ),
        h::i
        (
            setClass('icon icon-angle-right muted align-middle'),
            setStyle('color', '#313C52'),
        )
    );
}

div
(
    setClass('flex gap-x-4 mb-3'),
    backBtn
    (
        set::icon('back'),
        set::type('secondary'),
        set::back('execution-task'),
        $lang->goback,
    ),
    div
    (
        setClass('entity-label flex items-center gap-x-2 article-h1'),
        $lang->tree->common . $lang->colon . $root->name,
    )
);

div
(
    setClass('flex gap-x-4'),
    div
    (
        setClass('sidebar sidebar-left basis-2/6'),
        panel
        (
            set::title($title),
            tree(set('items', $tree)),
        )
    ),
    div
    (
        setClass('basis-4/6'),
        panel
        (
            set::title($execution->multiple ? $lang->tree->manageTaskChild : $lang->tree->manageProjectChild),
            div
            (
                setClass('flex'),
                div
                (
                    setClass('p-1 tree-item-content'),
                    $parentPath,
                ),
                form
                (
                    setClass('flex-1 form-grid'),
                    set::url(helper::createLink('tree', 'manageChild', "root=$root->id&viewType=task")),
                    $moduleRows,
                    set::actions(array('submit', 'cancel')),
                    set::back('execution-task'),
                    set::actionsClass('justify-start'),
                    formGroup
                    (
                        setClass('hidden'),
                        set::name('parentModuleID'),
                        set::value($currentModuleID),
                        set::control('hidden'),
                    ),
                    formGroup
                    (
                        setClass('hidden'),
                        set::name('maxOrder'),
                        set::value($maxOrder),
                        set::control('hidden'),
                    ),
                ),
            ),
        )
    )
);

render();
