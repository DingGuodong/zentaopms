<?php
/**
 * The en file of block module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     block
 * @version     $Id$
 * @link        http://www.zentao.net
 */
global $config;
$lang->block->id         = 'ID';
$lang->block->params     = 'Params';
$lang->block->name       = 'Nom';
$lang->block->style      = 'Style';
$lang->block->grid       = 'Position';
$lang->block->color      = 'Couleur';
$lang->block->reset      = 'Réinit';
$lang->block->story      = 'Story';
$lang->block->investment = 'Investment';
$lang->block->estimate   = 'Estimate';
$lang->block->last       = 'Last';

$lang->block->account = 'Compte';
$lang->block->title   = 'Titre';
$lang->block->module  = 'Module';
$lang->block->code    = 'Bloc';
$lang->block->order   = 'Ordre';
$lang->block->height  = 'Hauteur';
$lang->block->role    = 'Rôle';

$lang->block->lblModule    = 'Module';
$lang->block->lblBlock     = 'Bloc';
$lang->block->lblNum       = 'Numéro';
$lang->block->lblHtml      = 'HTML';
$lang->block->html         = 'HTML';
$lang->block->dynamic      = 'Historique';
$lang->block->assignToMe   = 'Todo';
$lang->block->wait         = 'En Attente';
$lang->block->doing        = 'En Cours';
$lang->block->done         = 'Terminées';
$lang->block->lblFlowchart = 'Organigramme';
$lang->block->welcome      = 'Bienvenue';
$lang->block->lblTesttask  = 'Détail Recette';
$lang->block->contribute   = 'Personal Contribution';
$lang->block->finish       = 'Terminé';
$lang->block->guide        = 'Guide';

$lang->block->leftToday           = 'Reste à faire';
$lang->block->myTask              = 'Tâches';
$lang->block->myStory             = 'Stories';
$lang->block->myBug               = 'Bugs';
$lang->block->myExecution         = 'Unclosed ' . $lang->executionCommon . 's';
$lang->block->myProduct           = 'Unclosed ' . $lang->productCommon . 's';
$lang->block->delayed             = 'Ajourné';
$lang->block->noData              = 'Pas de données pour ce type de rapport.';
$lang->block->emptyTip            = 'No data.';
$lang->block->createdTodos        = 'Todos Created';
$lang->block->createdRequirements = 'UR/Epics Created';
$lang->block->createdStories      = 'SR/Stories Created';
$lang->block->finishedTasks       = 'Tasks Finished';
$lang->block->createdBugs         = 'Bugs Created';
$lang->block->resolvedBugs        = 'Bugs Resolved';
$lang->block->createdCases        = 'Cases Created';
$lang->block->createdRisks        = 'Risks Created';
$lang->block->resolvedRisks       = 'Risks Resolved';
$lang->block->createdIssues       = 'Issues Created';
$lang->block->resolvedIssues      = 'Issues Resolved';
$lang->block->createdDocs         = 'Docs Created';
$lang->block->allExecutions       = 'All ' . $lang->executionCommon;
$lang->block->doingExecution      = 'Doning ' . $lang->executionCommon;
$lang->block->finishExecution     = 'Finish ' . $lang->executionCommon;
$lang->block->estimatedHours      = 'Estimated';
$lang->block->consumedHours       = 'Cost';
$lang->block->time                = 'No';
$lang->block->week                = 'Week';
$lang->block->month               = 'Month';
$lang->block->selectProduct       = "{$lang->productCommon} selection";
$lang->block->blockTitle          = '%2%s di %1%s';
$lang->block->remain              = 'Left';
$lang->block->allStories          = 'All';

$lang->block->createBlock        = 'Ajouter Bloc';
$lang->block->editBlock          = 'Editer Bloc';
$lang->block->ordersSaved        = 'L´ordre a été sauvegardé.';
$lang->block->confirmRemoveBlock = 'Voulez-vous vraiment supprimer ce bloc ?';
$lang->block->noticeNewBlock     = 'Une nouvelle disposition est disponible. Voulez-vous la consulter ?';
$lang->block->confirmReset       = 'Voulez-vous vraiment réinitialiser la disposition ?';
$lang->block->closeForever       = 'Fermeture Permanente';
$lang->block->confirmClose       = 'Voulez-vous vraiment fermer de façon permanente ce bloc ? Ensuite, plus personne ne pourra l´utiliser. Il pourra être réactivé par l´admin.';
$lang->block->remove             = 'Supprimer';
$lang->block->refresh            = 'Rafraichir';
$lang->block->nbsp               = ' ';
$lang->block->hidden             = 'Masquer';
$lang->block->dynamicInfo        = "<span class='timeline-tag'>%s</span> <span class='timeline-text'>%s<span class='label-action'>%s</span>%s<a href='%s' title='%s'>%s</a></span>";
$lang->block->noLinkDynamic      = "<span class='timeline-tag'>%s</span> <span class='timeline-text' title='%s'>%s<span class='label-action'>%s</span>%s<span class='label-name'>%s</span></span>";
$lang->block->cannotPlaceInLeft  = 'Impossible de placer le bloc à gauche.';
$lang->block->cannotPlaceInRight = 'Impossible de placer le bloc à droite.';
$lang->block->tutorial           = 'Enter the tutorial';

$lang->block->productName  = $lang->productCommon . ' Name';
$lang->block->totalStory   = 'Total Story';
$lang->block->totalBug     = 'Total Bug';
$lang->block->totalRelease = 'Release The Number';
$lang->block->totalTask    = 'The Total ' . $lang->task->common;

$lang->block->totalInvestment = 'Total investment';
$lang->block->totalPeople     = 'Total';
$lang->block->spent           = 'Has Been Spent';
$lang->block->budget          = 'Budget';
$lang->block->left            = 'Remain';

