<?php
declare(strict_types=1);
/**
 * The obtain view file of extension module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Tingting Dai <daitingting@easycorp.ltd>
 * @package     extension
 * @link        https://www.zentao.net
 */
namespace zin;

$searchForm = form
(
    set::class('mb-5'),
    set('action', createLink('extension', 'obtain', "type=bySearch")),
    formGroup
    (
        inputGroup
        (
            input
            (
                set::name('key'),
                set::value($this->post->key ? $this->post->key : ''),
                set::placeholder($lang->extension->bySearch)
            ),
            span
            (
                set::class('input-group-btn'),
                btn
                (
                    set::type('submit'),
                    icon('search')
                )
            )
        )
    ),
    set::actions(array())
);

$menuItems = array();
foreach(array('byUpdatedTime', 'byAddedTime', 'byDownloads') as $listType)
{
    $active = (strtolower($listType) == $type) ? 'active' : '';
    $menuItems[] = li
    (
        setClass('menu-item'),
        a
        (
            setClass($active),
            set::href(createLink('extension', 'obtain', "type=$listType")),
            $lang->extension->$listType,
        )
    );
}

sidebar
(
    set::class('bg-white p-4'),
    $searchForm,
    menu($menuItems),
    html($moduleTree)
);

$extensionItems = array();
$i = 1;
foreach($extensions as $extension)
{
    $currentRelease = $extension->currentRelease;
    $latestRelease  = isset($extension->latestRelease) ? $extension->latestRelease : '';

    $labelClass = $extension->offcial ? 'secondary' : 'warning';

    $extensionInfo = array();
    $extensionInfo[] = $lang->extension->author . ': ';
    $extensionInfo[] = html($extension->author);
    $extensionInfo[] = ' ' . $lang->extension->downloads . ': ' . $extension->downloads;
    $extensionInfo[] = ' ' . $lang->extension->compatible . ': ' . $lang->extension->compatibleList[$currentRelease->compatible];
    if(!empty($currentRelease->depends))
    {
        $extensionInfo[] = ' ' . $lang->extension->depends . ': ';
        foreach(json_decode($currentRelease->depends, true) as $code => $limit)
        {
            $extensionInfo[] .= $code;
            if($limit != 'all')
            { 
                $extensionInfo[] .= '(' . !empty($limit['min']) . '>= v' . $limit['min'];
                $extensionInfo[] .= !empty($limit['max']) . '<= v' . $limit['max'] . ') ';
            }
        }
    }

    $btnItems = array();
    $btnItems[] = array('text' => $lang->extension->view,  'url' => $extension->viewLink, 'data-toggle' => 'modal');
    if($currentRelease->public)
    {
        if($extension->type != 'computer' && $extension->type != 'mobile')
        {
            if(isset($installeds[$extension->code]))
            {
                if($installeds[$extension->code]->version != $extension->latestRelease->releaseVersion && $this->extension->checkVersion($extension->latestRelease->zentaoCompatible))
                {
                    $upgradeLink = inlink('upgrade',  "extension=$extension->code&downLink=" . helper::safe64Encode($currentRelease->downLink) . "&md5=$currentRelease->md5&type=$extension->type");
                    $btnItems[] = array('url' => $upgradeLink, 'text' => $lang->extension->upgrade, 'data-toggle' => 'modal');
                }
                else
                {
                    $btnItems[] = array('url' => 'javascript', 'text' => icon('ok') . $lang->extension->upgrade, 'disabled' => 'disabled', 'class' => 'text-success');
                }
            }
        }
    }
    $btnItems[] = array('text' => $lang->extension->downloadAB, 'url' => $currentRelease->downLink, 'target' => '_blank');
    $btnItems[] = array('text' => $lang->extension->site, 'url' => $extension->site, 'target' => '_blank');

    $stars = html::printStars($extension->stars, false);

    $extensionItems[] = div
    (
        set::class('mb-2'),
        div
        (
            set::class('font-bold mb-2'),
            $extension->name . "($currentRelease->releaseVersion)",
            span
            (
                set::class("label $labelClass size-sm font-medium ml-2"),
                $lang->extension->obtainOfficial[$extension->offcial]
            ),
            $latestRelease && $latestRelease->releaseVersion != $currentRelease->releaseVersion ? div
            (
                set::class('pull-right text-sm text-warning'),
                html(sprintf($lang->extension->latest, $latestRelease->viewLink, $latestRelease->releaseVersion, $latestRelease->zentaoCompatible))
            ) : null,
        ),
        div
        (
            set::class('mb-2'),
            $extension->abstract
        ),
        div
        (
            $extensionInfo,
            div 
            (
                set::class('mt-2'),
                $lang->extension->grade . ': ',
                html($stars)
            ),
            div
            (
                set::class('pull-right'),
                btnGroup
                (
                    set::items($btnItems),
                )
            )
        ),
        //$i < count($extensions) ? hr() : null
    );
    $i ++;
}

if($pager)
{
    $extensionItems[] = pager
    (
        set::style(array('float' => 'right')),
        set::type('full'),
        set::page($pager->page),
        set::recTotal($pager->recTotal),
        set::recPerPage($pager->recPerPage),
    );
}

div
(
    set::class('flex col gap-y-1 p-5 bg-white'),
    !empty($extensions) ?  $extensionItems : div
    (
        set::class('alert danger flex items-center'),
        icon
        (
            'info-sign',
            set::size('2x'),
            set('class', 'alert-icon')
        ),
        div
        (
            div
            (
                set::class('font-bold, text-lg'),
                $lang->extension->errorOccurs,
            ),
            p
            (
                $lang->extension->errorGetExtensions,                
            )
        )
    ),
);

render();

