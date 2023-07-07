<?php
declare(strict_types=1);
/**
* The UI file of story module of ZenTaoPMS.
*
* @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
* @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
* @author      chen.tao <chentao@easycorp.ltd>
* @package     story
* @link        https://www.zentao.net
*/

namespace zin;

/* Generate fields for the batch create form. */
$fnGenerateFields = function() use ($lang, $fields)
{
    /* Generate fields with the appropriate properties. */
    $items   = array();
    $items[] = array('name' => 'id', 'label' => $lang->idAB, 'control' => 'index', 'width' => '32px');

    return array_merge($items, array_map(function($name, $field)
    {
        $field['name'] = $name;
        if(!empty($field['options'])) $field['items'] = $field['options'];
        unset($field['options']);

        return $field;
    }, array_keys($fields), array_values($fields)));
};

/* Generate customized fields. */
$fnGenerateCustomizedFields = function() use ($showFields, $customFields)
{
    $showFields    = ",$showFields,";
    $fields        = array();
    $defaultFields = explode(',', 'branch,platform,plan,spec,pri,estimate');
    foreach($customFields as $name => $text) $fields[] = array('name' => $name, 'text' => $text, 'show' => str_contains($showFields, ",$name,"), 'default' => in_array($name, $defaultFields));
    return $fields;
};

formBatchPanel
(
    on::click('#saveButton', 'customSubmit'),
    on::click('#saveDraftButton', 'customSubmit'),
    set::id('dataform'),
    set::title($storyID ? $storyTitle . ' - ' . $this->lang->story->subdivide : $this->lang->story->batchCreate),
    set::uploadParams('module=story&params=' . helper::safe64Encode("productID=$productID&branch=$branch&moduleID=$moduleID&storyID=$storyID&executionID=$executionID&plan=&type=$type")),
    set::pasteField('title'),
    set::customFields(array('items' => $fnGenerateCustomizedFields())),
    set::items($fnGenerateFields()),
    set::onRenderRow(jsRaw('renderRowData')),
    set::actions(array
    (
        array('text' => $lang->save,             'id' => 'saveButton',      'class' => 'primary'),
        array('text' => $lang->story->saveDraft, 'id' => 'saveDraftButton', 'class' => 'secondary'),
        array('text' => $lang->goback, 'data-back' => 'APP', 'class' => 'open-url'),
    ))
);

render();
