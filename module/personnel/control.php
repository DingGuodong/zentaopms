<?php
/**
 * The control file of personnel of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     personnel
 * @version     $Id$
 * @link        http://www.zentao.net
 */
class personnel extends control
{
    /**
     * 获取可访问人员列表。
     * Get a list of people who can be accessed.
     *
     * @param  int    $programID
     * @param  int    $deptID
     * @param  string $browseType
     * @param  int    $param
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function accessible(int $programID = 0, int $deptID = 0, string $browseType = 'browse', int $param = 0, int $recTotal = 0, int $recPerPage = 15, int $pageID = 1)
    {
        common::setMenuVars('program', $programID);
        $this->app->loadLang('user');

        $program = $this->loadMOdel('program')->getByID($programID);

        /* Build the search form. */
        $queryID       = $browseType == 'bysearch' ? (int)$param : 0;
        $actionURL     = $this->createLink('personnel', 'accessible', "pargramID={$programID}&deptID={$deptID}&browseType=bysearch&quertID=myQueryID");
        $this->config->personnel->accessible->search['params']['role']['values']   = $this->lang->user->roleList;
        $this->config->personnel->accessible->search['params']['gender']['values'] = $this->lang->user->genderList;
        $this->personnel->buildSearchForm($queryID, $actionURL);

        /* Get personnel list. */
        $personnelList = $this->personnel->getAccessiblePersonnel($programID, $deptID, $browseType, $queryID);

        /* Set the pager. */
        $this->app->loadClass('pager', true);
        $recTotal      = count($personnelList);
        $pager         = new pager($recTotal, $recPerPage, $pageID);
        $personnelList = array_chunk($personnelList, $pager->recPerPage);

