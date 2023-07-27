<?php
declare(strict_types=1);
/**
 * The createButton view file of doc module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian<tianshujie@easycorp.ltd>
 * @package     doc
 * @link        https://www.zentao.net
 */
namespace zin;
$objectID      = zget($lib, $lib->type, 0);
$templateParam = $this->config->edition == 'max' ? '&from=template' : '';
$buttonItems   = array();
foreach($lang->doc->createList as $typeKey => $typeName)
{
    $docType = zget($config->doc->iconList, $typeKey);
    $params  = "objectType={$lib->type}&objectID={$objectID}&libID={$lib->id}&moduleID={$moduleID}&type={$typeKey}";
    if($typeKey == 'template' and $config->edition == 'max') $params = "objectType={$lib->type}&objectID=$objectID&libID={$lib->id}&moduleID={$moduleID}&type=html&from=template";

    $buttonItems[] = array
    (
        'content'     => array('html' => "<img class='mr-2' src='static/svg/{$docType}.svg'/>{$typeName}", 'class' => 'flex w-full'),
        'url'         => createLink('doc', 'create', $params),
        'data-app'    => $app->tab,
        'data-toggle' => strpos($this->config->doc->officeTypes, $typeKey) !== false ? 'modal' : ''
    );

    if($typeKey == 'template') $buttonItems[] = array('type' => 'divider');
}

$createButton = btngroup
(
    btn
    (
        setClass('btn primary ml-2'),
        set::icon('plus'),
        set::url(createLink('doc', 'create', "objectType={$type}&objectID={$objectID}&libID={$lib->id}&moduleID={$moduleID}&type=html{$templateParam}")),
        set('data-app', $app->tab),
        $lang->doc->create
    ),
    dropdown
    (
        btn(setClass('btn primary dropdown-toggle'),
        setStyle(array('padding' => '6px', 'border-radius' => '0 2px 2px 0'))),
        set::placement('bottom-end'),
        set::items($buttonItems),
    )
);
