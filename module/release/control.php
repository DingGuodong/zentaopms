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
class release extends control
{
    /**
     * Common actions.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @access public
     * @return void
     */
    public function commonAction($productID, $branch = 0)
    {
        $this->loadModel('product')->setMenu($productID, $branch);

        $product = $this->product->getById($productID);
        if(empty($product)) $this->locate($this->createLink('product', 'create'));
        $this->view->product  = $product;
        $this->view->branch   = $branch;
        $this->view->branches = $product->type == 'normal' ? array() : $this->loadModel('branch')->getPairs($product->id);
    }

    /**
     * Browse releases.
     *
     * @param  int    $productID
     * @param  string $branch
     * @param  string $type
     * @param  string $orderBy
     * @param  string $param
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browse(int $productID, string $branch = 'all', string $type = 'all', string $orderBy = 't1.date_desc', string $param = '', int $recTotal = 0, int $recPerPage = 20, int $pageID = 1)
    {
        $this->app->loadClass('pager', true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $this->commonAction($productID, $branch);

        $uri = $this->app->getURI(true);
        $this->session->set('releaseList', $uri, 'product');
        $this->session->set('buildList', $uri);
        $showBranch  = $this->view->product->type != 'normal';

        if(!$showBranch)
        {
            unset($this->config->release->dtable->fieldList['branch']);
            unset($this->config->release->search['fields']['branch']);
            unset($this->config->release->search['params']['branch']);
        }

        $queryID   = $type == 'bySearch' ? (int)$param : 0;
        $actionURL = $this->createLink('release', 'browse', "productID={$productID}&branch={$branch}&type=bySearch&orderBy={$orderBy}&param=myQueryID");
        $this->release->buildSearchForm($queryID, $actionURL, $this->view->product, $branch);

        $releases = $type == 'bySearch' ? $this->release->getListBySearch($productID, $queryID, $orderBy, $pager) : $this->release->getList($productID, $branch, $type, $orderBy, $queryID, $pager);

        $this->view->title       = $this->view->product->name . $this->lang->colon . $this->lang->release->browse;
        $this->view->releases    = $this->releaseZen->processReleaseListData($releases);
        $this->view->type        = $type;
        $this->view->orderBy     = $orderBy;
        $this->view->param       = $param;
        $this->view->pager       = $pager;
        $this->view->showBranch  = $showBranch;
        $this->view->recTotal    = $pager->recTotal;
        $this->view->branchPairs = $this->loadModel('branch')->getPairs($productID);
        $this->display();
    }

    /**
     * Create a release.
     *
     * @param  int        $productID
     * @param  string|int $branch
     * @access public
     * @return void
     */
    public function create($productID, $branch = 'all')
    {
        if(!empty($_POST))
        {
            $releaseID = $this->release->create($productID, $branch);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('release', $releaseID, 'opened');

            $result = $this->executeHooks($releaseID);
            $message = $result ? $result : $this->lang->saveSuccess;

            if($this->viewType == 'json') return $this->send(array('result' => 'success', 'message' => $message, 'id' => $releaseID));
            if(isonlybody()) return $this->send(array('result' => 'success', 'message' => $message, 'closeModal' => true, 'callback' => "parent.loadProductBuilds($productID)"));

            return $this->send(array('result' => 'success', 'message' => $message, 'locate' => inlink('view', "releaseID=$releaseID")));
        }

        $builds         = $this->loadModel('build')->getBuildPairs($productID, $branch, 'notrunk|withbranch|hasproject', 0, 'execution', '', false);
        $releasedBuilds = $this->release->getReleasedBuilds($productID, $branch);
        foreach($releasedBuilds as $build) unset($builds[$build]);

        $this->commonAction($productID, $branch);
        $this->view->title          = $this->view->product->name . $this->lang->colon . $this->lang->release->create;
        $this->view->productID      = $productID;
        $this->view->builds         = $builds;
        $this->view->users          = $this->loadModel('user')->getPairs('noclosed');
        $this->view->lastRelease    = $this->release->getLast($productID, $branch);

        $this->display();
    }

