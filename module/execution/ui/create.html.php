<?php
declare(strict_types=1);
/**
 * The create view file of execution module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian<tianshujie@easycorp.ltd>
 * @package     execution
 * @link        https://www.zentao.net
 */
namespace zin;

jsVar('methodTip', $lang->execution->agileplusMethodTip);
jsVar('+projectID', $projectID);
jsVar('copyProjectID', $copyProjectID);
jsVar('weekend', $config->execution->weekend);
jsVar('errorSameProducts', $lang->execution->errorSameProducts);
jsVar('errorSameBranches', $lang->execution->errorSameBranches);
jsVar('isStage', $isStage);
jsVar('copyExecutionID', $copyExecutionID);
jsVar('multiBranchProducts', $multiBranchProducts);
jsVar('executionID', $executionID);

$methodBox = null;
if(!empty($project->model) && $project->model == 'agileplus')
{
    unset($lang->execution->typeList['stage'], $lang->execution->typeList['']);
    $methodBox = formRow(
        formGroup
        (
            set::width('1/2'),
            set::name('type'),
            set::label($lang->execution->method),
            set::items($lang->execution->typeList),
            set::value($type),
            on::change('setType'),
        ),
        formGroup
        (
            set::width('1/2'),
            div
            (
                setClass('pl-2 flex self-center'),
                setStyle(['color' => 'var(--form-label-color)']),
                icon
                (
                    'help',
                    set('data-toggle', 'tooltip'),
                    set('id', 'methodHover'),
                )
            )
        )
    );
}

$typeBox = null;
if((empty($project) || $project->model != 'kanban') && $type != 'kanban')
{
    $typeBox = formRow(
        formGroup
        (
            set::width('1/2'),
            set::label($showExecutionExec ? $lang->execution->execType : $lang->execution->type),
            picker
            (
                set::id($isStage ? 'attribute' : 'lifetime'),
                set::name($isStage ? 'attribute' : 'lifetime'),
                set::items($isStage ? $lang->stage->typeList : $lang->execution->lifeTimeList),
                !$isStage ? on::change('showLifeTimeTips') : null
            )
        ),
        formGroup
        (
            set::width('1/2'),
            set::id('lifeTimeTips'),
            set::class('text-gray hidden'),
            span($lang->execution->typeDesc),
        ),
    );
}

