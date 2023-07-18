<?php
declare(strict_types=1);
/**
 * The edit view file of execution module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian<tianshujie@easycorp.ltd>
 * @package     execution
 * @link        https://www.zentao.net
 */
namespace zin;

jsVar('weekend', $config->execution->weekend);
jsVar('errorSameProducts', $lang->execution->errorSameProducts);
jsVar('errorSameBranches', $lang->execution->errorSameBranches);
jsVar('unmodifiableProducts',$unmodifiableProducts);
jsVar('unmodifiableBranches', $unmodifiableBranches);
jsVar('multiBranchProducts', $multiBranchProducts);
jsVar('linkedStoryIDList', $linkedStoryIDList);
jsVar('confirmSync', $lang->execution->confirmSync);
jsVar('unLinkProductTip', $lang->project->unLinkProductTip);
jsVar('typeTip', $lang->execution->typeTip);
jsVar('projectID', $execution->project);
jsVar('allProducts', $allProducts);
jsVar('branchGroups', $branchGroups);
jsVar('isWaterfall', isset($project) && ($project->model == 'waterfall' || $project->model == 'waterfallplus'));
jsVar('executionAttr', $execution->attribute);
jsVar('window.lastProjectID', $execution->project);

$projectBox = null;
if(isset($project))
{
    if($project->model == 'scrum')
    {
        $projectBox = formGroup
            (
                set::width('1/2'),
                set::name('project'),
                set::label($lang->execution->projectName),
                set::items($allProjects),
                set::value($execution->project),
                on::change('changeProject'),
            );
    }
    elseif($project->model == 'kanban')
    {
        $projectBox = formHidden('project', $execution->project);
    }
    elseif($project->model == 'agileplus')
    {
        $projectBox = formGroup
            (
                set::class('items-center'),
                set::label($lang->execution->method),
                zget($lang->execution->typeList, $execution->type)
            );
    }
    elseif($app->tab == 'project' && $project->model == 'waterfallplus')
    {
        $projectBox = formGroup
            (
                set::width('1/2'),
                set::name('parent'),
                set::label($lang->programplan->parent),
                set::items($parentStageList),
                set::value($execution->parent),
            );
    }
}

$typeBox = null;
if($project->model == 'waterfall' || $project->model == 'waterfallplus')
{
    $typeBox = formRow
        (
            formGroup
            (
                set::width($enableOptionalAttr ? '1/2' : '1/8'),
                set::label($lang->stage->type),
                set::class('items-center'),
                $enableOptionalAttr ? picker
                (
                    set::name('attribute'),
                    set::items($lang->stage->typeList),
                    set::value($execution->attribute),
                ) : span(zget($lang->stage->typeList, $execution->attribute)),
            ),
            formGroup
            (
                div
                (
                    setClass('pl-2 flex self-center'),
                    setStyle(array('color' => 'var(--form-label-color)')),
                    icon
                    (
                        'help',
                        set('data-toggle', 'tooltip'),
                        set('id', 'typeHover'),
                    )
                )
            )
        );
}
elseif($execution->type != 'kanban')
{
    $typeBox = formRow
        (
            formGroup
            (
                set::width('1/2'),
                set::label($lang->execution->type),
                picker
                (
                    set::id('lifetime'),
                    set::name('lifetime'),
                    set::items($lang->execution->lifeTimeList),
                    set::value($execution->lifetime),
                    on::change('showLifeTimeTips')
                )
            ),
            formGroup
            (
                set::width('1/2'),
                set::id('lifeTimeTips'),
                set::class('text-gray'),
                set::class($execution->lifetime != 'ops' ? 'hidden' : ''),
                span($lang->execution->typeDesc),
            ),
        );
}