$lang->block->summary = new stdclass();
$lang->block->summary->welcome = 'Zentao has been with you for %s days. <strong>Yesterday</strong>, you has finished <span class="text-success">%s</span> reviews, <span class="text-success">%s</span> tasks , <span class="text-success">%s</span> bugs were resolved.';

$lang->block->dashboard['default'] = 'Dashboard';
$lang->block->dashboard['my']      = 'My';

$lang->block->titleList['flowchart']      = 'Flow Chart';
$lang->block->titleList['guide']          = 'Guides';
$lang->block->titleList['statistic']      = 'Statistic';
$lang->block->titleList['recentproject']  = "Recent {$lang->projectCommon}";
$lang->block->titleList['assigntome']     = 'Assign to me';
$lang->block->titleList['projectteam']    = "{$lang->projectCommon} manpower input";
$lang->block->titleList['project']        = "{$lang->projectCommon} List";
$lang->block->titleList['dynamic']        = 'Dynamic';
$lang->block->titleList['list']           = 'Todo List';
$lang->block->titleList['contribute']     = 'Contribute';
$lang->block->titleList['scrumoverview']  = 'Scrumoverview';
$lang->block->titleList['scrumtest']      = 'Scrum Test';
$lang->block->titleList['scrumlist']      = 'Scrum List';
$lang->block->titleList['sprint']         = 'Sprint';
$lang->block->titleList['projectdynamic'] = "{$lang->projectCommon} Dynamic";
$lang->block->titleList['bug']            = 'Bug';
$lang->block->titleList['case']           = 'Case';
$lang->block->titleList['testtask']       = 'Test Task';

$lang->block->default['waterfallproject']['3']['title']  = "{$lang->projectCommon}Plan";
$lang->block->default['waterfallproject']['3']['module'] = 'waterfallproject';
$lang->block->default['waterfallproject']['3']['code']   = 'waterfallgantt';
$lang->block->default['waterfallproject']['3']['grid']   = 8;

$lang->block->default['waterfallproject']['6']['title']  = 'New Dynamic';
$lang->block->default['waterfallproject']['6']['module'] = 'waterfallproject';
$lang->block->default['waterfallproject']['6']['code']   = 'projectdynamic';
$lang->block->default['waterfallproject']['6']['grid']   = 4;

$lang->block->default['waterfallplusproject'] = $lang->block->default['waterfallproject'];

$lang->block->default['scrumproject']['1']['title']  = $lang->projectCommon . ' Overview';
$lang->block->default['scrumproject']['1']['module'] = 'scrumproject';
$lang->block->default['scrumproject']['1']['code']   = 'scrumoverview';
$lang->block->default['scrumproject']['1']['grid']   = 8;

$lang->block->default['scrumproject']['2']['title']  = $lang->executionCommon . ' List';
$lang->block->default['scrumproject']['2']['module'] = 'scrumproject';
$lang->block->default['scrumproject']['2']['code']   = 'scrumlist';
$lang->block->default['scrumproject']['2']['grid']   = 8;

$lang->block->default['scrumproject']['2']['params']['type']    = 'undone';
$lang->block->default['scrumproject']['2']['params']['count']   = '20';
$lang->block->default['scrumproject']['2']['params']['orderBy'] = 'id_desc';

$lang->block->default['scrumproject']['3']['title']  = 'Test Version';
$lang->block->default['scrumproject']['3']['module'] = 'scrumproject';
$lang->block->default['scrumproject']['3']['code']   = 'scrumtest';
$lang->block->default['scrumproject']['3']['grid']   = 8;

$lang->block->default['scrumproject']['3']['params']['type']    = 'wait';
$lang->block->default['scrumproject']['3']['params']['count']   = '15';
$lang->block->default['scrumproject']['3']['params']['orderBy'] = 'id_desc';

$lang->block->default['scrumproject']['4']['title']  = $lang->executionCommon . ' Overview';
$lang->block->default['scrumproject']['4']['module'] = 'scrumproject';
$lang->block->default['scrumproject']['4']['code']   = 'sprint';
$lang->block->default['scrumproject']['4']['grid']   = 4;

$lang->block->default['scrumproject']['5']['title']  = 'Dynamic';
$lang->block->default['scrumproject']['5']['module'] = 'scrumproject';
$lang->block->default['scrumproject']['5']['code']   = 'projectdynamic';
$lang->block->default['scrumproject']['5']['grid']   = 4;

$lang->block->default['kanbanproject']    = $lang->block->default['scrumproject'];
$lang->block->default['agileplusproject'] = $lang->block->default['scrumproject'];

$lang->block->default['product']['1']['title']  = $lang->productCommon . ' Statistic';
$lang->block->default['product']['1']['module'] = 'product';
$lang->block->default['product']['1']['code']   = 'statistic';
$lang->block->default['product']['1']['grid']   = 8;

$lang->block->default['product']['1']['params']['type']  = 'all';
$lang->block->default['product']['1']['params']['count'] = '20';

$lang->block->default['product']['2']['title']  = "Vue d'ensemble du " . $lang->productCommon;
$lang->block->default['product']['2']['module'] = 'product';
$lang->block->default['product']['2']['code']   = 'overview';
$lang->block->default['product']['2']['grid']   = 4;

$lang->block->default['product']['3']['title']  = $lang->productCommon . 's Actifs';
$lang->block->default['product']['3']['module'] = 'product';
$lang->block->default['product']['3']['code']   = 'list';
$lang->block->default['product']['3']['grid']   = 8;

$lang->block->default['product']['3']['params']['count'] = 15;
$lang->block->default['product']['3']['params']['type']  = 'noclosed';

