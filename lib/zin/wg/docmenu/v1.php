<?php
declare(strict_types=1);
namespace zin;

require_once dirname(__DIR__) . DS . 'btn' . DS . 'v1.php';
require_once dirname(__DIR__) . DS . 'sidebar' . DS . 'v1.php';
class docMenu extends wg
{
    private array $modules = array();

    private array $mineTypes = array('mine', 'view', 'collect', 'createdby', 'editedby');

    protected static array $defineProps = array(
        'modules: array',
        'activeKey?: int',
        'settingLink?: string',
        'menuLink: string',
        'title?: string',
        'linkParams?: string="%s"',
        'libID?: int=0',
        'moduleID?: int=0',
        'spaceType?: string',
        'objectType?: string',
        'objectID?: int=0',
        'hover?: bool=true',
        'isThinmory?: bool=false',
    );

    public static function getPageCSS(): ?string
    {
        return file_get_contents(__DIR__ . DS . 'css' . DS . 'v1.css');
    }

    public static function getPageJS(): ?string
    {
        return file_get_contents(__DIR__ . DS . 'js' . DS . 'v1.js');
    }

    private function buildLink($item, $releaseID = 0): string
    {
        $url = zget($item, 'url', '');
        if(!empty($url)) return $url;
        $objectType = $this->objectType;
        if($objectType == 'project' && $item->type == 'execution') return '';
        if(in_array($item->type, array('apiLib', 'docLib')))
        {
            $this->libID    = $item->id;
            $this->moduleID = 0;
        }
        if($item->type == 'module') $this->moduleID = $item->id;

        $linkParams = sprintf($this->linkParams, "libID={$this->libID}&moduleID={$this->moduleID}");
        if(in_array($this->spaceType, array('product', 'project', 'custom'))) $linkParams = "objectID={$this->objectID}&{$linkParams}";

        $moduleName = $this->spaceType == 'api' ? 'api' : 'doc';
        $methodName = '';
        if($this->spaceType == 'api')
        {
            $methodName = 'index';
        }
        else if($item->type == 'annex')
        {
            $methodName = 'showFiles';
            $linkParams = "type={$objectType}&objectID={$item->objectID}";
        }
        else if($item->type == 'text')
        {
            $methodName = 'view';
            $linkParams = "docID={$item->id}";
        }
        else if($objectType == 'execution')
        {
            $moduleName = 'execution';
            $methodName = 'doc';
        }
        else
        {
            $methodName = $this->spaceMethod[$objectType] ? $this->spaceMethod[$objectType] : 'teamSpace';
            if(in_array($objectType, $this->mineTypes))
            {
                $moduleID = $item->id;
                if(in_array($item->type, array('docLib', 'annex', 'api', 'execution'))) $moduleID = 0;

                $type       = in_array(strtolower($item->type), $this->mineTypes) ? strtolower($item->type) : 'mine';
                $linkParams = "type={$type}&libID={$this->libID}&moduleID={$moduleID}";
            }
            if($item->type == 'module' && !empty($item->object) && $item->object == 'api')
            {
                $linkParams = str_replace(array('browseType=&', 'param=0'), array('browseType=byrelease&', "param={$this->release}"), $linkParams);
            }
        }

        if($releaseID)
        {
            if($this->currentModule == 'doc')
            {
                $linkParams = str_replace(array('browseType=&', 'param=0'), array('browseType=byrelease&', "param={$releaseID}"), $linkParams);
            }
            else
            {
                $moduleName = 'api';
                $methodName = 'index';
                $linkParams = "libID={$this->libID}&moduleID=0&apiID=0&version=0&release={$releaseID}";
            }
        }
        return helper::createLink($moduleName, $methodName, $linkParams);
    }

