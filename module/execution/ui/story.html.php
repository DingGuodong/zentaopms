<?php
declare(strict_types=1);
/**
 * The story view file of execution module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      dingguodong <dingguodong@easycorp.ltd>
 * @package     execution
 * @link        https://www.zentao.net
 */

namespace zin;

/* Show feature bar. */
featureBar
(
    set::current($type),
    set::link(createLink($app->rawModule, $app->rawMethod, "&executionID=$executionID&storyType=$storyType&orderBy=$orderBy&type={key}")),
    li(searchToggle(set::module('executionStory')))
);

jsVar('executionID', $executionID);
$linkStoryByPlanTips = $multiBranch ? sprintf($lang->execution->linkBranchStoryByPlanTips, $lang->project->branch) : $lang->execution->linkNormalStoryByPlanTips;
$linkStoryByPlanTips = $execution->multiple ? $linkStoryByPlanTips : str_replace($lang->execution->common, $lang->projectCommon, $linkStoryByPlanTips);
modal
(
    setID('linkStoryByPlan'),
    set::modalProps(array('title' => $lang->execution->linkStoryByPlan)),
    div
    (
        setClass('flex-auto'),
        icon('info-sign', setClass('warning-pale rounded-full mr-1')),
        $linkStoryByPlanTips
    ),
    form
    (
        setClass('text-center', 'py-4'),
        set::actions(array('submit')),
        set::submitBtnText($lang->execution->linkStory),
        formGroup
        (
            set::label($lang->execution->selectStoryPlan),
            set::required(true),
            select
            (
                set::name('plan'),
                set::items($allPlans)
            )
        ),
    )
);

/* Show tool bar. */
$canModifyProduct = common::canModify('product', $product);
$canCreate        = $canModifyProduct && hasPriv('story', 'create');
$canBatchCreate   = $canModifyProduct && hasPriv('story', 'canBatchCreate');
$createLink       = createLink('story', 'create', "product={$productID}&branch=0&moduleID=0&storyID=0&objectID={$executionID}&bugID=0&planID=0&todoID=0&extra=&storyType={$storyType}");
$batchCreateLink  = createLink('story', 'batchCreate', "productID={$productID}&branch=0&moduleID=0&storyID=0&executionID={$executionID}&plan=0&storyType={$storyType}");

/* Tutorial create link. */
if(commonModel::isTutorialMode())
{
    $wizardParams   = helper::safe64Encode("productID={$productID}&branch=0&moduleID=0");
    $createLink     = $this->createLink('tutorial', 'wizard', "module=story&method=create&params={$wizardParams}");
    $canBatchCreate = false;
}

$createItem      = array('text' => $lang->story->create,      'url' => $createLink);
$batchCreateItem = array('text' => $lang->story->batchCreate, 'url' => $batchCreateLink);

$canLinkStory     = $canModifyProduct && hasPriv('story', 'linkStory');
$canlinkPlanStory = $canModifyProduct && hasPriv('story', 'importPlanStories');
$linkStoryUrl     = createLink('story', 'linkStory', "project={$executionID}");

if(commonModel::isTutorialMode())
{
    $wizardParams     = helper::safe64Encode("project={$executionID}");
    $linkStoryUrl     = createLink('tutorial', 'wizard', "module=project&method=linkStory&params=$wizardParams");
    $canlinkPlanStory = false;
}

$linkItem     = array('text' => $lang->story->linkStory, 'url' => $linkStoryUrl);
$linkPlanItem = array('text' => $lang->execution->linkStoryByPlan, 'url' => '#linkStoryByPlan', 'data-toggle' => 'modal', 'data-size' => 'sm');