$lang->block->default['product']['4']['title']  = 'Mes Stories';
$lang->block->default['product']['4']['module'] = 'product';
$lang->block->default['product']['4']['code']   = 'story';
$lang->block->default['product']['4']['grid']   = 4;

$lang->block->default['product']['4']['params']['count']   = 15;
$lang->block->default['product']['4']['params']['orderBy'] = 'id_desc';
$lang->block->default['product']['4']['params']['type']    = 'assignedTo';

$lang->block->default['execution']['1']['title']  = 'Rapport de execution';
$lang->block->default['execution']['1']['module'] = 'execution';
$lang->block->default['execution']['1']['code']   = 'statistic';
$lang->block->default['execution']['1']['grid']   = 8;

$lang->block->default['execution']['1']['params']['type']  = 'all';
$lang->block->default['execution']['1']['params']['count'] = '20';

$lang->block->default['execution']['2']['title']  = "Vue d'ensemble du execution";
$lang->block->default['execution']['2']['module'] = 'execution';
$lang->block->default['execution']['2']['code']   = 'overview';
$lang->block->default['execution']['2']['grid']   = 4;

$lang->block->default['execution']['3']['title']  = 'Executions Actifs';
$lang->block->default['execution']['3']['module'] = 'execution';
$lang->block->default['execution']['3']['code']   = 'list';
$lang->block->default['execution']['3']['grid']   = 8;

$lang->block->default['execution']['3']['params']['count']   = 15;
$lang->block->default['execution']['3']['params']['orderBy'] = 'id_desc';
$lang->block->default['execution']['3']['params']['type']    = 'undone';

$lang->block->default['execution']['4']['title']  = 'Mes Tâches';
$lang->block->default['execution']['4']['module'] = 'execution';
$lang->block->default['execution']['4']['code']   = 'task';
$lang->block->default['execution']['4']['grid']   = 4;

$lang->block->default['execution']['4']['params']['count']   = 15;
$lang->block->default['execution']['4']['params']['orderBy'] = 'id_desc';
$lang->block->default['execution']['4']['params']['type']    = 'assignedTo';

$lang->block->default['execution']['5']['title']  = 'Build List';
$lang->block->default['execution']['5']['module'] = 'execution';
$lang->block->default['execution']['5']['code']   = 'build';
$lang->block->default['execution']['5']['grid']   = 8;

$lang->block->default['execution']['5']['params']['count']   = 15;
$lang->block->default['execution']['5']['params']['orderBy'] = 'id_desc';

$lang->block->default['qa']['1']['title']  = 'Test Report';
$lang->block->default['qa']['1']['module'] = 'qa';
$lang->block->default['qa']['1']['code']   = 'statistic';
$lang->block->default['qa']['1']['grid']   = 8;

$lang->block->default['qa']['1']['params']['type']  = 'noclosed';
$lang->block->default['qa']['1']['params']['count'] = '20';

$lang->block->default['qa']['2']['title']  = 'My Bugs';
$lang->block->default['qa']['2']['module'] = 'qa';
$lang->block->default['qa']['2']['code']   = 'bug';
$lang->block->default['qa']['2']['grid']   = 4;

$lang->block->default['qa']['2']['params']['count']   = 15;
$lang->block->default['qa']['2']['params']['orderBy'] = 'id_desc';
$lang->block->default['qa']['2']['params']['type']    = 'assignedTo';

$lang->block->default['qa']['3']['title']  = 'Mes CasTests';
$lang->block->default['qa']['3']['module'] = 'qa';
$lang->block->default['qa']['3']['code']   = 'case';
$lang->block->default['qa']['3']['grid']   = 4;

$lang->block->default['qa']['3']['params']['count']   = 15;
$lang->block->default['qa']['3']['params']['orderBy'] = 'id_desc';
$lang->block->default['qa']['3']['params']['type']    = 'assigntome';

$lang->block->default['qa']['4']['title']  = 'Builds en attente';
$lang->block->default['qa']['4']['module'] = 'qa';
$lang->block->default['qa']['4']['code']   = 'testtask';
$lang->block->default['qa']['4']['grid']   = 8;

$lang->block->default['qa']['4']['params']['count']   = 15;
$lang->block->default['qa']['4']['params']['orderBy'] = 'id_desc';
$lang->block->default['qa']['4']['params']['type']    = 'wait';

$lang->block->default['full']['my']['1']['title']  = 'Bienvenue';
$lang->block->default['full']['my']['1']['module'] = 'welcome';
$lang->block->default['full']['my']['1']['code']   = 'welcome';
$lang->block->default['full']['my']['1']['grid']   = 8;

$lang->block->default['full']['my']['2']['title']  = 'Historique';
$lang->block->default['full']['my']['2']['module'] = 'dynamic';
$lang->block->default['full']['my']['2']['code']   = 'dynamic';
$lang->block->default['full']['my']['2']['grid']   = 4;

$lang->block->default['full']['my']['3']['title']  = 'Guides';
$lang->block->default['full']['my']['3']['module'] = 'guide';
$lang->block->default['full']['my']['3']['code']   = 'guide';
$lang->block->default['full']['my']['3']['grid']   = 8;

$lang->block->default['full']['my']['4']['title']  = 'My Todos';
$lang->block->default['full']['my']['4']['module'] = 'todo';
$lang->block->default['full']['my']['4']['code']   = 'list';
$lang->block->default['full']['my']['4']['grid']   = 4;

$lang->block->default['full']['my']['4']['params']['count'] = '20';

$lang->block->default['full']['my']['5']['title']  = "{$lang->projectCommon} Statistic";
$lang->block->default['full']['my']['5']['module'] = 'project';
$lang->block->default['full']['my']['5']['code']   = 'statistic';
$lang->block->default['full']['my']['5']['grid']   = 8;

$lang->block->default['full']['my']['5']['params']['count'] = '20';