    private function buildMenuTree(array $items, int $parentID = 0): array
    {
        if(empty($items)) $items = $this->modules;
        if(empty($items)) return array();

        $activeKey   = $this->prop('activeKey');
        $parentItems = array();
        foreach($items as $setting)
        {
            if(!is_object($setting)) continue;

            $setting->parentID = $parentID;

            $itemID = 0;
            if(!in_array(strtolower($setting->type), $this->mineTypes)) $itemID = $setting->id ? $setting->id : $parentID;

            $moduleName = ($setting->type == 'apiLib' || (isset($setting->objectType) && $setting->objectType == 'api')) ? 'api' : 'doc';
            $selected   = $itemID && $itemID == $activeKey;

            $item = array(
                'key'         => $itemID,
                'text'        => $setting->name,
                'hint'        => $setting->name,
                'icon'        => $this->getIcon($setting),
                'url'         => $this->buildLink($setting),
                'titleAttrs'  => array('data-app' => $this->tab, 'class' => 'item-title w-full'),
                'data-id'     => $itemID,
                'data-lib'    => in_array($setting->type, array('docLib', 'apiLib')) ? $itemID : zget($setting, 'libID', ''),
                'data-type'   => $setting->type,
                'data-parent' => $setting->parentID,
                'data-module' => $moduleName,
                'selected'    => $this->prop('isThinmory') ? $selected : zget($setting, 'active', $selected),
                'actions'     => $this->getActions($setting)
            );

            if($this->prop('isThinmory')) $item = array_merge($item, array(
                'data-wizard' => $setting->wizard,
                'data-order'  => $setting->order,
                'data-grade'  => $setting->grade,
                'data-path'   => $setting->path,
            ));

            $children = zget($setting, 'children', array());
            if(!empty($children))
            {
                $children = $this->buildMenuTree($children, $itemID);
                $item['items'] = $children;
            }

            $parentItems[] = $item;
        }
        return $parentItems;
    }

    private function setMenuTreeProps(): void
    {
        global $app, $lang;
        $this->lang          = $lang;
        $this->tab           = $app->tab;
        $this->rawModule     = $app->rawModule;
        $this->rawMethod     = $app->rawMethod;
        $this->currentModule = $app->moduleName;

        $this->release     = $this->prop('release', 0);
        $this->libID       = $this->prop('libID');
        $this->moduleID    = $this->prop('moduleID');
        $this->modules     = $this->prop('modules');
        $this->linkParams  = $this->prop('linkParams', '%s');
        $this->spaceType   = $this->prop('spaceType', '');
        $this->objectType  = $this->prop('objectType', '');
        $this->objectID    = $this->prop('objectID', 0);
        $this->spaceMethod = $this->prop('spaceMethod');

        if($this->rawModule == 'api' && $this->rawMethod == 'view') $this->spaceType = 'api';
        if(empty($this->modules['project']))
        {
            $this->setProp('items', $this->buildMenuTree(array(), $this->libID));
        }
        else
        {
            $items = array();
            $index = 0;
            foreach($this->modules as $treeType => $modules)
            {
                if($treeType == 'project')
                {
                    $treeTitle = $lang->projectCommon;
                    $treeIcon  = 'project';
                }
                elseif($treeType == 'execution')
                {
                    $treeTitle = $lang->execution->common;
                    $treeIcon  = 'run';
                }
                else
                {
                    $treeTitle = $lang->files;
                    $treeIcon  = 'paper-clip';
                }
                $items[] = array(
                    'text'  => $treeTitle,
                    'icon'  => $treeIcon,
                    'class' => 'project-tree-title ' . ($index > 0 ? 'border-t mt-2 pt-2' : '')
                );

                $items = array_merge($items, $this->buildMenuTree($modules, $this->libID));
                $index ++;
            }
            $this->setProp('items', $items);
        }
    }

