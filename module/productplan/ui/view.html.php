<?php
declare(strict_types=1);
/**
 * The view view file of productplan module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Wang Yidong <yidong@easycorp.ltd>
 * @package     productplan
 * @link        https://www.zentao.net
 */
namespace zin;

$unlinkURL['story'] = helper::createLink('productplan', 'unlinkStory', "storyID=%s&planID={$plan->id}");
$unlinkURL['bug']   = helper::createLink('productplan', 'unlinkBug',   "bugID=%s&planID={$plan->id}");

$confirmLang['story']    = $lang->productplan->confirmUnlinkStory;
$confirmLang['bug']      = $lang->productplan->confirmUnlinkBug;
$confirmLang['start']    = $lang->productplan->confirmStart;
$confirmLang['finish']   = $lang->productplan->confirmFinish;
$confirmLang['activate'] = $lang->productplan->confirmActivate;
$confirmLang['delete']   = $lang->productplan->confirmDelete;

jsVar('initLink',    $link);
jsVar('type',        $type);
jsVar('orderBy',     $orderBy);
jsVar('planID',      $plan->id);
jsVar('confirmLang', $confirmLang);
jsVar('unlinkURL',   $unlinkURL);
jsVar('childrenAB',  $lang->story->childrenAB);

$decodeParam = helper::safe64Decode($param);
$bugCols     = array();
$storyCols   = array();
foreach($config->productplan->defaultFields['story'] as $field) $storyCols[$field] = zget($config->story->dtable->fieldList, $field, array());
foreach($config->productplan->defaultFields['bug'] as $field)   $bugCols[$field]   = zget($config->bug->dtable->fieldList, $field, array());

$storyCols['title']['link']         = $this->createLink('story', 'view', "storyID={id}");
$storyCols['title']['nestedToggle'] = false;
$storyCols['assignedTo']['type']    = 'user';
$storyCols['actions']['width']      = 50;
$bugCols['assignedTo']['type']      = 'user';
$storyCols['module']['type']        = 'text';
$storyCols['module']['map']         = $modulePairs;
$storyCols['actions']['list']       = $config->productplan->actionList;
$bugCols['actions']['list']         = $config->productplan->actionList;
$storyCols['actions']['menu']       = array('unlinkStory');
$bugCols['actions']['menu']         = array('unlinkBug');
$storyCols['actions']['minWidth']   = 60;
$bugCols['actions']['minWidth']     = 60;

$canBeChanged              = common::canBeChanged('plan', $plan);
$canBatchUnlinkStory       = common::hasPriv('productPlan', 'batchUnlinkStory');
$canBatchCloseStory        = common::hasPriv('story', 'batchClose');
$canBatchEditStory         = common::hasPriv('story', 'batchEdit');
$canBatchReviewStory       = common::hasPriv('story', 'batchReview');
$canBatchChangeBranchStory = common::hasPriv('story', 'batchChangeBranch');
$canBatchChangeModuleStory = common::hasPriv('story', 'batchChangeModule');
$canBatchChangePlanStory   = common::hasPriv('story', 'batchChangePlan');
$canBatchChangeStageStory  = common::hasPriv('story', 'batchChangeStage');
$canBatchAssignToStory     = common::hasPriv('story', 'batchAssignTo');
$canBatchUnlinkBug         = common::hasPriv('productPlan', 'batchUnlinkBug');
$canBatchEditBug           = common::hasPriv('bug', 'batchEdit');
$canBatchChangePlanBug     = common::hasPriv('bug', 'batchChangePlan');

$canBatchActionStory = ($canBeChanged && ($canBatchUnlinkStory || $canBatchCloseStory || $canBatchEditStory || $canBatchReviewStory || $canBatchChangeBranchStory || $canBatchChangeModuleStory || $canBatchChangePlanStory || $canBatchChangeStageStory || $canBatchAssignToStory));
$canBatchActionBug   = ($canBeChanged && ($canBatchUnlinkBug || $canBatchEditBug || $canBatchChangePlanBug));

unset($lang->story->reviewResultList[''], $lang->story->reviewResultList['revert']);
unset($lang->story->reasonList[''], $lang->story->reasonList['subdivided'], $lang->story->reasonList['duplicate']);
unset($plans[''], $lang->story->stageList[''], $users['']);

