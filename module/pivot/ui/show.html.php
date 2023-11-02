<?php
declare(strict_types = 1);
/**
 * The show view file of pivot module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Gang Liu <liugang@easycorp.ltd>
 * @package     pivot
 * @link        https://www.zentao.net
 */
namespace zin;

jsVar('pivotID', $pivot->id);

$filters = array();
$options = array();
foreach($pivot->filters as $filter)
{
    $type  = $filter['type'];
    $name  = $filter['name'];
    $field = $filter['field'];
    $value = zget($filter, 'default', '');
    $from  = zget($filter, 'from');
    if($from == 'query')
    {
        $typeOption = $filter['typeOption'];
        if($type == 'select' && !isset($options[$typeOption])) $options[$typeOption] = $this->getFilterOptions($typeOption);

        $filters[] = filter(set(array('title' => $name, 'type' => $type, 'name' => $field, 'value' => $value, 'items' => zget($options, $typeOption, array()))));
    }
    else
    {
        if($type == 'select' && !isset($options[$field]))
        {
            $fieldSetting = $pivot->fieldSettings->$field;
            $options[$field] = $this->getFilterOptions($fieldSetting->type, $fieldSetting->object, $fieldSetting->field, $pivot->sql);
        }

        $filters[] = resultFilter(set(array('title' => $name, 'type' => $type, 'name' => $field, 'value' => $value, 'items' => zget($options, $field, array()))));
    }
}

$generateData = function() use ($module, $method, $lang, $title, $pivot, $filters, $data, $configs)
{
    if(empty($module) || empty($method)) return div(setClass('bg-canvas center text-gray w-full h-40'), $lang->pivot->noPivot);

    list($cols, $rows, $cellSpan) = $this->convertDataForDtable($data, $configs);

    return array
    (
        panel
        (
            setID('pivotPanel'),
            set::title($title),
            set::shadow(false),
            set::bodyClass('pt-0'),
            $pivot->desc ? to::titleSuffix(
                icon
                (
                    setClass('cursor-pointer'),
                    setData(array('toggle' => 'tooltip', 'title' => $pivot->desc, 'placement' => 'right', 'className' => 'text-gray border border-light', 'type' => 'white')),
                    'help'
                )
            ) : null,
            $filters ? div
            (
                setID('conditions'),
                setClass('flex justify-between bg-canvas'),
                div
                (
                    setClass('flex flex-wrap w-full'),
                    $filters
                ),
                button(setClass('btn primary'), on::click('loadCustomPivot'), $lang->pivot->query)
            ) : null,
            dtable
            (
                set::striped(true),
                set::bordered(true),
                set::cols($cols),
                set::data($rows),
                set::emptyTip($lang->error->noData),
                set::onRenderCell(jsRaw('renderCell')),
                set::plugins(array('header-group', $cellSpan ? 'cellspan' : null)),
                $cellSpan ? set::getCellSpan(jsRaw('getCellSpan')) : null,
                $cellSpan ? set::cellSpanOptions($cellSpan) : null
            )
        )
    );
};
