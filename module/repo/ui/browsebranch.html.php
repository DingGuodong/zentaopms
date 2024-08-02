<?php
declare(strict_types=1);
/**
 * The browseBranch view file of repo module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yang Li <liyang@easycorp.ltd>
 * @package     repo
 * @link        https://www.zentao.net
 */
namespace zin;

jsVar('searchUrl', createLink('repo', 'browsebranch', "repoID={$repo->id}&objectID={$objectID}&keyword=%s"));
$module = $app->tab == 'devops' ? 'repo' : $app->tab;
dropmenu
(
    set::module($module),
    set::tab($module),
    set::url(createLink($module, 'ajaxGetDropMenu', "objectID=$objectID&module={$app->rawModule}&method={$app->rawMethod}"))
);

featureBar
(
    (in_array($app->tab, array('project', 'execution')) && count($repoPairs) > 1) ? dropmenu
    (
        set::id('repoDropmenu'),
        set::text($repo->name),
        set::objectID($repo->id),
        set::url(createLink('repo', 'ajaxGetDropMenu', "repoID={$repo->id}&module=repo&method=browsebranch&projectID={$objectID}"))
    ) : null,
    div
    (
        setClass('flex'),
        input
        (
            set::placeholder($lang->searchAB),
            set::name('keyword'),
            set::value(base64_decode($keyword))
        ),
        btn
        (
            setClass('primary ml-2'),
            $lang->searchAB,
            on::click('searchList')
        )
    )
);

$config->repo->dtable->branch->fieldList['committer']['map'] = $users;
$branchList = initTableData($branchList, $config->repo->dtable->branch->fieldList);
$urlParams = array(
    'repoID'     => $repo->id,
    'objectID'   => $objectID,
    'keyword'    => urlencode($keyword),
    'orderBy'    => '{name}_{sortType}',
    'recPerPage' => $pager->recPerPage,
    'pageID'     => $pager->pageID
);
dtable
(
    set::cols($config->repo->dtable->branch->fieldList),
    set::data($branchList),
    set::sortLink(createLink('repo', 'browsebranch', $urlParams)),
    set::orderBy($orderBy),
    set::footPager(usePager())
);
