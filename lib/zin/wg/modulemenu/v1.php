<?php
declare(strict_types=1);
namespace zin;

require_once dirname(__DIR__) . DS . 'btn' . DS . 'v1.php';

class moduleMenu extends wg
{
    private static array $filterMap = array();

    protected static array $defineProps = array(
        'modules: array',
        'activeKey?: int',
        'settingLink?: string',
        'closeLink: string',
        'showDisplay?: bool=true',
        'allText?: string',
        'title?: string'
    );

    public static function getPageCSS(): string|false
    {
        return file_get_contents(__DIR__ . DS . 'css' . DS . 'v1.css');
    }

    private array $modules = array();

    private function buildMenuTree(int|string $parentID = 0): array
    {
        $children = $this->getChildModule($parentID);
        if(count($children) === 0) return [];

        $activeKey = $this->prop('activeKey');
        $treeItems = array();

        foreach($children as $child)
        {
            $item = array(
                'key' => $child->id,
                'text' => $child->name,
                'url' => $child->url
            );
            $items = $this->buildMenuTree($child->id);
            if(count($items) !== 0)      $item['items'] = $items;
            if($child->id == $activeKey) $item['active'] = true;
            $treeItems[] = $item;
        }

        return $treeItems;
    }

    private function getChildModule(int|string $id): array
    {
        return array_filter($this->modules, function($module) use($id)
        {
            if(!isset($module->parent)) return false;

            /* Remove the rendered module. */
            if(isset(static::$filterMap["{$module->parent}-{$module->id}"])) return false;

            if($module->parent != $id) return false;

            static::$filterMap["{$module->parent}-{$module->id}"] = true;
            return true;
        });
    }

    private function setMenuTreeProps(): void
    {
        $this->modules = $this->prop('modules');
        $this->setProp('items', $this->buildMenuTree());
    }

    private function getTitle(): string
    {
        if($this->prop('title')) return $this->prop('title');

        global $lang;
        $activeKey = $this->prop('activeKey');

        if(empty($activeKey))
        {
            $allText = $this->prop('allText');
            if(empty($allText)) return $lang->all;
            return $allText;
        }

        foreach($this->modules as $module)
        {
            if($module->id == $activeKey) return $module->name;
        }

        return '';
    }

    private function buildActions(): wg|null
    {
        $settingLink = $this->prop('settingLink');
        $settingText = $this->prop('settingText');
        $showDisplay = $this->prop('showDisplay');
        if(!$settingLink && !$showDisplay) return null;

        global $app;
        $lang = $app->loadLang('datatable')->datatable;
        $currentModule = $app->rawModule;
        $currentMethod = $app->rawMethod;

        if(!$settingText) $settingText = $lang->moduleSetting;

        $datatableId = $app->moduleName . ucfirst($app->methodName);

        return div
        (
            setClass('col gap-2 py-3 px-7'),
            $settingLink ? btn
            (
                set::type('primary-pale'),
                set::url($settingLink),
                set::size('md'),
                $settingText
            ) : null,
            $showDisplay ? btn
            (
                toggle::modal(),
                set::size('md'),
                set::type('ghost text-gray'),
                set::url(createLink('datatable', 'ajaxDisplay', "datatableId=$datatableId&moduleName=$app->moduleName&methodName=$app->methodName&currentModule=$currentModule&currentMethod=$currentMethod")),
                $lang->displaySetting
            ) : null
        );
    }

    private function buildCloseBtn(): wg|null
    {
        $closeLink = $this->prop('closeLink');
        if(!$closeLink) return null;

        $activeKey = $this->prop('activeKey');
        if(empty($activeKey)) return null;

        return a
        (
            set('href', $closeLink),
            icon('close', setStyle('color', 'var(--color-slate-600)'))
        );
    }

    protected function build(): wg
    {
        $this->setMenuTreeProps();
        $title = $this->getTitle();

        $treeProps = $this->props->pick(array('items', 'activeClass', 'activeIcon', 'activeKey', 'onClickItem', 'defaultNestedShow', 'changeActiveKey', 'isDropdownMenu'));

        return div
        (
            setClass('module-menu shadow-sm rounded bg-canvas col rounded-sm'),
            h::header
            (
                setClass('h-10 flex items-center pl-4 flex-none gap-3'),
                span
                (
                    setClass('module-title text-lg font-semibold'),
                    $title
                ),
                $this->buildCloseBtn(),
            ),
            zui::tree
            (
                set::_class('col flex-auto scrollbar-hover overflow-y-auto overflow-x-hidden pl-4 pr-1'),
                set::nestedShow(true),
                set::hover(true),
                set::preserve(true),
                set($treeProps)
            ),
            $this->buildActions(),
        );
    }
}
