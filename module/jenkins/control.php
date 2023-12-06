<?php
declare(strict_types=1);
/**
 * The control file of jenkins module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Chenqi <chenqi@cnezsoft.com>
 * @package     product
 * @version     $Id: ${FILE_NAME} 5144 2020/1/8 8:10 下午 chenqi@cnezsoft.com $
 * @link        https://www.zentao.net
 */
class jenkins extends control
{
    /**
     * jenkins constructor.
     * @param string $moduleName
     * @param string $methodName
     */
    public function __construct(string $moduleName = '', string $methodName = '')
    {
        parent::__construct($moduleName, $methodName);

        if(stripos($this->methodName, 'ajax') === false && !commonModel::hasPriv('space', 'browse')) $this->loadModel('common')->deny('space', 'browse', false);
        $this->loadModel('ci')->setMenu();
    }

    /**
     * Jenkins 列表。
     * Browse jenkinss.
     *
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browse(string $orderBy = 'id_desc', int $recTotal = 0, int $recPerPage = 20, int $pageID = 1)
    {
        $this->app->loadClass('pager', true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->title       = $this->lang->jenkins->common . $this->lang->colon . $this->lang->jenkins->browse;
        $this->view->jenkinsList = $this->loadModel('pipeline')->getList('jenkins', $orderBy, $pager);
        $this->view->orderBy     = $orderBy;
        $this->view->pager       = $pager;

        $this->display();
    }

    /**
     * Create a jenkins.
     *
     * @access public
     * @return void
     */
    public function create()
    {
        if($_POST)
        {
            $jenkins = form::data($this->config->jenkins->form->create)
                ->add('type', 'jenkins')
                ->add('private',md5(rand(10,113450)))
                ->add('createdBy', $this->app->user->account)
                ->add('createdDate', helper::now())
                ->trim('url,token,account,password')
                ->skipSpecial('url,token,account,password')
                ->remove('appType')
                ->get();
            $jenkinsID = $this->loadModel('pipeline')->create($jenkins);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('jenkins', $jenkinsID, 'created');
            if($this->viewType == 'json') return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'id' => $jenkinsID));
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('space', 'browse')));
        }

        $this->view->title      = $this->lang->jenkins->common . $this->lang->colon . $this->lang->jenkins->create;

        $this->display();
    }

    /**
     * 编辑一个Jenkins服务器。
     * Edit a jenkins.
     *
     * @param  int    $jenkinsID
     * @access public
     * @return void
     */
    public function edit(int $jenkinsID)
    {
        $jenkins = $this->loadModel('pipeline')->getByID($jenkinsID);
        if($_POST)
        {
            $this->pipeline->update($jenkinsID);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $newJenkins = $this->pipeline->getByID($jenkinsID);
            $actionID   = $this->loadModel('action')->create('jenkins', $jenkinsID, 'edited');
            $changes    = common::createChanges($jenkins, $newJenkins);
            $this->action->logHistory($actionID, $changes);
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'load' => true, 'closeModal' => true));
        }

        $this->view->title   = $this->lang->jenkins->common . $this->lang->colon . $this->lang->jenkins->edit;
        $this->view->jenkins = $jenkins;
        $this->display();
    }

    /**
     * 删除一条jenkins数据。
     * Delete a jenkins.
     *
     * @param  int    $jenkinsID
     * @access public
     * @return void
     */
    public function delete($jenkinsID)
    {
        $jobs = $this->jenkins->getJobPairs($jenkinsID);
        if(!empty($jobs)) return $this->sendError($this->lang->jenkins->error->linkedJob, true);

        $this->jenkins->delete(TABLE_PIPELINE, $jenkinsID);
        return $this->send(array('result' => 'success', 'load' => $this->createLink('space', 'browse')));
    }

    /**
     * 获取Jenkins任务列表。
     * AJAX: Get jenkins tasks.
     *
     * @param  int    $jenkinsID
     * @access public
     * @return void
     */
    public function ajaxGetJenkinsTasks(int $jenkinsID = 0)
    {
        $tasks = array();
        if($jenkinsID) $tasks = $this->jenkins->getTasks($jenkinsID, 3);

        $this->view->tasks = $this->jenkinsZen->buildTree($tasks);
        $this->display();
    }
}
