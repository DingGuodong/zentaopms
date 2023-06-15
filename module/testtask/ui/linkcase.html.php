<?php
declare(strict_types=1);
/**
 * The browse view file of testsuite module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Mengyi Liu <liumengyi@easycorp.ltd>
 * @package     testsuite
 * @link        https://www.zentao.net
 */
namespace zin;

$items = array();
foreach($lang->testtask->featureBar['linkcase'] as $key => $label)
{
    $isActive = $key == $type;
    if($key == 'bysuite' || $key == 'bybuild')
    {
        if($isActive) $label = $key == 'bysuite' ? $suiteList[$param]->name : zget($testTask, $param);
        $subItems = array();
        $dataList = $key == 'bysuite' ? $suiteList : $testTask;
        if($dataList)
        {
            foreach($dataList as $dataID => $data)
            {
                $text = $key == 'bysuite' ? $data->name : $data;
                $subItems[] = array
                (
                    'text'   => $text,
                    'active' => $key == $type && $dataID == $param,
                    'url'    => createLink('testtask', 'linkCase', "taskID={$taskID}&type={$key}&param={$dataID}"),
                    'badge'  => $dataID == $param && !empty($pager->recTotal) ? array('text' => $pager->recTotal, 'class' => 'size-sm rounded-full white') : null,
                    'props'  => ['data-id' => $text, 'data-load' => 'table']
                );
            }
        }
        else
        {
            $subItems[] = array ('text' => $key == 'bysuite' ? $lang->testsuite->noticeNone : $lang->testtask->noticeNoOther);
        }
        $items[] = array
        (
            'text'   => $label,
            'active' => $isActive,
            'type'   => 'dropdown',
            'caret'  => 'down',
            'items'  => $subItems,
            'badge'  => $isActive && !empty($pager->recTotal) ? array('text' => $pager->recTotal, 'class' => 'size-sm rounded-full white') : null,
            'props'  => array('data-id' => $label)
        );
    }
    else
    {
        $items[] = array
        (
            'text'   => $label,
            'active' => $isActive,
            'url'    => createLink('testtask', 'linkCase', "taskID={$taskID}&type={$key}"),
            'badge'  => $isActive && !empty($pager->recTotal) ? array('text' => $pager->recTotal, 'class' => 'size-sm rounded-full white') : null,
            'props'  => ['data-id' => $label, 'data-load' => 'table']
        );
    }
}
featureBar
(
    set::items($items),
    set::current($type),
    set::linkParams("taskID=$taskID&type={key}"),
    to::before(a(set::href(createLink('testtask', 'browse', "product={$task->product}")), set::class('btn secondary'), icon('back'), $lang->goback)),
    li(searchToggle())
);

$footToolbar = array('items' => array
(
    array('text' => $lang->save, 'className' => 'batch-btn ajax-btn', 'data-url' => helper::createLink('testtask', 'linkCase', "taskID={$taskID}&type={$type}&param={$param}"))
), 'btnProps' => array('size' => 'sm', 'btnType' => 'secondary'));

div
(
    set('class', 'mb-2'),
    icon('unlink'),
    span
    (
        set('class', 'font-semibold ml-2'),
        $lang->testtask->unlinkedCases . "({$pager->recTotal})"
    )
);
$cases = initTableData($cases, $config->testtask->linkcase->dtable->fieldList, $this->testcase);
$data  = array_values($cases);
dtable
(
    set::userMap($users),
    set::data($data),
    set::cols($config->testtask->linkcase->dtable->fieldList),
    set::fixedLeftWidth('33%'),
    set::checkable(true),
    set::footToolbar($footToolbar),
    set::onRenderCell(jsRaw('window.renderCell')),
    set::footPager(usePager()),
);

render();

