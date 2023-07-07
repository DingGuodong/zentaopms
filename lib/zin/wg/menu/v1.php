<?php
namespace zin;

require_once dirname(__DIR__) . DS . 'actionitem' . DS . 'v1.php';

class menu extends wg
{
    static $defineProps = 'items?:array';

    public function onBuildItem($item)
    {
        if(!($item instanceof item)) $item = item(set($item));
        return actionItem
        (
            set('name', 'menu'),
            inherit($item)
        );
    }

    /**
     * @return builder
     */
    protected function build()
    {
        $items = $this->prop('items');
        return h::menu
        (
            setClass('menu'),
            set($this->getRestProps()),
            is_array($items) ? array_map(array($this, 'onBuildItem'), $this->prop('items')) : null,
            $this->children(),
        );
    }
}
