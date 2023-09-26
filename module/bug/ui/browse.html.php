<?php
declare(strict_types=1);
/**
 * The browse view file of bug module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Tingting Dai <daitingting@easycorp.ltd>
 * @package     bug
 * @link        https://www.zentao.net
 */
namespace zin;

jsVar('productID', $product->id);
jsVar('branch',    $branch);

$queryMenuLink = createLink('bug', 'browse', "productID={$product->id}&branch={$branch}&browseType=bySearch&param={queryID}");
featureBar
(
    set::current($browseType == 'bysearch' ? $param : $browseType),
    set::linkParams("product={$product->id}&branch={$branch}&browseType={key}&param={$param}&orderBy={$orderBy}&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"),
    set::queryMenuLinkCallback(fn($key) => str_replace('{queryID}', (string)$key, $queryMenuLink)),
    li(searchToggle())
);

$canBeChanged         = common::canModify('product', $product);
$canBatchEdit         = $canBeChanged && hasPriv('bug', 'batchEdit');
$canBatchConfirm      = $canBeChanged && hasPriv('bug', 'batchConfirm');
$canBatchActivate     = $canBeChanged && hasPriv('bug', 'batchActivate');
$canBatchChangeBranch = $canBeChanged && hasPriv('bug', 'batchChangeBranch');
$canBatchChangeModule = $canBeChanged && hasPriv('bug', 'batchChangeModule');
$canBatchResolve      = $canBeChanged && hasPriv('bug', 'batchResolve');
$canBatchAssignTo     = $canBeChanged && hasPriv('bug', 'batchAssignTo');
$canBatchClose        = hasPriv('bug', 'batchClose');
$canManageModule      = hasPriv('tree', 'browse');
$canBatchAction       = $canBatchEdit || $canBatchConfirm || $canBatchClose || $canBatchActivate || $canBatchChangeBranch || $canBatchChangeModule || $canBatchResolve || $canBatchAssignTo;

if(!isonlybody())
{
    $canCreate      = false;
    $canBatchCreate = false;
    if($canBeChanged)
    {
        $canCreate      = hasPriv('bug', 'create');
        $canBatchCreate = hasPriv('bug', 'batchCreate');

        $selectedBranch  = $branch != 'all' ? $branch : 0;
        $createLink      = $this->createLink('bug', 'create', "productID={$product->id}&branch=$selectedBranch&extra=moduleID=$currentModuleID");
        $batchCreateLink = $this->createLink('bug', 'batchCreate', "productID={$product->id}&branch=$branch&executionID=0&moduleID=$currentModuleID");
        if(commonModel::isTutorialMode())
        {
            $wizardParams = helper::safe64Encode("productID={$product->id}&branch=$branch&extra=moduleID=$currentModuleID");
            $createLink   = $this->createLink('tutorial', 'wizard', "module=bug&method=create&params=$wizardParams");
        }

        $createItem      = array('text' => $lang->bug->create,      'url' => $createLink);
        $batchCreateItem = array('text' => $lang->bug->batchCreate, 'url' => $batchCreateLink);
    }

    toolbar
    (
        hasPriv('bug', 'report') ? item(set(array
        (
            'icon'  => 'bar-chart',
            'text'  => $lang->bug->report->common,
            'class' => 'ghost',
            'url'   => createLink('bug', 'report', "productID=$product->id&browseType=$browseType&branch=$branch&module=$currentModuleID")
        ))) : null,
        hasPriv('bug', 'export') ? item(set(array
        (
            'text'  => $lang->bug->export,
            'icon'  => 'export',
            'class' => 'ghost',
            'url'   => createLink('bug', 'export', "productID={$product->id}&orderBy=$orderBy&browseType=$browseType"),
            'data-toggle' => 'modal'
        ))) : null,
        $canCreate && $canBatchCreate ? btngroup
        (
            btn(setClass('btn primary'), set::icon('plus'), set::url($createLink), $lang->bug->create),
            dropdown
            (
                btn(setClass('btn primary dropdown-toggle'), setStyle(array('padding' => '6px', 'border-radius' => '0 2px 2px 0'))),
                set::items(array($createItem, $batchCreateItem)),
                set::placement('bottom-end'),
            )
        ) : null,
        $canCreate && !$canBatchCreate ? item(set($createItem + array('class' => 'btn primary', 'icon' => 'plus'))) : null,
        $canBatchCreate && !$canCreate ? item(set($batchCreateItem + array('class' => 'btn primary', 'icon' => 'plus'))) : null,
    );
}

$closeLink   = createLink('bug', 'browse', "productID={$product->id}&branch=$branch&browseType=$browseType&param=0&orderBy=$orderBy&recTotal=0&recPerPage={$pager->recPerPage}");
$settingLink = $canManageModule ? createLink('tree', 'browse', "productID={$product->id}&view=bug&currentModuleID=0&branch=0&from={$this->lang->navGroup->bug}") : '';
sidebar
(
    moduleMenu(set(array
    (
        'modules'     => $moduleTree,
        'activeKey'   => $currentModuleID,
        'closeLink'   => $closeLink,
        'settingLink' => $settingLink,
    )))
);

