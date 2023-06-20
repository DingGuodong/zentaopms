<?php
declare(strict_types=1);
/**
 * The view view file of design module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian<tianshujie@easycorp.ltd>
 * @package     design
 * @link        https://www.zentao.net
 */
namespace zin;

jsVar('type', strtolower($design->type));
detailHeader
(
    to::title(entityLabel(set(array('entityID' => $design->id, 'level' => 1, 'text' => $design->name)))),
);

/* Construct suitable actions for the current task. */
$operateMenus = array();
foreach($config->design->view->operateList['main'] as $operate)
{
    if(!common::hasPriv('design', $operate)) continue;

    $operateMenus[] = $config->design->actionList[$operate];
}

/* Construct common actions for task. */
$commonActions = array();
foreach($config->design->view->operateList['common'] as $operate)
{
    if(!common::hasPriv('design', $operate)) continue;
    $commonActions[] = $config->design->actionList[$operate];
}

$moduleName = empty($project->hasProduct) ? 'projectstory' : 'story';
$storyName  = zget($stories, $design->story, '');
if($common::hasPriv($moduleName, 'view'))
{
    $storyItem = a(
        set::href(helper::createLink($moduleName, 'view', "id={$design->story}")),
        $storyName,
    );
}
else
{
    $storyItem = $storyName;
}

detailBody
(
    sectionList
    (
        section
        (
            set::title($lang->design->desc),
            set::content(empty($design->desc) ? $lang->noDesc : $design->desc),
            set::useHtml(true)
        ),
    ),
    history(),
    floatToolbar
    (
        set::prefix
        (
            array(array('icon' => 'back', 'text' => $lang->goback, 'url' => 'javascript:goBack("design-browse")'))
        ),
        set::main($operateMenus),
        set::suffix($commonActions),
        set::object($design)
    ),
    detailSide
    (
        panel
        (
            set::title($lang->design->basicInfo),
            tableData
            (
                item
                (
                    set::name($lang->design->name),
                    $design->name
                ),
                item
                (
                    set::name($lang->design->product),
                    empty($design->productName) ? $lang->noData : $design->productName
                ),
                item
                (
                    set::name($lang->design->story),
                    $storyItem,
                ),
                item
                (
                    set::name($lang->design->submission),
                    empty($design->commit) ? $lang->noData : html($design->commit)
                ),
                item
                (
                    set::name($lang->design->createdBy),
                    zget($users, $design->createdBy)
                ),
                item
                (
                    set::name($lang->design->createdDate),
                    substr($design->createdDate, 0, 11)
                ),
            ),
        ),
    ),
);

/* ====== Render page ====== */
render();