    private function getActions($item): array|null
    {
        if(!$this->prop('isThinmory')) return array();
        $versionBtn = array();
        if(isset($item->versions) && $item->versions)
        {
            global $lang;
            $versionTitle = $lang->build->common;
            $versionBtn = array(
                'key'       => 'version',
                'text'      => $versionTitle,
                'hint'      => $versionTitle,
                'className' => 'versions-list',
                'type'      => 'dropdown',
                'dropdown'  => array(
                    'placement' => 'bottom-end',
                    'items'     => array()
                )
            );

            foreach($item->versions as $version)
            {
                if($version->id == $this->release)
                {
                    $versionBtn['text'] = $version->version;
                    $versionBtn['hint'] = $version->version;
                }

                $versionBtn['dropdown']['items'][] = array(
                    'text'   => $version->version,
                    'hint'   => $version->version,
                    'url'    => $this->buildLink($item, $version->id),
                    'active' => $version->id == $this->release
                );
            }
        }

        $moreBtn = array();
        if(!isset($item->hasAction) || $item->hasAction || in_array($item->type, array('mine', 'view', 'collect', 'createdBy', 'editedBy')))
        {
            $actions = $this->getOperateItems($item);
            if($actions)
            {
                $moreBtn = array(
                    'key'      => 'more',
                    'icon'     => 'ellipsis-v',
                    'type'     => 'dropdown',
                    'caret'    => false,
                    'dropdown' => array(
                        'placement' => $this->prop('isThinmory') ? 'bottom-start' : 'bottom-end',
                        'items'     => $actions
                    )
                );
            }
        }

        $actions = array();
        if($versionBtn) $actions[] = $versionBtn;
        if($moreBtn)    $actions[] = $moreBtn;
        return $actions ? $actions : null;
    }

    private function getOperateItems($item): array
    {
        $menus = array();
        if(in_array($item->type, array('docLib', 'apiLib')))
        {
            $itemID     = $item->id ? $item->id : $item->parentID;
            $moduleName = $item->type == 'docLib' ? 'doc' : 'api';
            if(hasPriv($moduleName, 'addCatalog'))
            {
                $menus[] = array(
                    'key'     => 'adddirectory',
                    'icon'    => 'add-directory',
                    'text'    => $this->lang->doc->libDropdown['addModule'],
                    'onClick' => jsRaw("() => addModule({$itemID}, 'child')")
                );
            }

            if(hasPriv($moduleName, 'editLib'))
            {
                $menus[] = array(
                    'key'         => 'editlib',
                    'icon'        => 'edit',
                    'text'        => $this->lang->doc->libDropdown['editLib'],
                    'data-toggle' => 'modal',
                    'data-url'    => createlink($moduleName, 'editLib', "libID={$itemID}")
                );
            }

            if(hasPriv($moduleName, 'deleteLib'))
            {
                $menus[] = array(
                    'key'          => 'dellib',
                    'icon'         => 'trash',
                    'text'         => $this->lang->doc->libDropdown['deleteLib'],
                    'innerClass'   => 'ajax-submit',
                    'data-url'     => createLink($moduleName, 'deleteLib', "libID={$itemID}"),
                    'data-confirm' => $this->lang->{$moduleName}->confirmDeleteLib
                );
            }
        }
        elseif($item->type == 'module')
        {
            $moduleName = $item->objectType == 'api' ? 'api' : 'doc';
            if(hasPriv($moduleName, 'addCatalog'))
            {
                $menus[] = array(
                    'key'     => 'adddirectory',
                    'icon'    => 'add-directory',
                    'text'    => $this->lang->doc->libDropdown['addSameModule'],
                    'onClick' => jsRaw("() => addModule({$item->id}, 'same')")
                );
                $menus[] = array(
                    'key'     => 'addsubdirectory',
                    'icon'    => 'add-directory',
                    'text'    => $this->lang->doc->libDropdown['addSubModule'],
                    'onClick' => jsRaw("() => addModule({$item->id}, 'child')")
                );
            }

            if(hasPriv($moduleName, 'editCatalog'))
            {
                $menus[] = array(
                    'key'  => 'editmodule',
                    'icon' => 'edit',
                    'text' => $this->lang->doc->libDropdown['editModule'],
                    'link' => '',
                    'data-toggle' => 'modal',
                    'data-url'    => createlink($moduleName, 'editCatalog', "moduleID={$item->id}&type=" . ($this->rawModule == 'api' ? 'api' : 'doc'))
                );
            }

            if(hasPriv($moduleName, 'deleteCatalog'))
            {
                $menus[] = array(
                    'key'          => 'delmodule',
                    'icon'         => 'trash',
                    'text'         => $this->lang->doc->libDropdown['delModule'],
                    'innerClass'   => 'ajax-submit',
                    'data-url'     => createLink($moduleName, 'deleteCatalog', "moduleID={$item->id}"),
                    'data-confirm' => $this->lang->doc->confirmDeleteModule
                );
            }
        }
        elseif(isset($item->wizard))
        {
            $menus = $this->getThinkwizardMenus($item);
        }

        return $menus;
    }