toolbar
(
    hasPriv('story', 'report') ? item(set(array
    (
        'text'  => $lang->story->report->common,
        'icon'  => 'bar-chart',
        'class' => 'ghost',
        'url'   => createLink('story', 'report', "productID={$productID}&branchID=&storyType={$storyType}&browseType={$type}&moduleID={$param}&chartType=pie&projectID={$execution->id}"),
    ))) : null,
    hasPriv('story', 'export') ? item(set(array
    (
        'text'        => $lang->story->export,
        'icon'        => 'export',
        'class'       => 'ghost',
        'url'         => createLink('story', 'export', "productID=$productID&orderBy=$orderBy&executionID=$executionID&browseType=$type&storyType=$storyType"),
        'data-toggle' => 'modal'
    ))) : null,

    $canCreate && $canBatchCreate ? btngroup
    (
        btn
        (
            setClass('btn primary'),
            set::icon('plus'),
            set::url($createLink),
            $lang->story->create
        ),
        dropdown
        (
            btn(setClass('btn primary dropdown-toggle'),
            setStyle(array('padding' => '6px', 'border-radius' => '0 2px 2px 0'))),
            set::items(array_filter(array($createItem, $batchCreateItem))),
            set::placement('bottom-end'),
        )
    ) : null,
    $canCreate && !$canBatchCreate ? item(set($createItem + array('class' => 'btn primary', 'icon' => 'plus'))) : null,
    $canBatchCreate && !$canCreate ? item(set($batchCreateItem + array('class' => 'btn primary', 'icon' => 'plus'))) : null,

    $canLinkStory && $canlinkPlanStory ? btngroup
    (
        btn(
            setClass('btn primary'),
            set::icon('plus'),
            set::url($linkStoryUrl),
            $lang->story->linkStory
        ),
        dropdown
        (
            btn(setClass('btn primary dropdown-toggle'),
            setStyle(array('padding' => '6px', 'border-radius' => '0 2px 2px 0'))),
            set::items(array_filter(array($linkItem, $linkPlanItem))),
            set::placement('bottom-end'),
        )
    ) : null,
    $canLinkStory && !$canlinkPlanStory ? item(set($linkItem + array('class' => 'btn primary', 'icon' => 'plus'))) : null,
    $canlinkPlanStory && !$canLinkStory ? item(set($linkPlanItem + array('class' => 'btn primary', 'icon' => 'plus'))) : null,
);

sidebar
(
    moduleMenu(set(array(
        'modules'     => $moduleTree,
        'activeKey'   => $param,
        'closeLink'   => $this->createLink('execution', 'story')
    )))
);

modal
(
    setID('taskModal'),
    set::modalProps(array('title' => $lang->story->batchToTask, 'titleClass' => 'flex-initial')),
    to::header
    (
        div
        (
            setClass('flex-auto'),
            icon('info-sign', setClass('warning-pale rounded-full mr-1')),
            $lang->story->batchToTaskTips
        )
    ),
    form
    (
        setClass('text-center', 'py-4'),
        setID('toTaskForm'),
        set::actions(array('submit')),
        set::submitBtnText($lang->execution->next),
        set::url(createLink('story', 'batchToTask', "executionID={$execution->id}&projectID={$execution->project}")),
        formGroup
        (
            set::label($lang->task->type),
            set::required(true),
            set::width('1/2'),
            select
            (
                set::name('type'),
                set::items($lang->task->typeList)
            )
        ),
        $lang->hourCommon !== $lang->workingHour ? formGroup
        (
            set::label($lang->story->one . $lang->hourCommon),
            set::required(true),
            set::width('1/2'),
            inputGroup
            (
                span
                (
                    setClass('input-group-addon'),
                    "≈ "
                ),
                input(set::name('hourPointValue')),
                span
                (
                    setClass('input-group-addon'),
                    $lang->workingHour
                )
            ),
        ) : null,
        formGroup
        (
            set::label($lang->story->field),
            checkList
            (
                set::name('fields[]'),
                set::inline(true),
                set::value(array_keys($lang->story->convertToTask->fieldList)),
                set::items($lang->story->convertToTask->fieldList)
            ),
            input
            (
                set::type('hidden'),
                set::name('storyIdList')
            )
        )
    )
);

$canBatchEdit        = common::hasPriv('story', 'batchEdit');
$canBatchClose       = common::hasPriv('story', 'batchClose') && $storyType != 'requirement';
$canBatchChangeStage = common::hasPriv('story', 'batchChangeStage') && $storyType != 'requirement';
$canBatchUnlink      = common::hasPriv('execution', 'batchUnlinkStory');
$canBatchToTask      = common::hasPriv('story', 'batchToTask', $checkObject) && $storyType != 'requirement';
$canBatchAssignTo    = common::hasPriv($storyType, 'batchAssignTo');
$canBatchAction      = $canBeChanged && in_array(true, array($canBatchEdit, $canBatchClose, $canBatchChangeStage, $canBatchUnlink, $canBatchToTask, $canBatchAssignTo));

