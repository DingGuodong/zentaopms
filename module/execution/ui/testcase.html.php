<?php
declare(strict_types=1);
/**
 * The testcase view file of execution module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian<tianshujie@easycorp.ltd>
 * @package     execution
 * @link        https://www.zentao.net
 */
namespace zin;

/* zin: Define the productMenu on main menu. */
$productDropdown = '';
if($execution->hasProduct)
{
    $productDropdown = productMenu(
        set::title($product ? $product->name : $productOption[$productID]),
        set::items($productOption),
        set::activeKey($productID),
        set::link(helper::createLink('execution', 'testcase', "executionID={$execution->id}&productID=%s")),
    );
}

$branchDropdown = '';
if($showBranch)
{
    $branchDropdown = productMenu(
        set::title($branchOption[$branchID]),
        set::items($branchOption),
        set::activeKey($branchID),
        set::link(helper::createLink('execution', 'testcase', "executionID={$execution->id}&productID={$productID}&branch=%s")),
    );
}

/* zin: Define the set::module('testcase') feature bar on main menu. */
featureBar
(
    to::before($productDropdown),
    to::before($branchDropdown),
    set::current($type),
    set::linkParams("executionID={$executionID}&productID={$productID}&branchID={$branchID}&type={key}&moduleID={$moduleID}&orderBy={$orderBy}&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}"),
    li(searchToggle(set::module('testcase')))
);

/* zin: Define the toolbar on main menu. */
$canCreateTestcase = hasPriv('testcase', 'create') && common::canModify('execution', $execution);
if($canCreateTestcase) $createItem = array('icon' => 'plus', 'class' => 'primary', 'text' => $lang->testcase->create, 'url' => $this->createLink('testcase', 'create', "productID={$productID}&branch=0&moduleID=0&from=execution&param={$execution->id}"));
toolbar
(
    !empty($createItem) ? item(set($createItem)) : null,
);

/* zin: Define the sidebar in main content. */
sidebar
(
    moduleMenu(set(array(
        'modules'   => $moduleTree,
        'activeKey' => $moduleID,
        'closeLink' => $this->createLink('execution', 'testcase', "executionID={$executionID}")
    )))
);

$config->testcase->dtable->fieldList['story']['map'] = $stories;
$config->testcase->dtable->fieldList['actions']['list']['edit']['url'] = str_replace('%executionID%', $executionID, $config->testcase->dtable->fieldList['actions']['list']['edit']['url']);
$config->testcase->dtable->fieldList['actions']['menu'] =  array(array('confirmStoryChange'), array('runCase', 'runResult', 'edit', 'createBug', 'create'));
foreach($cases as $case)
{
    initTableData(array($case), $config->testcase->dtable->fieldList, $this->testcase);

    $stages = array_filter(explode(',', $case->stage));
    foreach($stages as $key => $stage) $stages[$key] = zget($lang->testcase->stageList, $stage);
    $case->stage = implode($lang->comma, $stages);
}

jsVar('orderBy', $orderBy);
jsVar('sortLink', helper::createLink('execution', 'testcase', "executionID={$executionID}&productID={$productID}&branchID={$branchID}&type={$type}&moduleID={$moduleID}&orderBy={orderBy}&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}"));

dtable
(
    set::userMap($users),
    set::cols(array_values($config->testcase->dtable->fieldList)),
    set::data(array_values($cases)),
    set::sortLink(jsRaw('createSortLink')),
    set::footPager(
        usePager(),
        set::recPerPage($pager->recPerPage),
        set::recTotal($pager->recTotal),
        set::linkCreator(helper::createLink('execution', 'testcase', "executionID={$executionID}&productID={$productID}&branchID={$branchID}&type={$type}&moduleID={$moduleID}&orderBy={$orderBy}&recTotal={$pager->recTotal}&recPerPage={recPerPage}&pageID={page}")),
    ),
);

render();
