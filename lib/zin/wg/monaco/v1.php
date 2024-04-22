<?php
declare(strict_types=1);
namespace zin;

class monaco extends wg
{
    protected static array $defineProps = array(
        'id:string',
        'action?:string',
        'options?:array',
        'diffContent?:array',
        'selectedLines?:string'
    );

    protected static array $defaultProps = array(
        'action'      => 'create',
        'options'     => array(),
        'diffContent' => array()
    );
    public static function getPageJS(): ?string
    {
        return file_get_contents(__DIR__ . DS . 'js' . DS . 'v1.js');
    }

    protected function build()
    {
        global $app;
        $vsPath        = $app->getWebRoot() . 'js/monaco-editor/min/vs';
        $clientLang    = $app->clientLang;
        $id            = $this->prop('id');
        $action        = $this->prop('action');
        $options       = $this->prop('options');
        $diffContent   = $this->prop('diffContent');
        $selectedLines = $this->prop('selectedLines');

        if(!$options) $options = new stdclass();
        return div
        (
            jsVar('id', $id),
            jsVar('action', $action),
            jsVar('options', $options),
            jsVar('diffContent', !$diffContent ? new stdclass() : $diffContent),
            jsVar('vsPath', $vsPath),
            jsVar('clientLang', $clientLang),
            jsVar('selectedLines', $selectedLines),
            h::import($app->getWebRoot() . 'js/monaco-editor/min/vs/loader.js'),
            setID($id)
        );
    }
}