$footToolbar = array();
if($canBatchAction)
{
    if($canBatchToTask)
    {
        menu
        (
            set::id('batchToTask'),
            set::class('dropdown-menu'),
            $canBatchToTask ? item(set(array(
                'text'  => $lang->story->batchToTask,
                'url'   => '#taskModal',
                'data-toggle' => 'modal'
            ))) : null,
        );
    }

    if($canBatchToTask || $canBatchEdit)
    {
        $editClass = $canBatchEdit ? 'batch-btn' : 'disabled';
        $footToolbar['items'][] = array(
            'type'  => 'btn-group',
            'items' => array(
                array('text' => $lang->edit, 'class' => "btn secondary size-sm {$editClass}", 'btnType' => 'primary', 'data-url' => createLink('story', 'batchEdit', "productID=0&executionID={$execution->id}&branch=0&storyType={$storyType}")),
                array('caret' => 'up', 'class' => 'btn btn-caret size-sm secondary', 'url' => '#batchToTask', 'data-toggle' => 'dropdown', 'data-placement' => 'top-start'),
            )
        );
    }

    if($canBatchAssignTo)
    {
        $assignedToItems = array();
        foreach ($users as $account => $name)
        {
            $assignedToItems[] = array(
                'text'     => $name,
                'class'    => 'batch-btn ajax-btn',
                'data-url' => createLink('story', 'batchAssignTo', "toryType={$storyType}&assignedTo={$account}")
            );
        }

        menu
        (
            set::id('navAssignedTo'),
            set::class('dropdown-menu'),
            set::items($assignedToItems)
        );
    }

    if($canBatchAssignTo)
    {
        $footToolbar['items'][] = array(
            'caret'       => 'up',
            'text'        => $lang->story->assignedTo,
            'class'       => 'btn btn-caret size-sm secondary',
            'url'         => '#navAssignedTo',
            'data-toggle' => 'dropdown'
        );
    }

    if($canBatchClose)
    {
        $footToolbar['items'][] = array(
            'text'  => $lang->close,
            'class' => 'btn batch-btn ajax-btn size-sm secondary',
            'url'   => $this->createLink('story', 'batchClose', "productID=0&executionID={$execution->id}")
        );
    }

    if($canBatchChangeStage)
    {
        $stageItems = array();
        foreach($lang->story->stageList as $stageID => $stage)
        {
            $stageItems[] = array(
                'text'     => $stage,
                'class'    => 'batch-btn ajax-btn',
                'data-url' => createLink('story', 'batchChangeStage', "stageID=$stageID")
            );
        }

        menu
        (
            set::id('navStage'),
            set::class('dropdown-menu'),
            set::items($stageItems)
        );
    }

    if($canBatchChangeStage)
    {
        $footToolbar['items'][] = array(
            'caret'          => 'up',
            'text'           => $lang->story->stageAB,
            'class'          => 'btn btn-caret size-sm secondary',
            'url'            => '#navStage',
            'data-toggle'    => 'dropdown',
            'data-placement' => 'top-start'
        );
    }

    if($canBatchUnlink)
    {
        $footToolbar['items'][] = array(
            'text'  => $lang->execution->unlinkStoryAB,
            'class' => 'btn batch-btn ajax-btn size-sm secondary',
            'url'   => $this->createLink('execution', 'batchUnlinkStory', "executionID={$execution->id}")
        );
    }
}

/* DataTable columns. */
$setting = $this->datatable->getSetting('execution');
$cols    = array();
foreach($setting as $col)
{
    if(!$execution->hasProduct and $col['id'] == 'branch') continue;
    if(!$execution->hasProduct and !$execution->multiple and $value['id'] == 'plan') continue;
    if(!$execution->hasProduct and !$execution->multiple and $storyType == 'requirement' and $value['id'] == 'stage') continue;

    $col['name'] = $col['id'];
    if($col['id'] == 'title') $col['link'] = sprintf($col['link'], createLink('execution', 'storyView', array('storyID' => '{id}', 'execution' => $executionID)));

    $cols[] = $col;
}


/* DataTable data. */
$data = array();
foreach($stories as $story)
{
    $story->taskCount = $storyTasks[$story->id];
    $story->actions   = $this->story->buildActionButtonList($story, 'browse');
    $story->plan      = isset($story->planTitle) ? $story->planTitle : $plans[$story->plan];

    $data[] = $story;

    if(!isset($story->children)) continue;

    /* Children. */
    foreach($story->children as $key => $child)
    {
        $child->taskCount = $storyTasks[$child->id];
        $child->actions   = $this->story->buildActionButtonList($child, 'browse', $execution);

        $data[] = $child;
    }
}

jsVar('cases', $storyCases);
jsVar('summary', $summary);
jsVar('checkedSummary', str_replace('%storyCommon%', $lang->SRCommon, $lang->product->checkedSummary));
dtable
(
    set::userMap($users),
    set::customCols(true),
    set::groupDivider(true),
    set::cols($cols),
    set::data($data),
    set::className('shadow rounded'),
    set::footToolbar($footToolbar),
    set::footPager(
        usePager(),
        set::recPerPage($pager->recPerPage),
        set::recTotal($pager->recTotal),
        set::linkCreator(helper::createLink('execution', 'story', "executionID={$execution->id}&storyType={$storyType}&orderBy=$orderBy&type={$type}&param={$param}&recTotal={$recTotal}&recPerPage={recPerPage}&page={page}"))
    ),
    set::checkInfo(jsRaw('function(checkedIDList){return window.setStatistics(this, checkedIDList);}')),
);

render();
