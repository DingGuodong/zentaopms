<?php
declare(strict_types=1);
/**
 * The showsynccommit view file of repo module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Ke Zhao<zhaoke@easycorp.ltd>
 * @package     repo
 * @link        http://www.zentao.net
 */

namespace zin;

featureBar();

if(empty($branch))
{
    $link = helper::createLink('repo', 'ajaxSyncCommit', "repoID=$repoID");
}
else
{
    $link = helper::createLink('repo', 'ajaxSyncBranchCommit', "repoID=$repoID&branch=" . helper::safe64Encode(base64_encode($branch)));
}

jsVar('link',         $link);
jsVar('browseLink',   $browseLink);
jsVar('syncComplete', $lang->repo->notice->syncComplete);

div(
    set::class('content'),
    h3($lang->repo->notice->syncing),
    p($lang->repo->notice->syncedCount, span($version, set::id('commits')))
);

render();