$lang->block->default['full']['my']['6']['title']  = 'Personal Contribution';
$lang->block->default['full']['my']['6']['module'] = 'contribute';
$lang->block->default['full']['my']['6']['code']   = 'contribute';
$lang->block->default['full']['my']['6']['grid']   = 4;

$lang->block->default['full']['my']['7']['title']  = "Recent {$lang->projectCommon}s";
$lang->block->default['full']['my']['7']['module'] = 'project';
$lang->block->default['full']['my']['7']['code']   = 'recentproject';
$lang->block->default['full']['my']['7']['grid']   = 8;

$lang->block->default['full']['my']['8']['title']  = 'Todo';
$lang->block->default['full']['my']['8']['module'] = 'assigntome';
$lang->block->default['full']['my']['8']['code']   = 'assigntome';
$lang->block->default['full']['my']['8']['grid']   = 8;

$lang->block->default['full']['my']['8']['params']['todoCount']     = '20';
$lang->block->default['full']['my']['8']['params']['taskCount']     = '20';
$lang->block->default['full']['my']['8']['params']['bugCount']      = '20';
$lang->block->default['full']['my']['8']['params']['riskCount']     = '20';
$lang->block->default['full']['my']['8']['params']['issueCount']    = '20';
$lang->block->default['full']['my']['8']['params']['storyCount']    = '20';
$lang->block->default['full']['my']['8']['params']['reviewCount']   = '20';
$lang->block->default['full']['my']['8']['params']['meetingCount']  = '20';
$lang->block->default['full']['my']['8']['params']['feedbackCount'] = '20';

$lang->block->default['full']['my']['9']['title']  = 'Manpower Input';
$lang->block->default['full']['my']['9']['module'] = 'project';
$lang->block->default['full']['my']['9']['code']   = 'projectteam';
$lang->block->default['full']['my']['9']['grid']   = 8;

$lang->block->default['full']['my']['10']['title']  = "{$lang->projectCommon} List";
$lang->block->default['full']['my']['10']['module'] = 'project';
$lang->block->default['full']['my']['10']['code']   = 'project';
$lang->block->default['full']['my']['10']['grid']   = 8;

$lang->block->default['full']['my']['10']['params']['orderBy'] = 'id_desc';
$lang->block->default['full']['my']['10']['params']['count']   = '15';

/* Doc module block. */
$lang->block->default['doc']['1']['title']  = 'Statistic';
$lang->block->default['doc']['1']['module'] = 'doc';
$lang->block->default['doc']['1']['code']   = 'docstatistic';
$lang->block->default['doc']['1']['grid']   = 8;

$lang->block->default['doc']['2']['title']  = 'Dynamic';
$lang->block->default['doc']['2']['module'] = 'doc';
$lang->block->default['doc']['2']['code']   = 'docdynamic';
$lang->block->default['doc']['2']['grid']   = 4;

$lang->block->default['doc']['3']['title']  = 'My Collection Document';
$lang->block->default['doc']['3']['module'] = 'doc';
$lang->block->default['doc']['3']['code']   = 'docmycollection';
$lang->block->default['doc']['3']['grid']   = 8;

$lang->block->default['doc']['4']['title']  = 'Recently Update Document';
$lang->block->default['doc']['4']['module'] = 'doc';
$lang->block->default['doc']['4']['code']   = 'docrecentupdate';
$lang->block->default['doc']['4']['grid']   = 8;

$lang->block->default['doc']['5']['title']  = 'Browse Leaderboard';
$lang->block->default['doc']['5']['module'] = 'doc';
$lang->block->default['doc']['5']['code']   = 'docviewlist';
$lang->block->default['doc']['5']['grid']   = 4;

if($config->vision == 'rnd')
{
    $lang->block->default['doc']['6']['title']  = $lang->productCommon . ' Document';
    $lang->block->default['doc']['6']['module'] = 'doc';
    $lang->block->default['doc']['6']['code']   = 'productdoc';
    $lang->block->default['doc']['6']['grid']   = 8;

    $lang->block->default['doc']['6']['params']['count'] = '20';
}

$lang->block->default['doc']['7']['title']  = 'Favorite Leaderboard';
$lang->block->default['doc']['7']['module'] = 'doc';
$lang->block->default['doc']['7']['code']   = 'doccollectlist';
$lang->block->default['doc']['7']['grid']   = 4;

$lang->block->default['doc']['8']['title']  = $lang->projectCommon . ' Document';
$lang->block->default['doc']['8']['module'] = 'doc';
$lang->block->default['doc']['8']['code']   = 'projectdoc';
$lang->block->default['doc']['8']['grid']   = 8;

$lang->block->default['doc']['8']['params']['count'] = '20';

$lang->block->count   = 'Numéro';
$lang->block->type    = 'Type';
$lang->block->orderBy = 'Trié par';

$lang->block->availableBlocks['todo']        = 'Schedule';
$lang->block->availableBlocks['task']        = 'Tasks';
$lang->block->availableBlocks['bug']         = 'Bugs';
$lang->block->availableBlocks['case']        = 'Cases';
$lang->block->availableBlocks['story']       = 'Stories';
$lang->block->availableBlocks['requirement'] = 'Requirements';
$lang->block->availableBlocks['product']     = $lang->productCommon . 's';
$lang->block->availableBlocks['execution']   = $lang->executionCommon . 's';
$lang->block->availableBlocks['plan']        = 'Plans';
$lang->block->availableBlocks['release']     = 'Releases';
$lang->block->availableBlocks['build']       = 'Builds';
$lang->block->availableBlocks['testtask']    = 'Requests';
$lang->block->availableBlocks['risk']        = 'Risks';
$lang->block->availableBlocks['issue']       = 'Issues';
$lang->block->availableBlocks['meeting']     = 'Meetings';
$lang->block->availableBlocks['feedback']    = 'Feedbacks';
$lang->block->availableBlocks['ticket']      = 'Tickets';

