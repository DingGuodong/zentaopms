<?php
declare(strict_types=1);
/**
 * The create view file of mr module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Zeng Gang<zenggang@easycorp.ltd>
 * @package     mr
 * @link        https://www.zentao.net
 */
namespace zin;

h::importJs('js/misc/base64.js');
jsVar('hostType', strtolower($repo->SCM));
jsVar('hostID', $repo->gitService);
jsVar('repo', $repo);
jsVar('mrLang', $lang->mr);
jsVar('branchPrivs', array());

dropmenu(set::objectID($repo->id), set::text($repo->name), set::tab('repo'));

formPanel
(
    set::title($lang->mr->create),
    formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::required(true),
            set::label($lang->mr->sourceProject),
            set::name('sourceProject'),
            set::id('sourceProject'),
            set::items(array($project->id => $project->name_with_namespace)),
            set::disabled(true),
            on::change('onProjectChange'),
            on::change('onSourceProjectChange'),
        ),
        formGroup
        (
            set::labelWidth('5em'),
            set::required(true),
            set::label($lang->mr->sourceBranch),
            set::name('sourceBranch'),
            set::items(array()),
        ),
    ),
    formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::required(true),
            set::label($lang->mr->targetProject),
            set::id('targetProject'),
            set::name('targetProject'),
            set::items(array($project->id => $project->name_with_namespace)),
            set::disabled(true),
            on::change('onProjectChange'),
        ),
        formGroup
        (
            set::labelWidth('6em'),
            set::required(true),
            set::label($lang->mr->targetBranch),
            set::name('targetBranch'),
            set::items(array()),
        ),
    ),
    formGroup
    (
        set::required(true),
        set::name('title'),
        set::label($lang->mr->title),
    ),
    formGroup
    (
        set::width('1/2'),
        set::required(true),
        set::name('assignee'),
        set::label($lang->mr->reviewer),
        set::control('picker'),
        set::items($users),
    ),
    formRow
    (
        formGroup
        (
            set::label($lang->mr->submitType),
            set::name('needCI'),
            set::width('270px'),
            set::control(array('type' => 'checkbox', 'text' => $lang->mr->needCI, 'value' => '1')),
            on::change('onNeedCiChange'),
        ),
        formGroup
        (
            set::name('removeSourceBranch'),
            set::width('150px'),
            set::control(array('type' => 'checkbox', 'text' => $lang->mr->removeSourceBranch)),
        ),
        formGroup
        (
            set::name('squash'),
            set::control(array('type' => 'checkbox', 'text' => $lang->mr->squash)),
        ),
    ),
    formRow
    (
        setClass('hidden'),
        formGroup
        (
            set::width('1/2'),
            set::required(true),
            set::name('jobID'),
            set::label($lang->job->common),
            set::items($jobPairs),
        ),
    ),
    formGroup
    (
        set::name('description'),
        set::label($lang->mr->description),
        set::control('editor'),
    ),
);

render();
