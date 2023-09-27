<?php
/**
 * The control file of release module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     release
 * @version     $Id: control.php 4178 2013-01-20 09:32:11Z wwccss $
 * @link        http://www.zentao.net
 */
class projectrelease extends control
{
    public $products = array();

    /**
     * Construct function, load module auto.
     *
     * @param  string $moduleName
     * @param  string $methodName
     * @access public
     * @return void
     */
    public function __construct($moduleName = '', $methodName = '')
    {
        parent::__construct($moduleName, $methodName);
        $products = array();
        $this->loadModel('product');
        $this->loadModel('release');
        $this->loadModel('project');
    }

    /**
     * 项目发布列表。
     * Browse releases.
     *
     * @param  int    $projectID
     * @param  int    $executionID
     * @param  string $type
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browse(int $projectID = 0, int $executionID = 0, string $type = 'all', string $orderBy = 't1.date_desc', int $recTotal = 0, int $recPerPage = 15, int $pageID = 1)
    {
        /* 设置发布列表和版本列表 session。*/
        /* Set releaseList and buildList session. */
        $uri = $this->app->getURI(true);
        $this->session->set('releaseList', $uri, 'project');
        $this->session->set('buildList', $uri);

        /* 设置菜单。*/
        /* Set menu. */
        if($projectID)   $this->project->setMenu($projectID);
        if($executionID) $this->loadModel('execution')->setMenu($executionID, $this->app->rawModule, $this->app->rawMethod);

        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $releases = $this->projectrelease->getList($projectID, $type, $orderBy, $pager);

        /* 判断是否展示分支。*/
        /* Judge whether to show branch. */
        $showBranch = false;
        foreach($releases as $release)
        {
            if($release->productType != 'normal')
            {
                $showBranch = true;
                break;
            }
        }

        $project   = $this->project->getByID($projectID);
        $execution = $this->loadModel('execution')->getByID($executionID);

