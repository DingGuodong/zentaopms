<?php
declare(strict_types=1);
namespace zin;

class section extends wg
{
    protected static $defineProps = array(
        'title: string',          // 标题
        'content?: string|array', // 内容
        'useHtml?: bool=false'    // 内容是否解析 HTML 标签
    );

    protected static $defineBlocks = array(
        'subTitle' => array(),
        'actions'  => array(),
    );

    protected function onAddChild($child)
    {
        if(is_string($child) && !$this->props->has('content'))
        {
            $this->props->set('content', $child);
            return false;
        }
    }

    private function title(): wg
    {
        $title       = $this->prop('title');
        $actionsView = $this->block('actions');

        if(empty($actionsView)) return div(setClass('article-h2', 'mb-3'), $title);

        return div
        (
            setClass('flex', 'items-center', 'mb-3'),
            div(setClass('article-h2'), $title),
            $actionsView,
        );
    }

    private function headingTag(string $text): wg
    {
        return div
        (
            setClass('article-h4', 'my-1'),
            "[$text]"
        );
    }

    private function content(string $text): wg
    {
        $useHtml = $this->prop('useHtml');

        return div
        (
            setClass('article-content'),
            $useHtml ? html($text) : $text,
        );

    }

    private function buildContent(): wg|array|null
    {
        $content = $this->prop('content');
        if(!isset($content)) return null;

        if(is_string($content)) return $this->content($content);

        return array_map(function($x)
        {
            return div
            (
                setClass('my-4'),
                $this->headingTag($x['title']),
                $this->content($x['content'])
            );
        }, $content);
    }

    protected function build(): wg
    {
        return div
        (
            setClass('section pt-6 px-6 pb-4'),
            set($this->props->skip(array_keys(static::getDefinedProps()))),
            $this->title(),
            $this->block('subTitle'),
            $this->buildContent(),
            $this->children()
        );
    }
}
