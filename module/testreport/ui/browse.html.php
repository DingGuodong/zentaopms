<?php
declare(strict_types=1);
/**
 * The browse view file of testreport module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Mengyi Liu <liumengyi@easycorp.ltd>
 * @package     testreport
 * @link        https://www.zentao.net
 */
namespace zin;

foreach($reports as $report)
{
    $taskName = '';
    foreach(explode(',', $report->tasks) as $taskID) $taskName .= '#' . $taskID . $tasks[$taskID] . ' ';
    $report->tasks = $taskName;
}

$config->testreport->dtable->fieldList['execution']['map']     = $executions;
$config->testreport->dtable->fieldList['createdBy']['userMap'] = $users;

$tableData = initTableData($reports, $config->testreport->dtable->fieldList['actions'], $this->testreport);

$cols = array_values($config->testreport->dtable->fieldList);
$data = array_values($tableData);

if($app->rawModule != 'testreport' && $app->rawMethod != 'browse' && !isset($lang->{$app->rawModule}->featureBar["{$app->rawMethod}"])) $lang->{$app->rawModule}->featureBar["{$app->rawMethod}"] = $lang->testreport->featureBar['browse'];
featureBar
(
    set::current('all'),
    set::linkParams("objectID=$objectID&objectType=$objectType&extra=$extra&orderBy=$orderBy&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID"),
);
toolbar
(
    btngroup
    (
        btn
        (
            setClass('btn primary'),
            set::icon('plus'),
            set::url(helper::createLink('testreport', 'create', "objectID=0&objectType=testtask&productID={$objectID}")),
            $lang->testreport->create
        )
    )
);

dtable
(
    set::cols($cols),
    set::data($data),
    set::footPager(usePager()),
);

render();