foreach($lang->story->reviewResultList as $key => $result) $reviewResultItems[$key] = array('text' => $result,     'class' => 'batch-btn', 'data-type' => 'story', 'data-url' => $this->createLink('story', 'batchReview', "result=$key"));
foreach($lang->story->reasonList as $key => $reason)       $reviewRejectItems[]     = array('text' => $reason,     'class' => 'batch-btn', 'data-type' => 'story', 'data-url' => $this->createLink('story', 'batchReview', "result=reject&reason=$key"));
foreach($branchTagOption as $branchID => $branchName)      $branchItems[]           = array('text' => $branchName, 'class' => 'batch-btn', 'data-type' => 'story', 'data-url' => $this->createLink('story', 'batchChangeBranch', "branchID=$branchID"));
foreach($modules as $moduleID => $moduleName)              $moduleItems[]           = array('text' => $moduleName, 'class' => 'batch-btn', 'data-type' => 'story', 'data-url' => $this->createLink('story', 'batchChangeModule', "moduleID=$moduleID"));
foreach($plans as $planID => $planName)                    $planItems[]             = array('text' => $planName,   'class' => 'batch-btn', 'data-type' => 'story', 'data-url' => $this->createLink('story', 'batchChangePlan', "planID=$planID&oldPlanID={$plan->id}"));
foreach($lang->story->stageList as $key => $stageName)     $stageItems[]            = array('text' => $stageName,  'class' => 'batch-btn', 'data-type' => 'story', 'data-url' => $this->createLink('story', 'batchChangeStage', "stage=$key"));
foreach($users as $account => $realname)
{
    if($account == 'closed') continue;
    $assignItems[] = array('text' => $realname, 'class' => 'batch-btn', 'data-type' => 'story', 'data-url' => $this->createLink('story', 'batchAssignTo', "productID=$plan->product"), 'data-account' => $account);
}

if(isset($reviewResultItems['reject'])) $reviewResultItems['reject'] = array('class' => 'not-hide-menu', 'text' => $lang->story->reviewResultList['reject'], 'items' => $reviewRejectItems);
$reviewResultItems = array_values($reviewResultItems);

$navStoryActionItems = array();
if($canBatchCloseStory)  $navStoryActionItems[] = array('text' => $lang->close, 'class' => 'batch-btn', 'data-type' => 'story', 'data-page' => 'batch', 'data-url' => helper::createLink('story', 'batchClose', "productID={$plan->product}"));
if($canBatchEditStory)   $navStoryActionItems[] = array('text' => $lang->edit, 'class' => 'batch-btn',  'data-type' => 'story', 'data-page' => 'batch', 'data-url' => helper::createLink('story', 'batchEdit', "productID=$plan->product&projectID=$projectID&branch=$branch"));
if($canBatchReviewStory) $navStoryActionItems[] = array('class' => 'not-hide-menu', 'text' => $lang->story->review, 'items' => $reviewResultItems);
if($canBatchChangeBranchStory && $product->type != 'normal') $navStoryActionItems[] = array('class' => 'not-hide-menu', 'text' => $lang->product->branchName[$product->type], 'items' => $branchItems);
if($canBatchChangeModuleStory) $navStoryActionItems[] = array('class' => 'not-hide-menu', 'text' => $lang->story->moduleAB, 'items' => $moduleItems);
if($canBatchChangePlanStory)   $navStoryActionItems[] = array('class' => 'not-hide-menu', 'text' => $lang->story->planAB, 'items' => $planItems);
if($canBatchChangeStageStory)  $navStoryActionItems[] = array('class' => 'not-hide-menu', 'text' => $lang->story->stageAB, 'items' => $stageItems);
if($canBatchAssignToStory)     $navStoryActionItems[] = array('class' => 'not-hide-menu', 'text' => $lang->story->assignedTo, 'items' => $assignItems);

$storyFootToolbar = array();
if($canBatchActionStory)
{
    $storyFootToolbar = array('items' => array
    (
        array('type' => 'btn-group', 'items' => array
        (
            array('text' => $lang->productplan->unlinkStoryAB, 'className' => 'batch-btn size-sm secondary', 'disabled' => ($canBatchUnlinkStory ? '' : 'disabled'), 'btnType' => 'primary', 'data-type' => 'story', 'data-url' => helper::createLink('productplan', 'batchUnlinkStory', "planID=$plan->id&orderBy=$orderBy")),
            array('caret' => 'up', 'className' => 'size-sm secondary', 'btnType' => 'primary', 'data-toggle' => 'dropdown', 'data-placement' => 'top-start', 'items' => $navStoryActionItems),
        )),
    ));
}


$planItems = array();
foreach($plans as $planID => $planName) $planItems[] = array('text' => $planName, 'class' => 'batch-btn', 'data-type' => 'bug', 'data-url' => $this->createLink('bug', 'batchChangePlan', "planID=$planID"));

