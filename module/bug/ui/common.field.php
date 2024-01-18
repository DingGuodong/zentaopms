<?php
namespace zin;
global $lang;

$fields = defineFieldList('bug');

$fields->field('product')
    ->control('inputGroup')
    ->items(false)
    ->itemBegin('product')->control('picker')->items(data('products'))->value(data('bug.productID'))->itemEnd()
    ->item((data('product.type') !== 'normal' && isset(data('products')[data('bug.productID')])) ? field('branch')->control('picker')->boxClass('flex-none')->width('100px')->name('branch')->items(data('branches'))->value(data('bug.branch')) : null);

$fields->field('project')
    ->control('picker')
    ->items(data('projects'))
    ->value(data('projectID'));

$fields->field('execution')
    ->id('executionBox')
    ->control('picker')
    ->items(data('executions'))
    ->value(data('executionID'));

$fields->field('module')
    ->control(array('control' => 'modulePicker', 'manageLink' => createLink('tree', 'browse', 'rootID=' . data('bug.productID') . '&view=bug&currentModuleID=0&branch=' . data('bug.branch'))))
    ->items(data('moduleOptionMenu'))
    ->value(data('bug.moduleID'));

$fields->field('openedBuild')
    ->checkbox(array('text' => $lang->bug->allBugs, 'name' => 'allBuilds'))
    ->control('inputGroup')
    ->itemBegin('openedBuild[]')->control('picker')->items(data('builds'))->multiple()->itemEnd();

$fields->field('assignedTo')
    ->control('inputGroup')
    ->itemBegin('assignedTo')->control('picker')->items(data('productMembers'))->value(data('bug.assignedTo'))->itemEnd()
    ->itemBegin()->control('btn')->icon('refresh text-primary')->hint($lang->bug->loadAll)->id('allUsers')->itemEnd();

$fields->field('deadline')
    ->control('datePicker');

if(data('executionType') && data('executionType') == 'kanban')
{
    $fields->field('region')
        ->label($lang->kanbancard->region)
        ->control('picker')
        ->items(data('regionPairs'))
        ->value(data('regionID'));

    $fields->field('lane')
        ->label($lang->kanbancard->lane)
        ->control('picker')
        ->items(data('lanePairs'))
        ->value(data('laneID'));
}

$fields->field('title')
    ->control('colorInput', array('colorValue' => data('bug.color')));

$fields->field('type')
    ->width('1/6')
    ->control('picker')
    ->items($lang->bug->typeList);

$fields->field('severity')
    ->width('1/6')
    ->control('severityPicker')
    ->items($lang->bug->severityList);

$fields->field('pri')
    ->width('1/6')
    ->control('priPicker')
    ->items($lang->bug->priList);

$fields->field('steps')
    ->width('full')
    ->control('editor');

$fields->field('files')
    ->width('full')
    ->control('files');

$fields->field('story')
    ->wrapBefore()
    ->control('picker')
    ->items(empty(data('bug.stories')) ? array() : data('bug.stories'))
    ->value(data('bug.storyID'));

$fields->field('task')
    ->control('picker')
    ->items(array())
    ->value(data('bug.taskID'));

$fields->field('feedbackBy')
    ->control('input');

$fields->field('notifyEmail')
    ->control('input');

$fields->field('browser')
    ->control('picker')
    ->items($lang->bug->browserList)
    ->multiple();

$fields->field('os')
    ->control('picker')
    ->items($lang->bug->osList)
    ->multiple();

$fields->field('mailto')
    ->control('mailto')
    ->value(data('bug.mailto'));

$fields->field('keywords')
   ->control('input');

$fields->field('case')->control('hidden')->value(data('bug.caseID'));
$fields->field('caseVersion')->control('hidden')->value(data('bug.version'));
$fields->field('result')->control('hidden')->value(data('bug.runID'));
$fields->field('testtask')->control('hidden')->value(data('bug.testtask'));