        $this->view->title       = (isset($project->name) ? $project->name : $execution->name) . $this->lang->colon . $this->lang->release->browse;
        $this->view->products    = $this->loadModel('product')->getProducts($projectID);
        $this->view->projectID   = $projectID;
        $this->view->executionID = $executionID;
        $this->view->type        = $type;
        $this->view->from        = $this->app->tab;
        $this->view->project     = $project;
        $this->view->execution   = $execution;
        $this->view->releases    = $releases;
        $this->view->pager       = $pager;
        $this->view->orderBy     = $orderBy;
        $this->view->showBranch  = $showBranch;
        $this->display();
    }

    /**
     * 创建一个发布。
     * Create a release.
     *
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function create(int $projectID)
    {
        /* Set create config. */
        $this->config->projectrelease->create = $this->config->release->create;

        if(!empty($_POST))
        {
            $releaseID = $this->release->create(0, 0, $projectID);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $this->loadModel('action')->create('release', $releaseID, 'opened');

            $message = $this->executeHooks($releaseID);
            if($message) $this->lang->saveSuccess = $message;

            if($this->viewType == 'json') return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'id' => $releaseID));

            if(isonlybody()) return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'closeModal' => true));

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('view', "releaseID=$releaseID")));
        }

        /* Set menu. */
        $this->project->setMenu($projectID);
        $this->projectreleaseZen->commonAction($projectID);

        /* Get the builds that can select. */
        $builds         = $this->loadModel('build')->getBuildPairs($this->view->product->id, 'all', 'notrunk|withbranch|hasproject', $projectID, 'project', '', false);
        $releasedBuilds = $this->projectrelease->getReleasedBuilds($projectID);
        foreach($releasedBuilds as $build) unset($builds[$build]);

        $this->view->title       = $this->view->project->name . $this->lang->colon . $this->lang->release->create;
        $this->view->projectID   = $projectID;
        $this->view->builds      = $builds;
        $this->view->lastRelease = $this->projectrelease->getLast($projectID);
        $this->view->users       = $this->loadModel('user')->getPairs('noclosed');
        $this->display('release', 'create');
    }

    /**
     * 编辑一个发布。
     * Edit a release.
     *
     * @param  int    $releaseID
     * @access public
     * @return void
     */
    public function edit(int $releaseID)
    {
        /* Set edit config. */
        $this->config->projectrelease->edit = $this->config->release->edit;

        if(!empty($_POST))
        {
            $changes = $this->release->update($releaseID);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $files = $this->loadModel('file')->saveUpload('release', $releaseID);
            if($changes || $files)
            {
                $fileAction = '';
                if(!empty($files)) $fileAction = $this->lang->addFiles . join(',', $files) . "\n" ;
                $actionID = $this->loadModel('action')->create('release', $releaseID, 'Edited', $fileAction);
                if(!empty($changes)) $this->action->logHistory($actionID, $changes);
            }

            $message = $this->executeHooks($releaseID);
            if($message) $this->lang->saveSuccess = $message;

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('view', "releaseID={$releaseID}")));
        }

        $release = $this->projectrelease->getById($releaseID);

        /* Set menu. */
        if(!$this->session->project)
        {
            $releaseProject = explode(',', $release->project);
            $this->session->set('project', $releaseProject[0], 'project');
        }
        $this->project->setMenu($this->session->project);
        $this->projectreleaseZen->commonAction($this->session->project, $release->product, $release->branch);

        /* Get the builds that can select. */
        $builds         = $this->loadModel('build')->getBuildPairs($release->product, $release->branch, 'notrunk|withbranch|hasproject', $this->session->project, 'project', $release->build, false);
        $bindBuilds     = $this->build->getByList($release->build);
        $releasedBuilds = $this->projectrelease->getReleasedBuilds($this->session->project);
        foreach($releasedBuilds as $releasedBuild)
        {
            if(!isset($bindBuilds[$releasedBuild])) unset($builds[$releasedBuild]);
        }

        $this->view->title   = $this->view->product->name . $this->lang->colon . $this->lang->release->edit;
        $this->view->release = $release;
        $this->view->builds  = $builds;
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->display('release', 'edit');
    }

    /**
     * View a release.
     *
     * @param  int    $releaseID
     * @param  string $type
     * @param  string $link
     * @param  string $param
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function view(int $releaseID, string $type = 'story', string $link = 'false', string $param = '', string $orderBy = 'id_desc', int $recTotal = 0, int $recPerPage = 100, int $pageID = 1)
    {
        echo $this->fetch('release', 'view', "releaseID={$releaseID}&type={$type}&link={$link}&param={$param}&orderBy={$orderBy}&recTotal={$recTotal}&recPerPage={$recPerPage}&pageID={$pageID}");
    }

    /**
     * Notify for release.
     *
     * @param  int    $releaseID
     * @access public
     * @return void
     */
    public function notify($releaseID)
    {
        echo $this->fetch('release', 'notify', "releaseID={$releaseID}&projectID={$this->session->project}");
    }

    /**
     * Delete a release.
     *
     * @param  int    $releaseID
     * @param  string $confirm      yes|no
     * @access public
     * @return void
     */
    public function delete($releaseID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            return print(js::confirm($this->lang->release->confirmDelete, $this->createLink('projectrelease', 'delete', "releaseID=$releaseID&confirm=yes")));
        }
        else
        {
            $this->loadModel('build');
            $this->release->delete(TABLE_RELEASE, $releaseID);

            $release = $this->dao->select('*')->from(TABLE_RELEASE)->where('id')->eq((int)$releaseID)->fetch();
            $builds  = $this->dao->select('*')->from(TABLE_BUILD)->where('id')->in($release->build)->fetchAll('id');
            $this->loadModel('build')->delete(TABLE_BUILD, $release->shadow);
            foreach($builds as $build)
            {
                if(empty($build->execution) and $build->createdDate == $release->createdDate) $this->build->delete(TABLE_BUILD, $build->id);
            }

            $message = $this->executeHooks($releaseID);
            if($message) $response['message'] = $message;

            /* if ajax request, send result. */
            if($this->server->ajax)
            {
                if(dao::isError())
                {
                    $response['result']  = 'fail';
                    $response['message'] = dao::getError();
                }
                else
                {
                    $response['result']  = 'success';
                    $response['message'] = '';
                }
                return $this->send($response);
            }
            return print(js::locate($this->session->releaseList, 'parent'));
        }
    }

    /**
     * Export the stories of release to HTML.
     *
     * @access public
     * @return void
     */
    public function export()
    {
        if(!empty($_POST))
        {
            $type = $this->post->type;
            $html = '';

            if($type == 'story' or $type == 'all')
            {
                $html .= "<h3>{$this->lang->release->stories}</h3>";
                $this->loadModel('story');

                $stories = $this->dbh->query($this->session->storyQueryCondition . " ORDER BY " . strtr($this->session->storyOrderBy, '_', ' '))->fetchAll();

                foreach($stories as $story) $story->title = "<a href='" . common::getSysURL() . $this->createLink('story', 'view', "storyID=$story->id") . "' target='_blank'>$story->title</a>";

                $fields = array('id' => $this->lang->story->id, 'title' => $this->lang->story->title);
                $rows   = $stories;

                $html .= '<table><tr>';
                foreach($fields as $fieldLabel) $html .= "<th><nobr>$fieldLabel</nobr></th>\n";
                $html .= '</tr>';
                foreach($rows as $row)
                {
                    $html .= "<tr valign='top'>\n";
                    foreach($fields as $fieldName => $fieldLabel)
                    {
                        $fieldValue = isset($row->$fieldName) ? $row->$fieldName : '';
                        $html .= "<td><nobr>$fieldValue</nobr></td>\n";
                    }
                    $html .= "</tr>\n";
                }
                $html .= '</table>';
            }

            if($type == 'bug' or $type == 'all')
            {
                $html .= "<h3>{$this->lang->release->bugs}</h3>";
                $this->loadModel('bug');

                $bugs = $this->dao->select('id, title')->from(TABLE_BUG)->where($this->session->linkedBugQueryCondition)
                    ->beginIF($this->session->bugOrderBy != false)->orderBy($this->session->bugOrderBy)->fi()
                    ->fetchAll('id');

                foreach($bugs as $bug) $bug->title = "<a href='" . common::getSysURL() . $this->createLink('bug', 'view', "bugID=$bug->id") . "' target='_blank'>$bug->title</a>";

                $fields = array('id' => $this->lang->bug->id, 'title' => $this->lang->bug->title);
                $rows   = $bugs;

                $html .= '<table><tr>';
                foreach($fields as $fieldLabel) $html .= "<th><nobr>$fieldLabel</nobr></th>\n";
                $html .= '</tr>';
                foreach($rows as $row)
                {
                    $html .= "<tr valign='top'>\n";
                    foreach($fields as $fieldName => $fieldLabel)
                    {
                        $fieldValue = isset($row->$fieldName) ? $row->$fieldName : '';
                        $html .= "<td><nobr>$fieldValue</nobr></td>\n";
                    }
                    $html .= "</tr>\n";
                }
                $html .= '</table>';
            }

            if($type == 'leftbug' or $type == 'all')
            {
                $html .= "<h3>{$this->lang->release->generatedBugs}</h3>";

                $bugs = $this->dao->select('id, title')->from(TABLE_BUG)->where($this->session->leftBugsQueryCondition)
                    ->beginIF($this->session->bugOrderBy != false)->orderBy($this->session->bugOrderBy)->fi()
                    ->fetchAll('id');

                foreach($bugs as $bug) $bug->title = "<a href='" . common::getSysURL() . $this->createLink('bug', 'view', "bugID=$bug->id") . "' target='_blank'>$bug->title</a>";

                $fields = array('id' => $this->lang->bug->id, 'title' => $this->lang->bug->title);
                $rows   = $bugs;

                $html .= '<table><tr>';
                foreach($fields as $fieldLabel) $html .= "<th><nobr>$fieldLabel</nobr></th>\n";
                $html .= '</tr>';
                foreach($rows as $row)
                {
                    $html .= "<tr valign='top'>\n";
                    foreach($fields as $fieldName => $fieldLabel)
                    {
                        $fieldValue = isset($row->$fieldName) ? $row->$fieldName : '';
                        $html .= "<td><nobr>$fieldValue</nobr></td>\n";
                    }
                    $html .= "</tr>\n";
                }
                $html .= '</table>';
            }

            $html = "<html><head><meta charset='utf-8'><title>{$this->post->fileName}</title><style>table, th, td{font-size:12px; border:1px solid gray; border-collapse:collapse;}</style></head><body>$html</body></html>";
            return print($this->fetch('file', 'sendDownHeader', array('fileName' => $this->post->fileName, 'html', $html)));
        }

        $this->display();
    }

    /**
     * Link stories
     *
     * @param  int    $releaseID
     * @param  string $browseType
     * @param  int    $param
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function linkStory($releaseID = 0, $browseType = '', $param = 0, $recTotal = 0, $recPerPage = 100, $pageID = 1)
    {
        if(!empty($_POST['stories']))
        {
            $this->projectrelease->linkStory($releaseID);
            return print(js::locate(inlink('view', "releaseID=$releaseID&type=story"), 'parent'));
        }
        $this->session->set('storyList', inlink('view', "releaseID=$releaseID&type=story&link=true&param=" . helper::safe64Encode("&browseType=$browseType&queryID=$param")), $this->app->tab);

        $release = $this->projectrelease->getByID($releaseID);
        if(!$this->session->project)
        {
            $releaseProject = explode(',', $release->project);
            $this->session->set('project', $releaseProject[0], 'project');
        }

        $builds  = $this->loadModel('build')->getByList($release->build);
        $project = $this->loadModel('project')->getByID($this->session->project);
        $this->projectreleaseZen->commonAction($this->session->project, $release->product);
        $this->loadModel('story');
        $this->loadModel('tree');
        $this->loadModel('product');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        /* Build search form. */
        $queryID = ($browseType == 'bySearch') ? (int)$param : 0;
        unset($this->config->product->search['fields']['product']);
        unset($this->config->product->search['fields']['project']);
        if(!$project->hasProduct and !$project->multiple) unset($this->config->product->search['fields']['plan']);
        $this->config->product->search['actionURL'] = $this->createLink('projectrelease', 'view', "releaseID=$releaseID&type=story&link=true&param=" . helper::safe64Encode('&browseType=bySearch&queryID=myQueryID'));
        $this->config->product->search['queryID']   = $queryID;
        $this->config->product->search['style']     = 'simple';
        $this->config->product->search['params']['plan']['values'] = $this->loadModel('productplan')->getPairs($release->product, $release->branch, 'withMainPlan', true);
        $this->config->product->search['params']['status']         = array('operator' => '=', 'control' => 'select', 'values' => $this->lang->story->statusList);

        $searchModules = array();
        $moduleGroups  = $this->loadModel('tree')->getOptionMenu($release->product, 'story', 0, explode(',', $release->branch));
        foreach($moduleGroups as $modules) $searchModules += $modules;
        $this->config->product->search['params']['module']['values'] = $searchModules;

        if($release->productType == 'normal')
        {
            unset($this->config->product->search['fields']['branch']);
            unset($this->config->product->search['params']['branch']);
        }
        else
        {
            $this->config->product->search['fields']['branch'] = sprintf($this->lang->product->branch, $this->lang->product->branchName[$release->productType]);
            $allBranchs = $this->loadModel('branch')->getPairs($release->product);
            $branches   = array('' => '', BRANCH_MAIN => $this->lang->branch->main);
            foreach(explode(',', trim($release->branch, ',')) as $branchID) $branches[$branchID] = zget($allBranchs, $branchID);
            $this->config->product->search['params']['branch']['values'] = $branches;
        }
        if($this->view->project->model == 'waterfall' && empty($this->view->project->hasProduct)) unset($this->config->product->search['fields']['plan']);
        $this->loadModel('search')->setSearchParams($this->config->product->search);

        $executionIdList = array();
        foreach($builds as $build) $executionIdList[] = empty($build->execution) ? $build->project : $build->execution;
        $executionIdList = array_unique($executionIdList);

        $allStories = array();
        if($browseType == 'bySearch')
        {
            $allStories = $this->story->getBySearch($release->product, $release->branch, $queryID, 'id', $executionIdList, 'story', $release->stories, $pager);
        }
        else
        {
            $allStories = $this->story->batchGetExecutionStories($executionIdList, $release->product, 't1.`order`_desc', 'byBranch', $release->branch, 'story', $release->stories, $pager);
        }

        $this->view->allStories     = $allStories;
        $this->view->release        = $release;
        $this->view->releaseStories = empty($release->stories) ? array() : $this->story->getByList($release->stories);
        $this->view->users          = $this->loadModel('user')->getPairs('noletter');
        $this->view->browseType     = $browseType;
        $this->view->param          = $param;
        $this->view->pager          = $pager;
        $this->display();
    }

    /**
     * Unlink story
     *
     * @param  int    $releaseID
     * @param  int    $storyID
     * @access public
     * @return void
     */
    public function unlinkStory($releaseID, $storyID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            return print(js::confirm($this->lang->release->confirmUnlinkStory, inlink('unlinkstory', "releaseID=$releaseID&storyID=$storyID&confirm=yes")));
        }
        else
        {
            $this->projectrelease->unlinkStory($releaseID, $storyID);
            return print(js::reload('parent'));
        }
    }

    /**
     * Batch unlink story.
     *
     * @param  int    $releaseID
     * @access public
     * @return void
     */
    public function batchUnlinkStory($releaseID)
    {
        $this->loadModel('release')->batchUnlinkStory($releaseID);
        return print(js::locate($this->createLink('projectrelease', 'view', "releaseID=$releaseID&type=story"), 'parent'));
    }

    /**
     * Link bugs.
     *
     * @param  int    $releaseID
     * @param  string $browseType
     * @param  int    $param
     * @param  string $type
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function linkBug($releaseID = 0, $browseType = '', $param = 0, $type = 'bug', $recTotal = 0, $recPerPage = 100, $pageID = 1)
    {
        if(!empty($_POST['bugs']))
        {
            $this->projectrelease->linkBug($releaseID, $type);
            return print(js::locate(inlink('view', "releaseID=$releaseID&type=$type"), 'parent'));
        }

        $this->session->set('bugList', inlink('view', "releaseID=$releaseID&type=$type&link=true&param=" . helper::safe64Encode("&browseType=$browseType&queryID=$param")), 'qa');
        /* Set menu. */
        $release = $this->projectrelease->getByID($releaseID);
        if(!$this->session->project)
        {
            $releaseProject = explode(',', $release->project);
            $this->session->set('project', $releaseProject[0], 'project');
        }

        $builds  = $this->loadModel('build')->getByList($release->build);
        $project = $this->loadModel('project')->getByID($this->session->project);
        $this->projectreleaseZen->commonAction($this->session->project, $release->product);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        /* Build the search form. */
        $this->loadModel('bug');
        $queryID = ($browseType == 'bysearch') ? (int)$param : 0;
        unset($this->config->bug->search['fields']['product']);
        unset($this->config->bug->search['fields']['project']);
        if(!$project->hasProduct and !$project->multiple) unset($this->config->bug->search['fields']['plan']);
        $this->config->bug->search['actionURL'] = $this->createLink('projectrelease', 'view', "releaseID=$releaseID&type=$type&link=true&param=" . helper::safe64Encode('&browseType=bySearch&queryID=myQueryID'));
        $this->config->bug->search['queryID']   = $queryID;
        $this->config->bug->search['style']     = 'simple';
        $this->config->bug->search['params']['plan']['values']          = $this->loadModel('productplan')->getPairs($release->product, $release->branch, 'withMainPlan', true);
        $this->config->bug->search['params']['execution']['values']     = $this->loadModel('product')->getExecutionPairsByProduct($release->product, $release->branch, $release->project);
        $this->config->bug->search['params']['openedBuild']['values']   = $this->loadModel('build')->getBuildPairs($release->product, $branch = 0);
        $this->config->bug->search['params']['resolvedBuild']['values'] = $this->config->bug->search['params']['openedBuild']['values'];

        $searchModules = array();
        $moduleGroups  = $this->loadModel('tree')->getOptionMenu($release->product, 'bug', 0, explode(',', $release->branch));
        foreach($moduleGroups as $modules) $searchModules += $modules;
        $this->config->bug->search['params']['module']['values'] = $searchModules;

        if($release->productType == 'normal')
        {
            unset($this->config->bug->search['fields']['branch']);
            unset($this->config->bug->search['params']['branch']);
        }
        else
        {
            $this->config->bug->search['fields']['branch'] = sprintf($this->lang->product->branch, $this->lang->product->branchName[$release->productType]);
            $allBranchs = $this->loadModel('branch')->getPairs($release->product);
            $branches   = array('' => '', BRANCH_MAIN => $this->lang->branch->main);
            foreach(explode(',', trim($release->branch, ',')) as $branchID) $branches[$branchID] = zget($allBranchs, $branchID);
            $this->config->bug->search['params']['branch']['values'] = $branches;
        }
        if($this->view->project->model == 'waterfall' && empty($this->view->project->hasProduct)) unset($this->config->bug->search['fields']['plan']);
        $this->loadModel('search')->setSearchParams($this->config->bug->search);

        $allBugs     = array();
        $releaseBugs = $type == 'bug' ? $release->bugs : $release->leftBugs;
        if($browseType == 'bySearch')
        {
            $allBugs = $this->bug->getBySearch($release->product, $release->branch, $queryID, 'id_desc', $releaseBugs, $pager);
        }
        else
        {
            if($type == 'bug')
            {
                $allBugs = $this->bug->getReleaseBugs(array_keys($builds), $release->product, $release->branch, $releaseBugs, $pager);
            }
            elseif($type == 'leftBug')
            {
                $allBugs = $this->bug->getProductLeftBugs(array_keys($builds), $release->product, $release->branch, $releaseBugs, $pager);
            }
        }

        $this->view->allBugs     = $allBugs;
        $this->view->releaseBugs = empty($releaseBugs) ? array() : $this->bug->getByIdList($releaseBugs);
        $this->view->release     = $release;
        $this->view->users       = $this->loadModel('user')->getPairs('noletter');
        $this->view->browseType  = $browseType;
        $this->view->param       = $param;
        $this->view->type        = $type;
        $this->view->pager       = $pager;
        $this->display();
    }

    /**
     * Unlink story
     *
     * @param  int    $releaseID
     * @param  int    $bugID
     * @param  string $type
     * @access public
     * @return void
     */
    public function unlinkBug($releaseID, $bugID, $type = 'bug')
    {
        $this->loadModel('release')->unlinkBug($releaseID, $bugID, $type);

        /* if ajax request, send result. */
        if($this->server->ajax)
        {
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
            }
            else
            {
                $response['result']  = 'success';
                $response['message'] = '';
            }
            return $this->send($response);
        }
        return print(js::reload('parent'));
    }

    /**
     * Batch unlink story.
     *
     * @param  int    $releaseID
     * @param  string $type
     * @access public
     * @return void
     */
    public function batchUnlinkBug($releaseID, $type = 'bug')
    {
        $this->loadModel('release')->batchUnlinkBug($releaseID, $type);
        return print(js::locate($this->createLink('projectrelease', 'view', "releaseID=$releaseID&type=$type"), 'parent'));
    }

    /**
     * Change status.
     *
     * @param  int    $releaseID
     * @param  string $status
     * @access public
     * @return void
     */
    public function changeStatus($releaseID, $status)
    {
        $this->loadModel('release')->changeStatus($releaseID, $status);
        if(dao::isError()) return print(js::error(dao::getError()));
        $actionID = $this->loadModel('action')->create('release', $releaseID, 'changestatus', '', $status);
        return print(js::reload('parent'));
    }

    /**
     * Ajax load builds.
     *
     * @param  int    $projectID
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function ajaxLoadBuilds($projectID, $productID)
    {
        $builds         = $this->loadModel('build')->getBuildPairs($productID, 'all', 'notrunk,withbranch,hasproject', $projectID, 'project', '', false);
        $releasedBuilds = $this->projectrelease->getReleasedBuilds($projectID);
        foreach($releasedBuilds as $build) unset($builds[$build]);

        return print(html::select('build[]', $builds, '', "class='form-control chosen' multiple"));
    }
}