$resolveItems = array();
foreach($lang->bug->resolutionList as $key => $resolution)
{
    if($key == 'duplicate' || $key == 'tostory') continue;
    if($key == 'fixed')
    {
        $buildItems = array();
        foreach($builds as $key => $build) $buildItems[] = array('text' => $build, 'class' => 'batch-btn ajax-btn not-open-url', 'data-url' => createLink('bug', 'batchResolve', "resolution=fixed&resolvedBuild=$key"));

        $resolveItems[] = array('text' => $resolution, 'class' => 'not-hide-menu', 'items' => $buildItems);
    }
    else
    {
        $resolveItems[] = array('text' => $resolution, 'class' => 'batch-btn ajax-btn not-open-url', 'data-url' => createLink('bug', 'batchResolve', "resolution=$key"));
    }
}

$batchItems = array
(
    array('text' => $lang->bug->confirm,  'class' => 'batch-btn ajax-btn not-open-url ' . ($canBatchConfirm ? '' : 'hidden'), 'data-url' => helper::createLink('bug', 'batchConfirm')),
    array('text' => $lang->bug->close,    'class' => 'batch-btn ajax-btn not-open-url ' . ($canBatchClose ? '' : 'hidden'), 'data-url' => helper::createLink('bug', 'batchClose')),
    array('text' => $lang->bug->activate, 'class' => 'batch-btn not-open-url ' . ($canBatchActivate ? '' : 'hidden'), 'data-url' => helper::createLink('bug', 'batchActivate', "productID=$product->id&branch=$branch")),
    array('text' => $lang->bug->resolve,  'class' => 'not-hide-menu ' . ($canBatchResolve ? '' : 'hidden'), 'items' => $resolveItems)
);

$branchItems = array();
foreach($branchTagOption as $branchID => $branchName)
{
    $branchItems[] = array('text' => $branchName, 'class' => 'batch-btn ajax-btn not-open-url', 'data-url' => helper::createLink('bug', 'batchChangeBranch', "branchID=$branchID"));
}

$moduleItems = array();
foreach($modules as $moduleID => $module)
{
    $moduleItems[] = array('text' => $module, 'class' => 'batch-btn ajax-btn not-open-url', 'data-url' => helper::createLink('bug', 'batchChangeModule', "moduleID=$moduleID"));
}

$assignedToItems = array();
foreach ($memberPairs as $key => $value)
{
    $assignedToItems[] = array('text' => $value, 'class' => 'batch-btn ajax-btn not-open-url', 'data-url' => helper::createLink('bug', 'batchAssignTo', "assignedTo=$key&productID={$product->id}&type=product"));
}

$footToolbar = array();
if($canBatchAction)
{
    $footToolbar['items'] = array();
    $footToolbar['items'][] = array('type' => 'btn-group', 'items' => array
    (
        array('text' => $lang->edit, 'className' => 'primary batch-btn not-open-url', 'disabled' => ($canBatchEdit ? '': 'disabled'), 'data-url' => createLink('bug', 'batchEdit', "productID={$product->id}&branch=$branch")),
        array('caret' => 'up', 'data-placement' => 'top-start', 'class' => 'btn btn-caret size-sm primary not-open-url', 'items' => $batchItems),
    ));
    if($canBatchChangeBranch && $this->session->currentProductType && $this->session->currentProductType != 'normal')
    {
        $footToolbar['items'][] = array('caret' => 'up', 'text' => $lang->product->branchName[$this->session->currentProductType], 'type' => 'dropdown', 'data-placement' => 'top-start', 'items' => $branchItems);
    }
    if($canBatchChangeModule)
    {
        $footToolbar['items'][] = array('caret' => 'up', 'text' => $lang->bug->abbr->module, 'type' => 'dropdown', 'data-placement' => 'top-start', 'items' => $moduleItems);
    }
    if($canBatchAssignTo)
    {
        $footToolbar['items'][] = array('caret' => 'up', 'text' => $lang->bug->assignedTo, 'type' => 'dropdown', 'data-placement' => 'top-start', 'items' => $assignedToItems);
    }
    $footToolbar['btnProps'] = array('size' => 'sm', 'btnType' => 'primary');
}

$cols = $this->loadModel('datatable')->getSetting('bug');
if(isset($cols['branch']))    $cols['branch']['map']    = $branchTagOption;
if(isset($cols['project']))   $cols['project']['map']   = $projectPairs;
if(isset($cols['execution'])) $cols['execution']['map'] = $executions;

if($product->type == 'normal') unset($cols['branch']);

$bugs = initTableData($bugs, $cols, $this->bug);

dtable
(
    set::cols($cols),
    set::data(array_values($bugs)),
    set::fixedLeftWidth('0.44'),
    set::userMap($users),
    set::customCols(true),
    set::checkable($canBatchAction),
    set::footToolbar($footToolbar),
    set::footPager(usePager()),
    set::onRenderCell(jsRaw('window.onRenderCell')),
    set::modules($modulePairs),
    set::emptyTip($lang->bug->notice->noBug),
    set::createTip($lang->bug->create),
    set::createLink($canBeChanged && hasPriv('bug', 'create') ? array('module' => 'bug', 'method' => 'create', 'params' => "productID={$product->id}&branch={$branch}&extra=moduleID=$currentModuleID") : '')
);

render();
