<?php
declare(strict_types=1);
/**
 * Create view of program plan module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      chen.tao <chentao@easycorp.ltd>
 * @package     programplan
 * @link        https://www.zentao.net
 */

namespace zin;

$fields = $this->config->programplan->form->create;

/* Generate title that is tailored to specific situation. */
$title = $lang->programplan->create;
if($planID) $title = $programPlan->name . $lang->project->stage . '（' . $programPlan->begin . $lang->project->to . $programPlan->end . '）';

/* Generate product list dropdown menu while stage by product. */
$fnGenerateStageByProductList = function() use ($productID, $productList, $project)
{
    if(empty($productList) || $project->stageBy != 'product') return null;

    $defaultName = $productID != 0 ? zget($productList,$productID) : current($productList);

    $items = array();
    foreach($productList as $key => $product)
    {
        $items[] = array('text' => $product, 'active' => $productID == $key, 'data-url' => createLink('programplan', 'create', "projectID=$project->id&productID=$key"));
    }

    return dropdown
    (
        $defaultName,
        span(setClass('caret')),
        set::items($items)
    );
};

/* Generate customized fields. */
$fnGenerateCustomizedFields = function() use ($defaultFields, $customFields, $showFields, $custom)
{
    $items = array();

    foreach($customFields as $name => $text)
    {
        $items[] = array
        (
            'name' => $name,
            'text' => $text,
            'show' => str_contains($showFields, $name),
            'default' => str_contains($defaultFields, $name)
        );
    }

    return array('items' => $items, 'urlParams' => "module=programplan&section={$custom}&key=createFields");
};

/* Generate checkboxes for sub-stage management. */
$fnGenerateSubPlanManageFields = function() use ($lang, $planID, $project, $executionType)
{
    if(empty($planID) || $project->model != 'waterfallplus') return div();

    $typeList = $lang->programplan->typeList;

    $items = array();
    if(count($typeList) > 1)
    {
        foreach($typeList as $key => $value)
        {
            $items[] = div(setClass('px-1'), checkbox
            (
                set::type('radio'),
                set::name('executionType'),
                set::text($value),
                set::value($key),
                on::change('window.onChangeExecutionType'),
                set::checked($key == $executionType)
            ));
        }
    }
    else
    {
        $items[] = div(setClass('px-1'), zget($typeList, $executionType));
    }

    /* Append method tip. */
    $items[] = icon(setClass('icon-help'), setID('methodTip'));
    $items[] = tooltip(
        set::_to('#methodTip'),
        set::title($lang->programplan->methodTip),
        set::placement('right'),
        set::type('white'),
        set::className('text-darker border border-light')
    );

    return div
    (
        setClass('flex w-1/2 items-center'),
        div(setClass('font-bold'), $lang->programplan->subPlanManage . ':'),
        $items
    );
};

/* Generate form fields. */
$fnGenerateFields = function() use ($lang, $requiredFields, $showFields, $fields, $PMUsers, $enableOptionalAttr, $programPlan, $planID, $executionType)
{
    $items   = array();
    $items[] = array('name' => 'id', 'label' => $lang->idAB, 'control' => 'index', 'width' => '32px');

    $renderFields = implode(',', array_keys($requiredFields));
    $renderFields = ",$renderFields,$showFields,";

    foreach($fields as $name => $field)
    {
        $field['name'] = $name;

        /* Convert 'options' to 'items'. */
        if(!empty($field['options']))
        {
            $field['items'] = $field['options'];
        }
        unset($field['options']);

        /* Assgn item data to PM field. */
        if($name == 'PM')
        {
            $field['items'] = $PMUsers;
        }

        /* Form field name is plural nouns, so remove the suffix 's' */
        $name = trim($name, 's');
        /* Set hidden attribute. */
        if(!str_contains($renderFields, ",$name,"))
        {
            $field['hidden'] = true;
        }

        /* Sub-stage. */
        if($name == 'attribute' && !$enableOptionalAttr)
        {
            $field['disabled'] = true;
            $field['value']    = $programPlan->attribute;
        }

        if($name == 'acl' && !$enableOptionalAttr)
        {
            $field['disabled'] = true;
            $field['value']    = empty($programPlan) ? 'open' : $programPlan->acl;
        }

        /* Field for agileplus. */
        if($name == 'type' && $planID != 0 && $executionType == 'agileplus')
        {
            $field['hidden'] = false;
            $field['items']  = $lang->execution->typeList;
        }

        $items[] = $field;
    }

    return $items;
};

/* Generate default rendering data. */
$fnGenerateDefaultData = function() use ($config, $plans, $planID, $stages, $executionType)
{
    $items = array();

    /* Created a new project witho no stages. */
    if(empty($plans) && $planID == 0)
    {
        foreach($stages as $stage)
        {
            $item = new stdClass();

            $item->name = $stage->name;
            if(isset($config->setCode) && $config->setCode == 1)
            {
                $item->code = isset($stage->code) ? $stage->code : '';
            }
            $item->percent   = $stage->percent;
            $item->attribute = $stage->type;
            $item->acl       = 'open';
            $item->milestone = 0;

            $items[] = $item;
        }

        return $items;
    }

    /* Create stages for exist project. */
    foreach($plans as $plan)
    {
        $item = new stdClass();

        $item->disabled   = !isset($plan->setMilestone);
        $item->planIDList = $plan->id;
        $item->type       = $plan->type;
        $item->name       = $plan->name;
        if(isset($config->setCode) && $config->setCode == 1)
        {
            $item->code = $plan->code;
        }
        $item->PM           = $plan->PM;
        $item->percent      = $plan->percent;
        $item->attribute    = $plan->attribute;
        $item->acl          = $plan->acl;
        $item->milestone    = $plan->milestone;
        $item->begin        = $plan->begin;
        $item->end          = $plan->end;
        $item->realBegan    = $plan->realBegan;
        $item->realEnd      = $plan->realEnd;
        $item->desc         = $plan->desc;
        $item->setMilestone = $plan->setMilestone;
        if($config->edition == 'max' && $executionType == 'stage')
        {
            $item->output = empty($plan->output) ? 0 : explode(',', $plan->output);
        }
        $item->order     = $plan->order;

        $items[] = $item;
    }

    return $items;
};

/* ZIN: layout. */
jsVar('projectID', $project->id);
jsVar('productID', $productID);
jsVar('planID',    $planID);
jsVar('type',      $executionType);

featureBar(li
(
    setClass('nav-item'),
    a
    (
        setClass('active'),
        $title
    ),
    $fnGenerateStageByProductList()
));

toolbar
(
    btn(setClass('btn primary open-url'), set::icon('back'), set('data-back', 'APP'), $lang->goback)
);

formBatchPanel
(
    set::id('dataform'),
    set::onRenderRow(jsRaw('window.onRenderRow')),
    to::headingActions(array($fnGenerateSubPlanManageFields())),
    set::customFields($fnGenerateCustomizedFields()),
    set::items($fnGenerateFields()),
    set::data($fnGenerateDefaultData())
);
