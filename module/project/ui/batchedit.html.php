<?php
declare(strict_types=1);
/**
 * The batchedit view file of project module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Sun Guangming<sunguangming@easycorp.ltd>
 * @package     project
 * @link        https://www.zentao.net
 */
namespace zin;

$programAclList = array();
$projectAclList = array();
foreach($lang->program->subAcls as $acl => $label) $programAclList[] = array('text' => $label, 'value' => $acl);
foreach($lang->project->acls as $acl => $label)    $projectAclList[] = array('text' => $label, 'value' => $acl);

jsVar('longTime', LONG_TIME);
jsVar('weekend', $config->execution->weekend);
jsVar('programAclList', $programAclList);
jsVar('projectAclList', $projectAclList);
jsVar('disabledprograms', !empty($globalDisableProgram));
jsVar('beginLessThanParent', $lang->project->beginLessThanParent);
jsVar('endGreatThanParent', $lang->project->endGreatThanParent);

$setCode = (isset($config->setCode) and $config->setCode == 1);
formBatchPanel
(
    set::title($lang->project->batchEdit),
    set::mode('edit'),
    set::data(array_values($projects)),
    set::onRenderRow(jsRaw('renderRowData')),
    on::change('[name^=begin],[name^=end]', 'batchComputeWorkDays'),
    $config->systemMode != 'light' ? on::change('[name^=begin],[name^=end],[name^=parent]', 'batchCheckDate') : null,
    formBatchItem
    (
        set::name('id'),
        set::label($lang->idAB),
        set::control('hidden'),
        set::hidden(true)
    ),
    formBatchItem
    (
        set::name('id'),
        set::label($lang->idAB),
        set::control('index'),
        set::width('38px')
    ),
    formBatchItem
    (
        set::name('parent'),
        set::label($lang->project->program),
        set::control('picker'),
        set::items($programs),
        set::width('136px')
    ),
    formBatchItem
    (
        set::name('name'),
        set::required(true),
        set::label($lang->project->name),
    ),
    $setCode ? formBatchItem
    (
        set::name('code'),
        set::label($lang->project->code),
        set::required(strpos($config->project->edit->requiredFields, 'code') !== false),
        set::width('136px')
    ) : null,
    formBatchItem
    (
        set::name('PM'),
        set::label($lang->project->PM),
        set::control('picker'),
        set::ditto(true),
        set::defaultDitto('off'),
        set::items($PMUsers),
        set::width('136px')
    ),
    formBatchItem
    (
        set::name('begin'),
        set::required(true),
        set::label($lang->project->begin),
        set::control('date'),
        set::width('120px')
    ),
    formBatchItem
    (
        set::name('end'),
        set::required(true),
        set::label($lang->project->end),
        set::width('120px'),
        set::control(array
        (
            'control' => 'date',
            'display' => jsRaw("(value) => (value === '" . LONG_TIME . "' ? '" . $lang->project->longTime . "' : zui.formatDate(value, 'yyyy-MM-dd'))"),
            'actions' => array
            (
                array('text' => $lang->datepicker->dpText->TEXT_TODAY, 'data-set-date' => helper::today()),
                array('text' => $lang->project->longTime, 'data-set-date' => LONG_TIME)
            )
        )),
    ),
    formBatchItem
    (
        set::name('days'),
        set::label($lang->project->days),
        set::width('84px')
    ),
    formBatchItem
    (
        set::name('acl'),
        set::label($lang->project->acl),
        set::control('picker'),
        set::items(array()),
        set::width('76px')
    )
);

h::table
(
    setID('dateTipTemplate'),
    setClass('hidden'),
    h::tr
    (
        setClass('dateTip'),
        h::td
        (
            set::colspan($setCode ? 9 : 8),
            div
            (
                setClass('text-right'),
                span(setClass('beginLess text-warning hidden'), html($lang->project->beginLessThanParent)),
                span(setClass('endGreater text-warning hidden'), html($lang->project->endGreatThanParent)),
                a(setClass('underline text-warning'), set::href('javascript:;'), $lang->project->ignore)
            )
        )
    )
);

render();
