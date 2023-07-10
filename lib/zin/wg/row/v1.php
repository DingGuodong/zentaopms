<?php
namespace zin;

class row extends wg
{
    protected static array $defineProps = array(
        'justify?:string',
        'align?:string'
    );

    protected function build()
    {
        $classList = 'row';
        list($justify, $align) = $this->prop(array('justify', 'align'));
        if(!empty($justify)) $classList .= ' justify-' . $justify;
        if(!empty($align))   $classList .= ' items-' . $align;

        return div
        (
            setClass($classList),
            set($this->getRestProps()),
            $this->children()
        );
    }
}
