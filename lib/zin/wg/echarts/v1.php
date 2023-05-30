<?php
declare(strict_types=1);
namespace zin;

class echarts extends wg
{
    public static function getPageJS(): string|false
    {
        $echarts = dirname(__FILE__, 5) . DS . implode(DS, array('www', 'js', 'echarts', 'echarts.common.min.js'));
        return file_get_contents($echarts);
    }

    public function size(string|int $width, string|int $height): echarts
    {
        if(is_numeric($width))  $width  = "{$width}px";
        if(is_numeric($height)) $height = "{$height}px";
        $this->setProp('_size', array($width, $height));
        return $this;
    }

    public function theme(string|array $value): echarts
    {
        $this->setProp('theme', $value);
        return $this;
    }

    protected function build()
    {
        return zui::echarts(inherit($this));
    }
}
