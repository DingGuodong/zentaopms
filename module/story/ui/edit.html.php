<?php
declare(strict_types=1);
/**
 * The edit file of story module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yidong Wang<Yidong@easycorp.ltd>
 * @package     story
 * @link        http://www.zentao.net
 */
namespace zin;

$canEditContent = str_contains(',draft,changing,', ",{$story->status},");
$forceReview    = $this->story->checkForceReview();
$assignedToList = $story->status == 'closed' ? $users + array('closed' => 'Closed') : $users;

$planCount    = !empty($story->planTitle) ? count($story->planTitle) : 0;
$multiplePlan = ($this->session->currentProductType != 'normal' && empty($story->branch) && $planCount > 1);

$minStage    = $story->stage;
$stageList   = implode(',', array_keys($this->lang->story->stageList));
$minStagePos = strpos($stageList, $minStage);
if($story->stages and $branchTagOption)
{
    foreach($story->stages as $branch => $stage)
    {
        $position = strpos(",{$stageList},", ",{$stage},");
        if($position !== false and $position > $minStagePos)
        {
            $minStage    = $stage;
            $minStagePos = $position;
        }
    }
}

jsVar('storyType', $story->type);
jsVar('storyID', $story->id);
jsVar('storyStatus', $story->status);
jsVar('lastReviewer', explode(',', $lastReviewer));
jsVar('reviewers', $reviewers);
jsVar('reviewerNotEmpty', $lang->story->notice->reviewerNotEmpty);
jsVar('oldProductID', $story->product);
jsVar('twins', $story->twins);
jsVar('relievedTwinsTip', $lang->story->relievedTwinsTip);
jsVar('parentStory', !empty($story->children));
jsVar('moveChildrenTips', $lang->story->moveChildrenTips);
jsVar('executionID', isset($objectID) ? $objectID : 0);
jsVar('langTreeManage', $lang->tree->manage);

detailHeader
(
    to::prefix($lang->story->edit),
    to::title
    (
        entityLabel
        (
            set::level(1),
            set::entityID($story->id),
            set::reverse(true),
            $story->title
        )
    ),
);

