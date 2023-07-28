<?php
declare(strict_types=1);
/**
 * The view file of doc module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Sun Guangming<sunguangming@easycorp.ltd>
 * @package     doc
 * @link        https://www.zentao.net
 */
namespace zin;

featureBar
(
    li(backBtn(setClass('ghost'), set::icon('back'), $lang->goback)),
);

if($libID && common::hasPriv('doc', 'create')) include 'createbutton.html.php';
include 'lefttree.html.php';

toolbar
(
    $canExport ? item(set(array
    (
        'id'          => $exportMethod,
        'icon'        => 'export',
        'class'       => 'ghost export',
        'text'        => $lang->export,
        'url'         => $createLink('doc', $exportMethod, "libID={$libID}&moduleID={$moduleID}"),
        'data-toggle' => 'modal'
    ))) : null,
    common::hasPriv('doc', 'createLib') ? item(set(array
    (
        'icon'        => 'plus',
        'class'       => 'btn secondary',
        'text'        => $lang->doc->createLib,
        'url'         => createLink('doc', 'createLib', "type={$type}&objectID={$objectID}"),
        'data-toggle' => 'modal'
    ))) : null,
    $libID && common::hasPriv('doc', 'create') ? $createButton : null
);

$versionList = array();
for($itemVersion = $doc->version; $itemVersion > 0; $itemVersion--)
{
    $versionList[] = array('text' => "V$itemVersion", 'url' => createLink('doc', 'view', "docID={$docID}&version={$itemVersion}"));
}

$star        = strpos($doc->collector, ',' . $app->user->account . ',') !== false ? 'star' : 'star-empty';
$collectLink = $this->createLink('doc', 'collect', "objectID=$doc->id&objectType=doc");
$starBtn     = "<a data-url='$collectLink' title='{$lang->doc->collect}' class='ajax-submit btn btn-link'>" . html::image("static/svg/{$star}.svg", "class='$star'") . '</a>';

/* 导入资产库的按钮. */
if($config->vision == 'rnd' and $config->edition == 'max' and $app->tab == 'project')
{
    $canImportToPracticeLib  = (common::hasPriv('doc', 'importToPracticeLib')  and helper::hasFeature('practicelib'));
    $canImportToComponentLib = (common::hasPriv('doc', 'importToComponentLib') and helper::hasFeature('componentlib'));

    if($canImportToPracticeLib)  $items[] = array('text' => $lang->doc->importToPracticeLib,  'url' => '#importToPracticeLib',  'data-toggle' => 'modal');
    if($canImportToComponentLib) $items[] = array('text' => $lang->doc->importToComponentLib, 'url' => '#importToComponentLib', 'data-toggle' => 'modal');

    $importLibBtn = $items ? dropdown
    (
        btn
        (
            setClass('ghost btn square btn-default'),
            icon('diamond')
        ),
        set::items($items)
    ) : null;
}

$createInfo = $doc->status == 'draft' ? zget($users, $doc->addedBy) . " {$lang->colon} " . substr($doc->addedDate, 0, 10) . (common::checkNotCN() ? ' ' : '') . $lang->doc->createAB : zget($users, $doc->releasedBy) . " {$lang->colon} " . substr($doc->releasedDate, 0, 10) . (common::checkNotCN() ? ' ' : '') . $lang->doc->release;

$keywordsLabel = array();
if($doc->keywords)
{
    foreach($doc->keywords as $keywords)
    {
        if($keywords)
        {
            $keywordsLabel[] = span
            (
                setClass('label secondary-outline'),
                $keywords
            );
        }
    }
}

/* Build editor group. */
$editorGroup = '';
if(!empty($editors))
{
    $space       = common::checkNotCN() ? ' ' : '';
    $firstEditor = current($editors);
    $editorInfo  = zget($users, $firstEditor->account) . ' ' . substr($firstEditor->date, 0, 10) . $space . $lang->doc->update;

    array_shift($editors);

    $items = array();
    foreach($editors as $editor)
    {
        $info = zget($users, $editor->account) . ' ' . substr($editor->date, 0, 10) . $space . $lang->doc->update;
        $items[] = array('text' => $info);
    }

    $editorGroup = dropdown
    (
        btn
        (
            setClass('ghost btn square btn-default'),
            $editorInfo
        ),
        set::items($items)
    );
}

panel
(
    div
    (
        setClass('panel-heading'),
        div
        (
            setClass('title'),
            $doc->title
        ),
        $doc->deleted ? span(setClass('label danger'), $lang->doc->deleted) : null,
        dropdown
        (
            btn
            (
                setClass('ghost btn square btn-default'),
                'V' . ($version ? $version : $doc->version)
            ),
            set::items($versionList)
        ),
        div
        (
            setClass('panel-actions flex'),
            div
            (
                setClass('toolbar'),
                btn
                (
                    set::url('javascript:fullScreen()'),
                    setClass('btn ghost'),
                    icon('fullscreen'),
                ),
                common::hasPriv('doc', 'collect') ? html($starBtn) : null,
                ($config->vision == 'rnd' and $config->edition == 'max' and $app->tab == 'project') ? $importLibBtn : null,
                (!$isRelease && common::hasPriv('doc', 'edit')) ? btn
                (
                    set::url(createLink('doc', 'edit', "docID=$doc->id")),
                    setClass('btn ghost'),
                    icon('edit'),
                ) : null,
                (!$isRelease && common::hasPriv('doc', 'delete')) ? btn
                (
                    set::url(createLink('doc', 'delete', "docID=$doc->id")),
                    setClass('btn ghost ajax-submit'),
                    set('data-confirm', $lang->doc->confirmDelete),
                    icon('trash'),
                ) : null,
                btn
                (
                    set::id('hisTrigger'),
                    set::url('###)'),
                    setClass('btn ghost'),
                    icon('clock'),
                ),
            ),
            div
            (
                set::id('editorBox'),
                setClass('flex'),
                $editorGroup
            )
        )
    ),
    div
    (
        set::Class('panel-body'),
        set::id('content'),
        div
        (
            setClass('info'),
            span
            (
                setClass('user-time'),
                icon('contacts'),
                $createInfo,
            ),
            span
            (
                setClass('user-time'),
                icon('star'),
                $doc->collects ? $doc->collects : 0,
            ),
            span
            (
                setClass('user-time'),
                icon('eye'),
                $doc->views,
            ),
            $keywordsLabel ? span
            (
                setClass('keywords'),
                $keywordsLabel
            ) : null,
        ),
        html($doc->content)
    ),
    h::hr(),
    $doc->files ? fileList
    (
        set::files($doc->files)
    ) : null
);

panel
(
    set::id('history'),
    setClass('hidden'),
    history()
);
