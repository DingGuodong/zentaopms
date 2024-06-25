<?php
declare(strict_types=1);

/**
 * HttpClient of mock.
 *
 * @copyright Copyright 2009-2022 QingDao Nature Easy Soft Network Technology Co,LTD (www.cnezsoft.com)
 * @author    guanxiying <guanxiying@easycorp.ltd>
 * @package
 * @license   LGPL
 * @version   $Id$
 * @Link      https://www.zentao.net
 */
class httpClient
{
    /**
     * Http.
     *
     * @param  string              $url
     * @param  string|array|object $data
     * @param  array               $options   This is option and value pair, like CURLOPT_HEADER => true. Use curl_setopt function to set options.
     * @param  array               $headers   Set request headers.
     * @param  string              $dataType
     * @param  string              $method    POST|PATCH|PUT
     * @param  int                 $timeout
     * @param  bool                $httpCode  Return a array contains response, http code, body, header. such as [response, http_code, 'body' => body, 'header' => header].
     * @param  bool                $log       Save to log or not
     * @static
     * @access public
     * @return string|array
     */
    public function request(string $url, string|array|object|null $data = null, array $options = array(), array $headers = array(), string $dataType = 'data', string $method = 'POST', int $timeout = 30, bool $httpCode = false, bool $log = true): string|array
    {
        $paths = explode('/', $url);
        $year  = (int)$paths[count($paths)-1];

        $data = new stdclass();
        $data->days = array();
        $data->days[] = (object)array('name' => '元旦', 'date' => $year . '-01-01', 'isOffDay' => true);
        $data->days[] = (object)array('name' => '国庆', 'date' => $year . '-10-01', 'isOffDay' => true);
        $data->days[] = (object)array('name' => '国庆', 'date' => $year . '-10-02', 'isOffDay' => true);

        return json_encode($data);
    }
}

class holidayTest
{
    public function __construct()
    {
         global $tester;
         $this->objectModel = $tester->loadModel('holiday');
    }

