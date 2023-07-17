<?php
declare(strict_types=1);
namespace zin;

class echarts extends wg
{
    public static function getPageJS(): string|false
    {
        global $app;
        $jsFile = $app->getWebRoot() . 'js/echarts/echarts.common.min.js';
        return 'window.createEcharts=(name,selector,options) => $.getScript("' . $jsFile . '",() => zui.create(name,selector,options));';
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

    public function responsive(bool $value): echarts
    {
        $this->setProp('responsive', $value);
        return $this;
    }

    protected function build(): zui
    {
        return zui::echarts(inherit($this), set::_call('~createEcharts'));
    }
}