$lang->block->moduleList['product']    = $lang->productCommon;
$lang->block->moduleList['project']    = $lang->projectCommon;
$lang->block->moduleList['execution']  = $lang->execution->common;
$lang->block->moduleList['qa']         = 'Test';
$lang->block->moduleList['todo']       = 'Todo';
$lang->block->moduleList['doc']        = 'Doc';
$lang->block->moduleList['assigntome'] = $lang->block->assignToMe;
$lang->block->moduleList['dynamic']    = $lang->block->dynamic;
$lang->block->moduleList['guide']      = $lang->block->guide;
$lang->block->moduleList['welcome']    = $lang->block->welcome;
$lang->block->moduleList['html']       = $lang->block->html;
$lang->block->moduleList['contribute'] = $lang->block->contribute;

$lang->block->modules['project'] = new stdclass();
$lang->block->modules['project']->availableBlocks['project']       = "{$lang->projectCommon} List";
$lang->block->modules['project']->availableBlocks['recentproject'] = "Recent {$lang->projectCommon}";
$lang->block->modules['project']->availableBlocks['statistic']     = "{$lang->projectCommon} Statistic";
$lang->block->modules['project']->availableBlocks['projectteam']   = "{$lang->projectCommon} Manpower Input";

$lang->block->modules['scrumProject'] = new stdclass();
$lang->block->modules['scrumProject']->availableBlocks['scrumoverview']  = "{$lang->projectCommon} Overview";
$lang->block->modules['scrumProject']->availableBlocks['scrumlist']      = $lang->executionCommon . ' List';
$lang->block->modules['scrumProject']->availableBlocks['sprint']         = $lang->executionCommon . ' Overview';
$lang->block->modules['scrumProject']->availableBlocks['scrumtest']      = 'Test Version';
$lang->block->modules['scrumProject']->availableBlocks['projectdynamic'] = 'Dynamics';

$lang->block->modules['waterfallProject'] = new stdclass();
$lang->block->modules['waterfallProject']->availableBlocks['waterfallgantt'] = "{$lang->projectCommon} Plan";
$lang->block->modules['waterfallProject']->availableBlocks['projectdynamic'] = 'Dynamics';

$lang->block->modules['agileplus']     = $lang->block->modules['scrumProject'];
$lang->block->modules['waterfallplus'] = $lang->block->modules['waterfallProject'];

$lang->block->modules['product'] = new stdclass();
$lang->block->modules['product']->availableBlocks['statistic'] = $lang->productCommon . ' Report';
$lang->block->modules['product']->availableBlocks['overview']  = $lang->productCommon . ' Overview';
$lang->block->modules['product']->availableBlocks['list']      = $lang->productCommon . ' List';
$lang->block->modules['product']->availableBlocks['story']     = 'Story';
$lang->block->modules['product']->availableBlocks['plan']      = 'Plan';
$lang->block->modules['product']->availableBlocks['release']   = 'Release';

$lang->block->modules['execution'] = new stdclass();
$lang->block->modules['execution']->availableBlocks['statistic'] = $lang->execution->common . ' Statistiques';
$lang->block->modules['execution']->availableBlocks['overview']  = $lang->execution->common . " Vue d'ensemble";
$lang->block->modules['execution']->availableBlocks['list']      = $lang->execution->common . ' Liste';
$lang->block->modules['execution']->availableBlocks['task']      = 'Tâches';
$lang->block->modules['execution']->availableBlocks['build']     = 'Build';

$lang->block->modules['qa'] = new stdclass();
$lang->block->modules['qa']->availableBlocks['statistic'] = 'Rapport de Tests';
$lang->block->modules['qa']->availableBlocks['bug']       = 'Bug';
$lang->block->modules['qa']->availableBlocks['case']      = 'CasTests';
$lang->block->modules['qa']->availableBlocks['testtask']  = 'Build';

$lang->block->modules['todo'] = new stdclass();
$lang->block->modules['todo']->availableBlocks['list'] = 'Todo';

$lang->block->modules['doc'] = new stdclass();
$lang->block->modules['doc']->availableBlocks['docstatistic']    = 'Statistic';
$lang->block->modules['doc']->availableBlocks['docdynamic']      = 'Dynamic';
$lang->block->modules['doc']->availableBlocks['docmycollection'] = 'My Collection';
$lang->block->modules['doc']->availableBlocks['docrecentupdate'] = 'Recently Update';
$lang->block->modules['doc']->availableBlocks['docviewlist']     = 'Browse Leaderboard';
if($config->vision == 'rnd') $lang->block->modules['doc']->availaableBlocks['productdoc'] = $lang->productCommon . 'Document';
$lang->block->modules['doc']->availableBlocks['doccollectlist']  = 'Favorite Leaderboard';
$lang->block->modules['doc']->availableBlocks['projectdoc']      = $lang->projectCommon . 'Document';

$lang->block->orderByList = new stdclass();

$lang->block->orderByList->product = array();
$lang->block->orderByList->product['id_asc']      = 'Tri par ID Produit ASC';
$lang->block->orderByList->product['id_desc']     = 'Tri par ID Produit DESC';
$lang->block->orderByList->product['status_asc']  = 'Tri par Statut ASC';
$lang->block->orderByList->product['status_desc'] = 'Tri par Statut DESC';

$lang->block->orderByList->project = array();
$lang->block->orderByList->project['id_asc']      = "{$lang->projectCommon} ID ASC";
$lang->block->orderByList->project['id_desc']     = "{$lang->projectCommon} ID DESC";
$lang->block->orderByList->project['status_asc']  = "{$lang->projectCommon} Status ASC";
$lang->block->orderByList->project['status_desc'] = "{$lang->projectCommon} Status DESC";

