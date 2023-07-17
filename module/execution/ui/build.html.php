<?php
declare(strict_types=1);
/**
 * The build view file of execution module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian<tianshujie@easycorp.ltd>
 * @package     execution
 * @link        https://www.zentao.net
 */
namespace zin;

/* zin: Define the set::module('executionBuild') feature bar on main menu. */
featureBar
(
    set::current($type),
    set::linkParams("executionID={$execution->id}&type={key}&param={$param}&orderBy={$orderBy}&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}"),
    div
    (
        set::class('select-product-box'),
        picker
        (
            set::name('product'),
            set::value($product),
            set::items($products),
            on::change('changeProduct'),
        ),
    ),
    li(searchToggle(set::module('executionBuild'))),
);

/* zin: Define the toolbar on main menu. */
$canCreateBuild = hasPriv('build', 'create') && common::canModify('execution', $execution);
if($canCreateBuild) $createItem = array('icon' => 'plus', 'class' => 'primary', 'text' => $lang->build->create, 'url' => $this->createLink('build', 'create', "executionID={$execution->id}"));
toolbar
(
    !empty($createItem) ? item(set($createItem)) : null,
);

jsVar('orderBy', $orderBy);
jsVar('executionID', $execution->id);
jsVar('sortLink', helper::createLink('execution', 'build', "executionID={$execution->id}&type={$type}&param={$param}&orderBy={orderBy}&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}"));
jsVar('changeProductLink', helper::createLink('execution', 'build', "executionID={$execution->id}&type=product&param={productID}&orderBy={$orderBy}&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}"));
jsVar('scmPathTip', $lang->build->scmPath);
jsVar('filePathTip', $lang->build->filePath);
jsVar('confirmDelete', $lang->build->confirmDelete);
dtable
(
    set::userMap($users),
    set::cols(array_values($config->build->dtable->fieldList)),
    set::data(array_values($builds)),
    set::sortLink(jsRaw('createSortLink')),
    set::onRenderCell(jsRaw('window.renderCell')),
    set::footPager(
        usePager(),
        set::recPerPage($pager->recPerPage),
        set::recTotal($pager->recTotal),
        set::linkCreator(helper::createLink('execution', 'build', "executionID={$execution->id}&type={$type}&param={$param}&orderBy={$orderBy}&recTotal={recTotal}&recPerPage={page}")),
    ),
);

/* ====== Render page ====== */
render();
