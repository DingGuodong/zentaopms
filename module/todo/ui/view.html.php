<?php
declare(strict_types=1);
/**
 * The view view file of todo module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Mengyi Liu <liumengyi@easycorp.ltd>
 * @package     todo
 * @link        https://www.zentao.net
 */
namespace zin;

jsVar('selectProduct',   $lang->todo->selectProduct);
jsVar('selectExecution', $lang->execution->selectExecution);
jsVar('todoID',          $todo->id);

$isInModal = isAjaxRequest('modal');

/* Generate title suffix for bug,task,story type. */
$fnGenerateTitleSuffix = function() use($todo)
{
    if($todo->type == 'bug')   return btn(set::url(createLink('bug',   'view', "id={$todo->objectID}")), set::text('  BUG#'   . $todo->objectID), setClass('ghost'));
    if($todo->type == 'task')  return btn(set::url(createLink('task',  'view', "id={$todo->objectID}")), set::text('  TASK#'  . $todo->objectID), setClass('ghost'));
    if($todo->type == 'story') return btn(set::url(createLink('story', 'view', "id={$todo->objectID}")), set::text('  STORY#' . $todo->objectID), setClass('ghost'));
};

/* Render modal for creating story. */
$fnRenderCreateStoryModal = function() use ($lang, $products)
{
    modal
    (
        setID('productModal'),
        set::modalProps(array('title' => $lang->product->select)),
        empty($products) ? div
        (
            setClass('text-center', 'pb-8'),
            span($lang->product->noProduct),
            btn
            (
                $lang->product->create,
                set
                (
                    array(
                        'url'   => createLink('product', 'create'),
                        'id'    => 'createProduct',
                        'class' => 'secondary-pale',
                        'icon'  => 'plus'
                    )
                )
            )
        ) : form
        (
            formGroup
            (
                inputGroup
                (
                    select
                    (
                        on::change('getProgramByProduct(this)'),
                        set
                        (
                            array(
                                'id'       => 'product',
                                'class'    => 'form-control',
                                'name'     => 'product',
                                'items'    => $products,
                                'required' => true
                            )
                        )
                    ),
                    input
                    (
                        set::type('hidden'),
                        set::name('productProgram'),
                        set::value(0)
                    ),
                    btn
                    (
                        on::click('toStory'),
                        set
                        (
                            array(
                                'id'    => 'toStoryButton',
                                'class' => 'primary',
                                'text'  => $lang->todo->reasonList['story']
                            )
                        )
                    )
                )
            ),
            set::actions(array()),
            setClass('pb-6')
        ),
    );
};

/* Render modal for creating task. */
$fnRenderCreateTaskModal = function() use ($lang, $projects, $executions)
{
    modal
    (
        setID('executionModal'),
        set::modalProps(array('title' => $lang->execution->selectExecution)),
        form
        (
            setClass('text-center', 'pb-4'),
            set::actions(array()),
            formGroup
            (
                set::label($lang->todo->project),
                select
                (
                    on::change('getExecutionByProject(this)'),
                    set
                    (
                        array(
                            'id'       => 'project',
                            'name'     => 'project',
                            'items'    => $projects,
                            'required' => true
                        )
                    )
                )
            ),
            formGroup
            (
                set::label($lang->todo->execution),
                select
                (
                    set
                    (
                        array(
                            'id'       => 'execution',
                            'name'     => 'execution',
                            'items'    => $executions,
                            'required' => true
                        )
                    )
                )
            ),
            btn
            (
                $lang->todo->reasonList['task'],
                on::click('toTask'),
                set
                (
                    array(
                        'id'    => 'toTaskButton',
                        'class' => array('primary', 'text-center')
                    )
                )
            )
        )
    );
};