$lang->block->orderByList->execution = array();
$lang->block->orderByList->execution['id_asc']      = 'Tri par ID Execution ASC';
$lang->block->orderByList->execution['id_desc']     = 'Tri par ID Execution DESC';
$lang->block->orderByList->execution['status_asc']  = 'Tri par Statut ASC';
$lang->block->orderByList->execution['status_desc'] = 'Tri par Statut DESC';

$lang->block->orderByList->task = array();
$lang->block->orderByList->task['id_asc']        = 'Tri par ID Tâche ASC';
$lang->block->orderByList->task['id_desc']       = 'Tri par ID Tâche DESC';
$lang->block->orderByList->task['pri_asc']       = 'Tri par Priorité de tâche ASC';
$lang->block->orderByList->task['pri_desc']      = 'Tri par Priorité de tâche DESC';
$lang->block->orderByList->task['estimate_asc']  = 'Tri par durée estimée tâche ASC';
$lang->block->orderByList->task['estimate_desc'] = 'Tri par durée estimée tâche DESC';
$lang->block->orderByList->task['status_asc']    = 'Tri par Statut ASC';
$lang->block->orderByList->task['status_desc']   = 'Tri par Statut DESC';
$lang->block->orderByList->task['deadline_asc']  = 'Tri par Date Butoir Tâche ASC';
$lang->block->orderByList->task['deadline_desc'] = 'Tri par Date Butoir Tâche DESC';

$lang->block->orderByList->bug = array();
$lang->block->orderByList->bug['id_asc']        = 'Tri par ID Bug ASC';
$lang->block->orderByList->bug['id_desc']       = 'Tri par ID Bug DESC';
$lang->block->orderByList->bug['pri_asc']       = 'Tri par Priorité Bug ASC';
$lang->block->orderByList->bug['pri_desc']      = 'Tri par Priorité Bug DESC';
$lang->block->orderByList->bug['severity_asc']  = 'Tri par Sévérité Bug ASC';
$lang->block->orderByList->bug['severity_desc'] = 'Tri par Sévérité Bug DESC';

$lang->block->orderByList->case = array();
$lang->block->orderByList->case['id_asc']   = 'Tri par ID CasTest ASC';
$lang->block->orderByList->case['id_desc']  = 'Tri par ID CasTest DESC';
$lang->block->orderByList->case['pri_asc']  = 'Tri par Priorité ASC';
$lang->block->orderByList->case['pri_desc'] = 'Tri par Priorité DESC';

$lang->block->orderByList->story = array();
$lang->block->orderByList->story['id_asc']      = 'Tri par ID Story ASC';
$lang->block->orderByList->story['id_desc']     = 'Tri par ID Story DESC';
$lang->block->orderByList->story['pri_asc']     = 'Tri par Priorité Story ASC';
$lang->block->orderByList->story['pri_desc']    = 'Tri par Priorité Story DESC';
$lang->block->orderByList->story['status_asc']  = 'Tri par Statut Story ASC';
$lang->block->orderByList->story['status_desc'] = 'Tri par Statut Story DESC';
$lang->block->orderByList->story['stage_asc']   = 'Tri par Phase Story ASC';
$lang->block->orderByList->story['stage_desc']  = 'Tri par Phase Story DESC';

$lang->block->todoCount     = 'Todo';
$lang->block->taskCount     = 'Task';
$lang->block->bugCount      = 'Bug';
$lang->block->riskCount     = 'Risk';
$lang->block->issueCount    = 'Issues';
$lang->block->storyCount    = 'Stories';
$lang->block->reviewCount   = 'Reviews';
$lang->block->meetingCount  = 'Meetings';
$lang->block->feedbackCount = 'Feedbacks';
$lang->block->ticketCount   = 'Tickets';

$lang->block->typeList = new stdclass();

$lang->block->typeList->task['assignedTo'] = 'Tâches qui me sont assignées';
$lang->block->typeList->task['openedBy']   = "Tâches que j'ai créées";
$lang->block->typeList->task['finishedBy'] = "Tâches que j'ai terminées";
$lang->block->typeList->task['closedBy']   = "Tâches que j'ai fermées";
$lang->block->typeList->task['canceledBy'] = "Tâches que j'ai annulées";

$lang->block->typeList->bug['assignedTo'] = 'Bugs qui me sont assignés';
$lang->block->typeList->bug['openedBy']   = "Bugs que j'ai détectés";
$lang->block->typeList->bug['resolvedBy'] = "Bugs que j'ai résolus";
$lang->block->typeList->bug['closedBy']   = "Bugs que j'ai fermés";

$lang->block->typeList->case['assigntome'] = 'CasTests qui me sont assignés';
$lang->block->typeList->case['openedbyme'] = "CasTests que j'ai créés";;

$lang->block->typeList->story['assignedTo'] = 'Stories qui me sont assignées';
$lang->block->typeList->story['reviewBy']   = 'Stories besoins que j’accepte es';
$lang->block->typeList->story['openedBy']   = "Stories que j'ai créées";
$lang->block->typeList->story['reviewedBy'] = "Stories que j'ai acceptées";
$lang->block->typeList->story['closedBy']   = "Stories que j'ai fermées";

$lang->block->typeList->product['noclosed'] = 'Ouverts';
$lang->block->typeList->product['closed']   = 'Fermés';
$lang->block->typeList->product['all']      = 'Tous';
$lang->block->typeList->product['involved'] = 'Impliqués';

$lang->block->typeList->project['undone']   = 'Unfinished';
$lang->block->typeList->project['doing']    = 'Ongoing';
$lang->block->typeList->project['all']      = 'All';
$lang->block->typeList->project['involved'] = 'Involved';

