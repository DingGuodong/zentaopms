<?php
/**
 * The control file of kanban module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yuchun Li <liyuchun@easycorp.ltd>
 * @package     kanban
 * @version     $Id: control.php 4460 2021-10-26 11:03:02Z chencongzhi520@gmail.com $
 * @link        https://www.zentao.net
 */
class kanban extends control
{
    /**
     * Kanban space.
     *
     * @param  string $browseType all|my|other|closed
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function space($browseType = 'my', $recTotal = 0, $recPerPage = 15, $pageID = 1)
    {

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->title       = $this->lang->kanbanspace->common;
        $this->view->spaceList   = $this->kanban->getSpaceList($browseType, $pager);
        $this->view->browseType  = $browseType;
        $this->view->pager       = $pager;
        $this->view->users       = $this->loadModel('user')->getPairs('noletter');
        $this->view->usersAvatar = $this->user->getAvatarPairs();

        $this->display();
    }

    /**
     * Create a space.
     *
     * @access public
     * @return void
     */
    public function createSpace()
    {
        if(!empty($_POST))
        {
            $spaceID = $this->kanban->createSpace();

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('kanbanSpace', $spaceID, 'created');
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $this->view->users = $this->loadModel('user')->getPairs('noclosed|nodeleted');

        $this->display();
    }

    /**
     * Edit a space.
     *
     * @param  int    $spaceID
     * @access public
     * @return void
     */
    public function editSpace($spaceID)
    {
        $this->loadModel('action');
        if(!empty($_POST))
        {
            $changes = $this->kanban->updateSpace($spaceID);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $actionID = $this->action->create('kanbanSpace', $spaceID, 'edited');
            $this->action->logHistory($actionID, $changes);

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $this->view->space = $this->kanban->getSpaceById($spaceID);
        $this->view->users = $this->loadModel('user')->getPairs('noclosed');

        $this->display();
    }

    /*
     * Close a space.
     *
     * @param  int    $spaceID
     * @access public
     * @return void
     */
    public function closeSpace($spaceID)
    {
        $this->loadModel('action');

        if(!empty($_POST))
        {
            $changes = $this->kanban->closeSpace($spaceID);

            if(dao::isError()) die(js::error(dao::getError()));

            $actionID = $this->action->create('kanbanSpace', $spaceID, 'closed', $this->post->comment);
            $this->action->logHistory($actionID, $changes);

            die(js::reload('parent.parent'));
        }

        $this->view->space   = $this->kanban->getSpaceById($spaceID);
        $this->view->actions = $this->action->getList('kanbanSpace', $spaceID);
        $this->view->users   = $this->loadModel('user')->getPairs('noletter');

        $this->display();
    }

    /**
     * Delete a space.
     *
     * @param  int    $spaceID
     * @param  string $confirm
     * @access public
     * @return void
     */
    public function deleteSpace($spaceID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->kanban->confirmDelete, $this->createLink('kanban', 'deleteSpace', "spaceID=$spaceID&confirm=yes")));
        }
        else
        {
            $this->kanban->delete(TABLE_KANBANSPACE, $spaceID);
            die(js::reload('parent'));
        }
    }

    /**
     * Create a kanban.
     *
     * @param  int    $spaceID
     * @access public
     * @return void
     */
    public function create($spaceID = 0)
    {
        if(!empty($_POST))
        {
            $kanbanID = $this->kanban->create();

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('kanban', $kanbanID, 'created');
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $this->view->users      = $this->loadModel('user')->getPairs('noclosed|nodeleted');
        $this->view->spaceID    = $spaceID;
        $this->view->spacePairs = array(0 => '') + $this->kanban->getSpacePairs();

        $this->display();
    }

    /**
     * Edit a kanban.
     *
     * @param  int    $kanbanID
     * @access public
     * @return void
     */
    public function edit($kanbanID = 0)
    {
        $this->loadModel('action');
        if(!empty($_POST))
        {
            $changes = $this->kanban->update($kanbanID);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $actionID = $this->action->create('kanban', $kanbanID, 'edited');
            $this->action->logHistory($actionID, $changes);

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $this->view->users      = $this->loadModel('user')->getPairs('noclosed');
        $this->view->spacePairs = array(0 => '') + $this->kanban->getSpacePairs();
        $this->view->kanban     = $this->kanban->getByID($kanbanID);

        $this->display();
    }

    /*
     * Close a kanban.
     *
     * @param  int    $kanbanID
     * @access public
     * @return void
     */
    public function close($kanbanID)
    {
        $this->loadModel('action');

        if(!empty($_POST))
        {
            $changes = $this->kanban->close($kanbanID);

            if(dao::isError()) die(js::error(dao::getError()));

            $actionID = $this->action->create('kanban', $kanbanID, 'closed', $this->post->comment);
            $this->action->logHistory($actionID, $changes);

            die(js::reload('parent.parent'));
        }

        $this->view->kanban  = $this->kanban->getByID($kanbanID);
        $this->view->actions = $this->action->getList('kanban', $kanbanID);
        $this->view->users   = $this->loadModel('user')->getPairs('noletter');

        $this->display();
    }

     /**
     * View a kanban.
     *
     * @param  int    $kanbanID
     * @access public
     * @return void
     */
    public function view($kanbanID)
    {
        $kanban = $this->kanban->getByID($kanbanID);

        if(!$kanban)
        {
            if(defined('RUN_MODE') && RUN_MODE == 'api') return $this->send(array('status' => 'fail', 'code' => 404, 'message' => '404 Not found'));
            die(js::error($this->lang->notFound) . js::locate($this->createLink('kanban', 'space')));
        }

        $kanbanIdList = $this->kanban->getCanViewObjects();
        if(!$this->app->user->admin and !in_array($kanbanID, $kanbanIdList)) die(js::error($this->lang->kanban->accessDenied) . js::locate('back'));

        $space = $this->kanban->getSpaceByID($kanban->space);

        $this->kanban->setSwitcher($kanban);
        $this->kanban->setHeaderActions($kanban);

        $userList    = array();
        $avatarPairs = $this->dao->select('account, avatar')->from(TABLE_USER)->where('deleted')->eq(0)->fetchPairs();
        foreach($avatarPairs as $account => $avatar)
        {
            if(!$avatar) continue;
            $userList[$account]['avatar'] = $avatar;
        }

        $this->view->title    = $this->lang->kanban->view;
        $this->view->regions  = $this->kanban->getKanbanData($kanbanID);
        $this->view->userList = $userList;
        $this->view->kanban   = $kanban;

        $this->display();
    }

    /**
     * Delete a kanban.
     *
     * @param  int    $kanbanID
     * @param  string $confirm
     * @access public
     * @return void
     */
    public function delete($kanbanID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->kanban->confirmDelete, $this->createLink('kanban', 'delete', "kanbanID=$kanbanID&confirm=yes")));
        }
        else
        {
            $this->kanban->delete(TABLE_KANBAN, $kanbanID);
            die(js::locate($this->createLink('kanban', 'space'), 'parent'));
        }
    }

    /**
     * Create a region.
     *
     * @param  int    $kanbanID
     * @access public
     * @return void
     */
    public function createRegion($kanbanID)
    {
        if(!empty($_POST))
        {
            $kanban       = $this->kanban->getByID($kanbanID);
            $copyRegionID = (int)$_POST['region'];
            unset($_POST['region']);

            $regionID = $this->kanban->createRegion($kanban, '', $copyRegionID);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $regions     = $this->kanban->getRegionPairs($kanbanID);
        $regionPairs = array();
        foreach($regions as $regionID => $region)
        {
            $regionPairs[$regionID] = $this->lang->kanban->copy . $region . $this->lang->kanban->styleCommon;
        }

        $this->view->regions = array('custom' => $this->lang->kanban->custom) + $regionPairs;
        $this->display();
    }

    /**
     * Delete a region
     *
     * @param  int    $regionID
     * @param  string $confirm
     * @access public
     * @return void
     */
    public function deleteRegion($regionID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->kanban->confirmDelete, $this->createLink('kanban', 'deleteRegion', "regionID=$regionID&confirm=yes")));
        }
        else
        {
            $this->kanban->delete(TABLE_KANBANREGION, $regionID);
            die(js::reload('parent'));
        }
    }

    /**
     * Create a lane for a kanban.
     *
     * @param  int    $kanbanID
     * @param  int    $regionID
     * @access public
     * @return void
     */
    public function createLane($kanbanID, $regionID)
    {
        if(!empty($_POST))
        {
            $laneID = $this->kanban->createLane($kanbanID, $regionID, $lane = null);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('kanbanLane', $laneID, 'created');
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $this->view->lanes = $this->kanban->getLanePairsByRegion($regionID);
        $this->display();
    }
    
    /**
     * Delete a lane.
     *
     * @param  int    $laneID
     * @param  string $confirm no|yes
     * @access public
     * @return void
     */
    public function deleteLane($laneID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->kanban->confirmDelete, $this->createLink('kanban', 'deleteLane', "laneID=$laneID&confirm=yes"), ''));
        }
        else
        {
            $this->kanban->delete(TABLE_KANBANLANE, $laneID);
            die(js::reload('parent'));
        }
    }

    /**
     * Create a column for a kanban.
     *
     * @param  int    $columnID
     * @param  string $position left|right
     * @access public
     * @return void
     */
    public function createColumn($columnID, $position = 'left')
    {
        $column = $this->kanban->getColumnByID($columnID);

        if($_POST)
        {
            $order    = $position == 'left' ? $column->order : $column->order + 1;
            $columnID = $this->kanban->createColumn($column->region, null, $order);
            if(dao::isError()) $this->send(array('message' => dao::getError(), 'result' => 'fail'));

            $this->loadModel('action')->create('kanbanColumn', $columnID, 'Created');
            $this->send(array('message' => $this->lang->saveSuccess, 'result' => 'success', 'locate' => 'parent'));
        }

        $this->view->title    = $this->lang->kanban->createColumn;
        $this->view->column   = $column;
        $this->view->position = $position;
        $this->display();
    }

    /**
     * Delete a column.
     *
     * @param  int    $columnID
     * @param  string $confirm no|yes
     * @access public
     * @return void
     */
    public function deleteColumn($columnID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->kanban->confirmDelete, $this->createLink('kanban', 'deleteColumn', "columnID=$columnID&confirm=yes"), ''));
        }
        else
        {
            $this->kanban->delete(TABLE_KANBANCOLUMN, $columnID);
            die(js::reload('parent'));
        }
    }

    /**
     * Create a card.
     *
     * @param  int    $kanbanID
     * @param  int    $regionID
     * @param  int    $groupID
     * @param  int    $laneID
     * @param  int    $columnID
     * @access public
     * @return void
     */
    public function createCard($kanbanID = 0, $regionID = 0, $groupID = 0, $laneID = 0, $columnID = 0)
    {
        if($_POST)
        {
            $cardID = $this->kanban->createCard($kanbanID, $regionID, $groupID, $laneID, $columnID);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $this->loadModel('action')->create('kanbancard', $cardID, 'created');

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $this->view->users = $this->loadModel('user')->getPairs('noclosed|nodeleted');
        $this->display();
    }

    /**
     * Edit a card.
     *
     * @param  int    $cardID
     * @access public
     * @return void
     */
    public function editCard($cardID)
    {
        $this->loadModel('action');
        if(!empty($_POST))
        {
            $changes = $this->kanban->updateCard($cardID);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $actionID = $this->action->create('kanbanCard', $cardID, 'edited');
            $this->action->logHistory($actionID, $changes);

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $this->view->card     = $this->kanban->getCardByID($cardID);
        $this->view->actions  = $this->action->getList('kanbancard', $cardID);
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed');
        $this->view->allUsers = $this->loadModel('user')->getPairs();

        $this->display();
    }

    /**
     * View a card.
     *
     * @param  int    $cardID
     * @access public
     * @return void
     */
    public function viewCard($cardID)
    {
        $this->loadModel('action');

        $card   = $this->kanban->getCardByID($cardID);
        $kanban = $this->kanban->getByID($card->kanban);
        $space  = $this->kanban->getSpaceById($kanban->space);

        $this->view->card        = $card;
        $this->view->actions     = $this->action->getList('kanbancard', $cardID);
        $this->view->users       = $this->loadModel('user')->getPairs('noletter');
        $this->view->space       = $space;
        $this->view->kanban      = $kanban;
        $this->view->usersAvatar = $this->user->getAvatarPairs();

        $this->display();
    }

	/**
	 * Delete a card.
	 *
	 * @param  int    $cardID
	 * @param  string $confirm no|yes
	 * @access public
	 * @return void
	 */
    public function deleteCard($cardID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->kanban->confirmDelete, $this->createLink('kanban', 'deleteCard', "cardID=$cardID&confirm=yes")));
        }
        else
        {
            $this->kanban->delete(TABLE_KANBANCARD, $cardID);

            if(isonlybody()) die(js::reload('parent.parent'));
            die(js::reload('parent'));
        }
    }

    /**
     * Set WIP.
     *
     * @param  int    $columnID
     * @param  int    $executionID
     * @param  string $from kanban|execution
     * @access public
     * @return void
     */
    public function setWIP($columnID, $executionID = 0, $from = 'kanban')
    {
        if($_POST)
        {
            $this->kanban->setWIP($columnID);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('kanbancolumn', $columnID, 'Edited', '', $executionID);
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $this->app->loadLang('story');

        $column = $this->kanban->getColumnById($columnID);
        if(!$column) die(js::error($this->lang->notFound) . js::locate($this->createLink('execution', 'kanban', "executionID=$executionID")));

        $title  = isset($column->parentName) ? $column->parentName . '/' . $column->name : $column->name;

        $this->view->title  = $title . $this->lang->colon . $this->lang->kanban->setWIP . '(' . $this->lang->kanban->WIP . ')';
        $this->view->column = $column;
        $this->view->from   = $from;

        if($from != 'kanban') $this->view->status = zget($this->config->kanban->{$column->laneType . 'ColumnStatusList'}, $column->type);
        $this->display();
    }

    /**
     * Set lane info.
     *
     * @param  int    $laneID
     * @param  int    $executionID
     * @param  string $from kanban|execution
     * @access public
     * @return void
     */
    public function setLane($laneID, $executionID = 0, $from = 'kanban')
    {
        if($_POST)
        {
            $this->kanban->setLane($laneID);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('kanbanlane', $laneID, 'Edited', '', $executionID);

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        $lane = $this->kanban->getLaneById($laneID);
        if(!$lane) die(js::error($this->lang->notFound) . js::locate($this->createLink('execution', 'kanban', "executionID=$executionID")));

        $this->view->title = $from == 'kanban' ? $this->lang->edit . '“' . $lane->name . '”' . $this->lang->kanbanlane->common : zget($this->lang->kanban->laneTypeList, $lane->type) . $this->lang->colon . $this->lang->kanban->setLane;
        $this->view->lane  = $lane;
        $this->view->from  = $from;

        $this->display();
    }

    /**
     * Set lane column info.
     *
     * @param  int $columnID
     * @param  int $executionID
     * @param  string $from kanban|execution
     * @access public
     * @return void
     */
    public function setColumn($columnID, $executionID = 0, $from = 'kanban')
    {
        $column = $this->kanban->getColumnById($columnID);

        if($_POST)
        {
            /* Check lane column name is unique. */
            $exist = $this->kanban->getColumnByName($this->post->name, $column->lane);
            if($exist and $exist->id != $columnID and $from != 'kanban')
            {
                return $this->sendError($this->lang->kanban->noColumnUniqueName);
            }

            $changes = $this->kanban->updateLaneColumn($columnID, $column);
            if(dao::isError()) return $this->sendError(dao::getError());
            if($changes)
            {
                $actionID = $this->loadModel('action')->create('kanbancolumn', $columnID, 'Edited', '', $executionID);
                $this->action->logHistory($actionID, $changes);
            }

            return $this->sendSuccess(array('locate' => 'parent'));
        }

        $this->view->column = $column;
        $this->display();
    }

    /**
     * AJAX: Update the cards sorting of the lane column.
     *
     * @param  string $laneType story|bug|task
     * @param  int    $columnID
     * @param  string $orderBy id_desc|id_asc|pri_desc|pri_asc|lastEditedDate_desc|lastEditedDate_asc|deadline_desc|deadline_asc|assignedTo_asc
     * @access public
     * @return void
     */
    public function ajaxCardsSort($laneType, $columnID, $orderBy = 'id_desc')
    {
        $oldCards = array();
        $column   = $this->dao->select('parent,cards')->from(TABLE_KANBANCOLUMN)->where('id')->eq($columnID)->fetch();

        /* Get the cards of the kanban column. */
        if($column->parent == -1)
        {
            $childColumns = $this->dao->select('id,cards')->from(TABLE_KANBANCOLUMN)->where('parent')->eq($columnID)->fetchAll();
            foreach($childColumns as $childColumn)
            {
                $oldCards[$childColumn->id] = $childColumn->cards;
            }
        }
        else
        {
            $oldCards[$columnID] = $column->cards;
        }

        /* Update Kanban column card order. */
        $table = $this->config->objectTables[$laneType];
        foreach($oldCards as $colID => $cards)
        {
            if(empty($cards)) continue;
            $objects = $this->dao->select('id')->from($table)
                ->where('id')->in($cards)
                ->orderBy($orderBy)
                ->fetchPairs('id');

            $objectIdList = ',' . implode(',', $objects) . ',';
            $this->dao->update(TABLE_KANBANCOLUMN)->set('cards')->eq($objectIdList)->where('id')->eq($colID)->exec();
        }
        echo true;
    }

    /**
     * Change the order through the lane move up and down.
     *
     * @param  int     $executionID
     * @param  string  $currentType
     * @param  string  $targetType
     * @access public
     * @return void
     */
    public function laneMove($executionID, $currentType, $targetType)
    {
        if(empty($targetType)) return false;

        $this->kanban->updateLaneOrder($executionID, $currentType, $targetType);

        if(!dao::isError())
        {
            $laneID = $this->dao->select('id')->from(TABLE_KANBANLANE)->where('execution')->eq($executionID)->andWhere('type')->eq($currentType)->fetch('id');
            $this->loadModel('action')->create('kanbanlane', $laneID, 'Moved');
        }

        die(js::locate($this->createLink('execution', 'kanban', 'executionID=' . $executionID . '&type=all'), 'parent'));
    }

    /**
     * Ajax get contact users.
     *
     * @param  int    $contactListID
     * @access public
     * @return string
     */
    public function ajaxGetContactUsers($contactListID)
    {
        $this->loadModel('user');
        $list = $contactListID ? $this->user->getContactListByID($contactListID) : '';

        $users = $this->user->getPairs('devfirst|nodeleted|noclosed', $list ? $list->userList : '', $this->config->maxCount);

        if(!$contactListID) return print(html::select('team[]', $users, '', "class='form-control chosen' multiple"));

        return print(html::select('team[]', $users, $list->userList, "class='form-control chosen' multiple"));
    }

    /**
     * Ajax get kanban menu.
     *
     * @param  int    $kanbanID
     * @param  string $moduleName
     * @param  string $methodName
     * @access public
     * @return void
     */
    public function ajaxGetKanbanMenu($kanbanID, $moduleName, $methodName)
    {
        $kanbanIdList = $this->kanban->getCanViewObjects();
        $this->view->kanbanList = $this->dao->select('*')->from(TABLE_KANBAN)
            ->where('deleted')->eq('0')
            ->andWhere('id')->in($kanbanIdList)
            ->fetchGroup('space');

        $this->view->kanbanID   = $kanbanID;
        $this->view->spaceList  = $this->kanban->getSpacePairs('all');
        $this->view->module     = $moduleName;
        $this->view->method     = $methodName;
        $this->display();
    }
}