/* Render modal for creating bug. */
$fnRenderCreateBugModal = function() use ($lang, $projects, $projectProducts)
{
    modal
    (
        setID('projectProductModal'),
        set::modalProps(array('title' => $lang->product->select)),
        form
        (
            setClass('text-center', 'pb-4'),
            set::actions(array()),
            formGroup
            (
                set::label($lang->todo->project),
                select
                (
                    on::change('getProductByProject(this)'),
                    set
                    (
                        array(
                            'id'       => 'bugProject',
                            'name'     => 'bugProject',
                            'items'    => $projects,
                            'required' => true
                        )
                    )
                )
            ),
            formGroup
            (
                set::label($lang->todo->product),
                select
                (
                    set
                    (
                        array(
                            'id'       => 'bugProduct',
                            'name'     => 'bugProduct',
                            'items'    => $projectProducts,
                            'required' => true
                        )
                    )
                )
            ),
            btn
            (
                $lang->todo->reasonList['bug'],
                on::click('toBug'),
                set
                (
                    array(
                        'id'    => 'toBugButton',
                        'class' => array('primary', 'text-center'),
                        // 'data-backdrop' => false,
                        'data-toggle' => 'modal',
                        // 'data-type' => 'html',
                    )
                )
            )
        )
    );
};

/* Generate goback url. */
$fnGenerateGoBackUrl = function() use ($app, $todo, $user)
{
    if($this->session->todoList)
    {
        $browseLink = empty($todo->deleted) ? $this->session->todoList : $this->createLink('action', 'trash');
    }
    elseif($todo->account == $app->user->account)
    {
        $browseLink = $this->createLink('my', 'todo');
    }
    else
    {
        $browseLink = $this->createLink('user', 'todo', "userID=$user->id");
    }

    return $browseLink;
};