    private function getThinkwizardMenus($item): array
    {
        $canAddChild = true;
        if(!empty($item->children))
        {
            foreach($item->children as $child)
            {
                if($child->type == 'question')
                {
                    $canAddChild = false;
                    break;
                }
            }
        }

        $menus = array();
        if($item->type != 'question') $menus[] = array(
            'key'     => 'addNode',
            'icon'    => 'add-chapter',
            'text'    => $this->lang->thinkwizard->designer->treeDropdown['addSameNode'],
            'onClick' => jsRaw("() => addStep({$item->id}, 'same')")
        );
        if($item->grade != 3 && $item->type == 'node' && $canAddChild) $menus[] = array(
            'key'     => 'addNode',
            'icon'    => 'add-sub-chapter',
            'text'    => $this->lang->thinkwizard->designer->treeDropdown['addChildNode'],
            'onClick' => jsRaw("() => addStep({$item->id}, 'child')")
        );
        $levelType   = $item->type != 'node' ? 'same' : 'child';
        $confirmTips = $this->lang->thinkwizard->step->deleteTips[$item->type];

        $menus = array_merge($menus, array(
            array(
                'key'  => 'editNode',
                'icon' => 'edit',
                'text' => $this->lang->thinkwizard->designer->treeDropdown['edit'],
                'url'  => createLink('thinkwizard', 'design', "wizardID={$item->wizard}&stepID={$item->id}&status=edit")
            ),
            !$item->hasQuestion ? array(
                'key'          => 'deleteNode',
                'icon'         => 'trash',
                'text'         => $this->lang->thinkwizard->designer->treeDropdown['delete'],
                'innerClass'   => 'ajax-submit',
                'data-url'     => createLink('thinkwizard', 'deleteStep', "stepID={$item->id}"),
                'data-confirm' => $confirmTips,
            ) : array(
                'key'            => 'deleteNode',
                'icon'           => 'trash',
                'text'           => $this->lang->thinkwizard->designer->treeDropdown['delete'],
                'innerClass'     => 'text-gray opacity-50',
                'data-toggle'    => 'tooltip',
                'data-title'     => $this->lang->thinkwizard->step->cannotDeleteNode,
                'data-placement' => 'right',
            ),
            array('type' => 'divider'),
            array(
                'key'  => 'addTransition',
                'icon' => 'transition',
                'text' => $this->lang->thinkwizard->designer->treeDropdown['addTransition'],
                'url'  => createLink('thinkwizard', 'design', "wizardID={$item->wizard}&stepID={$item->id}&status=create&addType=transition&levelType=$levelType")
            ),
        ));
        if(empty($item->children) || $item->grade == 3 && $item->type == 'node') $menus = array_merge($menus, array(
            array('type' => 'divider'),
            array(
                'key'  => 'addRadio',
                'icon' => 'radio',
                'text' => $this->lang->thinkwizard->designer->treeDropdown['addRadio'],
                'url'  => createLink('thinkwizard', 'design', "wizardID={$item->wizard}&stepID={$item->id}&status=create&addType=radio&levelType=$levelType")
            ),
            array(
                'key'  => 'addCheckbox',
                'icon' => 'checkbox',
                'text' => $this->lang->thinkwizard->designer->treeDropdown['addCheckbox'],
                'url'  => createLink('thinkwizard', 'design', "wizardID={$item->wizard}&stepID={$item->id}&status=create&addType=checkbox&levelType=$levelType")
            ),
            array(
                'key'  => 'addInput',
                'icon' => 'input',
                'text' => $this->lang->thinkwizard->designer->treeDropdown['addInput'],
                'url'  => createLink('thinkwizard', 'design', "wizardID={$item->wizard}&stepID={$item->id}&status=create&addType=input&levelType=$levelType")
            ),
            array(
                'key'  => 'addTableInput',
                'icon' => 'cell-input',
                'text' => $this->lang->thinkwizard->designer->treeDropdown['addTableInput'],
                'url'  => createLink('thinkwizard', 'design', "wizardID={$item->wizard}&stepID={$item->id}&status=create&addType=tableInput&levelType=$levelType")
            ),
        ));
        return $menus;
    }

