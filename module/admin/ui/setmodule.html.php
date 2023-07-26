<?php
declare(strict_types=1);
/**
 * The set module view file of admin module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Gang Liu <liugang@easycorp.ltd>
 * @package     admin
 * @link        https://www.zentao.net
 */
namespace zin;

$rows = array();
foreach($config->featureGroup as $group => $features)
{
    if(strpos(",$disabledFeatures,", ",$group,") !== false) continue;

    $hasData = false;
    foreach($features as $feature)
    {
        $code = $group . ucfirst($feature);
        if(strpos(",$disabledFeatures,", ",$code,") !== false) continue;
        $hasData = true;
    }

    if($hasData)
    {
        $items = array();
        foreach($features as $feature)
        {
            $code = $group. ucfirst($feature);
            if(strpos(",$disabledFeatures,", ",$code,") !== false) continue;

            if($code == 'myScore')
            {
                $value = $useScore ? 1 : 0;
            }
            else
            {
                $value = strpos(",$closedFeatures,", ",$code,") === false ? '1' : '0';
            }

            $items[] = checkbox
            (
                set::rootClass('w-40'),
                set::id("module{$code}"),
                set::name("module[{$code}]"),
                set::value(1),
                set::checked($value == 1),
                on::change('checkModule'),
                $lang->admin->setModule->{$feature}
            );

            $items[] = input
            (
                set::type('hidden'),
                set::id("module{$code}"),
                set::name("module[{$code}]"),
                set::value($value),
            );
        }
        $rows[] = h::tr
        (
            setClass('border-t'),
            h::td
            (
                setClass('p-2.5'),
                checkbox
                (
                    set::id("allChecker{$group}"),
                    set::name("allChecker[$group]"),
                    on::change('checkGroup'),
                    $lang->admin->setModule->{$group}
                )
            ),
            h::td
            (
                setClass('flex flex-wrap p-2.5 border-l'),
                $items
            )
        );
    }
}

formPanel
(
    set::title($lang->admin->setModuleIndex),
    set::actions(false),
    h::table
    (
        setClass('border w-full'),
        h::thead
        (
            h::tr
            (
                h::th
                (
                    setClass('text-md p-2.5'),
                    setStyle(array('width' => '100px')),
                    $lang->admin->setModule->module
                ),
                h::th
                (
                    setClass('text-md p-2.5 border-l'),
                    $lang->admin->setModule->optional
                )
            ),
        ),
        h::tbody
        (
            $rows,
            h::tr
            (
                setClass('border-t'),
                h::td
                (
                    setClass('p-2.5'),
                    checkbox
                    (
                        set::id('allCheckeer'),
                        on::change('checkAll'),
                        $lang->selectAll
                    )
                ),
                h::td
                (
                    setClass('form-actions inline-flex gap-4 p-2.5 border-l'),
                    button
                    (
                        setClass('btn primary'),
                        set::type('submit'),
                        $lang->save
                    ),
                    button
                    (
                        setClass('btn open-url'),
                        set(array('data-back' => 'APP')),
                        $lang->goback
                    )
                )
            )
        )
    ),
);

render();