$navBugActionItems = array();
if($canBatchEditBug)       $navBugActionItems[] = array('text' => $lang->edit, 'class' => 'batch-btn', 'data-type' => 'bug', 'data-page' => 'batch', 'data-url' => helper::createLink('bug', 'batchEdit', "productID=$plan->product&branch=$branch"));
if($canBatchChangePlanBug) $navBugActionItems[] = array('class' => 'not-hide-menu', 'text' => $lang->story->planAB, 'items' => $planItems);

$bugFootToolbar = array();
if($canBatchActionBug)
{
    $bugFootToolbar = array('items' => array
    (
        array('type' => 'btn-group', 'items' => array
        (
            $canBatchUnlinkBug ? array('text' => $lang->productplan->unlinkAB, 'className' => 'batch-btn size-sm secondary', 'btnType' => 'primary', 'data-type' => 'bug', 'data-url' => helper::createLink('productplan', 'batchUnlinkBug', "planID=$plan->id&orderBy=$orderBy")) : null,
            array('caret' => 'up', 'className' => 'size-sm secondary', 'btnType' => 'primary', 'data-toggle' => 'dropdown', 'data-placement' => 'top-start', 'items' => $navBugActionItems),
        )),
    ));
}

$planStories = initTableData($planStories, $storyCols, $this->productplan);
$planBugs    = initTableData($planBugs,    $bugCols,   $this->productplan);
foreach($planStories as $story) $story->estimate = $story->estimate . $config->hourUnit;

$createStoryLink      = common::hasPriv('story', 'create') ? $this->createLink('story', 'create', "productID=$plan->product&branch=$plan->branch&moduleID=0&storyID=0&projectID=$projectID&bugID=0&planID=$plan->id") : null;
$batchCreateStoryLink = common::hasPriv('story', 'batchCreate') ? $this->createLink('story', 'batchCreate', "productID=$plan->product&branch=$plan->branch&moduleID=0&story=0&project=$projectID&plan={$plan->id}") : null;

$branchNames = '';
if($product->type != 'normal')
{
    foreach(explode(',', (string)$branch) as $branchID) $branchNames .= "{$branchOption[$branchID]},";
}

$fnGetChildrenPlans = function($childrenPlans)
{
    $childrenPlanItems = array();
    foreach($childrenPlans as $childrenPlan)
    {
        $childrenPlanItems[] = a(set::href(inlink('view', "planID={$childrenPlan->id}")), "#{$childrenPlan->id} {$childrenPlan->title}");
        $childrenPlanItems[] = h::br();
    }

    array_pop($childrenPlanItems);
    return $childrenPlanItems;
};

detailHeader
(
    to::prefix
    (
        backBtn(set::icon('back'), set::type('secondary'), set::url($this->session->productPlanList), $lang->goback),
        div(setClass('nav-divider')),
        entityLabel(set(array('entityID' => $plan->id, 'level' => 1, 'text' => $plan->title))),
        span(setClass('label circle primary'), ($plan->begin == FUTURE_TIME || $plan->end == FUTURE_TIME) ? $lang->productplan->future : $plan->begin . '~' . $plan->end),
        $plan->deleted ? span(setClass('label danger'), $lang->product->deleted) : null,
    ),
    (!$plan->deleted && $actionMenus) ? to::suffix(btnGroup(set::items($actionMenus))) : null
);

