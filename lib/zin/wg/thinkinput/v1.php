<?php
declare(strict_types=1);
namespace zin;

requireWg('thinkNode');

/**
 * 思引师填空部件类。
 * thinmory Input widget class.
 */
class thinkInput extends thinkNode
{
    protected static array $defineProps = array(
        'required?: bool',                      // 是否必填
        'isRequiredName?: string="required"',   // 是否必填对应的name
    );

    private function buildRequiredControl(): wg
    {
        global $lang;
        list($required, $isRequiredName) = $this->prop(array('required', 'isRequiredName', 'requiredRows', 'requiredRowsName'));
        return formRow
        (
            formGroup
            (
                setClass('w-1/2'),
                setStyle(array('display' => 'flex')),
                set::label($lang->thinkwizard->step->label->required),
                radioList
                (
                    set::name($isRequiredName),
                    set::inline(true),
                    set::items($lang->thinkwizard->step->requiredList),
                    set::value($required ? $required : 0),
                )
            ),
        );
    }

    protected function buildBody(): array
    {
        $items = parent::buildBody();
        $items[] = $this->buildRequiredControl();
        return $items;
    }
}
