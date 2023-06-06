<?php
declare(strict_types=1);
/**
 * The browse view file of testtask module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yuting Wang <wangyuting@easycorp.ltd>
 * @package     testtask
 * @link        https://www.zentao.net
 */
namespace zin;
$scope    = $this->session->testTaskVersionScope;
$status   = $this->session->testTaskVersionStatus;
$viewName = $scope == 'local'? $productName : $lang->testtask->all;
jsVar('condition', "productID=$productID&branch=$branch&type=$scope,$status&orderBy=$orderBy&recTotal=0&recPerPage={$pager->recPerPage}&pageID=1");

$this->testtask->buildOperateMenu(null, 'browse');
foreach($tasks as $task)
{
    $actions = array();
    foreach($this->config->testtask->dtable->fieldList['actions']['actionsMap'] as $actionCode => $actionMap)
    {
        $isClickable = $this->testtask->isClickable($task, $actionCode);
        $actions[]   = $isClickable ? $actionCode : array('name' => $actionCode, 'disabled' => true);
    }
    $task->actions = $actions;
}

$productDropdown = productMenu
(
    set::title($viewName),
    set::items(array
    (
        array('text' => $lang->testtask->all, 'url' => helper::createLink('testtask', 'browse', "productID=$productID&branch=0&type=all,$status")),
        array('text' => $productName, 'url' => helper::createLink('testtask', 'browse', "productID=$productID&branch=$branch&type=local,$status"))
    ))
);
featureBar
(
    set::current($status),
    set::linkParams("productID={$productID}&branch=$branch&type={$scope},{key}"),
    to::before($productDropdown),
    inputGroup
    (
        set::class('ml-4'),
        $lang->testtask->beginAndEnd,
        input
        (
            set::name('begin'),
            set::type('date'),
            set::value($beginTime),
        ),
        $lang->testtask->to,
        input
        (
            set::name('end'),
            set::type('date'),
            set::value($endTime),
        )
    )
);

$cols = array_values($config->testtask->dtable->fieldList);
$data = array_values($tasks);
toolbar
(
    btngroup
    (
        btn
        (
            setClass('btn primary'),
            set::icon('plus'),
            set::url(helper::createLink('testtask', 'create', "product=$productID")),
            $lang->testtask->create
        )
    )
);

$footerHTML = $status == 'totalstatus' ? sprintf($lang->testtask->allSummary, count($tasks), $waitCount, $testingCount, $blockedCount, $doneCount) : sprintf($lang->testtask->pageSummary, count($tasks));
dtable
(
    set::cols($cols),
    set::data($data),
    set::userMap($users),
    set::fixedLeftWidth('0.44'),
    set::footer(array(array('html' => $footerHTML), 'flex', 'pager')),
    set::footPager(usePager()),
);

render();
