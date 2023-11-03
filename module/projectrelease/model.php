<?php
/**
 * The model file of release module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     release
 * @version     $Id: model.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php
class projectreleaseModel extends model
{
    /**
     * 获取项目发布列表。
     * Get list of releases.
     *
     * @param  int    $projectID
     * @param  string $type
     * @param  string $orderBy
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getList(int $projectID, string $type = 'all', string $orderBy = 't1.date_desc', object $pager = null): array
    {
        $releases = $this->dao->select('t1.*, t2.name AS productName, t2.type AS productType')->from(TABLE_RELEASE)->alias('t1')
            ->leftJoin(TABLE_PRODUCT)->alias('t2')->on('t1.product = t2.id')
            ->where('t1.deleted')->eq('0')
            ->andWhere("FIND_IN_SET($projectID, t1.project)")
            ->beginIF($type != 'all' && $type != 'review')->andWhere('t1.status')->eq($type)->fi()
            ->beginIF($type == 'review')->andWhere("FIND_IN_SET('{$this->app->user->account}', t1.reviewers)")->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();

        $buildIdList   = array();
        $productIdList = array();
        foreach($releases as $release)
        {
            $buildIdList = array_merge($buildIdList, explode(',', $release->build));
            $productIdList[$release->product] = $release->product;
        }

        $branchGroup = $this->loadModel('branch')->getByProducts($productIdList);
        $builds      = $this->dao->select("id, project, product, branch, execution, name, scmPath, filePath")->from(TABLE_BUILD)->where('id')->in(array_unique($buildIdList))->fetchAll('id');

        foreach($releases as $release) $this->projectreleaseTao->processRelease($release, $branchGroup, $builds);

        return $releases;
    }

    /**
     * 获取最新的发布。
     * Get last release.
     *
     * @param  int         $projectID
     * @access public
     * @return bool|object
     */
    public function getLast(int $projectID): object|bool
    {
        return $this->dao->select('id, name')->from(TABLE_RELEASE)
            ->where('deleted')->eq(0)
            ->andWhere("FIND_IN_SET({$projectID}, project)")
            ->orderBy('date DESC')
            ->limit(1)
            ->fetch();
    }

    /**
     * 获取项目已发布的版本。
     * Get released builds from project.
     *
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getReleasedBuilds(int $projectID): array
    {
        /* Get release. */
        $releases = $this->dao->select('shadow,build')->from(TABLE_RELEASE)
            ->where('deleted')->eq(0)
            ->andWhere("FIND_IN_SET({$projectID}, project)")
            ->fetchAll();

        /* Get released builds. */
        $buildIdList = '';
        foreach($releases as $release) $buildIdList .= ",{$release->build},{$release->shadow}";
        $buildIdList = explode(',', trim($buildIdList, ','));
        return array_unique($buildIdList);
    }

    /**
     * Judge btn is clickable or not.
     *
     * @param  int    $release
     * @param  string $action
     * @static
     * @access public
     * @return bool
     */
    public static function isClickable($release, $action)
    {
        $action = strtolower($action);

        if($action == 'notify') return $release->bugs or $release->stories;
        if($action == 'play')   return $release->status == 'terminate';
        if($action == 'pause')  return $release->status == 'normal';
        return true;
    }

    /**
     * Build project release action menu.
     *
     * @param  object $release
     * @param  string $type
     * @access public
     * @return string
     */
    public function buildOperateMenu($release, $type = 'view')
    {
        $function = 'buildOperate' . ucfirst($type) . 'Menu';
        return $this->$function($release);
    }

    /**
     * Build project release view action menu.
     *
     * @param  object $release
     * @access public
     * @return string
     */
    public function buildOperateViewMenu($release)
    {
        $canBeChanged = common::canBeChanged('projectrelease', $release);
        if($release->deleted || !$canBeChanged || isInModal()) return '';

        $menu   = '';
        $params = "releaseID=$release->id";

        if(common::hasPriv('projectrelease', 'changeStatus', $release))
        {
            $changedStatus = $release->status == 'normal' ? 'terminate' : 'normal';
            $menu .= html::a(inlink('changeStatus', "$params&status=$changedStatus"), '<i class="icon-' . ($release->status == 'normal' ? 'pause' : 'play') . '"></i> ' . $this->lang->release->changeStatusList[$changedStatus], 'hiddenwin', "class='btn btn-link' title='{$this->lang->release->changeStatusList[$changedStatus]}'");
        }

        $menu .= "<div class='divider'></div>";
        $menu .= $this->buildFlowMenu('release', $release, 'view', 'direct');
        $menu .= "<div class='divider'></div>";

        $editClickable   = $this->buildMenu('projectrelease', 'edit',   $params, $release, 'view', '', '', '', '', '', '', false);
        $deleteClickable = $this->buildMenu('projectrelease', 'delete', $params, $release, 'view', '', '', '', '', '', '', false);
        if(common::hasPriv('projectrelease', 'edit')   and $editClickable)   $menu .= html::a(helper::createLink('projectrelease', 'edit', $params), "<i class='icon-common-edit icon-edit'></i> " . $this->lang->edit, '', "class='btn btn-link' title='{$this->lang->edit}'");
        if(common::hasPriv('projectrelease', 'delete') and $deleteClickable) $menu .= html::a(helper::createLink('projectrelease', 'delete', $params), "<i class='icon-common-delete icon-trash'></i> " . $this->lang->delete, '', "class='btn btn-link' title='{$this->lang->delete}' target='hiddenwin'");


        return $menu;
    }

    /**
     * Build project release browse action menu.
     *
     * @param  object $release
     * @access public
     * @return string
     */
    public function buildOperateBrowseMenu($release)
    {
        $canBeChanged = common::canBeChanged('projectrelease', $release);
        if(!$canBeChanged) return '';

        $menu          = '';
        $params        = "releaseID=$release->id";
        $changedStatus = $release->status == 'normal' ? 'terminate' : 'normal';

        if(common::hasPriv('projectrelease', 'linkStory')) $menu .= html::a(inlink('view', "$params&type=story&link=true"), '<i class="icon-link"></i> ', '', "class='btn' title='{$this->lang->release->linkStory}'");
        if(common::hasPriv('projectrelease', 'linkBug'))   $menu .= html::a(inlink('view', "$params&type=bug&link=true"),   '<i class="icon-bug"></i> ',  '', "class='btn' title='{$this->lang->release->linkBug}'");
        $menu .= $this->buildMenu('projectrelease', 'changeStatus', "$params&status=$changedStatus", $release, 'browse', $release->status == 'normal' ? 'pause' : 'play', 'hiddenwin', '', '', '',$this->lang->release->changeStatusList[$changedStatus]);
        $menu .= $this->buildMenu('projectrelease', 'edit',   $params, $release, 'browse');
        $menu .= $this->buildMenu('projectrelease', 'notify', $params, $release, 'browse', 'bullhorn', '', 'iframe', true);
        $clickable = $this->buildMenu('projectrelease', 'delete', $params, $release, 'browse', '', '', '', '', '', '', false);
        if(common::hasPriv('projectrelease', 'delete', $release))
        {
            $deleteURL = helper::createLink('projectrelease', 'delete', "$params&confirm=yes");
            $class = 'btn';
            if(!$clickable) $class .= ' disabled';
            $menu .= html::a("javascript:ajaxDelete(\"$deleteURL\", \"releaseList\", confirmDelete)", '<i class="icon-trash"></i>', '', "class='{$class}' title='{$this->lang->release->delete}'");
        }

        return $menu;
    }
}
