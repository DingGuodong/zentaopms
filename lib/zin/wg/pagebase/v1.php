<?php
namespace zin;

class pageBase extends wg
{
    static $tag = 'html';

    static $defineProps = array
    (
        'metas?: string|array',
        'title?: string',
        'bodyProps?: array',
        'bodyClass?: array|string',
        'zui?: bool',
        'lang?: string',
        'display?: bool',
        'rawContent?: bool'
    );

    static $defaultProps = array
    (
        'zui' => false,
        'display' => true,
        'metas' => array('<meta charset="utf-8">', '<meta http-equiv="X-UA-Compatible" content="IE=edge">', '<meta name="viewport" content="width=device-width, initial-scale=1">', '<meta name="renderer" content="webkit">')
    );

    static $defineBlocks = array('head' => array());

    protected function created()
    {
        if($this->prop('display')) $this->display();
    }

    protected function buildHead()
    {
        return $this->block('head');
    }

    protected function buildBody()
    {
        return $this->children();
    }

    protected function build()
    {
        global $lang, $config, $app;

        $zui  = $this->prop('zui');
        $head = $this->buildHead();
        $body = $this->buildBody();

        $jsConfig   = \js::getJSConfigVars();
        $bodyProps  = $this->prop('bodyProps');
        $bodyClass  = $this->prop('bodyClass');
        $metas      = $this->prop('metas');
        $rawContent = $this->prop('rawContent', !zin::$rawContentCalled);
        $title      = $this->props->get('title', data('title')) . " - $lang->zentaoPMS";
        $attrs      = $this->getRestProps();
        $css        = array(data('pageCSS'), '/*{{ZIN_PAGE_CSS}}*/');
        $js         = array('/*{{ZIN_PAGE_JS}}*/', data('pageJS'));
        $imports    = context::current()->getImportList();

        $jsConfig->zin = true;
        if($config->debug)
        {
            $js[] = h::createJsVarCode('window.zin', ['page' => $this->toJsonData(), 'definedProps' => wg::$definedPropsMap, 'wgBlockMap' => wg::$wgToBlockMap, 'config' => jsRaw('window.config')]);
            $js[] = 'console.log("[ZIN] ", window.zin)';
        }
        else
        {
            $js[] = h::createJsVarCode('window.zin', array());
        }

        $currentLang = $this->props->get('lang');
        if(empty($currentLang)) $currentLang = $app->getClientLang();
        // $zeneditorPath = $app->getWebRoot() . 'js/zeneditor/tiptap-component.esm.js';
        $zeneditorPath = 'https://zui-dist.oop.cc/zeneditor/tiptap-component.esm.js';

        return h::html
        (
            before(html('<!DOCTYPE html>')),
            set($attrs),
            set::lang($currentLang),
            h::head
            (
                html($metas),
                h::title($title),
                $this->block('headBefore'),
                $zui ? h::importCss($config->zin->zuiPath . 'zui.zentao.css', set::id('zuiCSS')) : null,
                $zui ? h::importJs($config->zin->zuiPath . 'zui.zentao.umd.cjs', set::id('zuiJS')) : null,
                h::jsVar('window.config', $jsConfig, setID('configJS')),
                $zui ? h::importJs($app->getWebRoot() . 'js/zui3/zin.js', set::id('zinJS')) : null,
                html("<script async type=\"module\" src=\"$zeneditorPath\"></script>"),
                $head,
            ),
            h::body
            (
                set($bodyProps),
                set::class($bodyClass),
                empty($imports) ? null : h::import($imports),
                h::css($css, setClass('zin-page-css')),
                $body,
                $rawContent ? rawContent() : null,
                h::js($js, setClass('zin-page-js'))
            )
        );
    }
}