$lang->block->typeList->projectAll['all']       = 'All';
$lang->block->typeList->projectAll['undone']    = 'Undone';
$lang->block->typeList->projectAll['wait']      = 'Wait';
$lang->block->typeList->projectAll['doing']     = 'Doing';
$lang->block->typeList->projectAll['suspended'] = 'Suspended';
$lang->block->typeList->projectAll['closed']    = 'Closed';

$lang->block->typeList->execution['undone']   = 'Unfinished';
$lang->block->typeList->execution['doing']    = 'Ongoing';
$lang->block->typeList->execution['all']      = 'All';
$lang->block->typeList->execution['involved'] = 'Involved';

$lang->block->typeList->scrum['undone']   = 'Unfinished';
$lang->block->typeList->scrum['doing']    = 'Ongoing';
$lang->block->typeList->scrum['all']      = 'All';
$lang->block->typeList->scrum['involved'] = 'Involved';

$lang->block->typeList->testtask['wait']    = 'En attente';
$lang->block->typeList->testtask['doing']   = 'En cours';
$lang->block->typeList->testtask['blocked'] = 'Bloquées';
$lang->block->typeList->testtask['done']    = 'Jouées';
$lang->block->typeList->testtask['all']     = 'Toutes';

$lang->block->typeList->risk['all']      = 'All';
$lang->block->typeList->risk['active']   = 'Active';
$lang->block->typeList->risk['assignTo'] = 'Assign To';
$lang->block->typeList->risk['assignBy'] = 'Assign By';
$lang->block->typeList->risk['closed']   = 'Closed';
$lang->block->typeList->risk['hangup']   = 'Hangup';
$lang->block->typeList->risk['canceled'] = 'Canceled';

$lang->block->typeList->issue['all']      = 'All';
$lang->block->typeList->issue['open']     = 'Open';
$lang->block->typeList->issue['assignto'] = 'Assign To';
$lang->block->typeList->issue['assignby'] = 'Assign By';
$lang->block->typeList->issue['closed']   = 'Closed';
$lang->block->typeList->issue['resolved'] = 'Resolved';
$lang->block->typeList->issue['canceled'] = 'Canceled';

$lang->block->welcomeList['06:00'] = 'Bonjour, %s';
$lang->block->welcomeList['11:30'] = 'Bonjour, %s';
$lang->block->welcomeList['13:30'] = 'Bonjour, %s';
$lang->block->welcomeList['19:00'] = 'Bonsoir, %s';

$lang->block->gridOptions[8] = 'Left';
$lang->block->gridOptions[4] = 'Right';

$lang->block->flowchart            = array();
$lang->block->flowchart['admin']   = array('Administrateur', 'Add Departments', 'Ajoute Utilisateurs', 'Administre Privilèges');
if($config->systemMode == 'ALM') $lang->block->flowchart['program'] = array('Program Owner', 'Create Program', "Link {$lang->productCommon}", "Create {$lang->projectCommon}", "Budgeting and planning", 'Add Stakeholder');
$lang->block->flowchart['product'] = array($lang->productCommon . ' Owner', 'Ajoute ' . $lang->productCommon . '/Modules', 'Ajoute ' . $lang->executionCommon . 's', 'Ajoute Stories', 'Maintient Plans', 'Crée Releases');
$lang->block->flowchart['project'] = array('Project Manager', "Add {$lang->productCommon}s and " . $lang->execution->common . 's', 'Maintain Teams', 'Link Stories', 'Create Tasks', 'Track');
$lang->block->flowchart['dev']     = array('Développeurs', 'Réclament Tâches/Bugs', 'Effectuent Tâches', 'Corrigent Bugs', 'MàJ Statuts', 'Terminent Tâches/Bugs');
$lang->block->flowchart['tester']  = array('Testeurs', 'Rédigent CasTests', 'Jouent CasTests', 'Détectent Bugs', 'Vérifient Corrections', 'Ferment Bugs');

$lang->block->zentaoapp = new stdclass();
$lang->block->zentaoapp->common               = 'ZenTao App';
$lang->block->zentaoapp->thisYearInvestment   = 'Investment The Year';
$lang->block->zentaoapp->sinceTotalInvestment = 'Total Investment';
$lang->block->zentaoapp->myStory              = 'My Story';
$lang->block->zentaoapp->allStorySum          = 'Total Stories';
$lang->block->zentaoapp->storyCompleteRate    = 'Story CompleteRate';
$lang->block->zentaoapp->latestExecution      = 'Latest Execution';
$lang->block->zentaoapp->involvedExecution    = 'Involved Execution';
$lang->block->zentaoapp->mangedProduct        = "Manged {$lang->productCommon}";
$lang->block->zentaoapp->involvedProject      = "Involved {$lang->projectCommon}";
$lang->block->zentaoapp->customIndexCard      = 'Custom Index Cards';
$lang->block->zentaoapp->createStory          = 'Story Create';
$lang->block->zentaoapp->createEffort         = 'Effort Create';
$lang->block->zentaoapp->createDoc            = 'Doc Create';
$lang->block->zentaoapp->createTodo           = 'Todo Create';
$lang->block->zentaoapp->workbench            = 'Workbench';
$lang->block->zentaoapp->notSupportKanban     = 'The mobile terminal does not support the R&D Kanban mode';
$lang->block->zentaoapp->notSupportVersion    = 'This version of ZenTao is not currently supported on the mobile terminal';
$lang->block->zentaoapp->incompatibleVersion  = 'The current version of ZenTao is lower, please upgrade to the latest version and try again';
$lang->block->zentaoapp->canNotGetVersion     = 'Failed to get ZenTao version, please confirm if the URL is correct';
$lang->block->zentaoapp->desc                 = "ZenTao mobile app provides you with a mobile work environment, which is convenient for managing personal to-do tasks at any time, tracking {$lang->projectCommon} progress, and enhancing the flexibility and agility of {$lang->projectCommon} management.";
$lang->block->zentaoapp->downloadTip          = 'Scan QR code to download';