$productsBox = null;
if(isset($project->hasProduct) && !empty($project->hasProduct) && $products)
{
    $i = 0;
    foreach($products as $product)
    {
        $hasBranch = $product->type != 'normal' && isset($branchGroups[$product->id]);
        $branches  = isset($branchGroups[$product->id]) ? $branchGroups[$product->id] : array();
        if(!isset($linkedBranches)) $branchIdList = isset($product->branches) ? implode(',', $product->branches) : '';
        if(isset($linkedBranches))  $branchIdList = !empty($linkedBranches[$product->id]) ? $linkedBranches[$product->id] : '';
        if(empty($productID) || (!empty($productID) || $productID != $product->id))
        {
            $plans  = isset($productPlans[$product->id]) ? $productPlans[$product->id] : array();
            $planID = isset($product->plans) ? implode(',', $product->plans) : '';
        }
        else
        {
            $plans  = !empty($productPlan) ? $productPlan : array();
            $planID = isset($productPlan[$plan->id]) ? $plan->id : '';
        }

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
                            set::disabled($isStage && $project->stageBy == 'project'),
                            on::change('loadBranches'),
                            $isStage && $project->stageBy == 'project' ? formHidden("products[$i]", $product->id) : null,
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
                        set::value($branchIdList),
                        set::disabled($isStage && $project->stageBy == 'project'),
                        set::multiple(true),
                        on::change("loadPlans('#products{$i}', this)")
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
                        set::value($planID),
                        set::multiple(true)
                    )
                ),
                $isStage && $project->stageBy == 'project' ? null : div
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
elseif(!empty($project) && empty($project->hasProduct) && !in_array($project->model, array('waterfall', 'kanban', 'waterfallplus')))
{
    $planProductID = current(array_keys($allProducts));
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
                set::items(isset($productPlan) ? $productPlan : array()),
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
                on::change('loadBranches')
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
                    set::multiple(true),
                    on::change("loadPlans('#products0', this)")
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
                    set::items($productPlan),
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

formPanel
(
    set::class('createPanel'),
    to::heading(div
    (
        setClass('panel-title text-lg'),
        $showExecutionExec ? $lang->execution->createExec : $lang->execution->create,
    )),
    to::headingActions
    (
        btn
        (
            setClass('primary-pale'),
            set::icon('copy'),
            set::url('#copyExecutionModal'),
            set('data-destoryOnHide', true),
            set('data-toggle', 'modal'),
            $showExecutionExec ? $lang->execution->copyExec : $lang->execution->copy
        )
    ),
    formGroup
    (
        set::width('1/2'),
        set::name('project'),
        set::label($lang->execution->projectName),
        set::items($allProjects),
        set::value($projectID),
        on::change('refreshPage'),
    ),
    $methodBox,
    formGroup
    (
        set::width('1/2'),
        set::name('name'),
        set::label($showExecutionExec ? $lang->execution->execName : $lang->execution->name),
        set::value($name),
    ),
    isset($config->setCode) && $config->setCode == 1 ? formGroup
    (
        set::width('1/2'),
        set::name('code'),
        set::label($showExecutionExec ? $lang->execution->execCode : $lang->execution->code),
        set::value($code),
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
                    set('id', 'begin'),
                    set::value((isset($plan) && !empty($plan->begin) ? $plan->begin : date('Y-m-d'))),
                    set::placeholder($lang->execution->begin),
                    on::change('computeWorkDays')
                ),
                $lang->project->to,
                datePicker
                (
                    set::name('end'),
                    set('id', 'end'),
                    set::value((isset($plan) && !empty($plan->end) ? $plan->end : ''),
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
                set::value(isset($plan) && !empty($plan->begin) ? (helper::workDays($plan->begin, $plan->end) + 1) : ''),
            ),
            div
            (
                setClass('input-control-suffix z-50'),
                $lang->execution->day
            )
        )
    ),
    $typeBox,
    $isStage && isset($config->setPercent) && $config->setPercent == 1 ? formGroup
    (
        set::width('1/2'),
        set::name('percent'),
        set::label($lang->stage->percent),
        set::required(true),
    ) : null,
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
        set::value($team),
    ),
    formGroup
    (
        set::width('1/2'),
        set::label($lang->execution->copyTeam),
        picker
        (
            set::id('teams'),
            set::name('teams'),
            set::items($teams),
            set::value(empty($copyExecution) ? $projectID : $copyExecutionID),
            set('data-placeholder', $lang->execution->copyTeamTip),
            on::change('loadMembers'),
        )
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
                set::value(empty($copyExecution) ? '' : $copyExecution->PM),
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
                set::value(empty($copyExecution) ? '' : $copyExecution->PO),
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
                set::value(empty($copyExecution) ? '' : $copyExecution->QD),
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
                set::value(empty($copyExecution) ? '' : $copyExecution->RD),
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
            set::multiple(true),
        )
    ),
    h::hr(),
    formGroup
    (
        set::name('desc'),
        set::label($showExecutionExec ? $lang->execution->execDesc : $lang->execution->desc),
        set::control('editor'),
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
            set::value($acl),
            on::change('setWhite(this.value)'),
        )
    ),
    formGroup
    (
        set::label($lang->whitelist),
        set::id('whitelistBox'),
        picker
        (
            set::name('whitelist[]'),
            set::items($users),
            set::multiple(true),
        )
    ),
);

modalTrigger
(
    modal
    (
        set::id('copyExecutionModal'),
        set::footerClass('justify-center'),
        to::header
        (
            span
            (
                h4
                (
                    set::class('copy-title'),
                    $lang->execution->copyTitle
                )
            ),
            picker
            (
                set::class('pickerProject'),
                set::name('project'),
                set::items($copyProjects),
                set::value($projectID),
                on::change('loadProjectExecutions'),
            ),
        ),
        to::footer
        (
            btn
            (
                setClass('primary btn-wide hidden confirmBtn'),
                set::text($lang->confirm),
                on::click('setCopyExecution'),
            )
        ),
        div
        (
            set::id('copyExecutions'),
            setClass('flex items-center flex-wrap'),
        )
    )
);

modalTrigger
(
    modal(set::id('tipsModal'))
);

/* ====== Render page ====== */
render();
