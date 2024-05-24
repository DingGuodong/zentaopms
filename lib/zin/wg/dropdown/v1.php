<?php
declare(strict_types=1);
namespace zin;

require_once dirname(__DIR__) . DS . 'menu' . DS . 'v1.php';
require_once dirname(__DIR__) . DS . 'btn' . DS . 'v1.php';

class dropdown extends wg
{
    protected static array $defineProps = array(
        'items?:array',
        'placement?:string',
        'strategy?:string',
        'offset?: int',
        'flip?: bool',
        'arrow?: string',
        'trigger?: string',
        'menu?: array',
        'target?: string',
        'id?: string',
        'menuClass?: string',
        'hasIcons?: bool',
        'staticMenu?: bool',
        'triggerProps?: array',
        'caret?: bool'
    );

    protected static array $defineBlocks = array
    (
        'trigger' => array('map' => 'btn,a'),
        'menu'    => array('map' => 'menu'),
        'items'   => array('map' => 'item')
    );

    protected function build(): array
    {
        list($items, $placement, $strategy, $offset, $flip, $arrow, $trigger, $menuProps, $target, $id, $menuClass, $hasIcons, $staticMenu, $triggerProps, $caret) = $this->prop(array('items', 'placement', 'strategy', 'offset', 'flip', 'arrow', 'trigger', 'menu', 'target', 'id', 'menuClass', 'hasIcons', 'staticMenu', 'triggerProps', 'caret'));

        $triggerBlock = $this->block('trigger');
        $menu         = $this->block('menu');
        $itemsList    = $this->block('items');

        if(empty($id))                      $id        = $this->gid;
        if(empty($target) && empty($items)) $target    = "#$id";
        if(empty($menuProps))               $menuProps = array();
        if(is_null($caret))                 $caret     = true;

        if(empty($triggerBlock))        $triggerBlock = h::a($this->children());
        elseif(is_array($triggerBlock)) $triggerBlock = $triggerBlock[0];
        $triggerID = '';
        if($triggerBlock instanceof node)
        {
            if($triggerBlock instanceof btn) $triggerBlock->setDefaultProps(array('caret' => $caret));
            $triggerProps = array_merge(array
            (
                'data-target'         => $triggerBlock->hasProp('target', 'href') ? null : $target,
                'data-toggle'         => 'dropdown',
                'data-placement'      => $placement,
                'data-strategy'       => $strategy,
                'data-offset'         => $offset,
                'data-flip'           => $flip,
                'data-arrow'          => $arrow,
                'data-trigger'        => $trigger
            ), is_array($triggerProps) ? $triggerProps : array());
            $triggerBlock->setProp($triggerProps);

            $triggerID = $triggerBlock->id();
            if(empty($triggerID))
            {
                $triggerID = "$id-toggle";
                $triggerBlock->setProp('id', $triggerID);
            }
        }

        if(empty($menu))
        {
            if($staticMenu)
            {
                $menu = new menu
                (
                    setClass('dropdown-menu'),
                    set::items($items),
                    divorce($itemsList)
                );

                if($hasIcons === null)
                {
                    if(is_array($items))
                    {
                        foreach($items as $item)
                        {
                            if((is_array($item) and isset($item['icon'])) || (($item instanceof node) && $item->hasProp('icon')))
                            {
                                $hasIcons = true;
                                break;
                            }
                        }
                    }
                    if(!$hasIcons)
                    {
                        foreach($itemsList as $item)
                        {
                            if(($item instanceof node) && $item->hasProp('icon'))
                            {
                                $hasIcons = true;
                                break;
                            }
                        }
                    }
                }
            }
            else
            {
                if(empty($items)) $items = array();
                if(!empty($itemsList))
                {
                    foreach($itemsList as $item)
                    {
                        if(!($item instanceof item)) continue;
                        $items[] = $item->props->toJSON();
                    }
                }
                foreach($items as $index => $item)
                {
                    if($item instanceof setting)
                    {
                        $item = $item->toArray();
                        $items[$index] = $item;
                    }
                    if(!isset($item['icon']) || empty($item['icon']) || str_starts_with($item['icon'], 'icon-')) continue;
                    $items[$index]['icon'] = 'icon-' . $item['icon'];
                }

                $menuProps['items'] = $items;

                $dropdownOptions = array_merge(array
                (
                    'placement'      => $placement,
                    'strategy'       => $strategy,
                    'arrow'          => $arrow,
                    'flip'           => $flip,
                    'offset'         => $offset,
                    'target'         => $target,
                    'className'      => $menuClass,
                    'hasIcons'       => $hasIcons,
                    'menu'           => $menuProps
                ), $this->getRestProps());
                if($triggerBlock instanceof node)
                {
                    $triggerBlock->add(on::init()->call('zui.create', 'dropdown', jsRaw('$element'), $dropdownOptions));
                }
                else
                {
                    $dropdownOptions['_to']     = "#$triggerID";
                    $dropdownOptions['trigger'] = $trigger;
                    $menu = zui::dropdown(set($dropdownOptions));
                }
            }
        }
        elseif(is_array($menu))
        {
            $menu = $menu[0];
        }

        if($menu instanceof menu)
        {
            $menu->setProp($menuProps);
            $menu->setProp('class', $menuClass);
            $menu->setProp('id',    $id);
            if($hasIcons) $menu->setProp('class', 'has-icons');
        }

        return array($triggerBlock, $menu);
    }
}