detailBody
(
    set::id('dataform'),
    set::isForm(true),
    $canEditContent ? set::actions(array
    (
        array('tag' => 'button', 'class' => 'primary', 'id' => 'saveButton', 'data-on' => 'click', 'data-call' => 'customSubmit', 'data-params' => 'event', 'text' => $lang->save),
        array('tag' => 'button', 'class' => 'secondary', 'id' => 'saveDraftButton', 'data-on' => 'click', 'data-call' => 'customSubmit', 'data-params' => 'event', 'text' => $story->status == 'changing' ? $lang->story->doNotSubmit : $lang->story->saveDraft),
        isInModal() ? null : array('text' => $lang->goback, 'back' => 'APP'),
    )) : null,
    sectionList
    (
        section
        (
            set::title($lang->story->title),
            formGroup
            (
                set::name('title'),
                set::value($story->title),
                set::disabled(!$canEditContent)
            )
        ),
        $canEditContent ? section
        (
            set::title($lang->story->reviewers),
            formGroup
            (
                row
                (
                    setClass('reviewerRow'),
                    span
                    (
                        setClass('reviewerBox'),
                        picker
                        (
                            set::id('reviewer'),
                            set::name('reviewer[]'),
                            set::items($hiddenProduct ? $teamUsers : $productReviewers),
                            set::value($reviewers),
                            set::multiple(true),
                            on::change('changeReviewer'),
                        ),
                    ),
                    $forceReview ? null : checkbox
                    (
                        set::id('needNotReview'),
                        set::name('needNotReview'),
                        set::checked(empty($reviewers)),
                        set::value(1),
                        set::text($lang->story->needNotReview),
                        on::change('changeNeedNotReview(e.target)'),
                    ),
                )
            )
        ) : null,
        section
        (
            set::title($lang->story->legendSpec),
            $canEditContent ? formGroup(editor(set::name('spec'), htmlSpecialString($story->spec))) : set::content($story->spec),
            $canEditContent ? null : set::useHtml(true)
        ),
        section
        (
            set::title($lang->story->verify),
            $canEditContent ? formGroup(editor(set::name('verify'), htmlSpecialString($story->verify))) : set::content($story->verify),
            $canEditContent ? null : set::useHtml(true)
        ),
        empty($twins) ? null : section
        (
            set::title($lang->story->changeSyncTip),
            to::suffix(set::title($lang->story->syncTip), icon('help')),
            h::ul
            (
                array_values(array_map(function($twin) use($story, $branches)
                {
                    global $lang;
                    $branch     = isset($branches[$twin->branch]) ? $branches[$twin->branch] : '';
                    $stage      = $lang->story->stageList[$twin->stage];
                    $labelClass = $story->branch == $twin->branch ? 'primary' : '';

                    return h::li
                    (
                        setClass('twins'),
                        $branch ? label(setClass($labelClass . ' circle branch size-sm'), set::title($branch), $branch) : null,
                        label(setClass('circle size-sm'), $twin->id),
                        common::hasPriv('story', 'view') ? a(set::href($this->createLink('story', 'view', "id={$twin->id}")), setClass('title'), set::title($twin->title), set('data-toggle', 'modal'), $twin->title) : span(setClass('title'), $twin->title),
                        label(setClass('size-sm'), set::title($stage), $stage),
                        common::hasPriv('story', 'relieved') ? a(set::title($lang->story->relievedTwins), setClass("relievedTwins unlink size-xs"), on::click('unlinkTwins'), set('data-id', $twin->id), icon('unlink')) : null,
                    );
                }, $twins))
            ),
        ),
        $canEditContent || $story->files ? section
        (
            set::title($lang->story->legendAttach),
            $canEditContent ? upload() : null,
            $story->files ? fileList
            (
                set::files($story->files),
                set::fieldset(false),
                set::object($story),
            ) : null,
        ) : null,
        section
        (
            set::title($lang->story->comment),
            formGroup
            (
                editor(set::name('comment'))
            )
        )
    ),
    history(),
    detailSide
    (
        tableData
        (
            set::title($lang->story->legendBasicInfo),
            $story->parent <= 0 ? item
            (
                set::trClass($hiddenProduct ? 'hidden' : ''),
                set::name($lang->story->product),
                row
                (
                    picker
                    (
                        set::id('product'),
                        set::name('product'),
                        set::items($products),
                        set::value($story->product),
                        on::change('loadProduct'),
                    ),
                    span
                    (
                        setClass('branchIdBox'),
                        setClass($product->type == 'normal' ? 'hidden' : ''),
                        $product->type != 'normal' ? picker
                        (
                            set::id('branch'),
                            set::name('branch'),
                            set::items($branchTagOption),
                            set::value($story->branch),
                            on::change('loadBranch'),
                        ) : null
                    )
                )
            ) : null,
            $story->parent > 0 && $product->type != 'normal' ? item
            (
                set::name(sprintf($lang->product->branch, $lang->product->branchName[$product->type])),
                picker(setID('branch'), set::name('branch'), set::items($branchTagOption), set::value($story->branch))
            ) : null,
            item
            (
                set::name($lang->story->module),
                inputGroup
                (
                    span
                    (
                        set('id', 'moduleIdBox'),
                        picker
                        (
                            set::name('module'),
                            set::items($moduleOptionMenu),
                            set::value($story->module)
                        ),
                    ),
                    count($moduleOptionMenu) == 1 ? btn(set::url($this->createLink('tree', 'browse', "rootID={$story->product}&view=story&currentModuleID=0&branch={$story->branch}")), set('data-toggle', 'modal'), $lang->tree->manage) : null,
                    count($moduleOptionMenu) == 1 ? btn(set('data-on', 'click'), set('data-call', 'loadProductModules'), set('data-params', $story->product), setClass('refresh'), icon('refresh')) : null,
                )
            ),
            $story->parent >= 0 && $story->type == 'story' ? item
            (
                set::trClass($hiddenParent ? 'hidden' : null),
                set::name($lang->story->parent),
                picker(setID('parent'), set::name('parent'), set::items(array_filter($stories)), set::value($story->parent)),
            ) : null,
            item
            (
                set::trClass($hiddenPlan ? 'hidden' : null),
                set::name($lang->story->plan),
                inputGroup
                (
                    span
                    (
                        set::id('planIdBox'),
                        picker(setID('plan'), set::name($multiplePlan ? 'plan[]' : 'plan'), set::items($plans), set::value($story->plan), set::multiple($multiplePlan)),
                    ),
                    empty($plans) ? btn(set::url($this->createLink('productplan', 'create', "productID={$story->product}&branch={$story->branch}")), set('data-toggle', 'modal'), icon('plus')) : null,
                    empty($plans) ? btn(set('data-on', 'click'), set('data-call', 'loadProductPlans'), set('data-params', $story->product), setClass('refresh'), icon('refresh')) : null,
                )
            ),
            item
            (
                set::name($lang->story->source),
                picker(setID('source'), set::name('source'), set::items($lang->story->sourceList), set::value($story->source), on::change('toggleFeedback(e.target)'))
            ),
            item
            (
                set::name($lang->story->sourceNote),
                input(set::name('sourceNote'), set::value($story->sourceNote))
            ),
            item
            (
                set::name($lang->story->status),
                span(setClass("status-{$story->status}"), $this->processStatus('story', $story)),
                formHidden('status', $story->status),
            ),
            $story->type == 'story' ? item
            (
                set::name($lang->story->stage),
                picker(setID('stage'), set::name('stage'), set::items($lang->story->stageList), set::value($minStage))
            ) : null,
            item
            (
                set::name($lang->story->category),
                picker(setID('category'), set::name('category'), set::items($lang->story->categoryList), set::value($story->category))
            ),
            item
            (
                set::name($lang->story->pri),
                priPicker(set::name('pri'), set::items($lang->story->priList), set::value($story->pri))
            ),
            item
            (
                set::name($lang->story->estimate),
                $story->parent >= 0 ? input(set::name('estimate'), set::value($story->estimate)) : $story->estimate,
            ),
            item
            (
                set::trClass('feedbackBox'),
                set::trClass(in_array($story->source, $config->story->feedbackSource) ? '' : 'hidden'),
                set::name($lang->story->feedbackBy),
                input(set::name('feedbackBy'), set::value($story->feedbackBy)),
            ),
            item
            (
                set::trClass('feedbackBox'),
                set::trClass(in_array($story->source, $config->story->feedbackSource) ? '' : 'hidden'),
                set::name($lang->story->notifyEmail),
                input(set::name('notifyEmail'), set::value($story->notifyEmail)),
            ),
            item
            (
                set::name($lang->story->keywords),
                input(set::name('keywords'), set::value($story->keywords)),
            ),
            item
            (
                set::name($lang->story->mailto),
                inputGroup
                (
                    picker(setID('mailto'), set::name('mailto[]'), set::items($users), set::value(empty($story->mailto) ? '' : $story->mailto), set::multiple(true)),
                    $contactList ? picker
                    (
                        setID('contactListMenu'),
                        set::name('contactListMenu'),
                        set::items($contactList),
                        set::value()
                    ) : btn
                    (
                        set('url', createLink('my', 'managecontacts', 'listID=0&mode=new')),
                        set('title', $lang->user->contacts->manage),
                        set('data-toggle', 'modal'),
                        icon('cog'),
                    ),
                    $contactList ? null : btn
                    (
                        set('id', 'refreshMailto'),
                        set('class', 'text-black'),
                        icon('refresh')
                    )
                )
            ),
        ),
        tableData
        (
            set::title($lang->story->legendLifeTime),
            item
            (
                set::name($lang->story->openedBy),
                zget($users, $story->openedBy)
            ),
            item
            (
                set::name($lang->story->assignedTo),
                picker
                (
                    setID('assignedTo'),
                    set::name('assignedTo'),
                    set::items($hiddenProduct ? $teamUsers : $assignedToList),
                    set::value($story->assignedTo)
                )
            ),
            $story->status == 'reviewing' ? item
            (
                set::name($lang->story->reviewers),
                picker
                (
                    set::id('reviewer'),
                    set::name('reviewer[]'),
                    set::items($hiddenProduct ? $teamUsers : $productReviewers),
                    set::value($reviewers),
                    set::multiple(true),
                    on::change('changeReviewer'),
                )
            ) : null,
            $story->status == 'closed' ? item
            (
                set::name($lang->story->closedBy),
                picker(setID('closedBy'), set::name('closedBy'), set::items($users), set::value($story->closedBy))
            ) : null,
            $story->status == 'closed' ? item
            (
                set::name($lang->story->closedReason),
                picker(setID('closedReason'), set::name('closedReason'), set::items($lang->story->reasonList), set::value($story->closedReason), on::change('setStory'))
            ) : null,
        ),
        tableData
        (
            set::title($lang->story->legendMisc),
            $story->status == 'closed' ? item
            (
                set::trClass('duplicateStoryBox'),
                set::name($lang->story->duplicateStory),
                picker(setID('duplicateStory'), set::name('duplicateStory'), set::items($productStories), set::value($story->duplicateStory), set::placeholder($lang->bug->placeholder->duplicate))
            ) : null,
            item
            (
                set::name($story->type == 'story' ? $lang->requirement->linkStory : $lang->story->linkStory),
                (common::hasPriv('story', 'linkStories') && $story->type == 'story') ? btn(setClass('secondary'), set::id('linkStoriesLink'), set('data-toggle', 'modal'), set('data-size', 'lg'), on::click('linkStories'), $lang->story->linkStoriesAB) : null,
                (common::hasPriv('requirement', 'linkRequirements') && $story->type == 'requirement') ? btn(setClass('secondary'), set::id('linkStoriesLink'), set('data-toggle', 'modal'), set('data-size', 'lg'), on::click('linkStories'), $lang->story->linkRequirementsAB) : null,
            ),
            item
            (
                set::name(' '),
                !empty($story->linkStoryTitles) ? h::ul
                (
                    setID('linkedStories'),
                    array_values(array_map(function($linkStoryID, $linkStoryTitle) use($story)
                    {
                        $linkStoryField = $story->type == 'story' ? 'linkStories' : 'linkRequirements';
                        return h::li
                        (
                            set::title($linkStoryTitle),
                            checkbox(set::name($linkStoryField . '[]'), set::rootClass('inline'), set::value($linkStoryID), set::checked(true)),
                            label(setClass('circle size-sm'), $linkStoryID),
                            span(setClass('linkStoryTitle'), $linkStoryTitle)
                        );
                    }, array_keys($story->linkStoryTitles), array_values($story->linkStoryTitles)))
                ) : null,
                div(set::id('linkStoriesBox')),
            )
        ),
    )
);

render();
