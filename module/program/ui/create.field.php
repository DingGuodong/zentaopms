<?php
namespace zin;
global $lang;

$fields = defineFieldList('program.create', 'program');

$fields->field('begin')
    ->control('inputGroup')
    ->required()
    ->label($lang->project->dateRange)
    ->itemBegin('begin')->require()->type('datePicker')->value(date('Y-m-d'))->placeholder($lang->project->begin)->itemEnd()
    ->itemBegin()->type('addon')->label($lang->project->to)->text($lang->colon)->itemEnd()
    ->itemBegin('end')->require()->type('datePicker')->placeholder($lang->project->end)->itemEnd();

$fields->field('acl')->value('open');