    private function getIcon($item): string
    {
        $type = $item->type;
        if($type == 'apiLib')    return 'interface-lib';
        if($type == 'docLib')    return 'wiki-lib';
        if($type == 'annex')     return 'annex-lib';
        if($type == 'execution') return 'execution';
        if($type == 'text')      return 'file-text';
        if($type == 'word')      return 'file-word';
        if($type == 'ppt')       return 'file-powerpoint';
        if($type == 'excel')     return 'file-excel';
        return '';
    }

    private function getTitle(): string
    {
        global $lang;
        $activeKey = $this->prop('activeKey');

        if(empty($activeKey)) return $this->prop('title');

        foreach($this->modules as $module)
        {
            if($module->id == $activeKey) return $module->name;
        }

        return '';
    }

    private function buildBtns(): node|null
    {
        $settingLink = $this->prop('settingLink');
        $settingText = $this->prop('settingText');
        if(!$settingLink) return null;

        global $app;
        $lang = $app->loadLang('datatable')->datatable;
        if(!$settingText) $settingText = $lang->moduleSetting;

        return div
        (
            setClass('col gap-2 py-3 px-7'),
            $settingLink ? a
            (
                setClass('btn'),
                setStyle('background', 'rgb(var(--color-primary-50-rgb))'),
                setStyle('box-shadow', 'none'),
                set('data-app', $app->tab),
                set('data-size', array('width' => '600px', 'height' => '200px')),
                set('data-toggle', 'modal'),
                set('data-class-name', 'doc-setting-modal'),
                set::href($settingLink),
                span
                (
                    setClass('text-primary'),
                    $settingText
                )
            ) : null
        );
    }

    protected function build(): array
    {
        $this->setMenuTreeProps();
        $title     = $this->getTitle();
        $menuLink  = $this->prop('menuLink', '');
        $objectID  = $this->prop('objectID', 0);
        $treeProps = set($this->props->pick(array('items', 'activeClass', 'activeIcon', 'activeKey', 'onClickItem', 'defaultNestedShow', 'changeActiveKey', 'isDropdownMenu', 'hover')));

        $isInSidebar = $this->parent instanceof sidebar;

        $header = h::header
        (
            setClass('doc-menu-header h-10 flex items-center pl-4 flex-none gap-3', $isInSidebar ? 'is-fixed rounded rounded-r-none canvas' : ''),
            span
            (
                setClass('module-title text-lg font-semibold clip'),
                $title
            )
        );
        return array
        (
            $isInSidebar && !$menuLink ? $header : null,
            div
            (
                $menuLink ? dropmenu
                (
                    set::id('docDropmenu'),
                    set::menuID('docDropmenuMenu'),
                    set::objectID($objectID),
                    set::text($title),
                    set::url($menuLink)
                ) : null,
                div
                (
                    setClass('doc-menu rounded shadow ring bg-white col'),
                    h::main
                    (
                        setClass($menuLink ? 'pt-3' : ''),
                        setClass('col flex-auto overflow-y-auto overflow-x-hidden px-1 pb-2'),
                        setStyle('--menu-selected-bg', 'none'),
                        zui::tree(set::_tag('menu'), $treeProps)
                    ),
                    $this->buildBtns()
                ),
                $isInSidebar ? h::js("$('#mainContainer').addClass('has-doc-menu-header')") : null
            )
        );
    }
}
