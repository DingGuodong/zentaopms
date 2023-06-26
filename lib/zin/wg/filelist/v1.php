<?php
declare(strict_types=1);
namespace zin;

require_once dirname(__DIR__) . DS . 'section' . DS . 'v1.php';

class fileList extends wg
{
    protected static $defineProps = array(
        'files?:array',
        'fieldset?:bool=true',
        'method?:string="view"',
        'showDelete?:bool=true',
        'showEdit?:bool=true',
        'object?:object',
    );

    public static function getPageCSS(): string|false
    {
        return file_get_contents(__DIR__ . DS . 'css' . DS . 'v1.css');
    }

    public static function getPageJS(): string|false
    {
        return file_get_contents(__DIR__ . DS . 'js' . DS . 'v1.js');
    }

    private function fileList(): wg
    {
        global $app;

        $files        = $this->prop('files');
        $method       = $this->prop('method');
        $showDelete   = $this->prop('showDelete');
        $showEdit     = $this->prop('showEdit');
        $object       = (object)$this->prop('object');
        $fileListView = h::ul(setClass('files-list col relative'));

        jsVar('method', $method);
        jsVar('showDelete', $showDelete);
        jsVar('sessionString', session_name() . '=' . session_id());

        foreach($files as $file)
        {
            $fileItemView = li
                (
                    setClass('mb-2'),
                    html($app->loadTarget('file')->printFile($file, $method, $showDelete, $showEdit, $object))
                );

            $fileListView->add($fileItemView);
        }

        return $fileListView;
    }

    protected function build(): wg
    {
        global $lang;

        $fieldset  = $this->prop('fieldset');
        $isInModal = isAjaxRequest('modal');
        $px = $isInModal ? 'px-3' : 'px-6';
        $pb = $isInModal ? 'pb-3' : 'pb-6';

        return $fieldset ? new section
        (
            setClass('files', 'pt-4', $px, $pb, 'canvas'),
            set::title($lang->files),
            to::actions
            (
                icon('paper-clip'),
            ),
            div($this->fileList()),
        ) : div($this->fileList());
    }
}