    /**
     * 测试通过 ID 获取节假日。
     * Test get holiday by id.
     *
     * @param  int           $id
     * @access public
     * @return object|string|array
     */
    public function getByIdTest(int $id): object|string|array
    {
        $objects = $this->objectModel->getById($id);

        if($objects === false) return 'Object not found';
        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * 测试获取节假日列表
     * Test get holiday list.
     *
     * @param  string $year
     * @param  string $type
     * @access public
     * @return string|array
     */
    public function getListTest(string $year = '', string $type = 'all'): string|array
    {
        if($year == 'thisyear') $year = date('Y');
        if($year == 'lastyear') $year = date('Y', strtotime('-1 year'));
        $objects = $this->objectModel->getList($year, $type);

        if(dao::isError()) return dao::getError();

        return implode(',', array_keys($objects));
    }

    /**
     * 测试获取年份。
     * Test get year pairs.
     *
     * @access public
     * @return int|array
     */
    public function getYearPairsTest(): int|array
    {
        $objects = $this->objectModel->getYearPairs();

        if(dao::isError()) return dao::getError();

        return count($objects);
    }

    /**
     * 测试创建一个家假日。
     * Test create a holiday.
     *
     * @param  array             $param
     * @access public
     * @return object|bool|array
     */
    public function createTest(array $param = array()): object|bool|array
    {
        $defaultParam['type']  = 'holiday';
        $defaultParam['begin'] = '2022-01-01';
        $defaultParam['end']   = '2022-02-01';
        $defaultParam['name']  = '测试创建holiday';
        $defaultParam['desc']  = '默认的holiday';

        $holiday = new stdclass();

        foreach($defaultParam as $field => $defaultValue) $holiday->{$field} = $defaultValue;
        foreach($param as $key => $value) $holiday->{$key} = $value;

        $lastInsertID = $this->objectModel->create($holiday);

        if(dao::isError())
        {
            return dao::getError();
        }
        else
        {
            $object = $this->objectModel->getByID($lastInsertID);
            return $object;
        }
    }

    /**
     * 测试更新一个节假日。
     * Test update a holiday.
     *
     * @param  int    $holidayID
     * @param  array  $param
     * @access public
     * @return object
     */
    public function updateTest(int $holidayID, array $param = array()): string|array
    {
        global $tester;
        $object = $tester->dbh->query("SELECT * FROM " . TABLE_HOLIDAY  ." WHERE id = {$holidayID}")->fetch();

        $holiday = new stdclass();
        foreach($object as $field => $value)
        {
            if(in_array($field, array_keys($param)))
            {
                $holiday->{$field} = $param[$field];
            }
            else
            {
                $holiday->{$field} = $value;
            }
        }

        $this->objectModel->update($holiday);
        unset($_POST);

        if(dao::isError())
        {
            return dao::getError();
        }
        else
        {
            return 'true';
        }
    }

    /**
     * 测试通过开始和结束日期获取节假日。
     * Test get holidays by begin and end.
     *
     * @param  string       $begin
     * @param  string       $end
     * @access public
     * @return int|array
     */
    public function getHolidaysTest(string $begin, string $end): int|array
    {
        $begin = date('Y-m-d', strtotime($begin));
        $end   = date('Y-m-d', strtotime($end));
        $objects = $this->objectModel->getHolidays($begin, $end);

        if(dao::isError()) return dao::getError();

        return count($objects);
    }

    /**
     * 测试获取工作日。
     * Test get working days.
     *
     * @param  string    $begin
     * @param  string    $end
     * @access public
     * @return int|array
     */
    public function getWorkingDaysTest(string $begin = '', string $end = ''): int|array
    {
        $begin = date('Y-m-d', strtotime($begin));
        $end   = date('Y-m-d', strtotime($end));
        $objects = $this->objectModel->getWorkingDays($begin, $end);

        if(dao::isError()) return dao::getError();

        return count($objects);
    }

    /**
     * 测试获取实际工作日。
     * Test get actual working days.
     *
     * @param  string    $begin
     * @param  string    $end
     * @access public
     * @return int|array
     */
    public function getActualWorkingDaysTest(string $begin, string $end): int|array
    {
        $begin = $begin ? date('Y-m-d', strtotime($begin)) : '0000-00-00';
        $end   = $end ? date('Y-m-d', strtotime($end)) : '';
        $objects = $this->objectModel->getActualWorkingDays($begin, $end);

        if(dao::isError()) return dao::getError();

        return count($objects);
    }

    /**
     * 测试获取开始和结束日期之间的日期。
     * Test get the dates between the begin and end.
     *
     * @param  string    $begin
     * @param  string    $end
     * @access public
     * @return int|array
     */
    public function getDaysBetweenTest(string $begin, string $end): int|array
    {
        $objects = $this->objectModel->getDaysBetween($begin, $end);

        if(dao::isError()) return dao::getError();

        return count($objects);
    }

    /**
     * 测试某天是否是节假日.
     * Test judge if is holiday.
     *
     * @param  string      $date
     * @access public
     * @return string|array
     */
    public function isHolidayTest(string $date): string|array
    {
        $objects = $this->objectModel->isHoliday($date);

        if(dao::isError()) return dao::getError();

        return $objects === true ? "It is a holiday" : "It is not a holiday";
    }

    /**
     * 测试某天是否是工作日。
     * Test judge if is a working day.
     *
     * @param  string       $date
     * @access public
     * @return string|array
     */
    public function isWorkingDayTest(string $date): string|array
    {
        $date = !empty($date) ? date('Y-m-d', strtotime($date)) : '0000-00-00';
        $objects = $this->objectModel->isWorkingDay($date);

        if(dao::isError()) return dao::getError();

        return $objects === true ? 'It is a working day' : 'It is not a working day';
    }

    /**
     * 测试更新项目工期。
     * Test update project duration.
     *
     * @param  int       $testProjectID
     * @param  int       $holidayID
     * @param  bool      $updateDuration
     * @access public
     * @return int|array
     */
    public function updateProgramPlanDurationTest(int $testProjectID, int $holidayID, bool $updateDuration): int|array
    {
        global $tester;

        if($updateDuration)
        {
            $holiday = $tester->dao->select('*')->from(TABLE_HOLIDAY)->where('id')->eq($holidayID)->fetch();
            $this->objectModel->updateProgramPlanDuration($holiday->begin, $holiday->end);
        }

        $project = $tester->dao->select('*')->from(TABLE_PROJECT)->where('id')->eq($testProjectID)->fetch();

        if(dao::isError()) return dao::getError();

        return $project->planDuration;
    }

    /**
     * 测试更新项目的实际工期。
     * Test update project real duration.
     *
     * @param  int       $testProjectID
     * @param  int       $holiday
     * @param  bool      $updateDuration
     * @access public
     * @return int|array
     */
    public function updateProjectRealDurationTest(int $testProjectID, int $holidayID, bool $updateDuration): int|array
    {
        global $tester;

        if($updateDuration)
        {
            $holiday = $tester->dao->select('*')->from(TABLE_HOLIDAY)->where('id')->eq($holidayID)->fetch();
            $this->objectModel->updateProjectRealDuration($holiday->begin, $holiday->end);
        }

        $project = $tester->dao->select('*')->from(TABLE_PROJECT)->where('id')->eq($testProjectID)->fetch();

        if(dao::isError()) return dao::getError();

        return $project->realDuration;
    }

    /**
     * 测试更新任务计划工期。
     * Test update task plan duration.
     *
     * @param  int       $testTaskID
     * @param  int       $holidayID
     * @param  bool      $updateDuration
     * @access public
     * @return int|array
     */
    public function updateTaskPlanDurationTest(int $testTaskID, int $holidayID, bool $updateDuration): int|array
    {
        global $tester;

        if($updateDuration)
        {
            $holiday = $tester->dao->select('*')->from(TABLE_HOLIDAY)->where('id')->eq($holidayID)->fetch();
            $this->objectModel->updateTaskPlanDuration($holiday->begin, $holiday->end);
        }

        $task = $tester->dao->select('*')->from(TABLE_TASK)->where('id')->eq($testTaskID)->fetch();

        if(dao::isError()) return dao::getError();

        return $task->planDuration;
    }

    /**
     * 测试更新任务实际工期。
     * Test update task real duration.
     *
     * @param  int       $testTaskID
     * @param  int       $holidayID
     * @param  bool      $updateDuration
     * @access public
     * @return int|array
     */
    public function updateTaskRealDurationTest(int $testTaskID, int $holidayID, bool $updateDuration): int|array
    {
        global $tester;

        if($updateDuration)
        {
            $holiday = $tester->dao->select('*')->from(TABLE_HOLIDAY)->where('id')->eq($holidayID)->fetch();
            $this->objectModel->updateTaskRealDuration($holiday->begin, $holiday->end);
        }

        $task = $tester->dao->select('*')->from(TABLE_TASK)->where('id')->eq($testTaskID)->fetch();

        if(dao::isError()) return dao::getError();

        return $task->realDuration;
    }

    /**
     * 测试通过 API 获取节假日。
     * Test get holidays by api.
     *
     * @param  string       $year
     * @access public
     * @return int|array
     */
    public function getHolidayByAPITest(string $year): int|array
    {
        global $app;
        $app->wwwRoot = dirname(__FILE__, 5) . DS . 'www' . DS;
        common::$httpClient = new httpClient();

        if($year == 'this year') $year = '2023';
        if($year == 'last year') $year = '2022';
        if($year == 'next year') $year = '2024';
        $objects = $this->objectModel->getHolidayByAPI($year);

        if(dao::isError()) return dao::getError();

        return count($objects);
    }

    /**
     * 测试创建一个家假日。
     * Test create a holiday.
     *
     * @param  array             $holidayParams
     * @access public
     * @return object|bool|array
     */
    public function batchCreateTest(array $holidayParams = array()): object|bool|array
    {
        $defaultParam['type']  = 'holiday';
        $defaultParam['begin'] = '2022-01-01';
        $defaultParam['end']   = '2022-02-01';
        $defaultParam['name']  = '测试创建holiday';
        $defaultParam['desc']  = '默认的holiday';


        $holidays = array();
        foreach($holidayParams as $holidayParam)
        {
            $holiday = new stdclass();
            foreach($defaultParam as $field => $defaultValue) $holiday->{$field} = $defaultValue;
            foreach($holidayParam as $key => $value) $holiday->{$key} = $value;
            $holidays[] = $holiday;
        }

        $lastInsertID = $this->objectModel->batchCreate($holidays);

        if(dao::isError())
        {
            return dao::getError();
        }
        else
        {
            $object = $this->objectModel->getByID($lastInsertID);
            return $object;
        }
    }
}
