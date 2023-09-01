<?php
declare(strict_types=1);
/**
 * The ajaxGetCFD view file of execution module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian<tianshujie@easycorp.ltd>
 * @package     execution
 * @link        https://www.zentao.net
 */
namespace zin;
panel
(
    div
    (
        set::className('flex flex-nowrap justify-between'),
        div
        (
            set('class', 'panel-title'),
            $execution->name . $lang->execution->CFD,
        ),
        common::hasPriv('execution', 'cfd') ? btn
        (
            setClass('ghost text-gray'),
            set::url(createLink('execution', 'cfd', "executionID={$execution->id}")),
            $lang->more
        ) : null,
    ),
    div
    (
        common::hasPriv('execution', 'cfd') ? isset($chartData['labels']) && count($chartData['labels']) != 1 ? echarts
        (
            set::series($chartSeries),
            set::tooltip(array(
                'trigger'     => 'axis',
                'axisPointer' => array('type' => 'cross', 'label' => array('backgroundColor' => '#6a7985')),
                'textStyle'   => array('fontWeight' => 100),
                'formatter'   => "RAWJS<function(rowDatas){return window.randTipInfo(rowDatas);}>RAWJS"
            )),
            set::legend(array(
                'data' => array_keys(array_reverse($chartData['line']))
            )),
            set::grid(array(
                'left'         => '3%',
                'right'        => '5%',
                'bottom'       => '3%',
                'containLabel' => true
            )),
            set::xAxis(array(array(
                'type' => 'category',
                'boundaryGap' => false,
                'data' => $chartData['labels'],
                'name' => $lang->execution->burnXUnit,
                'axisLine' => array('show' => true, 'lineStyle' =>array('color' => '#999', 'width' =>1))
            ))),
            set::yAxis(array(array(
                'type'          => 'value',
                'name'          => $lang->execution->count,
                'minInterval'   => 1,
                'nameTextStyle' => array('fontWeight' => 'normal'),
                'axisPointer'   => array('label' => array('show' => true, 'precision' => 0)),
                'axisLine'      => array('show' => true, 'lineStyle' => array('color' => '#999', 'width' => 1))
            )))
        )->size('100%', '150%') : div
        (
            setClass('table-empty-tip text-center'),
            span
            (
                setClass('text-gray'),
                $lang->execution->noPrintData
            )
        ) : null
    ),
);

/* ====== Render page ====== */
render();
