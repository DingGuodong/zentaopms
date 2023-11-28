<?php
declare(strict_types=1);
class messageTest
{
    /**
     * 构造函数。
     * Construct function.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
         global $tester;
         $this->objectModel = $tester->loadModel('message');
    }

    /**
     * 测试获取消息。
     * Test get messages.
     *
     * @param  string       $status
     * @access public
     * @return string|array
     */
    public function getMessagesTest(string $status): string|array
    {
        $objects = $this->objectModel->getMessages($status);

        if(dao::isError()) return dao::getError();

        return implode(',', array_keys($objects));
    }

    /**
     * 获取对象类型。
     * Get objectTypes.
     *
     * @access public
     * @return void
     */
    public function getObjectTypesTest(): array
    {
        $objects = $this->objectModel->getObjectTypes();

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get object actions.
     *
     * @access public
     * @return void
     */
    public function getObjectActionsTest()
    {
        $objects = $this->objectModel->getObjectActions();

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Check send.
     *
     * @param  int    $objectType
     * @param  int    $objectID
     * @param  int    $actionType
     * @param  int    $actionID
     * @param  string $actor
     * @access public
     * @return void
     */
    public function sendTest($objectType, $objectID, $actionType, $actionID, $actor = '')
    {
        $objects = $this->objectModel->send($objectType, $objectID, $actionType, $actionID, $actor = '');

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Save notice.
     *
     * @param  int    $objectType
     * @param  int    $objectID
     * @param  int    $actionType
     * @param  int    $actionID
     * @param  string $actor
     * @access public
     * @return void
     */
    public function saveNoticeTest($objectType, $objectID, $actionType, $actionID, $actor = '')
    {
        $objects = $this->objectModel->saveNotice($objectType, $objectID, $actionType, $actionID, $actor = '');

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get toList.
     *
     * @param  int    $objectType
     * @access public
     * @return void
     */
    public function getToListTest($objectType)
    {
        global $tester;
        $table   = $tester->config->objectTables[$objectType];
        $object  = $tester->dao->select('*')->from($table)->where('id')->eq('1')->fetch();
        $objects = $this->objectModel->getToList($object, $objectType);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get notice todos.
     *
     * @access public
     * @return void
     */
    public function getNoticeTodosTest()
    {
        $objects = $this->objectModel->getNoticeTodos();

        if(dao::isError()) return dao::getError();

        return $objects;
    }
}
