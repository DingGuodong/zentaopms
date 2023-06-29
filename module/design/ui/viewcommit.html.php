<?php
declare(strict_types=1);
/**
 * The viewCommit view file of design module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian<tianshujie@easycorp.ltd>
 * @package     design
 * @link        https://www.zentao.net
 */
namespace zin;

modalHeader(set::title($lang->design->viewCommit));

$canLinkCommit  = hasPriv('design', 'create');
$linkBtnClass   = empty($design->commit) ? 'ml-4' : 'mb-4';
$linkCommitItem = $canLinkCommit ? btn(
    setClass("linkCommitBtn {$linkBtnClass}"),
    set::icon('plus'),
    set::text($lang->design->linkCommit),
    set::type('primary'),
    set('url', createLink('design', 'linkCommit', "designID=$design->id")),
    set('data-load', 'modal'),
) : null;

if(empty($design->commit))
{
    div
    (
        setClass('no-data-box'),
        span
        (
            setClass('text-gray'),
            $lang->design->noCommit,
        ),
        $linkCommitItem
    );
}
else
{
    div
    (
        setClass('flex justify-end'),
        $linkCommitItem
    );

    $tableData = initTableData($design->commit, $config->design->viewcommit->dtable->fieldList);
    dtable
    (
        set::userMap($users),
        set::cols($config->design->viewcommit->dtable->fieldList),
        set::data($tableData),
        set::footPager(
            usePager(),
            set::recPerPage($pager->recPerPage),
            set::recTotal($pager->recTotal),
            set::linkCreator(helper::createLink('design', 'viewCommit', "designID={$designID}&recTotal={recTotal}&recPerPage={recPerPage}&pageID={pageID}", '', true)),
        ),
    );
}

/* ====== Render page ====== */
render();
