<?php
declare(strict_types=1);
/**
 * The step5 view file of install module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Mengyi Liu <liumengyi@easycorp.ltd>
 * @package     install
 * @link        https://www.zentao.net
 */
namespace zin;

set::zui(true);

if(!isset($this->config->installed) || !$this->config->installed)
{
    h::js("zui.Modal.alert('{$lang->install->errorNotSaveConfig}').then((res) => {openUrl('" . inlink('step3') . "')});");
    render('pagebase');
    return;
}

div
(
    set::id('main'),
    div
    (
        set::id('mainContent'),
        isset($success) ? panel
        (
            set::title($lang->install->success),
            cell
            (
                icon('check-circle'),
                $afterSuccess,
            ),
        ) : formPanel
        (
            setClass('bg-canvas'),
            set::title($lang->install->getPriv),
            set::formClass('w-96 m-auto'),
            formRow
            (
                formGroup
                (
                    set::label($lang->install->company),
                    set::name('company'),
                ),
            ),
            formRow
            (
                setClass('hidden'),
                formGroup
                (
                    set::label($lang->install->working),
                    set::name('flow'),
                    set::items($lang->install->workingList),
                    set::value('full'),
                ),
            ),
            formRow
            (
                formGroup
                (
                    set::label($lang->install->account),
                    set::name('account'),
                ),
            ),
            formRow
            (
                formGroup
                (
                    set::label($lang->install->password),
                    password
                    (
                        set::name('password'),
                        set::placeholder($lang->install->placeholder->password),
                    )
                ),
            ),
            formRow
            (
                formGroup
                (
                    set::label(' '),
                    checkbox
                    (
                        set::text($lang->install->importDemoData),
                        set::name('importDemoData'),
                    ),
                ),
            ),
        ),
    ),
);

render('pagebase');