$productsBox = null;
if($project->model != 'waterfall' && $project->model != 'waterfallplus')
{
    if(!empty($project) && !empty($project->hasProduct) && $linkedProducts)
    {
        $i = 0;
        foreach($linkedProducts as $product)
        {
            $hasBranch = $product->type != 'normal' && isset($branchGroups[$product->id]);
            $branches  = isset($branchGroups[$product->id]) ? $branchGroups[$product->id] : array();
            $plans     = isset($productPlans[$product->id]) ? $productPlans[$product->id] : array();
            $productsBox[] = formRow
                (
                    set::class('productsBox'),
                    formGroup
                    (
                        set::width($hasBranch ? '1/4' : '1/2'),
                        setClass('linkProduct'),
                        set::required(true),
                        $i == 0 ? set::label($lang->project->manageProducts) : set::label(''),
                        inputGroup
                        (
                            div
                            (
                                setClass('grow'),
                                picker
                                (
                                    set::id("products{$i}"),
                                    set::name("products[$i]"),
                                    set::value($product->id),
                                    set::items($allProducts),
                                    set::last($product->id),
                                    $hasBranch ? set::lastBranch(implode(',', $product->branches)) : null,
                                    set::disabled($execution->type == 'stage' && $project->stageBy == 'project'),
                                    on::change('productChange'),
                                    $execution->type == 'stage' && $project->stageBy == 'project' ? formHidden("products[$i]", $product->id) : null,
                                )
                            ),
                        )
                    ),
                    formGroup
                    (
                        set::width('1/4'),
                        setClass('ml-px'),
                        $hasBranch ? null : setClass('hidden'),
                        inputGroup
                        (
                            $lang->product->branchName['branch'],
                            picker
                            (
                                set::id("branch{$i}"),
                                set::name("branch[$i][]"),
                                set::items($branches),
                                set::value(implode(',', $product->branches)),
                                set::multiple(true),
                                on::change("branchChange")
                            )
                        ),
                    ),
                    formGroup
                    (
                        set::width('1/2'),
                        set::label($lang->project->associatePlan),
                        set::class('planBox'),
                        inputGroup
                        (
                            set::id("plan{$i}"),
                            picker
                            (
                                set::name("plans[$product->id][]"),
                                set::items($plans),
                                set::value(implode(',', $product->plans)),
                                set::multiple(true)
                            )
                        ),
                        $isStage && $project->stageBy == 'project' ? null : div
                        (
                            setClass('pl-2 flex self-center text-gray line-btn'),
                            btn
                            (
                                setClass('btn btn-link text-gray addLine'),
                                on::click('addNewLine'),
                                icon('plus')
                            ),
                            btn
                            (
                                setClass('btn btn-link text-gray removeLine'),
                                setClass($i == 0 ? 'hidden' : ''),
                                icon('trash'),
                                on::click('removeLine'),
                            ),
                        )
                    ),
                );

            $i ++;
        }
    }
    elseif(!empty($project) && empty($project->hasProduct))
    {
        $planProductID = current(array_keys($linkedProducts));
        $productsBox[] = formRow(
            formGroup
            (
                set::width('1/2'),
                set::label($lang->execution->linkPlan),
                set('id', 'plansBox'),
                set::class('planBox'),
                picker
                (
                    set::name("plans[{$planProductID}][]"),
                    set::items(isset($productPlans[$planProductID]) ? $productPlans[$planProductID] : array()),
                    set::multiple(true),
                    formHidden('products[]', $planProductID),
                    formHidden('branch[0][0]', 0),
                )
            ),
        );
    }
    else
    {
        $productsBox [] = formRow
            (
                set::class('productsBox'),
                formGroup
                (
                    set::width('1/2'),
                    setClass('linkProduct'),
                    set::required(true),
                    set::label($lang->project->manageProducts),
                    picker
                    (
                        set::id('products0'),
                        set::name('products[0]'),
                        set::items($allProducts),
                        on::change('productChange')
                    )
                ),
                formGroup
                (
                    set::width('1/4'),
                    setClass('hidden ml-px'),
                    inputGroup
                    (
                        $lang->product->branchName['branch'],
                        picker
                        (
                            set::id('branch0'),
                            set::name('branch[0][]'),
                            set::control('picker'),
                            set::multiple(true),
                            on::change('branchChange')
                        )
                    ),
                ),
                formGroup
                (
                    set::width('1/2'),
                    set::label($lang->project->associatePlan),
                    set::class('planBox'),
                    inputGroup
                    (
                        set::id("plan0"),
                        picker
                        (
                            set::name('plans[0][]'),
                            set::items(array()),
                            set::multiple(true)
                        )
                    ),
                    $isStage && $project->stageBy == 'product' ? null : div
                    (
                        setClass('pl-2 flex self-center line-btn'),
                        btn
                        (
                            setClass('btn btn-link text-gray addLine'),
                            on::click('addNewLine'),
                            icon('plus')
                        ),
                        btn
                        (
                            setClass('btn btn-link text-gray removeLine'),
                            setClass('hidden'),
                            icon('trash'),
                            on::click('removeLine'),
                        ),
                    ),
                ),
            );
    }
}
else
{
    if(!empty($project) && !empty($project->hasProduct))
    {
        $hasBranch = $product->type != 'normal' and isset($branchGroups[$product->id]);
        $branches  = isset($branchGroups[$product->id]) ? $branchGroups[$product->id] : array();
        $plans     = isset($productPlans[$product->id]) ? $productPlans[$product->id] : array();
        $productsBox[] = formRow
            (
                set::class('productsBox'),
                formGroup
                (
                    set::width($hasBranch ? '1/4' : '1/2'),
                    setClass('linkProduct'),
                    set::required(true),
                    $i == 0 ? set::label($lang->project->manageProducts) : set::label(''),
                    inputGroup
                    (
                        div
                        (
                            setClass('grow'),
                            picker
                            (
                                set::id("products{$i}"),
                                set::name("products[$i]"),
                                set::value($product->id),
                                set::items($allProducts),
                                set::last($product->id),
                                $hasBranch && $product->branches ? set::lastBranch(implode(',', $product->branches)) : null,
                                set::disabled($project->model == 'waterfall' || $project->model == 'waterfallplus'),
                                on::change('productChange'),
                                $project->model == 'waterfall' || $project->model == 'waterfallplus' ? formHidden("products[$i]", $product->id) : null,
                            )
                        ),
                    )
                ),
                formGroup
                (
                    set::width('1/4'),
                    setClass('ml-px'),
                    $hasBranch ? null : setClass('hidden'),
                    inputGroup
                    (
                        $lang->product->branchName['branch'],
                        picker
                        (
                            set::id("branch{$i}"),
                            set::name("branch[$i][]"),
                            set::items($branches),
                            set::value(isset($product->branches) ? implode(',', $product->branches) : ''),
                            set::disabled($project->model == 'waterfall' || $project->model == 'waterfallplus'),
                            set::multiple(true),
                            on::change('branchChange')
                        )
                    ),
                ),
                formGroup
                (
                    set::width('1/2'),
                    set::label($lang->project->associatePlan),
                    set::class('planBox'),
                    inputGroup
                    (
                        set::id("plan{$i}"),
                        picker
                        (
                            set::name("plans[$product->id][]"),
                            set::items($plans),
                            set::value(isset($product->plans) ? $product->plans : ''),
                            set::multiple(true)
                        )
                    ),
                ),
            );

        $i ++;
    }
    $productsBox[] = formHidden('products[]', key($linkedProducts));
    $productsBox[] = formHidden('branch', json_encode(array_values($linkedBranches)));
}