    /**
     * Edit a release.
     *
     * @param  int    $releaseID
     * @access public
     * @return void
     */
    public function edit($releaseID)
    {
        if(!empty($_POST))
        {
            $changes = $this->release->update($releaseID);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            if($changes)
            {
                $actionID = $this->loadModel('action')->create('release', $releaseID, 'Edited');
                if(!empty($changes)) $this->action->logHistory($actionID, $changes);
            }

            $result  = $this->executeHooks($releaseID);
            $message = $result ? $result : $this->lang->saveSuccess;

            return $this->send(array('result' => 'success', 'message' => $message, 'locate' => inlink('view', "releaseID=$releaseID")));
        }
        $this->loadModel('story');
        $this->loadModel('bug');
        $this->loadModel('build');

        /* Get release and build. */
        $release = $this->release->getById((int)$releaseID);
        $this->commonAction($release->product);

        $builds         = $this->loadModel('build')->getBuildPairs($release->product, $release->branch, 'notrunk|withbranch|hasproject', 0, 'project', $release->build, false);
        $releasedBuilds = $this->release->getReleasedBuilds($release->product);
        foreach($releasedBuilds as $releasedBuild)
        {
            if(strpos(',' . trim($release->build, ',') . ',', ",{$releasedBuild},") === false) unset($builds[$releasedBuild]);
        }

        $this->view->title      = $this->view->product->name . $this->lang->colon . $this->lang->release->edit;
        $this->view->release    = $release;
        $this->view->builds     = $builds;
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed');

        $this->display();
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
        $releaseID = (int)$releaseID;
        $release   = $this->release->getByID($releaseID, true);
        if(!$release) return print(js::error($this->lang->notFound) . js::locate($this->createLink('product', 'index')));

        $uri = $this->app->getURI(true);
        if(!empty($release->build)) $this->session->set('buildList', $uri, 'project');
        if($type == 'story') $this->session->set('storyList', $uri, 'product');
        if($type == 'bug' or $type == 'leftBug') $this->session->set('bugList', $uri, 'qa');

        $this->loadModel('story');
        $this->loadModel('bug');

        /* Load pager. */
        $this->app->loadClass('pager', true);
        if($this->app->getViewType() == 'mhtml') $recPerPage = 10;

        $sort = common::appendOrder($orderBy);
        if(strpos($sort, 'pri_') !== false) $sort = str_replace('pri_', 'priOrder_', $sort);
        $sort .= ',buildID_asc';

        $storyPager = new pager($type == 'story' ? $recTotal : 0, $recPerPage, $type == 'story' ? $pageID : 1);
        $stories = $this->dao->select("t1.*,t2.id as buildID, t2.name as buildName, IF(t1.`pri` = 0, {$this->config->maxPriValue}, t1.`pri`) as priOrder")->from(TABLE_STORY)->alias('t1')
            ->leftJoin(TABLE_BUILD)->alias('t2')->on("FIND_IN_SET(t1.id, t2.stories)")
            ->where('t1.id')->in($release->stories)
            ->andWhere('t1.deleted')->eq(0)
            ->beginIF($type == 'story')->orderBy($sort)->fi()
            ->page($storyPager)
            ->fetchAll('id');

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'story', false);
        $stages = $this->dao->select('*')->from(TABLE_STORYSTAGE)->where('story')->in(array_keys($stories))->andWhere('branch')->eq($release->branch)->fetchPairs('story', 'stage');
        foreach($stages as $storyID => $stage) $stories[$storyID]->stage = $stage;

