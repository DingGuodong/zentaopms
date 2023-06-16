<?php
declare(strict_types=1);
/**
 * The batchedit view file of execution module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Sun Guangming<sunguangming@easycorp.ltd>
 * @package     execution 
 * @link        https://www.zentao.net
 */
namespace zin;

$setCode    = (isset($config->setCode) and $config->setCode == 1);
$showMethod = $app->tab == 'project' and isset($project) and ($project->model == 'agileplus' or $project->model == 'waterfallplus');

jsVar('stageList', $lang->stage->typeList);

formBatchPanel
(
    set::mode('edit'),
    set::data(array_values($executions)),
    set::onRenderRow(jsRaw('renderRowData')),
    formBatchItem
    (
        set::name('id'),
        set::label($lang->idAB),
        set::control('hidden'),
        set::hidden(true),
    ),
    formBatchItem
    (
        set::name('id'),
        set::label($lang->idAB),
        set::control('index'),
        set::width('38px'),
    ),
    formBatchItem
    (
        set::name('name'),
        set::label($lang->execution->name),
        set::width('240px'),
    ),
    $showMethod ? formBatchItem
    (
        set::name('type'),
        set::label($lang->execution->method),
        set::control('select'),
        set::items($lang->execution->typeList),
        set::disabled(true),
        set::width('64px'),
    ) : null,
    $setCode ? formBatchItem
    (
        set::name('code'),
        set::label($lang->execution->code),
        set::width('136px'),
    ) : null,
    formBatchItem
    (
        set::name('PM'),
        set::label($lang->execution->execPM),
        set::control('select'),
        set::ditto(true),
        set::defaultDitto('off'),
        set::items($pmUsers),
        set::width('136px'),
    ),
    formBatchItem
    (
        set::name('PO'),
        set::label($lang->execution->PO),
        set::control('select'),
        set::ditto(true),
        set::defaultDitto('off'),
        set::items($poUsers),
        set::width('80px'),
        set::hidden(true),
    ),
    formBatchItem
    (
        set::name('QD'),
        set::label($lang->execution->QD),
        set::control('select'),
        set::ditto(true),
        set::defaultDitto('off'),
        set::items($qdUsers),
        set::width('80px'),
        set::hidden(true),
    ),
    formBatchItem
    (
        set::name('RD'),
        set::label($lang->execution->RD),
        set::control('select'),
        set::ditto(true),
        set::defaultDitto('off'),
        set::items(array()),
        set::width('80px'),
        set::hidden(true),
    ),
    formBatchItem
    (
        set::name('lifetime'),
        set::label($lang->execution->type),
        set::control('select'),
        set::items($lang->execution->lifeTimeList),
        set::width('80px'),
    ),
    formBatchItem
    (
        set::name('begin'),
        set::label($lang->execution->begin),
        set::control('date'),
        set::width('76px'),
    ),
    formBatchItem
    (
        set::name('end'),
        set::label($lang->execution->end),
        set::control('date'),
        set::width('76px'),
    ),
    formBatchItem
    (
        set::name('team'),
        set::label($lang->execution->teamname),
        set::control('text'),
        set::width('136px'),
        set::hidden(true),
    ),
    formBatchItem
    (
        set::name('desc'),
        set::label($lang->execution->desc),
        set::control('textarea'),
        set::width('160px'),
        set::hidden(true),
    ),
    formBatchItem
    (
        set::name('days'),
        set::label($lang->execution->days),
        set::control
        (
            array
            (
                'type'   => 'inputControl',
                'suffix' => $lang->execution->day,
                'suffixWidth' => 20
            )
        ),
        set::width('76px'),
    ),
);

render();