$lang->block->zentaoclient = new stdClass();
$lang->block->zentaoclient->common = 'ZenTao Client';
$lang->block->zentaoclient->desc   = 'The ZenTao client provides functions such as chat, information notification, robot, and embedding ZenTao applet, which makes teamwork more convenient without frequently switching browsers.';

$lang->block->zentaoclient->edition = new stdclass();
$lang->block->zentaoclient->edition->win64   = 'Windows';
$lang->block->zentaoclient->edition->linux64 = 'Linux';
$lang->block->zentaoclient->edition->mac64   = 'Mac OS';

$lang->block->guideTabs['flowchart']      = 'Flowchart';
$lang->block->guideTabs['systemMode']     = 'Operating Modes';
$lang->block->guideTabs['visionSwitch']   = 'Interface Switch';
$lang->block->guideTabs['themeSwitch']    = 'Theme Switch';
$lang->block->guideTabs['preference']     = 'Personalized setting';
$lang->block->guideTabs['downloadClient'] = 'Desktop Client download';
$lang->block->guideTabs['downloadMobile'] = 'Mobile Apps download';

$lang->block->themes['default']    = 'Default';
$lang->block->themes['blue']       = 'Young Blue';
$lang->block->themes['green']      = 'Green';
$lang->block->themes['red']        = 'Red';
$lang->block->themes['pink']       = 'Pink';
$lang->block->themes['blackberry'] = 'Blackberry';
$lang->block->themes['classic']    = 'Classic';
$lang->block->themes['purple']     = 'Purple';

$lang->block->visionTitle            = 'The user interface of ZenTao is divided into 【Full feature interface】 and 【Operation Management Interface】.';
$lang->block->visions['rnd']         = new stdclass();
$lang->block->visions['rnd']->key    = 'rnd';
$lang->block->visions['rnd']->title  = 'Full feature interface';
$lang->block->visions['rnd']->text   = "Integrate the program, {$lang->productCommon}, {$lang->projectCommon}, execution, test, etc., and provide the lifecycle {$lang->projectCommon} management solution.";
$lang->block->visions['lite']        = new stdclass();
$lang->block->visions['lite']->key   = 'lite';
$lang->block->visions['lite']->title = 'Operation Management Interface';
$lang->block->visions['lite']->text  = "Specially designed for Non-R&D teams, and based on the visual Kanban {$lang->projectCommon} management model.";

$lang->block->customModes['light'] = 'Light Mode';
$lang->block->customModes['ALM']   = 'ALM Mode';

$lang->block->customModeTip = new stdClass();
$lang->block->customModeTip->common = 'There are 2 running modes of ZenTao:  Light Mode and ALM Mode.';
$lang->block->customModeTip->ALM    = 'The concept is more complete and rigorous, and the function is more abundant. It is suitable for medium and large R&D teams.';
$lang->block->customModeTip->light  = "Provides the core function of {$lang->projectCommon} management, suitable for small R&D teams.";

$lang->block->productstatistic = new stdclass();
$lang->block->productstatistic->totalStory      = 'Total Story';
$lang->block->productstatistic->closed          = 'Closed';
$lang->block->productstatistic->notClosed       = 'Not Closed';
$lang->block->productstatistic->storyStatistics = 'Story Statistics';
$lang->block->productstatistic->monthDone       = 'Completed this month <span class="text-success font-bold">%s</span>';
$lang->block->productstatistic->monthOpened     = 'Added this month <span class="text-black font-bold">%s</span>';
$lang->block->productstatistic->news            = 'Latest product advancements';
$lang->block->productstatistic->newPlan         = 'Latest Plan';
$lang->block->productstatistic->newExecution    = 'Latest Execution';
$lang->block->productstatistic->newRelease      = 'Latest Release';

$lang->block->projectstatistic->story       = 'Story';
$lang->block->projectstatistic->cost        = 'Cost';
$lang->block->projectstatistic->task        = 'Task';
$lang->block->projectstatistic->bug         = 'Bug';
$lang->block->projectstatistic->storyPoints = 'Story Points';
$lang->block->projectstatistic->done        = 'Done';
$lang->block->projectstatistic->undone      = 'Undone';
$lang->block->projectstatistic->costs       = 'Costs';
$lang->block->projectstatistic->consumed    = 'Consumed';
$lang->block->projectstatistic->remainder   = 'Remainder';
$lang->block->projectstatistic->number      = 'Number';
$lang->block->projectstatistic->wait        = 'Wait';
$lang->block->projectstatistic->doing       = 'Doing';
$lang->block->projectstatistic->resolved    = 'Resolved';
$lang->block->projectstatistic->activated   = 'Activated';
$lang->block->projectstatistic->unit        = 'unit';
$lang->block->projectstatistic->SP          = 'SP';
$lang->block->projectstatistic->personDay   = 'personDay';
$lang->block->projectstatistic->day         = 'day';
$lang->block->projectstatistic->hour        = 'hour';

$lang->block->productoverview = new stdclass();
$lang->block->productoverview->totalProductCount       = 'Total Product Count';
$lang->block->productoverview->productReleasedThisYear = 'Number Of Releases This Year';
$lang->block->productoverview->releaseCount            = 'Total Release Count';

$lang->block->productlist = new stdclass();
$lang->block->productlist->unclosedFeedback  = 'Number Of Feedback Not Closed';
$lang->block->productlist->activatedStory    = 'Activate Requirements';
$lang->block->productlist->storyCompleteRate = 'Requirement Completion Rate';
$lang->block->productlist->activatedBug      = 'Activate Bugs';