detailBody
(
    sectionList
    (
        tabs
        (
            setClass('w-full'),
            tabPane
            (
                to::prefix(icon($lang->icons['story'])),
                set::key('stories'),
                set::title($lang->productplan->linkedStories),
                set::active($type == 'story'),
                div
                (
                    setClass('tabnActions'),
                    dropdown
                    (
                        btn(set::text($lang->story->create), set::target('_parent'), setClass('secondary' . (empty($createStoryLink) ? ' disabled' : '')), set::icon('plus'), set::caret(true), set::url($createStoryLink)),
                        set::items(array(array('text' => $lang->story->batchCreate, 'url' => $batchCreateStoryLink, 'class' => empty($batchCreateStoryLink) ? 'disabled' : ''))),
                        set::trigger('hover'),
                        set::placement('bottom-end'),
                    ),
                    !common::hasPriv('productplan', 'linkStory') ? null : btn(set::text($lang->productplan->linkStory), setClass('primary link'), set::icon('link'), set::onclick('showLink(this)'), set('data-type', 'story'), set('data-linkurl', inlink('linkStory', "planID={$plan->id}" . (($link == 'true' && $type == 'story') ? $decodeParam : "&browseType=&param=") . "&orderBy={$orderBy}"))),
                ),
                dtable
                (
                    set::id('storyDTable'),
                    set::userMap($users),
                    set::bordered(true),
                    set::cols($storyCols),
                    set::data(array_values($planStories)),
                    set::checkable($canBatchActionStory),
                    set::onRenderCell(jsRaw('window.renderStoryCell')),
                    set::footToolbar($storyFootToolbar),
                    set::sortLink(createLink('productplan', 'view', "planID={$plan->id}&type=story&orderBy={name}_{sortType}&link=false&param={$param}&recTotal={$storyPager->recTotal}&recPerPage={$storyPager->recPerPage}&page={$storyPager->pageID}")),
                    set::footer(array('checkbox', 'toolbar', array('html' => $summary, 'className' => "text-dark"), 'flex', 'pager')),
                    set::footPager
                    (
                        usePager('storyPager', '', array(
                            'recPerPage' => $storyPager->recPerPage,
                            'recTotal' => $storyPager->recTotal,
                            'linkCreator' => helper::createLink('productplan', 'view', "planID={$plan->id}&type=story&orderBy={$orderBy}&link=false&param={$param}&recTotal={$storyPager->recTotal}&recPerPage={recPerPage}&page={page}")
                        )),
                    ),
                )
            ),
            tabPane
            (
                to::prefix(icon(setClass('text-red'), $lang->icons['bug'])),
                set::key('bugs'),
                set::title($lang->productplan->linkedBugs),
                set::active($type == 'bug'),
                div
                (
                    setClass('tabnActions'),
                    !common::hasPriv('productplan', 'linkBug') ? null : btn(set::text($lang->productplan->linkBug), setClass('primary link'), set::icon('link'), set::onclick('showLink(this)'), set('data-type', 'bug'), set('data-linkurl', inlink('linkBug', "planID={$plan->id}" . (($link == 'true' && $type == 'bug') ? $decodeParam : "&browseType=&param=") . "&orderBy={$orderBy}"))),
                ),
                dtable
                (
                    set::id('bugDTable'),
                    set::userMap($users),
                    set::bordered(true),
                    set::cols($bugCols),
                    set::data(array_values($planBugs)),
                    set::checkable($canBatchActionBug),
                    set::footToolbar($bugFootToolbar),
                    set::sortLink(createLink('productplan', 'view', "planID={$plan->id}&type=bug&orderBy={name}_{sortType}&link=false&param={$param}&recTotal={$bugPager->recTotal}&recPerPage={$bugPager->recPerPage}&page={$bugPager->pageID}")),
                    set::footer(array('checkbox', 'toolbar', array('html' => sprintf($lang->productplan->bugSummary, count($planBugs)), 'className' => "text-dark"), 'flex', 'pager')),
                    set::footPager
                    (
                        usePager('bugPager', '', array(
                            'recPerPage' => $bugPager->recPerPage,
                            'recTotal' => $bugPager->recTotal,
                            'linkCreator' => helper::createLink('productplan', 'view', "planID={$plan->id}&type=bug&orderBy={$orderBy}&link=false&param={$param}&recTotal={$bugPager->recTotal}&recPerPage={recPerPage}&page={page}")
                        )),
                    ),
                )
            ),
            tabPane
            (
                to::prefix(icon(setClass('text-info'), 'info')),
                set::key('planInfo'),
                set::title($lang->productplan->view),
                set::active($type == 'planInfo'),
                tableData
                (
                    set::title($lang->productplan->basicInfo),
                    item(set::name($lang->productplan->title), $plan->title),
                    $plan->parent > 0 ? item(set::name($lang->productplan->parent), a(set::href(inlink('view', "planID={$parentPlan->id}")), "#{$parentPlan->id} {$parentPlan->title}")) : null,
                    $product->type != 'normal' ? item(set::name($lang->product->branch), $branchNames) : null,
                    item(set::name($lang->productplan->begin), $plan->begin == FUTURE_TIME ? $lang->productplan->future : $plan->begin),
                    item(set::name($lang->productplan->end), $plan->end == FUTURE_TIME ? $lang->productplan->future : $plan->end),
                    $plan->parent == '-1' ? item(set::name($lang->productplan->children), $fnGetChildrenPlans($childrenPlans)) : null,
                    item(set::name($lang->productplan->status), $lang->productplan->statusList[$plan->status]),
                    item(set::name($lang->productplan->desc), empty($plan->desc) ? $lang->noData : html(($plan->desc))),
                ),
                h::hr(setClass('mt-4')),
                history(
                    set::hasCommont(false),
                    set::commentBtn(false),
                    set::methodName('view_1'),
                ),
            ),
        )
    )
);

render();