/* Generate action buttons and related menus within float toolbar. */
$fnGenerateFloatToolbarBtns = function() use ($lang, $config, $todo, $projects, $fnGenerateGoBackUrl, $fnRenderCreateStoryModal, $fnRenderCreateTaskModal, $fnRenderCreateBugModal)
{
    /* Deleted item without action buttons. */
    if($todo->deleted) return array();

    /* Verify privilege of current account. */
    if(!$this->app->user->admin && $this->app->user->account != $todo->account && $this->app->user->account != $todo->assignedTo) return array();

    /* Prepare variables for verifying. */
    $status      = $todo->status;
    $canStart    = hasPriv('todo', 'start');
    $canActivate = hasPriv('todo', 'activate');
    $canClose    = hasPriv('todo', 'close');
    $canEdit     = hasPriv('todo', 'edit');
    $canDelete   = hasPriv('todo', 'delete');

    $actionList = array('prefix' => array(), 'main' => array(), 'suffix' => array());

    $actionList['prefix'][] = array('icon' => 'back', 'url' => $fnGenerateGoBackUrl(), 'hint' => $lang->goback . $lang->backShortcutKey, 'text' => $lang->goback);

    /* Common action buttons. */
    $canStart    && $status == 'wait'                          ? $actionList['main'][] = array('icon' => 'play',  'url' => createLink('todo', 'start',    "todoID={$todo->id}"), 'text' => $lang->todo->abbr->start) : null;
    $canActivate && ($status == 'done' || $status == 'closed') ? $actionList['main'][] = array('icon' => 'magic', 'url' => createLink('todo', 'activate', "todoID={$todo->id}"), 'text' => $lang->activate) : null;
    $canClose    && $status == 'done'                          ? $actionList['main'][] = array('icon' => 'off',   'url' => createLink('todo', 'close',    "todoID={$todo->id}"), 'text' => $lang->close) : null;
    $canEdit                                                   ? $actionList['main'][] = array('icon' => 'edit',  'url' => createLink('todo', 'edit',     "todoID={$todo->id}"), 'text' => $lang->edit) : null;
    $canDelete                                                 ? $actionList['main'][] = array('icon' => 'trash', 'url' => createLink('todo', 'delete',   "todoID={$todo->id}"), 'text' => $lang->delete) : null;

    /* The status is 'done' or 'closed' without more action buttons. */
    if($status == 'done' || $status == 'closed') return $actionList;

    $actionList['main'][] = array('icon' => 'checked', 'url' => createLink('todo', 'finish', "todoID={$todo->id}"), 'text' => $lang->todo->abbr->finish);

    $canCreateStory = hasPriv('story', 'create');
    $canCreateTask  = hasPriv('task',  'create');
    $canCreateBug   = hasPriv('bug',   'create');
    $printBtn       = $config->vision == 'lite' && empty($projects);

    /* Render more button. */
    if($printBtn && ($canCreateStory || $canCreateTask || $canCreateBug))
    {
        $actionList['suffix'][] = array('url' => '#navActions', 'text' => $lang->todo->transform, 'data-toggle' => 'dropdown', 'data-placement' => 'top-end', 'caret' => 'up');
    }

    /* Popup menu of more button. */
    $storyTarget = $canCreateStory && $config->vision == 'lite' ? '#projectModal' : '#productModal';
    menu
    (
        set::id('navActions'),
        setClass('menu dropdown-menu'),
        set::items(array
        (
            $canCreateStory ? array('text' => $lang->todo->reasonList['story'], 'id' => 'toStoryLink', 'data-url' => '###', 'data-toggle' => 'modal', 'data-target' => $storyTarget,           'data-backdrop' => false, 'data-moveable' => true, 'data-position' => 'center') : null,
            $canCreateTask  ? array('text' => $lang->todo->reasonList['task'],  'id' => 'toTaskLink',  'data-url' => '###', 'data-toggle' => 'modal', 'data-target' => '#executionModal',      'data-backdrop' => false, 'data-moveable' => true, 'data-position' => 'center') : null,
            $canCreateBug   ? array('text' => $lang->todo->reasonList['bug'],   'id' => 'toBugLink',   'data-url' => '###', 'data-toggle' => 'modal', 'data-target' => '#projectProductModal', 'data-backdrop' => false, 'data-moveable' => true, 'data-position' => 'center') : null,
        ))
    );

    /* Render popup modal for each more buttons. */
    $canCreateStory && $fnRenderCreateStoryModal();
    $canCreateTask  && $fnRenderCreateTaskModal();
    $canCreateBug   && $fnRenderCreateBugModal();

    return $actionList;
};
$actionList = $fnGenerateFloatToolbarBtns();

/* Generate from data and item. */
$fnGenerateFrom = function() use ($app, $lang, $config, $todo)
{
    if(!in_array($todo->type, array('story', 'task', 'bug')) || empty($todo->object)) return array(null, null);

    /* Generate from data. */
    $app->loadLang($todo->type);
    $objectData = array();
    foreach($config->todo->related[$todo->type]['title'] as $index => $relatedTitle)
    {
        $content = zget($todo->object, $config->todo->related[$todo->type]['content'][$index], '');
        $objectData[] = item
        (
            set::title($lang->{$todo->type}->{$relatedTitle}),
            empty($content) ? $lang->noData : html($content),
        );
    }

    $fromItemData = section
    (
        set::title(zget($lang->todo->fromList, $todo->type)),
        sectionCard
        (
            entityLabel
            (
                set::entityID($todo->objectID),
                set::text($todo->name),
            ),
            $objectData,
        ),
    );

    /* Generate from item. */
    $fromItem = item
    (
        set::name(zget($lang->todo->fromList, $todo->type)),
        a
        (
            set::href(createLink($todo->type, 'view', "id={$todo->objectID}", '', false)),
            set('data-toggle', 'modal'),
            set('data-data-type', 'html'),
            set('data-type', 'ajax'),
            $todo->name,
        ),
    );

    return array($fromItem, $fromItemData);
};
list($fromItem, $fromItemData) = $fnGenerateFrom();

