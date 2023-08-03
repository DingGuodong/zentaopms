<?php
declare(strict_types=1);
/**
 * The showimport view file of caselib module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yuting Wang <wangyuting@easycorp.ltd>
 * @package     caselib
 * @link        https://www.zentao.net
 */
namespace zin;
jsVar('stepData', $stepData);
jsVar('libID', $libID);

$items[] = array
(
    'name'  => 'id',
    'label' => $lang->idAB,
    'control' => 'index',
    'width' => '32px'
);

$items[] = array
(
    'name'  => 'title',
    'label' => $lang->testcase->title,
    'width' => '240px',
);

$items[] = array
(
    'name'    => 'module',
    'label'   => $lang->testcase->module,
    'control' => 'picker',
    'items'   => $modules,
    'width'   => '200px',
);

$items[] = array
(
    'name'    => 'type',
    'label'   => $lang->testcase->type,
    'control' => 'picker',
    'items'   => $lang->testcase->typeList,
    'width'   => '160px',
);

$items[] = array
(
    'name'    => 'pri',
    'label'   => $lang->testcase->pri,
    'control' => 'pripicker',
    'items'   => $lang->testcase->priList,
    'width'   => '80px',
);

$items[] = array
(
    'name'    => 'precondition',
    'label'   => $lang->testcase->precondition,
    'control' => 'textarea',
    'width'   => '240px',
);

$items[] = array
(
    'name'  => 'keywords',
    'label' => $lang->testcase->keywords,
    'width' => '240px',
);

$items[] = array
(
    'name'    => 'stage',
    'label'   => $lang->testcase->stage,
    'control' => 'picker',
    'multiple' => true,
    'items'   => $lang->testcase->stageList,
    'width'   => '240px',
);

$items[] = array
(
    'name'  => 'stepDesc',
    'label' => $lang->testcase->stepDesc,
    'width' => '320px'
);

$items[] = array
(
    'name'  => 'stepExpect',
    'label' => $lang->testcase->stepExpect,
    'width' => '320px'
);

formBatchPanel
(
    set::title($lang->caselib->import),
    set::items($items),
    set::data(array_values($caseData)),
    set::mode('edit'),
    set::actionsText(false),
    set::onRenderRow(jsRaw('handleRenderRow')),
    input(set::class('hidden'), set::name('isEndPage'), set::value($isEndPage ? '1' : '0')),
    input(set::class('hidden'), set::name('pagerID'), set::value($pagerID)),
    $dataInsert !== '' ? input(set::class('hidden'), set::name('insert'), set::value($dataInsert)) : null
);

render();
