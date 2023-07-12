<?php
declare(strict_types=1);
/**
 * The import from lib view file of testcase module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Tingting Dai <daitingting@easycorp.ltd>
 * @package     testcase
 * @link        https://www.zentao.net
 */
namespace zin;

jsVar('app', $app->tab);
jsVar('productID', $productID);
jsVar('branch', $branch);
jsVar('canImportModules', $canImportModules);

featureBar
(
    to::before
    (
        backBtn
        (
            $lang->goback,
            set::icon('back'),
            set::url($this->session->caseList),
            set::class('secondary')
        ),
        picker
        (
            zui::width('200px'),
            set::name('fromlib'),
            set::items($libraries),
            set::value($libID),
            set::placeholder($lang->testcase->placeholder->selectLib),
            on::change('reload')
        )
    ),
);

div
(
    setID('searchFormPanel'),
    set('data-module', 'testsuite'),
    searchToggle
    (
        set::open(true),
        set::module('testsuite')
    )
);

$config->testcase->importfromlib->dtable->fieldList['fromModule']['map'] = $libModules;
if($product->type == 'normal') unset($config->testcase->importfromlib->dtable->fieldList['branch']);

foreach($cases as $case)
{
    $case->fromModule = $case->module;

    $caseBranches = $branches;
    $caseBranch   = ($branch == 'all' || empty($branch)) ? 0 : $branch;
    foreach($caseBranches as $branchID => $branchName)
    {
        if(empty($canImportModules[$branchID][$case->id]))
        {
            unset($caseBranches[$branchID]);

            if($caseBranch == $branchID) $caseBranch = key($caseBranches);
        }
    }

    $case->moduleItems = $canImportModules[$caseBranch][$case->id];
    $case->branchItems = $caseBranches;
    $case->branch      = $caseBranch;
}

$footToolbar = array('items' => array(array('text' => $lang->testcase->import, 'btnType' => 'secondary', 'className' => 'import-btn')));

formBase
(
    setID('importFromLibForm'),
    set::action(createLink('testcase', 'importFromLib', "product={$productID}&branch={$branch}&libID={$libID}")),
    set::actions(array()),
    on::change('[name*=branch]', 'updateModules'),

    dtable
    (
        set::cols($config->testcase->importfromlib->dtable->fieldList),
        set::data($cases),
        set::onRenderCell(jsRaw('window.renderModuleItem')),
        set::checkable(true),
        set::footToolbar($footToolbar),
        set::footPager(usePager()),
    )
);

div
(
    setID('branchSelect'),
    setClass('hidden'),
    picker
    (
        zui::width('176px'),
        set::name('branch[]'),
    )
);

div
(
    setID('moduleSelect'),
    setClass('hidden'),
    picker
    (
        zui::width('176px'),
        set::name('module[]'),
    )
);

render();