if(helper::isAjaxRequest('modal')) modalHeader(set::title($lang->execution->edit));
formPanel
(
    set::class('editPanel'),
    !helper::isAjaxRequest('modal') ? modalHeader(set::title($lang->execution->edit)) : null,
    $projectBox,
    formGroup
    (
        set::width('1/2'),
        set::name('name'),
        set::label($lang->execution->name),
        set::value($execution->name),
    ),
    isset($config->setCode) && $config->setCode == 1 ? formGroup
    (
        set::width('1/2'),
        set::name('code'),
        set::label($lang->execution->code),
        set::value($execution->code),
    ) : null,
    formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::label($lang->execution->dateRange),
            set::required(true),
            inputGroup
            (
                datePicker
                (
                    set::name('begin'),
                    set::type('date'),
                    set('id', 'begin'),
                    set::value($execution->begin),
                    set::placeholder($lang->execution->begin),
                    on::change('computeWorkDays')
                ),
                $lang->project->to,
                datePicker
                (
                    set::name('end'),
                    set::type('date'),
                    set('id', 'end'),
                    set::value(($execution->end),
                    set::placeholder($lang->execution->end),
                    on::change('computeWorkDays')
                    ),
                )
            ),
        ),
        formGroup
        (
            radioList
            (
                set::name('delta'),
                set::inline(true),
                set::items($lang->execution->endList),
                set::value((strtotime($execution->end) - strtotime($execution->begin)) / 3600 / 24 + 1),
                on::change('computeEndDate'),
            )
        ),
    ),
    formGroup
    (
        set::label($lang->execution->days),
        set::width('1/2'),
        inputGroup
        (
            setClass('has-suffix'),
            input
            (
                set::name('days'),
                set::value($execution->days),
            ),
            div
            (
                setClass('input-control-suffix z-50'),
                $lang->execution->day
            )
        )
    ),
    $typeBox,
    $execution->type == 'stage' && isset($config->setPercent) && $config->setPercent == 1 ? formGroup
    (
        set::width('1/2'),
        set::name('percent'),
        set::label($lang->stage->percent),
        set::value($execution->percent),
        set::required(true),
    ) : null,
    formGroup
    (
        set::width('1/2'),
        set::label($lang->execution->status),
        set::name('status'),
        set::items($lang->execution->statusList),
        set::value($execution->status),
    ),
    $productsBox,
    formRow
    (
        setClass('border-b border-b-1'),
        div
        (
            setClass('bg-lighter font-black px-3 py-1'),
            $lang->execution->teamSetting
        ),
    ),
    formGroup
    (
        set::width('1/2'),
        set::name('team'),
        set::label($lang->execution->teamname),
        set::value($execution->team),
    ),
    formRow
    (
        formGroup
        (
            set::width('1/4'),
            set::label($lang->execution->PM),
            picker
            (
                set::name('PM'),
                set::items($pmUsers),
                set::value($execution->PM),
            )
        ),
        formGroup
        (
            set::width('1/4'),
            set::label($lang->execution->PO),
            picker
            (
                set::name('PO'),
                set::items($poUsers),
                set::value($execution->PO),
            )
        ),
        formGroup
        (
            set::width('1/4'),
            set::label($lang->execution->QD),
            picker
            (
                set::name('QD'),
                set::items($qdUsers),
                set::value($execution->QD),
            )
        ),
        formGroup
        (
            set::width('1/4'),
            set::label($lang->execution->RD),
            picker
            (
                set::name('RD'),
                set::items($rdUsers),
                set::value($execution->RD),
            )
        ),
    ),
    formGroup
    (
        set::label($lang->execution->team),
        picker
        (
            set::name('teamMembers[]'),
            set::items($users),
            set::value(array_keys($teamMembers)),
            set::multiple(true),
        )
    ),
    h::hr(),
    formGroup
    (
        set::label($lang->execution->desc),
        editor
        (
            set::name('desc'),
            html($execution->desc),
        )
    ),
    formRow
    (
        set::id('aclList'),
        formGroup
        (
            set::width('1/2'),
            set::name('acl'),
            set::label($lang->execution->acl),
            set::control('radioList'),
            set::items($lang->execution->aclList),
            set::value($execution->acl),
            set::disabled($execution->grade == 2),
            on::change('setWhite(this.value)'),
        )
    ),
    formGroup
    (
        set::label($lang->whitelist),
        set::id('whitelistBox'),
        set::class($execution->acl == 'open' ? 'hidden' : ''),
        picker
        (
            set::name('whitelist'),
            set::items($users),
            set::multiple(true),
        )
    ),
);

/* ====== Render page ====== */
render();