/* Generate cycle configuration information. */
$fnGenerateCycleCfg = function() use ($lang, $todo)
{
    $todo->config = json_decode($todo->config);

    $cfg = '';

    if($todo->config->type == 'day')
    {
        if(isset($todo->config->day)) $cfg .= $lang->todo->every . $todo->config->day . $lang->day;

        if(isset($todo->config->specifiedDate))
        {
            $specifiedNotes = $lang->todo->specify;
            if(isset($todo->config->cycleYear)) $specifiedNotes .= $lang->todo->everyYear;
            $specifiedNotes .= zget($lang->datepicker->monthNames, $todo->config->specify->month) . $todo->config->specify->day . $lang->todo->day;
            $cfg .= $specifiedNotes;
        }
    }
    elseif($todo->config->type == 'week')
    {
        foreach(explode(',', $todo->config->week) as $week) $cfg .= $lang->todo->dayNames[$week] . ' ';
    }
    elseif($todo->config->type == 'month')
    {
        foreach(explode(',', $todo->config->month) as $month) $cfg .= $month . ' ';
    }
    $cfg .= '<br />';
    if($todo->config->beforeDays) $cfg .= sprintf($lang->todo->lblBeforeDays, $todo->config->beforeDays);

    return $cfg;
};

/* ZIN: layout. */
$isInModal && modalHeader();

!$isInModal && detailHeader
(
    $isInModal ? to::prefix('') : '',
    to::title
    (
        entityLabel
        (
            set::entityID($todo->id),
            set::level(1),
            set::text($todo->name)
        ),
        $todo->deleted ? span(setClass('label danger circle'), $lang->todo->deleted) : null
    ),
);

detailBody
(
    sectionList
    (
        section
        (
            set::title($lang->todo->desc),
            set::content(nl2br($todo->desc)),
            set::useHtml(true),
            to::actions($fnGenerateTitleSuffix()),
        ),
        $fromItemData,
        history(set::commentUrl(createLink('action', 'comment', "objectType=todo&objectID=$todo->id"))),

        /* Render float toolbar. */
        $actionList ? center(floatToolbar(set($actionList))) : null
    ),
    detailSide
    (
        /* Basic information. */
        tabs(set::collapse(true), tabPane
        (
            set::key('legendBasic'),
            set::title($lang->todo->legendBasic),
            set::active(true),
            tableData
            (
                item(set::name($lang->todo->pri),    priLabel(zget($lang->todo->priList, $todo->pri))),
                item(set::name($lang->todo->status), zget($lang->todo->statusList, $todo->status)),
                item(set::name($lang->todo->type),   zget($lang->todo->typeList, $todo->type)),

                $fromItem,

                item(set::name($lang->todo->account),     zget($users, $todo->account)),
                item(set::name($lang->todo->date),        formatTime($todo->date, DT_DATE1)),
                item(set::name($lang->todo->beginAndEnd), isset($times[$todo->begin]) ? $times[$todo->begin] : '', isset($times[$todo->end]) ?  ' ~ ' . $times[$todo->end] : ''),

                !isset($todo->assignedTo) ? null : item(set::name($lang->todo->assignedTo),   zget($users, $todo->assignedTo)),
                !isset($todo->assignedTo) ? null : item(set::name($lang->todo->assignedBy),   zget($users, $todo->assignedBy)),
                !isset($todo->assignedTo) ? null : item(set::name($lang->todo->assignedDate), formatTime($todo->assignedDate, DT_DATE1)),
            )
        )),
        /* Cycle information. */
        $todo->cycle ? tabs(set::collapse(true), tabPane
        (
            set::key('cycle'),
            set::title($lang->todo->cycle),
            set::active(true),
            tableData
            (
                item(set::name($lang->todo->beginAndEnd), $todo->config->begin . " ~ " . $todo->config->end),
                item(set::name($lang->todo->cycleConfig), html($fnGenerateCycleCfg())),
            )
        )) : null
    ),
);

render();
