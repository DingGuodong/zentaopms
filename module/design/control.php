<?php
/**
 * The control file of design module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2020 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian <tianshujie@easycorp.ltd>
 * @package     design
 * @version     $Id: control.php 5107 2020-09-02 09:46:12Z tianshujie@easycorp.ltd $
 * @link        http://www.zentao.net
 */
class design extends control
{
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

        $this->loadModel('product');
        $this->loadModel('project');
        $this->loadModel('task');
    }

    /**
     * Design common action.
     *
     * @param  int    $projectID
     * @param  int    $productID
     * @param  int    $designID
     * @access public
     * @return void
     */
    public function commonAction(int $projectID = 0, int $productID = 0, int $designID = 0)
    {
        $products    = $this->product->getProductPairsByProject($projectID);
        $products[0] = $this->lang->product->all;

        ksort($products);

        $productID = $this->product->getAccessableProductID($productID, $products);

        $this->lang->modulePageNav = $this->design->setMenu($projectID, $products, $productID);
        $this->project->setMenu($projectID);
        $this->view->products = $products;

        return $productID;
    }

    /**
     * Browse designs.
     *
     * @param  int    $projectID
     * @param  int    $productID
     * @param  string $type all|bySearch|HLDS|DDS|DBDS|ADS
     * @param  string $param
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browse(int $projectID = 0, int $productID = 0, string $type = 'all', string $param = '', string $orderBy = 'id_desc', int $recTotal = 0, int $recPerPage = 20, int $pageID = 1)
    {
        $productID = $this->commonAction($projectID, $productID);
        $project   = $this->project->getByID($projectID);

        /* Save session for design list and process product id. */
        $this->session->set('designList', $this->app->getURI(true), 'project');
        $this->session->set('reviewList', $this->app->getURI(true), 'project');

        /* Build the search form. */
        $products      = $this->product->getProductPairsByProject($projectID);
        $productIdList = $productID ? $productID : array_keys($products);
        $stories       = $this->loadModel('story')->getProductStoryPairs($productIdList, 'all', 0, 'active', 'id_desc', 0, 'full', 'story', false);
        $this->config->design->search['params']['story']['values'] = $stories;
        $this->config->design->search['params']['type']['values']  = $this->lang->design->typeList;

        $queryID   = ($type == 'bySearch') ? (int)$param : 0;
        $actionURL = $this->createLink('design', 'browse', "projectID=$projectID&productID=$productID&type=bySearch&queryID=myQueryID");
        $this->design->buildSearchForm($queryID, $actionURL);

        /* Init pager and get designs. */
        $this->app->loadClass('pager', true);
        $pager   = pager::init(0, $recPerPage, $pageID);

        if(!$project->hasProduct) unset($this->config->design->dtable->fieldList['product']);
        if($project->hasProduct) $this->config->design->dtable->fieldList['product']['map'] = $this->view->products;
        if(!helper::hasFeature('devops')) $this->config->design->dtable->fieldList['actions']['menu'] = array('edit', 'delete');

        $this->view->title     = $this->lang->design->common . $this->lang->colon . $this->lang->design->browse;
        $this->view->designs   = $this->design->getList($projectID, $productID, $type, $queryID, $orderBy, $pager);
        $this->view->projectID = $projectID;
        $this->view->productID = $productID;
        $this->view->type      = $type;
        $this->view->param     = $param;
        $this->view->orderBy   = $orderBy;
        $this->view->pager     = $pager;
        $this->view->users     = $this->loadModel('user')->getPairs('noletter');

        $this->display();
    }

    /**
     * Create a design.
     *
     * @param  int    $projectID
     * @param  int    $productID
     * @param  string $type
     * @access public
     * @return void
     */
    public function create(int $projectID = 0, int $productID = 0, string $type = 'all')
    {
        $productID = $this->commonAction($projectID, $productID);

        if($_POST)
        {
            $designID = $this->design->create($projectID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                return $this->send($response);
            }

            $this->loadModel('action')->create('design', $designID, 'created');

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = $this->createLink('design', 'browse', "projectID=$projectID&productID=$productID");
            return $this->send($response);
        }

        $products      = $this->product->getProductPairsByProject($projectID);
        $productIdList = $productID ? $productID : array_keys($products);

        $this->view->title      = $this->lang->design->common . $this->lang->colon . $this->lang->design->create;
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed');
        $this->view->stories    = $this->loadModel('story')->getProductStoryPairs($productIdList, 'all', 0, 'active', 'id_desc', 0, 'full', 'story', false);
        $this->view->productID  = $productID;
        $this->view->projectID  = $projectID;
        $this->view->type       = $type;
        $this->view->project    = $this->loadModel('project')->getByID($projectID);

        $this->display();
    }

    /**
     * Batch create designs.
     *
     * @param  int    $projectID
     * @param  int    $productID
     * @param  string $type
     * @access public
     * @return void
     */
    public function batchCreate(int $projectID = 0, int $productID = 0, string $type = 'all')
    {
        $productID = $this->commonAction($projectID, $productID);

        if($_POST)
        {
            $this->design->batchCreate($projectID, $productID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                return $this->send($response);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['load']    = inlink('browse', "projectID=$projectID&productID=$productID");

            return $this->send($response);
        }

        $products      = $this->product->getProductPairsByProject($projectID);
        $productIdList = $productID ? $productID : array_keys($products);

        $project = $this->loadModel('project')->getByID($projectID);

        $this->view->title      = $this->lang->design->common . $this->lang->colon . $this->lang->design->batchCreate;

        $this->view->stories  = $this->loadModel('story')->getProductStoryPairs($productIdList);
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed');
        $this->view->type     = $type;
        $this->view->typeList = $project->model == 'waterfall' ? $this->lang->design->typeList : $this->lang->design->plusTypeList;

        $this->display();
    }

    /**
     * View a design.
     *
     * @param  int    $designID
     * @access public
     * @return void
     */
    public function view(int $designID = 0)
    {
        $design = $this->design->getByID($designID);
        $this->commonAction($design->project, (int)$design->product, $designID);

        $this->session->set('revisionList', $this->app->getURI(true));
        $this->session->set('storyList', $this->app->getURI(true), 'product');

        $products      = $this->product->getProductPairsByProject($design->project);
        $productIdList = $design->product ? $design->product : array_keys($products);

        $project = $this->loadModel('project')->getByID($design->project);

        $this->view->title      = $this->lang->design->common . $this->lang->colon . $this->lang->design->view;

        $this->view->design  = $design;
        $this->view->stories = $this->loadModel('story')->getProductStoryPairs($productIdList);
        $this->view->users   = $this->loadModel('user')->getPairs('noletter');
        $this->view->actions = $this->loadModel('action')->getList('design', $design->id);
        $this->view->repos   = $this->loadModel('repo')->getRepoPairs('project', $design->project);
        $this->view->project  = $project;
        $this->view->typeList = $project->model == 'waterfall' ? $this->lang->design->typeList : $this->lang->design->plusTypeList;

        $this->display();
    }

    /**
     * Edit a design.
     *
     * @param  int    $designID
     * @access public
     * @return void
     */
    public function edit(int $designID = 0)
    {
        $design = $this->design->getByID($designID);
        $design = $this->design->getAffectedScope($design);
        $this->commonAction($design->project, (int)$design->product, $designID);

        if($_POST)
        {
            $changes = $this->design->update($designID);

            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                return $this->send($response);
            }

            if(!empty($changes))
            {
                $actionID = $this->loadModel('action')->create('design', $designID, 'changed');
                $this->action->logHistory($actionID, $changes);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['load']    = $this->createLink('design', 'view', "id={$designID}");
            return $this->send($response);
        }

        $products      = $this->product->getProductPairsByProject($design->project);
        $productIdList = $design->product ? $design->product : array_keys($products);
        $project       = $this->loadModel('project')->getByID($design->project);

        $this->view->title   = $this->lang->design->common . $this->lang->colon . $this->lang->design->edit;
        $this->view->design  = $design;
        $this->view->project = $project;
        $this->view->stories = $this->loadModel('story')->getProductStoryPairs($productIdList);
        $this->view->users   = $this->loadModel('user')->getPairs('noclosed');
        $this->view->typeList = $project->model == 'waterfall' ? $this->lang->design->typeList : $this->lang->design->plusTypeList;

        $this->display();
    }

    /**
     * Design link commits.
     *
     * @param  int    $designID
     * @param  int    $repoID
     * @param  string $begin
     * @param  string $end
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function linkCommit(int $designID = 0, int $repoID = 0, string $begin = '', string $end = '', int $recTotal = 0, int $recPerPage = 50, int $pageID = 1)
    {
        $design = $this->design->getById($designID);
        $this->commonAction($design->project, (int)$design->product, $designID);

        /* Get project and date. */
        $project = $this->loadModel('project')->getByID($design->project);
        $begin   = $begin ? date('Y-m-d', strtotime($begin)) : $project->begin;
        $end     = $end ? date('Y-m-d', strtotime($end)) : helper::today();

        /* Get the repository information through the repoID. */
        $repos  = $this->loadModel('repo')->getRepoPairs('project', $design->project);
        $repoID = $repoID ? $repoID : key($repos);

        $repo      = $this->loadModel('repo')->getRepoByID($repoID);
        $revisions = $this->repo->getCommits($repo, '', 'HEAD', '', '', $begin, date('Y-m-d 23:59:59', strtotime($end)));

        if($_POST)
        {
            $this->design->linkCommit($designID, $repoID);

            $result['result']  = 'success';
            $result['message'] = $this->lang->saveSuccess;
            $result['locate']  = 'parent';
            return $this->send($result);
        }

        /* Linked submission. */
        $linkedRevisions = array();
        $relations = $this->loadModel('common')->getRelations('design', $designID, 'commit');
        foreach($relations as $relation) $linkedRevisions[$relation->BID] = $relation->BID;

        foreach($revisions as $id => $commit)
        {
            if(isset($linkedRevisions[$commit->id])) unset($revisions[$id]);
        }

        /* Init pager. */
        $this->app->loadClass('pager', true);
        $pager     = new pager(count($revisions), $recPerPage, $pageID);
        $revisions = array_chunk($revisions, $pager->recPerPage);

        $this->view->title      = $this->lang->design->common . $this->lang->colon . $this->lang->design->linkCommit;

        $this->view->repos      = $repos;
        $this->view->repoID     = $repoID;
        $this->view->repo       = $repo;
        $this->view->revisions  = empty($revisions) ? $revisions : $revisions[$pageID - 1];
        $this->view->designID   = $designID;
        $this->view->begin      = $begin;
        $this->view->end        = $end;
        $this->view->design     = $design;
        $this->view->pager      = $pager;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->type       = $design->type;

        $this->display();
    }

    /**
     * Design unlink commits.
     *
     * @param  int    $designID
     * @param  int    $commitID
     * @param  string $confirm
     * @access public
     * @return void
     */
    public function unlinkCommit($designID = 0, $commitID = 0, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            return print(js::confirm($this->lang->design->confirmUnlink, inlink('unlinkCommit', "designID=$designID&commitID=$commitID&confirm=yes")));
        }
        else
        {
            $this->design->unlinkCommit($designID, $commitID);

            return print(js::reload('parent'));
        }
    }

    /**
     * View a design's commit.
     *
     * @param  int    $designID
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function viewCommit(int $designID = 0, int $recTotal = 0, int $recPerPage = 20, int $pageID = 1)
    {
        $design = $this->design->getByID($designID);
        $this->commonAction($design->project, (int)$design->product, $designID);

        /* Init pager. */
        $this->app->loadClass('pager', true);
        $pager   = pager::init(0, $recPerPage, $pageID);

        $design = $this->design->getCommit($designID, $pager);

        $this->view->title      = $this->lang->design->common . $this->lang->colon . $this->lang->design->submission;

        $this->view->design = $design;
        $this->view->pager  = $pager;
        $this->view->users  = $this->loadModel('user')->getPairs('noletter');
        $this->view->repos  = $this->loadModel('repo')->getRepoPairs('project', $design->project);

        $this->display();
    }

    /**
     * A version of the repository.
     *
     * @param  int    $repoID
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function revision($repoID = 0, $projectID = 0)
    {
        $repo    = $this->dao->select('*')->from(TABLE_REPOHISTORY)->where('id')->eq($repoID)->fetch();
        $repoURL = $this->createLink('repo', 'revision', "repoID=$repo->repo&objectID=$projectID&revistion=$repo->revision");
        header("location:" . $repoURL);
    }

    /**
     * Ajax get design drop menu.
     *
     * @param  int    $projectID
     * @param  int    $productID
     * @param  string $extra
     * @access public
     * @return void
     */
    public function ajaxGetDropMenu($projectID, $productID)
    {
        $products = $this->loadModel('product')->getProducts($projectID);

        $this->view->link      = helper::createLink('design', 'browse', "projectID=$projectID&productID=%s");
        $this->view->productID = $productID;
        $this->view->products  = $products;
        $this->view->projectID = $projectID;
        $this->display();
    }

    /**
     * Ajax get stories by productID and projectID.
     *
     * @param  int    $productID
     * @param  int    $projectID
     * @param  string $status
     * @param  string $hasParent
     * @access public
     * @return void
     */
    public function ajaxGetProductStories($productID, $projectID, $status = 'all', $hasParent = 'true')
    {
        $products      = $this->product->getProductPairsByProject($projectID);
        $productIdList = $productID ? $productID : array_keys($products);

        $stories = $this->loadModel('story')->getProductStoryPairs($productIdList, 'all', 0, $status, 'id_desc', 0, 'full', 'story', $hasParent);

        return print(html::select('story', $stories, '', "class='form-control'"));
    }

    /**
     * Delete a design.
     *
     * @param  int    $designID
     * @access public
     * @return void
     */
    public function delete($designID = 0)
    {
        $design = $this->design->getByID($designID);
        $this->design->delete(TABLE_DESIGN, $designID);
        $this->dao->delete()->from(TABLE_RELATION)->where('Atype')->eq('design')->andWhere('AID')->eq($designID)->andWhere('Btype')->eq('commit')->andwhere('relation')->eq('completedin')->exec();
        $this->dao->delete()->from(TABLE_RELATION)->where('Atype')->eq('commit')->andWhere('BID')->eq($designID)->andWhere('Btype')->eq('design')->andwhere('relation')->eq('completedfrom')->exec();

        if(dao::isError())
        {
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
        }
        else
        {
            $response['result'] = 'success';
            $response['load']   = inLink('browse', "projectID={$design->project}");
        }

        return $this->send($response);
    }

    /**
     * Update assign of design.
     *
     * @param  int    $designID
     * @access public
     * @return void
     */
    public function assignTo(int $designID = 0)
    {
        if($_POST)
        {
            $changes = $this->design->assign($designID);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action');
            if(!empty($changes))
            {
                $actionID = $this->action->create('design', $designID, 'Assigned', $this->post->comment, $this->post->assignedTo);
                $this->action->logHistory($actionID, $changes);
            }

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'closeModal' => true, 'load' => true));
        }

        $design = $this->design->getByID($designID);

        $this->view->title  = $this->lang->design->common . $this->lang->colon . $this->lang->design->assignedTo;
        $this->view->design = $design;
        $this->view->users  = $this->loadModel('project')->getTeamMemberPairs($design->project);
        $this->display();
    }
}
