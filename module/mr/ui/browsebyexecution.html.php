<?php
declare(strict_types=1);
/**
 * The browse view file of mr module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yanyi Cao <caoyanyi@easycorp.ltd>
 * @package     mr
 * @link        https://www.zentao.net
 */
namespace zin;

dropmenu
(
    set::url(createLink('execution', 'ajaxGetDropMenu', "objectID=$objectID&module={$app->rawModule}&method={$app->rawMethod}"))
);

foreach($MRList as $index => $MR)
{
    if(!isset($repoList[$MR->repoID]))
    {
        unset($MRList[$index]);
        continue;
    }

    $repo = $repoList[$MR->repoID];
    $MR->canDelete = ($app->user->admin or (isset($openIDList[$MR->hostID]) and isset($projects[$MR->hostID][$MR->sourceProject]->owner->id) and $projects[$MR->hostID][$MR->sourceProject]->owner->id == $openIDList[$MR->hostID])) ? '' : 'disabled';
    if($repo->SCM == 'Gitlab')
    {
        $MR->canEdit = (isset($projects[$MR->hostID][$MR->sourceProject]->isDeveloper) and $projects[$MR->hostID][$MR->sourceProject]->isDeveloper == true) ? '' : 'disabled';
    }
    elseif($repo->SCM == 'Gitea')
    {
        $MR->canEdit = (isset($projects[$MR->hostID][$MR->sourceProject]->allow_merge_commits) and $projects[$MR->hostID][$MR->sourceProject]->allow_merge_commits == true) ? '' : 'disabled';
    }
    elseif($repo->SCM == 'Gogs')
    {
        $MR->canEdit = (isset($projects[$MR->hostID][$MR->sourceProject]->permissions->push) and $projects[$MR->hostID][$MR->sourceProject]->permissions->push) ? '' : 'disabled';
    }

    if($repo->SCM == 'Gitlab')
    {
        $MR->sourceProject = isset($projects[$MR->hostID][$MR->sourceProject]) ? $projects[$MR->hostID][$MR->sourceProject]->name_with_namespace  : $MR->sourceProject;
        $MR->targetProject = isset($projects[$MR->hostID][$MR->targetProject]) ? $projects[$MR->hostID][$MR->targetProject]->name_with_namespace  : $MR->targetProject;
    }
    else
    {
        $MR->sourceProject = isset($projects[$MR->hostID][$MR->sourceProject]) ? $projects[$MR->hostID][$MR->sourceProject]->full_name : $MR->sourceProject;
        $MR->targetProject = isset($projects[$MR->hostID][$MR->targetProject]) ? $projects[$MR->hostID][$MR->targetProject]->full_name : $MR->targetProject;
    }

    $MR->mergeStatus = ($MR->status == 'closed' || $MR->status == 'merged') ? zget($lang->mr->statusList, $MR->status) : zget($lang->mr->mergeStatusList, $MR->mergeStatus);

    if($MR->status == 'merged' or $MR->status == 'closed')
    {
        $MR->approvalStatus = '-';
    }
    else
    {
        $MR->approvalStatus = empty($MR->approvalStatus) ? $lang->mr->approvalStatusList['notReviewed'] : $lang->mr->approvalStatusList[$MR->approvalStatus];
    }

    $MR->repoName = $repo->name;
}

/* Show source project column if the user browse the Merge Requests of all the repos. */
if(empty($repoID))
{
    $sourceProject['repoName']['name']     = 'repoName';
    $sourceProject['repoName']['title']    = $lang->mr->sourceProject;
    $sourceProject['repoName']['type']     = 'text';
    $sourceProject['repoName']['minWidth'] = '200';
    $sourceProject['repoName']['hint']     = '{sourceProject}';

    $offset = array_search('sourceBranch', array_keys($config->mr->dtable->fieldList));

    $config->mr->dtable->fieldList = array_slice($config->mr->dtable->fieldList, 0, $offset, true) + $sourceProject + array_slice($config->mr->dtable->fieldList, $offset, NULL, true);
}

$MRs = initTableData($MRList, $config->mr->dtable->fieldList, $this->mr);
$repoData = array(array(
    'text'     => $lang->mr->statusList['all'],
    'data-app' => $app->tab,
    'url'      => createLink('mr', 'browse', "repoID=0&mode={$mode}&param={$param}&objectID={$objectID}"),
    'active'   => !$repoID
));
foreach($repoList as $repo)
{
    if(!in_array($repo->SCM, $this->config->repo->gitServiceTypeList)) continue;

    $repoData[] = array(
        'text'     => $repo->name,
        'data-app' => $app->tab,
        'url'      => createLink('mr', 'browse', "repoID={$repo->id}&mode={$mode}&param={$param}&objectID={$objectID}"),
        'active'   => $repo->id == $repoID
    );
}

featureBar
(
    set::current($mode != 'status' ? $mode : $param),
    set::linkParams("repoID={$repoID}&mode=status&param={key}&objectID={$objectID}"),
    count($repoPairs) > 1 ? to::leading(
        dropdown
        (
            btn(setClass('ghost'), zget($repoPairs, $repoID, $lang->mr->statusList['all'])),
            set::items($repoData),
            set::placement('bottom-end')
        )
    ) : null
);

toolBar
(
    hasPriv('mr', 'create') ? item(
        set::text($lang->mr->create),
        set::icon('plus'),
        set::className('btn primary'),
        set::url(createLink('mr', 'create', "repoID=" . ($repoID ? $repoID : key($repoList)) . "&objectID={$objectID}")),
        set('data-app', $app->tab)
    ) : null
);

dtable
(
    set::userMap($users),
    set::cols($config->mr->dtable->fieldList),
    set::data($MRs),
    set::sortLink(createLink('mr', 'browse', "repoID={$repoID}&mode={$mode}&param={$param}&objectID={$objectID}&orderBy={name}_{sortType}&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}")),
    set::orderBy($orderBy),
    set::footPager(usePager())
);

render();
