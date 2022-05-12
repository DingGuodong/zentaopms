<?php
class groupTest
{
    public function __construct()
    {
         global $tester;
         $this->objectModel = $tester->loadModel('group');
    }

    /**
     * Test create a group.
     *
     * @param mixed $param
     * @access public
     * @return object
     */
    public function createObject($param)
    {
        foreach($param as $k => $v) $_POST[$k] = $v;

        $groupID = $this->objectModel->create();
        unset($_POST);

        if(dao::isError()) return dao::getError();

        $object = $this->objectModel->getById($groupID);

        return $object;
    }

    /**
     * Update a group.
     *
     * @param  int    $groupID
     * @access public
     * @return void
     */
    public function updateTest($groupID)
    {
        $objects = $this->objectModel->update($groupID);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Copy a group.
     *
     * @param  int    $groupID
     * @access public
     * @return void
     */
    public function copyTest($groupID)
    {
        $objects = $this->objectModel->copy($groupID);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Copy privileges.
     *
     * @param  string    $fromGroup
     * @param  string    $toGroup
     * @access public
     * @return void
     */
    public function copyPrivTest($fromGroup, $toGroup)
    {
        $objects = $this->objectModel->copyPriv($fromGroup, $toGroup);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Copy user.
     *
     * @param  string    $fromGroup
     * @param  string    $toGroup
     * @access public
     * @return void
     */
    public function copyUserTest($fromGroup, $toGroup)
    {
        $objects = $this->objectModel->copyUser($fromGroup, $toGroup);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get group lists.
     *
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getListTest($projectID = 0)
    {
        $objects = $this->objectModel->getList($projectID = 0);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get group pairs.
     *
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getPairsTest($projectID = 0)
    {
        $objects = $this->objectModel->getPairs($projectID = 0);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get group by id.
     *
     * @param  int    $groupID
     * @access public
     * @return object
     */
    public function getByIDTest($groupID)
    {
        $objects = $this->objectModel->getByID($groupID);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get group by account.
     *
     * @param  string    $account
     * @access public
     * @return array
     */
    public function getByAccountTest($account)
    {
        $objects = $this->objectModel->getByAccount($account);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get groups by accounts.
     *
     * @param  array  $accounts
     * @access public
     * @return array
     */
    public function getByAccountsTest($accounts)
    {
        $objects = $this->objectModel->getByAccounts($accounts);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get the account number in the group.
     *
     * @param  array  $groupIdList
     * @access public
     * @return array
     */
    public function getGroupAccountsTest($groupIdList)
    {
        $objects = $this->objectModel->getGroupAccounts($groupIdList);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get privileges of a groups.
     *
     * @param  int    $groupID
     * @access public
     * @return array
     */
    public function getPrivsTest($groupID)
    {
        $objects = $this->objectModel->getPrivs($groupID);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get user pairs of a group.
     *
     * @param  int    $groupID
     * @access public
     * @return array
     */
    public function getUserPairsTest($groupID)
    {
        $objects = $this->objectModel->getUserPairs($groupID);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get user programs of a group.
     *
     * @param  int    $groupID
     * @access public
     * @return array
     */
    public function getUserProgramsTest($groupID)
    {
        $objects = $this->objectModel->getUserPrograms($groupID);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get the ID of the group that has access to the program.
     *
     * @access public
     * @return array
     */
    public function getAccessProgramGroupTest()
    {
        $objects = $this->objectModel->getAccessProgramGroup();

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Delete a group.
     *
     * @param  int    $groupID
     * @param  null   $null      compatible with that of model::delete()
     * @access public
     * @return void
     */
    public function deleteTest($groupID, $null = null)
    {
        $objects = $this->objectModel->delete($groupID, $null = null);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Update privilege of a group.
     *
     * @param  int    $groupID
     * @access public
     * @return bool
     */
    public function updatePrivByGroupTest($groupID, $menu, $version)
    {
        $objects = $this->objectModel->updatePrivByGroup($groupID, $menu, $version);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Update view priv.
     *
     * @param  int    $groupID
     * @access public
     * @return bool
     */
    public function updateViewTest($groupID)
    {
        $objects = $this->objectModel->updateView($groupID);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Update privilege by module.
     *
     * @access public
     * @return void
     */
    public function updatePrivByModuleTest()
    {
        $objects = $this->objectModel->updatePrivByModule();

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Update users.
     *
     * @param  int    $groupID
     * @access public
     * @return void
     */
    public function updateUserTest($groupID)
    {
        $objects = $this->objectModel->updateUser($groupID);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Update project admins.
     *
     * @param  int    $groupID
     * @access public
     * @return void
     */
    public function updateProjectAdminTest($groupID)
    {
        $objects = $this->objectModel->updateProjectAdmin($groupID);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Sort resource.
     *
     * @access public
     * @return void
     */
    public function sortResourceTest()
    {
        $objects = $this->objectModel->sortResource();

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Check menu have module
     *
     * @param  string    $menu
     * @param  string    $moduleName
     * @access public
     * @return void
     */
    public function checkMenuModuleTest($menu, $moduleName)
    {
        $objects = $this->objectModel->checkMenuModule($menu, $moduleName);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get modules in menu
     *
     * @param  string    $menu
     * @access public
     * @return void
     */
    public function getMenuModulesTest($menu)
    {
        $objects = $this->objectModel->getMenuModules($menu);

        if(dao::isError()) return dao::getError();

        return $objects;
    }
}