        $bugPager = new pager($type == 'bug' ? $recTotal : 0, $recPerPage, $type == 'bug' ? $pageID : 1);
        $sort = common::appendOrder($orderBy);
        $bugs = $this->dao->select('*')->from(TABLE_BUG)
            ->where('id')->in($release->bugs)
            ->andWhere('deleted')->eq(0)
            ->beginIF($type == 'bug')->orderBy($sort)->fi()
            ->page($bugPager)
            ->fetchAll();
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'linkedBug');

        $sort = common::appendOrder($orderBy);
        if($type == 'leftBug' and strpos($orderBy, 'severity_') !== false) $sort = str_replace('severity_', 'severityOrder_', $sort);
        $leftBugPager = new pager($type == 'leftBug' ? $recTotal : 0, $recPerPage, $type == 'leftBug' ? $pageID : 1);

        $leftBugs = $this->dao->select("*, IF(`severity` = 0, {$this->config->maxPriValue}, `severity`) as severityOrder")->from(TABLE_BUG)
            ->where('deleted')->eq(0)
            ->beginIF($release->leftBugs)->andWhere('id')->in($release->leftBugs)->fi()
            ->beginIF($type == 'leftBug')->orderBy($sort)->fi()
            ->page($leftBugPager)
            ->fetchAll();

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'leftBugs');

        $this->commonAction($release->product);
        $product = $this->product->getById($release->product);

        $this->executeHooks($releaseID);

        $this->view->title         = "RELEASE #$release->id $release->name/" . $product->name;
        $this->view->release       = $release;
        $this->view->stories       = $stories;
        $this->view->bugs          = $bugs;
        $this->view->leftBugs      = $leftBugs;
        $this->view->actions       = $this->loadModel('action')->getList('release', $releaseID);
        $this->view->users         = $this->loadModel('user')->getPairs('noletter');
        $this->view->type          = $type;
        $this->view->link          = $link;
        $this->view->param         = $param;
        $this->view->orderBy       = $orderBy;
        $this->view->branchName    = $release->productType == 'normal' ? '' : $this->loadModel('branch')->getById($release->branch);
        $this->view->storyPager    = $storyPager;
        $this->view->bugPager      = $bugPager;
        $this->view->leftBugPager  = $leftBugPager;
        $this->view->builds        = $this->loadModel('build')->getBuildPairs($release->product, 'all', 'withbranch|hasproject', 0, 'execution', '', false);
        $this->view->summary       = $this->product->summary($stories);
        $this->view->storyCases    = $this->loadModel('testcase')->getStoryCaseCounts(array_keys($stories));

        if($this->app->getViewType() == 'json')
        {
            unset($this->view->storyPager);
            unset($this->view->bugPager);
            unset($this->view->leftBugPager);
        }

        $this->display();
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
        if($_POST)
        {
            if(isset($_POST['notify']))
            {
                $notify = implode(',', $this->post->notify);
                $this->dao->update(TABLE_RELEASE)->set('notify')->eq($notify)->where('id')->eq($releaseID)->exec();

                $this->release->sendmail($releaseID);
                $this->loadModel('action')->create('release', $releaseID, 'notified');
            }

            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $this->view->release = $this->release->getById($releaseID);
        $this->view->actions = $this->loadModel('action')->getList('release', $releaseID);
        $this->view->users   = $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->display();
    }

    /**
     * 删除发布。
     * Delete a release.
     *
     * @param  int    $releaseID
     * @access public
     * @return void
     */
    public function delete(int $releaseID)
    {
        $release = $this->release->getByID($releaseID);
        $this->release->delete(TABLE_RELEASE, $releaseID);

        $builds  = $this->dao->select('*')->from(TABLE_BUILD)->where('id')->in($release->build)->fetchAll();
        $this->dao->update(TABLE_BUILD)->set('deleted')->eq(1)->where('id')->eq($release->shadow)->exec();
        foreach($builds as $build)
        {
            if(empty($build->execution) and $build->createdDate == $release->createdDate) $this->build->delete(TABLE_BUILD, $build->id);
        }

        $response = array();
        $message  = $this->executeHooks($releaseID);
        if($message) $response['message'] = $message;

        /* if ajax request, send result. */
        if(dao::isError())
        {
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
        }
        else
        {
            $response['result'] = 'success';
            $response['load']   = inLink('browse', "productID={$release->product}");
        }

        return $this->send($response);
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
                    ->beginIF($this->session->bugOrderBy !== false)->orderBy($this->session->bugOrderBy)->fi()
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
                    ->beginIF($this->session->bugOrderBy !== false)->orderBy($this->session->bugOrderBy)->fi()
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
    public function linkStory(int $releaseID = 0, string $browseType = '', int $param = 0, int $recTotal = 0, int $recPerPage = 100, int $pageID = 1)
    {
        if(!empty($_POST['stories']))
        {
            $this->release->linkStory($releaseID);
            return print(js::locate(inlink('view', "releaseID=$releaseID&type=story"), 'parent'));
        }
        $this->session->set('storyList', inlink('view', "releaseID=$releaseID&type=story&link=true&param=" . helper::safe64Encode("&browseType=$browseType&queryID=$param")), 'product');

        $release = $this->release->getById($releaseID);
        $this->commonAction($release->product);
        $this->loadModel('story');
        $this->loadModel('tree');
        $this->loadModel('product');

        /* Load pager. */
        $this->app->loadClass('pager', true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        /* Build search form. */
        unset($this->config->product->search['fields']['product']);
        unset($this->config->product->search['fields']['project']);

        $queryID = ($browseType == 'bySearch') ? (int)$param : 0;
        $this->config->product->search['actionURL'] = $this->createLink('release', 'view', "releaseID=$releaseID&type=story&link=true&param=" . helper::safe64Encode('&browseType=bySearch&queryID=myQueryID'));
        $this->config->product->search['queryID']   = $queryID;
        $this->config->product->search['style']     = 'simple';
        $this->config->product->search['params']['plan']['values'] = $this->loadModel('productplan')->getPairsForStory($release->product, $release->branch, 'skipParent|withMainPlan');
        $this->config->product->search['params']['status'] = array('operator' => '=', 'control' => 'select', 'values' => $this->lang->story->statusList);

        $searchModules = array();
        $moduleGroups  = $this->loadModel('tree')->getOptionMenu($release->product, 'story', 0, explode(',', $release->branch));
        foreach($moduleGroups as $modules) $searchModules += $modules;
        $this->config->product->search['params']['module']['values'] = $searchModules;

        if($this->session->currentProductType == 'normal')
        {
            unset($this->config->product->search['fields']['branch']);
            unset($this->config->product->search['params']['branch']);
        }
        else
        {
            $allBranchs = $this->loadModel('branch')->getPairs($release->product);
            $branches   = array('' => '', BRANCH_MAIN => $this->lang->branch->main);
            foreach(explode(',', trim($release->branch, ',')) as $branchID) $branches[$branchID] = zget($allBranchs, $branchID);

            $this->config->product->search['fields']['branch'] = sprintf($this->lang->product->branch, $this->lang->product->branchName[$release->productType]);
            $this->config->product->search['params']['branch']['values'] = $branches;
        }
        $this->loadModel('search')->setSearchParams($this->config->product->search);

        $builds = $this->loadModel('build')->getByList($release->build);
        $executionIdList = array();
        foreach($builds as $build)
        {
            if(!empty($build->execution))
            {
                $executionIdList[$build->execution] = $build->execution;
            }
            elseif(!empty($build->project))
            {
                $executionIdList[$build->project] = $build->project;
            }
        }

        if($browseType == 'bySearch')
        {
            $allStories = $this->story->getBySearch($release->product, $release->branch, $queryID, 'id', $executionIdList, 'story', $release->stories, $pager);
        }
        else
        {
            $allStories = $this->story->batchGetExecutionStories(implode(',', $executionIdList), $release->product, 't1.`order`_desc', 'byBranch', $release->branch, 'story', $release->stories, $pager);
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
     * 移除关联的需求。
     * Unlink a story.
     *
     * @param  int    $releaseID
     * @param  int    $storyID
     * @access public
     * @return void
     */
    public function unlinkStory(int $releaseID, int$storyID)
    {
        $this->release->unlinkStory($releaseID, $storyID);

        return $this->sendSuccess(array('load' => $this->createLink('release', 'view', "releaseID=$releaseID&type=story")));
    }

    /**
     * Batch unlink story.
     *
     * @param  int    $releaseID
     * @access public
     * @return void
     */
    public function batchUnlinkStory(int $releaseID)
    {
        $this->release->batchUnlinkStory($releaseID);
        return $this->sendSuccess(array('load' => $this->createLink('release', 'view', "releaseID=$releaseID&type=story")));
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
            $this->release->linkBug($releaseID, $type);
            return print(js::locate(inlink('view', "releaseID=$releaseID&type=$type"), 'parent'));
        }

        $this->session->set('bugList', inlink('view', "releaseID=$releaseID&type=$type&link=true&param=" . helper::safe64Encode("&browseType=$browseType&queryID=$param")), 'qa');
        /* Set menu. */
        $release = $this->release->getByID($releaseID);
        $this->commonAction($release->product);

        /* Load pager. */
        $this->app->loadClass('pager', true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        /* Build the search form. */
        $this->loadModel('bug');
        unset($this->config->bug->search['fields']['product']);
        unset($this->config->bug->search['fields']['project']);

        $queryID = ($browseType == 'bysearch') ? (int)$param : 0;
        $this->config->bug->search['actionURL'] = $this->createLink('release', 'view', "releaseID=$releaseID&type=$type&link=true&param=" . helper::safe64Encode('&browseType=bySearch&queryID=myQueryID'));
        $this->config->bug->search['queryID']   = $queryID;
        $this->config->bug->search['style']     = 'simple';

        $this->config->bug->search['params']['plan']['values']          = $this->loadModel('productplan')->getPairsForStory($release->product, $release->branch, 'skipParent|withMainPlan');
        $this->config->bug->search['params']['execution']['values']     = $this->loadModel('product')->getExecutionPairsByProduct($release->product, $release->branch);
        $this->config->bug->search['params']['openedBuild']['values']   = $this->loadModel('build')->getBuildPairs($release->product, 'all', 'releasetag');
        $this->config->bug->search['params']['resolvedBuild']['values'] = $this->config->bug->search['params']['openedBuild']['values'];

        $searchModules = array();
        $moduleGroups  = $this->loadModel('tree')->getOptionMenu($release->product, 'bug', 0, explode(',', $release->branch));
        foreach($moduleGroups as $modules) $searchModules += $modules;
        $this->config->bug->search['params']['module']['values'] = $searchModules;

        if($this->session->currentProductType == 'normal')
        {
            unset($this->config->bug->search['fields']['branch']);
            unset($this->config->bug->search['params']['branch']);
        }
        else
        {
            $allBranchs = $this->loadModel('branch')->getPairs($release->product);
            $branches   = array('' => '', BRANCH_MAIN => $this->lang->branch->main);
            foreach(explode(',', trim($release->branch, ',')) as $branchID) $branches[$branchID] = zget($allBranchs, $branchID);

            $this->config->bug->search['fields']['branch'] = sprintf($this->lang->product->branch, $this->lang->product->branchName[$release->productType]);
            $this->config->bug->search['params']['branch']['values'] = $branches;
        }
        $this->loadModel('search')->setSearchParams($this->config->bug->search);

        $builds      = $this->loadModel('build')->getByList($release->build);
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
     * 移除关联的Bug。
     * Unlink linked bug.
     *
     * @param  int    $releaseID
     * @param  int    $bugID
     * @param  string $type
     * @access public
     * @return void
     */
    public function unlinkBug(int $releaseID, int $bugID, string $type = 'bug')
    {
        $this->release->unlinkBug($releaseID, $bugID, $type);

        /* if ajax request, send result. */
        if(dao::isError())
        {
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
        }
        else
        {
            $response['result']  = 'success';
            $response['message'] = '';
            $response['load']    = $this->createLink('release', 'view', "releaseID={$releaseID}&type={$type}");
        }
        return $this->send($response);
    }

    /**
     * Batch unlink story.
     *
     * @param  int    $releaseID
     * @param  string $type       bug|leftBug
     * @access public
     * @return void
     */
    public function batchUnlinkBug(int $releaseID, string $type = 'bug')
    {
        $this->release->batchUnlinkBug($releaseID, $type);
        return $this->sendSuccess(array('load' => $this->createLink('release', 'view', "releaseID=$releaseID&type=$type")));
    }

    /**
     * 激活/停止维护发布。
     * Change status.
     *
     * @param  int    $releaseID
     * @param  string $status     normal|terminate
     * @access public
     * @return void
     */
    public function changeStatus(int $releaseID, string $status)
    {
        $this->release->changeStatus($releaseID, $status);
        if(dao::isError()) return $this->sendError(dao::getError());

        $this->loadModel('action')->create('release', $releaseID, 'changestatus', '', $status);
        return $this->sendSuccess(array('load' => true));
    }
}
