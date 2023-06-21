<?php
declare(strict_types=1);
namespace zin;

class moduleMenu extends wg
{
    private $modules = array();

    protected static $defineProps = array(
        'modules: array',
        'activeKey?: int',
        'settingLink?: string',
        'closeLink: string',
        'branchType?: string=""',
        'showDisplay?: bool=true',
        'allText?: string'
    );

    public static function getPageCSS(): string|false
    {
        return file_get_contents(__DIR__ . DS . 'css' . DS . 'v1.css');
    }

    private function buildMenuTree(array $parentItems, int|string $parentID): array
    {
        $children = $this->getChildModule($parentID);
        if(count($children) === 0) return [];

        $activeKey = $this->prop('activeKey');

        foreach($children as $child)
        {
            $item = array(
                'key' => $child->id,
                'text' => $child->name,
                'url' => $child->url,
                'items' => array(),
                'active' => $child->id == $activeKey,
            );
            $items = $this->buildMenuTree($item['items'], $child->id);
            if(count($items) !== 0) $item['items'] = $items;
            else unset($item['items']);
            $parentItems[] = $item;
        }
        return $parentItems;
    }

    private function getChildModule(int|string $id): array
    {
        return array_filter($this->modules, fn($module) => $module->parent == $id);
    }

    private function setMenuTreeProps(): void
    {
        $this->modules = $this->prop('modules');
        $this->setProp('items', $this->buildMenuTree(array(), 0));
    }

    private function getTitle(): string
    {
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

    private function buildBtns()
    {
        $settingLink = $this->prop('settingLink');
        $showDisplay = $this->prop('showDisplay');
        $branchType  = $this->prop('branchType');
        if(!$settingLink && !$showDisplay) return null;

        global $app;
        $lang = $app->loadLang('datatable')->datatable;
        $currentModule = $app->rawModule;
        $currentMethod = $app->rawMethod;

        $datatableId = $app->moduleName . ucfirst($app->methodName);

        return div
        (
            setClass('col gap-2 py-3 px-7'),
            $settingLink
                ? a
                (
                    setClass('btn'),
                    setStyle('background', '#EEF5FF'),
                    setStyle('box-shadow', 'none'),
                    set::href($settingLink),
                    $lang->moduleSetting
                )
                : null,
            $showDisplay
                ? a
                (
                    setClass('btn white'),
                    set('data-toggle', 'modal'),
                    set::href(helper::createLink('datatable', 'ajaxDisplay', "datatableId=$datatableId&moduleName=$app->moduleName&methodName=$app->methodName&currentModule=$currentModule&currentMethod=$currentMethod&branchType=$branchType")),
                    $lang->displaySetting
                )
                : null,
        );
    }

    private function buildCloseBtn()
    {
        $activeKey = $this->prop('activeKey');
        if(empty($activeKey)) return null;

        return a
        (
            set('href', $this->prop('closeLink')),
            icon('close', setStyle('color', 'var(--color-slate-600)'))
        );
    }

    protected function build(): wg
    {
        $this->setMenuTreeProps();
        $title = $this->getTitle();


        return div
        (
            setClass('module-menu rounded shadow-sm bg-white col rounded-sm'),
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
            h::main
            (
                setClass('col flex-auto of-y-auto pl-4'),
                zui::tree(set($this->props->pick(array('items', 'activeClass', 'activeIcon', 'activeKey', 'onClickItem', 'defaultNestedShow', 'changeActiveKey', 'isDropdownMenu'))))
            ),
            $this->buildBtns(),
        );
    }
}
