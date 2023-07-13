<?php
declare(strict_types=1);
/**
 * The create view file of job module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Zeng Gang<zenggang@easycorp.ltd>
 * @package     job
 * @link        https://www.zentao.net
 */
namespace zin;

jsVar('frameList', $lang->job->frameList);
jsVar('repoPairs', $repoPairs);
jsVar('gitlabRepos', $gitlabRepos);

if($this->session->repoID)
{
    $repoName = $this->dao->select('name')->from(TABLE_REPO)->where('id')->eq($this->session->repoID)->fetch('name');
    dropmenu(set::objectID($this->session->repoID), set::text($repoName), set::tab('repo'));
}

formPanel
(
    set::title($lang->job->create),
    set::labelWidth('10em'),
    on::click('.add-param', 'addItem'),
    on::click('.delete-param', 'deleteItem'),
    on::click('.custom', 'setValueInput'),
    formGroup
    (
        set::name('name'),
        set::label($lang->job->name),
        set::required(true),
    ),
    formRow
    (
        formGroup
        (
            set::name('engine'),
            set::label($lang->job->engine),
            set::required(true),
            set::items(array('' => '') + $lang->job->engineList),
            set::value(''),
            on::change('changeEngine'),
        ),
        h::span
        (
            set::id('gitlabServerTR'),
            setClass('hidden leading-8 ml-2'),
            html($lang->job->engineTips->success),
        ),
    ),
    formRow
    (
        formGroup
        (
            set::label($lang->job->repo),
            set::required(true),
            set::name('repo'),
            set::items($repoPairs),
            on::change('changeRepo'),
        ),
        formGroup
        (
            setClass('reference hidden'),
            set::name('reference'),
            set::items(array()),
        ),
    ),
    formGroup
    (
        set::name('product'),
        set::label($lang->job->product),
        set::items(array()),
    ),
    formGroup
    (
        set::name('frame'),
        set::label($lang->job->frame),
        set::items(array()),
        on::change('changeFrame'),
    ),
    formRow
    (
        formGroup
        (
            set::name('triggerType'),
            set::label($lang->job->triggerType),
            set::items($lang->job->triggerTypeList),
            on::change('changeTriggerType'),
        ),
    ),
    formRow
    (
        setClass('svn-fields hidden'),
        formGroup
        (
            set::name('svnDir[]'),
            set::label($lang->job->svnDir),
            set::items(array()),
        ),
    ),
    formRow
    (
        setClass('sonarqube hidden'),
        formGroup
        (
            set::name('sonarqubeServer'),
            set::label($lang->job->sonarqubeServer),
            set::items(array('' => '') +$sonarqubeServerList),
            set::value(''),
            set::required(true),
        ),
    ),
    formRow
    (
        set::id('sonarProject'),
        setClass('sonarqube hidden'),
        formGroup
        (
            set::name('projectKey'),
            set::label($lang->job->projectKey),
            set::items(array()),
            set::required(true),
        ),
    ),
    formRow
    (
        setClass('comment-fields hidden'),
        formGroup
        (
            set::name('comment'),
            set::label($lang->job->comment),
            set::required(true),
        ),
        h::span
        (
            setClass('leading-8 ml-2'),
            html($lang->job->commitEx),
        ),
    ),
    formRow
    (
        setClass('custom-fields hidden'),
        formGroup
        (
            set::label(''),
            set::name('atDay'),
            set::control('checkListInline'),
            set::items($lang->datepicker->dayNames),
        ),
    ),
    formRow
    (
        setClass('custom-fields hidden'),
        formGroup
        (
            set::label(''),
            inputGroup
            (
                $lang->job->atTime,
                h::input
                (
                    setClass('form-control'),
                    set::name('atTime'),
                ),
            ),
        ),
    ),
    formRow
    (
        setClass('hidden'),
        set::id('jenkinsServerTR'),
        formGroup
        (
            set::label($lang->job->jkHost),
            set::required(true),
            inputGroup
            (
                picker
                (
                    set::name('jkServer'),
                    set::items($jenkinsServerList),
                    on::change('changeJenkinsServer'),
                ),
                $lang->job->pipeline,
                input
                (
                    set::name('jkTask'),
                    set::type('hidden'),
                ),
                dropmenu
                (
                    setStyle('width', '200px'),
                    set::id('pipelineDropmenu'),
                    set::text($lang->job->selectPipeline),
                    set::data(array('' => '')),
                ),
            ),
        ),
    ),
    formRow
    (
        set::id('paramDiv'),
        formGroup
        (
            set::label($lang->job->customParam),
            inputGroup
            (
                $lang->job->paramName,
                input
                (
                    setStyle('width', '50%'),
                    setClass('form-control'),
                    set::name('paramName[]'),
                ),
                $lang->job->paramValue,
                select
                (
                    setStyle('width', '25%'),
                    setClass('paramValue'),
                    set::name('paramValue[]'),
                    set::items($lang->job->paramValueList),
                ),
                input
                (
                    setStyle('width', '25%'),
                    setClass('form-control hidden paramValue'),
                    set::name('paramValue[]'),
                    set::disabled(true),
                ),
                span
                (
                    setClass('input-group-addon'),
                    checkbox
                    (
                        setClass('custom'),
                        set::name('custom'),
                        set::text($lang->job->custom),
                    ),
                ),
                span
                (
                    setClass('input-group-addon'),
                    h::a
                    (
                        setClass('add-param'),
                        set::href('javascript:void(0)'),
                        icon('plus'),
                    ),
                ),
                span
                (
                    setClass('input-group-addon'),
                    a
                    (
                        setClass('delete-param'),
                        set::href('javascript:void(0)'),
                        icon('close'),
                    ),
                ),
            ),
        ),
    ),
);

render();

