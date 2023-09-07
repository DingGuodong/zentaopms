<?php
declare(strict_types=1);
/**
 * The browse view file of release module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian<tianshujie@easycorp.ltd>
 * @package     release
 * @link        https://www.zentao.net
 */
namespace zin;

featureBar
(
    set::current($type),
    set::linkParams("projectID={$projectID}&executionID={$executionID}&type={key}&orderBy={$orderBy}"),
);

toolbar
(
    hasPriv('projectrelease', 'create') ? item(set
    ([
        'text'  => $lang->release->create,
        'icon'  => 'plus',
        'class' => 'btn primary',
        'url'   => $this->createLink('projectrelease', 'create', "projectID={$projectID}"),
    ])) : '',

);

jsVar('markerTitle', $lang->release->marker);
jsVar('showBranch', $showBranch);
jsVar('products', $products);
jsVar('orderBy', $orderBy);
jsVar('sortLink', helper::createLink('projectrelease', 'browse', "projectID={$project->id}&executionID={$executionID}&type={$type}&orderBy={orderBy}&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}"));
jsVar('pageAllSummary', $lang->release->pageAllSummary);
jsVar('pageSummary', $lang->release->pageSummary);
jsVar('type', $type);
jsVar('canViewProjectbuild', common::hasPriv('projectbuild', 'view'));

if($showBranch)
{
    $config->projectrelease->dtable->fieldList['branch']['map'] = $branchPairs;
}
else
{
    unset($config->projectrelease->dtable->fieldList['branch']);
}

$tableData = initTableData($releases, $config->projectrelease->dtable->fieldList);
dtable
(
    set::cols($config->projectrelease->dtable->fieldList),
    set::data($tableData),
    set::fixedLeftWidth('0.33'),
    set::onRenderCell(jsRaw('window.renderCell')),
    set::footer([jsRaw('function(){return window.setStatistics.call(this);}'), 'flex', 'pager']),
    set::footPager(usePager()),
);

/* ====== Render page ====== */
render();