        $this->view->title         = $this->lang->personnel->accessible;
        $this->view->deptID        = $deptID;
        $this->view->programID     = $programID;
        $this->view->acl           = $program->acl;
        $this->view->recTotal      = $recTotal;
        $this->view->recPerPage    = $recPerPage;
        $this->view->pageID        = $pageID;
        $this->view->pager         = $pager;
        $this->view->param         = $param;
        $this->view->browseType    = $browseType;
        $this->view->personnelList = empty($personnelList) ? $personnelList : $personnelList[$pageID - 1];
        $this->view->deptList      = $this->loadModel('dept')->getOptionMenu();
        $this->view->dept          = $this->dept->getByID($deptID);
        $this->view->deptTree      = $this->dept->getTreeMenu(0, array(new personnelModel, 'createMemberLink'), $programID);
        $this->display();
    }

    /**
     * Access to investable personnel.
     *
     * @param  int    $browseType
     * @param  string $orderBy
     * @access public
     * @return void
     */
    public function invest($programID = 0)
    {
        common::setMenuVars('program', $programID);
        $this->app->loadLang('user');

        $this->view->title      = $this->lang->personnel->invest;
        $this->view->investList = $this->personnel->getInvest($programID);
        $this->view->programID  = $programID;

        $this->display();
    }

    /**
     * Get white list personnel.
     *
     * @param  int    $objectID
     * @param  string $module     personnel|program|project|product
     * @param  string $objectType program|project|product|sprint
     * @param  string $orderby
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @param  int    $programID
     * @param  string $from       project|program|programproject
     * @access public
     * @return void
     */
    public function whitelist($objectID = 0, $module = 'personnel', $objectType = 'program', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1, $programID = 0, $from = '')
    {
        if($this->app->tab == 'program') common::setMenuVars('program', $objectID);

        /* Load lang and set session. */
        $this->app->loadLang('user');
        $this->app->session->set('whitelistList', $this->app->getURI(true), $this->app->tab);

        /* Load pager. */
        $this->app->loadClass('pager', true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        /* Set back link. */
        $goback = $this->session->projectList ? $this->session->projectList : $this->createLink('program', 'whitelist', "projectID=$objectID");
        if($from == 'program')  $goback = $this->createLink('program', 'browse');
        if($from == 'programproject') $goback = $this->session->programProject ? $this->session->programProject : $this->createLink('program', 'project', "programID=$programID");

        $this->view->title = $this->lang->personnel->whitelist;

        $this->view->pager      = $pager;
        $this->view->objectID   = $objectID;
        $this->view->objectType = $objectType;
        $this->view->whitelist  = $this->personnel->getWhitelist($objectID, $objectType, $orderBy, $pager);
        $this->view->depts      = $this->loadModel('dept')->getOptionMenu();
        $this->view->module     = $module;
        $this->view->goback     = $goback;
        $this->view->from       = $from;

        if($this->app->tab == 'execution') $objectType = 'execution';
        $dropMenuObjectID = $objectType . 'ID';
        $this->view->{$dropMenuObjectID} = $objectID;
        $this->view->projectProgramID    = $programID;

        $this->display();
    }

    /**
     * Adding users to the white list.
     *
     * @param  int     $objectID
     * @param  int     $deptID
     * @param  int     $copyID
     * @param  string  $objectType  program|project|product|sprint
     * @param  string  $module
     * @param  int     $programID
     * @param  string  $from        project|program|programproject
     * @access public
     * @return void
     */
    public function addWhitelist($objectID = 0, $deptID = 0, $copyID = 0, $objectType = 'program', $module = 'personnel', $programID = 0, $from = '')
    {
        if($this->app->tab == 'program') common::setMenuVars('program', $objectID);

        $this->app->loadLang('execution');

        if($_POST)
        {
            $this->personnel->addWhitelist($objectType, $objectID);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => $this->getError()));

            $this->loadModel('action')->create('whitelist', $objectID, 'managedWhitelist', '', $objectType);

            $locateLink = $this->session->whitelistList ? $this->session->whitelistList : $this->createLink($module, 'whitelist', "objectID=$objectID");
            $tab = $module == 'program' ? ($from == 'project' || $from == 'my' ? '#open=project' : '#open=program') : '';
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $locateLink . $tab));
        }

        $this->loadModel('dept');
        $deptUsers = empty($deptID) ? array() : $this->dept->getDeptUserPairs($deptID);

        $copyObjectType = $objectType;
        if($copyObjectType == 'sprint')
        {
            $object = $this->loadModel('project')->getByID($copyID);
            if(!empty($object->type) and $object->type == 'project') $copyObjectType = 'project';
        }
        $copyUsers   = empty($copyID) ? array() : $this->personnel->getWhitelistAccount($copyID, $copyObjectType);
        $appendUsers = array_unique($deptUsers + $copyUsers);

        $objectName = $this->lang->projectCommon . $this->lang->execution->or . $this->lang->execution->common;;
        if($objectType == 'program') $objectName = $this->lang->program->common;
        if($objectType == 'product') $objectName = $this->lang->productCommon;
        if($objectType == 'project') $objectName = $this->lang->projectCommon;
        $this->lang->personnel->selectObjectTips = sprintf($this->lang->personnel->selectObjectTips, $objectName);

        if($objectType == 'sprint' and $module == 'execution')
        {
            $execution = $this->loadModel('execution')->getByID($objectID);
            $this->lang->personnel->selectObjectTips = (!empty($execution) and $execution->type == 'kanban') ? str_replace($this->lang->execution->common, $this->lang->execution->kanban, $this->lang->personnel->selectObjectTips) : $this->lang->personnel->selectObjectTips;
        }

        $this->view->title      = $this->lang->personnel->addWhitelist;

        $this->view->objectID    = $objectID;
        $this->view->objectType  = $objectType;
        $this->view->objectName  = $objectName;
        $this->view->objects     = $this->personnel->getCopiedObjects($objectID, $objectType);
        $this->view->module      = $module;
        $this->view->deptID      = $deptID;
        $this->view->appendUsers = $appendUsers;
        $this->view->whitelist   = $this->personnel->getWhitelist($objectID, $objectType);
        $this->view->depts       = $this->dept->getOptionMenu();
        $this->view->users       = $this->loadModel('user')->getPairs('noclosed|nodeleted');
        $this->view->dept        = $this->dept->getByID($deptID);
        $this->view->programID   = $programID;
        $this->view->from        = $from;
        $this->view->copyID      = $copyID;

        $this->display();
    }

    /*
     * Removing users from the white list.
     *
     * @param  int     $id
     * @param  string  $confirm
     * @access public
     * @return void
     */
    public function unbindWhitelist($id = 0, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            $confirmURL = inlink('unbindWhitelist',"id=$id&confirm=yes");
            return $this->send(array('result' => 'fail', 'callback' => "zui.Modal.confirm({message: '{$this->lang->personnel->confirmDelete}', icon: 'icon-exclamation-sign', iconClass: 'warning-pale rounded-full icon-2x'}).then((res) => {if(res) $.ajaxSubmit({url: '$confirmURL'});});"));
        }
        else
        {
            $acl = $this->dao->select('*')->from(TABLE_ACL)->where('id')->eq($id)->fetch();
            if(empty($acl)) return $this->send(array('result' => 'success', 'load' => true));

            $objectTable  = $acl->objectType == 'product' ? TABLE_PRODUCT : TABLE_PROJECT;
            $whitelist    = $this->dao->select('whitelist')->from($objectTable)->where('id')->eq($acl->objectID)->fetch('whitelist');
            $newWhitelist = str_replace(',' . $acl->account, '', $whitelist);
            $this->dao->update($objectTable)->set('whitelist')->eq($newWhitelist)->where('id')->eq($acl->objectID)->exec();
            $this->dao->delete()->from(TABLE_ACL)->where('id')->eq($id)->exec();

            if($acl->objectType == 'product')
            {
                $product = $this->loadModel('product')->getByID($acl->objectID);
                if($product->program) $this->personnel->deleteProgramWhitelist($product->program, $acl->account);
            }
            if($acl->objectType == 'sprint')  $this->personnel->deleteProjectWhitelist($acl->objectID, $acl->account);

            $this->loadModel('user')->updateUserView($acl->objectID, $acl->objectType, array($acl->account));
            $this->loadModel('action')->create('whitelist', $acl->objectID, 'managedWhitelist', '', $acl->objectType);

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'load' => true));
        }
    }
}
