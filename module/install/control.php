<?php
/**
 * The control file of install currentModule of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     install
 * @version     $Id: control.php 4297 2013-01-27 07:51:45Z wwccss $
 * @link        http://www.zentao.net
 */
class install extends control
{
    /**
     * Construct function.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        if(!$this->app->installing && $this->app->tab != 'devops') helper::end();

        $this->app->loadLang('user');
        $this->app->loadLang('admin');
        $this->config->webRoot = getWebRoot();
    }

    /**
     * Index page of install module.
     *
     * @access public
     * @return void
     */
    public function index()
    {
        if(!isset($this->config->installed) or !$this->config->installed) $this->session->set('installing', true);

        $this->view->title = $this->lang->install->welcome;
        if(!isset($this->view->versionName)) $this->view->versionName = $this->config->version; // If the versionName variable has been defined in the max version, it cannot be defined here to avoid being overwritten.
        if($this->config->inQuickon)
        {
            $editionName = $this->config->edition === 'open' ? $this->lang->pmsName : $this->lang->{$this->config->edition . 'Name'};
            if($this->config->edition === 'max') $editionName = '';
            $this->view->versionName   = $editionName . str_replace(array('max', 'biz', 'ipd'), '', $this->view->versionName);
            $this->view->versionName   = $this->lang->devopsPrefix . $this->view->versionName;
            $this->lang->install->desc = $this->lang->install->desc . "\n" . $this->lang->install->devopsDesc;
        }
        $this->display();
    }

    /**
     * Checking agree license.
     *
     * @access public
     * @return void
     */
    public function license()
    {
        $this->view->title   = $this->lang->install->welcome;
        $this->view->license = $this->install->getLicense();
        $this->display();
    }

    /**
     * Check the system.
     *
     * @access public
     * @return void
     */
    public function step1()
    {
        $this->view->title          = $this->lang->install->checking;
        $this->view->phpVersion     = $this->install->getPhpVersion();
        $this->view->phpResult      = $this->install->checkPHP();
        $this->view->pdoResult      = $this->install->checkPDO();
        $this->view->pdoMySQLResult = $this->install->checkPDOMySQL();
        $this->view->jsonResult     = $this->install->checkJSON();
        $this->view->opensslResult  = $this->install->checkOpenssl();
        $this->view->mbstringResult = $this->install->checkMbstring();
        $this->view->zlibResult     = $this->install->checkZlib();
        $this->view->curlResult     = $this->install->checkCurl();
        $this->view->filterResult   = $this->install->checkFilter();
        $this->view->iconvResult    = $this->install->checkIconv();
        $this->view->tmpRootInfo    = $this->install->getTmpRoot();
        $this->view->tmpRootResult  = $this->install->checkTmpRoot();
        $this->view->dataRootInfo   = $this->install->getDataRoot();
        $this->view->dataRootResult = $this->install->checkDataRoot();
        $this->view->iniInfo        = $this->install->getIniInfo();

        $checkSession = ini_get('session.save_handler') == 'files';
        $this->view->sessionResult = 'ok';
        $this->view->checkSession  = $checkSession;
        if($checkSession)
        {
            $sessionResult = $this->install->checkSessionSavePath();
            $sessionInfo   = $this->install->getSessionSavePath();
            if($sessionInfo['path'] == '') $sessionResult = 'ok';

            $this->view->sessionResult = $sessionResult;
            $this->view->sessionInfo   = $sessionInfo;
        }

        $notice = '';
        if($this->config->framework->filterCSRF)
        {
            $httpType = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on') ? 'https' : 'http';
            if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https') $httpType = 'https';
            if(isset($_SERVER['REQUEST_SCHEME']) and strtolower($_SERVER['REQUEST_SCHEME']) == 'https') $httpType = 'https';

            $httpHost = zget($_SERVER, 'HTTP_HOST', '');
            if(empty($httpHost) or strpos($this->server->http_referer, "$httpType://$httpHost") !== 0) $notice = $this->lang->install->CSRFNotice;
        }

        $this->view->notice = $notice;
        $this->display();
    }

    /**
     * Set configs.
     *
     * @access public
     * @return void
     */
    public function step2()
    {
        if(!empty($_POST))
        {
            $return = $this->install->checkConfig();
            if($return->result != 'ok') return $this->send(array('result' => 'fail', 'callback' => "zui.Modal.alert({icon: 'icon-exclamation-sign', size: '480', iconClass: 'text-4xl text-warning',  message: '" . str_replace("'", '"', $return->error) . "'})"));

            $myConfig = array();
            foreach($_POST as $key => $value) $myConfig[$key] = $value;
            $this->session->set('myConfig', $myConfig);
            return $this->send(array('result' => 'success', 'load' => inlink('showTableProgress')));
        }
        $dbHost = $dbPort = $dbName = $dbUser = $dbPassword = '';

        /* Get mysql env in docker container. */
        if(getenv('MYSQL_HOST'))     $dbHost     = getenv('MYSQL_HOST');
        if(getenv('MYSQL_PORT'))     $dbPort     = getenv('MYSQL_PORT');
        if(getenv('MYSQL_DB'))       $dbName     = getenv('MYSQL_DB');
        if(getenv('MYSQL_USER'))     $dbUser     = getenv('MYSQL_USER');
        if(getenv('MYSQL_PASSWORD')) $dbPassword = getenv('MYSQL_PASSWORD');

        $this->view->title = $this->lang->install->setConfig;

        $this->view->dbHost     = $dbHost ? $dbHost : '127.0.0.1';
        $this->view->dbPort     = $dbPort ? $dbPort : '3306';
        $this->view->dbName     = $dbName ? $dbName : 'zentao';
        $this->view->dbUser     = $dbUser ? $dbUser : 'root';
        $this->view->dbPassword = $dbPassword ? $dbPassword : '';
        $this->display();
    }

    /**
     * Show create table progress box.
     *
     * @access public
     * @return void
     */
    public function showTableProgress()
    {
        $this->view->title = $this->lang->install->dbProgress;
        $this->display();
    }

    /**
     * Ajax create table and save log.
     *
     * @access public
     * @return void
     */
    public function ajaxCreateTable()
    {
        ignore_user_abort(true);
        set_time_limit(0);
        session_write_close();

        $logFile     = $this->install->buildDBLogFile('progress');
        $errorFile   = $this->install->buildDBLogFile('error');
        $successFile = $this->install->buildDBLogFile('success');
        if(file_exists($logFile))     unlink($logFile);
        if(file_exists($errorFile))   unlink($errorFile);
        if(file_exists($successFile)) unlink($successFile);

        $config           = json_decode(file_get_contents($this->install->buildDBLogFile('config')));
        $this->config->db = $config->db;
        $_POST            = (array)$config->post;
        $version          = $this->install->getDatabaseVersion();
        if($this->install->createTable($version, true)) file_put_contents($this->install->buildDBLogFile('success'), 'success');
    }

    /**
     * Ajax get progress and show in showTableProgress page.
     *
     * @param  int    $offset
     * @access public
     * @return void
     */
    public function ajaxShowProgress($offset = 0)
    {
        session_write_close();
        $logFile     = $this->install->buildDBLogFile('progress');
        $errorFile   = $this->install->buildDBLogFile('error');
        $successFile = $this->install->buildDBLogFile('success');

        $error  = !file_exists($errorFile)   ? '' : file_get_contents($errorFile);
        $finish = !file_exists($successFile) ? '' : file_get_contents($successFile);
        $log    = !file_exists($logFile)     ? '' : file_get_contents($logFile, false, null, $offset);
        $size   = 10 * 1024;
        if(!empty($log) && mb_strlen($log) > $size)
        {
            $left     = mb_substr($log, $size);
            $log      = mb_substr($log, 0, $size);
            $position = strpos($left, "\n");
            if($position !== false) $log .= substr($left, 0, $position + 1);
        }
        $log = trim($log);
        if(!empty($log)) $error = $finish = '';
        return print(json_encode(array('log' => str_replace("\n", "<br />", $log) . ($log ? '<br />' : ''), 'error' => $error, 'finish' => $finish, 'offset' => $offset + strlen($log))));
    }

    /**
     * Create the config file.
     *
     * @access public
     * @return void
     */
    public function step3()
    {
        if(!empty($_POST))
        {
            if(!isset($this->config->installed) || !$this->config->installed) return $this->send(array('result' => 'fail', 'message' => $this->lang->install->errorNotSaveConfig));
            return $this->send(array('result' => 'success', 'message' => '', 'load' => inlink('step4')));
        }
        /* Set the session save path when the session save path is null. */
        $customSession = false;
        $checkSession  = ini_get('session.save_handler') == 'files';
        if($checkSession)
        {
            if(!session_save_path())
            {
                /* Restart the session because the session save path is null when start the session last time. */
                session_write_close();

                $tmpRootInfo     = $this->install->getTmpRoot();
                $sessionSavePath = $tmpRootInfo['path'] . 'session';
                if(!is_dir($sessionSavePath)) mkdir($sessionSavePath, 0777, true);

                session_save_path($sessionSavePath);
                $customSession = true;

                $sessionResult = $this->install->checkSessionSavePath();
                if($sessionResult == 'fail') chmod($sessionSavePath, 0777);

                session_start();
                $this->session->set('installing', true);
            }
        }

        $this->view->app           = $this->app;
        $this->view->lang          = $this->lang;
        $this->view->config        = $this->config;
        $this->view->title         = $this->lang->install->saveConfig;
        $this->view->customSession = $customSession;
        $this->view->myConfig      = $this->session->myConfig;
        $this->display();
    }

    /**
     * Set system mode.
     *
     * @access public
     * @return void
     */
    public function step4()
    {
        if(!empty($_POST))
        {
            if(!isset($this->config->installed) || !$this->config->installed) return $this->send(array('result' => 'fail', 'message' => $this->lang->install->errorNotSaveConfig, 'load' => 'step3'));

            $this->loadModel('setting')->setItem('system.common.global.mode', $this->post->mode); // Update mode.
            $this->loadModel('custom')->disableFeaturesByMode($this->post->mode);
            return $this->send(array('result' => 'success', 'load' => inlink('step5')));
        }

        $this->view->title = $this->lang->install->selectMode;
        if(!isset($this->config->installed) || !$this->config->installed)
        {
            $this->view->error = $this->lang->install->errorNotSaveConfig;
            $this->display();
        }
        else
        {
            $this->app->loadLang('upgrade');

            list($disabledFeatures, $enabledScrumFeatures, $disabledScrumFeatures) = $this->loadModel('custom')->computeFeatures();

            $this->view->edition               = $this->config->edition;
            $this->view->disabledFeatures      = $disabledFeatures;
            $this->view->enabledScrumFeatures  = $enabledScrumFeatures;
            $this->view->disabledScrumFeatures = $disabledScrumFeatures;
            $this->display();
        }
    }

    /**
     * Create company, admin.
     *
     * @access public
     * @return void
     */
    public function step5()
    {
        if(!empty($_POST))
        {
            if(!isset($this->config->installed) || !$this->config->installed) return $this->send(array('result' => 'fail', 'message' => $this->lang->install->errorNotSaveConfig, 'load' => 'step3'));

            $this->loadModel('common');
            if($this->config->db->driver == 'dm') $this->install->execDMSQL();

            $this->install->grantPriv();
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->install->updateLang();
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            if($this->post->importDemoData) $this->install->importDemoData();

            $defaultProgram = $this->loadModel('setting')->getItem('owner=system&module=common&section=global&key=defaultProgram');
            if($this->config->systemMode == 'light' and empty($defaultProgram))
            {
                /* Lean mode create default program. */
                $programID = $this->loadModel('program')->createDefaultProgram();
                /* Set default program config. */
                $this->loadModel('setting')->setItem('system.common.global.defaultProgram', $programID);
            }

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('setting');
            $this->setting->updateVersion($this->config->version);
            $this->setting->setSN();
            $this->setting->setItem('system.common.global.flow', $this->post->flow);
            $this->setting->setItem('system.common.safe.mode', '1');
            $this->setting->setItem('system.common.safe.changeWeak', '1');
            $this->setting->setItem('system.common.global.cron', '1');

            $httpType = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on') ? 'https' : 'http';
            if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https') $httpType = 'https';
            if(isset($_SERVER['REQUEST_SCHEME']) and strtolower($_SERVER['REQUEST_SCHEME']) == 'https') $httpType = 'https';
            if(strpos($this->app->getClientLang(), 'zh') === 0) $this->loadModel('api')->createDemoData($this->lang->api->zentaoAPI, "{$httpType}://{$_SERVER['HTTP_HOST']}" . $this->app->config->webRoot . 'api.php/v1', '16.0');

            if($this->config->edition != 'open') $this->loadModel('upgrade')->processDataset();

            $link = $this->config->inQuickon ? inlink('app') : inlink('step6');
            return $this->send(array('result' => 'success', 'load' => $link));
        }

        if($this->config->inQuickon) $this->install->saveConfigFile();
        $this->app->loadLang('upgrade');

        $this->view->title = $this->lang->install->getPriv;
        if(!isset($this->config->installed) || !$this->config->installed)
        {
            $this->view->error = $this->lang->install->errorNotSaveConfig;
            $this->display();
        }
        else
        {
            $this->display();
        }
    }

    /**
     * Join zentao community or login pms.
     *
     * @access public
     * @return void
     */
    public function step6()
    {
        $this->loadModel('common');
        if(!isset($this->config->installed) or !$this->config->installed) $this->session->set('installing', true);

        $canDelFile  = is_writable($this->app->getAppRoot() . 'www');
        $installFile = $this->app->getAppRoot() . 'www/install.php';
        $upgradeFile = $this->app->getAppRoot() . 'www/upgrade.php';
        $installFileDeleted = ($canDelFile and file_exists($installFile)) ? unlink($installFile) : false;

        if($this->config->inQuickon)
        {
            $editionName = $this->config->edition === 'open' ? $this->lang->pmsName : $this->lang->{$this->config->edition . 'Name'};
            $this->lang->install->successLabel       = str_replace('IPD', '', $this->lang->install->successLabel);
            $this->lang->install->successNoticeLabel = str_replace('IPD', '', $this->lang->install->successNoticeLabel);
            $this->config->version                   = $editionName . str_replace(array('max', 'biz', 'ipd'), '', $this->config->version);
        }

        if($canDelFile and file_exists($upgradeFile)) unlink($upgradeFile);
        unset($_SESSION['installing']);
        unset($_SESSION['myConfig']);
        session_destroy();

        $logFile     = $this->install->buildDBLogFile('progress');
        $errorFile   = $this->install->buildDBLogFile('error');
        $successFile = $this->install->buildDBLogFile('success');
        if(file_exists($logFile))     unlink($logFile);
        if(file_exists($errorFile))   unlink($errorFile);
        if(file_exists($successFile)) unlink($successFile);

        $this->view->installFileDeleted = $installFileDeleted;
        $this->view->title              = $this->lang->install->success;
        $this->display();
    }

    /**
     * Install apps of devops.
     * 安装devops相关应用。
     *
     * @access public
     * @return void
     */
    public function app()
    {
        $this->loadModel('common');
        $this->loadModel('solution');
        $cloudSolution = $this->loadModel('store')->getSolution('name', 'devops');
        $components    = $this->loadModel('store')->solutionConfig('name', 'devops');

        if($_POST)
        {
            $solution = $this->solution->create($cloudSolution, $components);
            if(dao::isError()) $this->send(array('result' => 'failure', 'message' => dao::getError()));

            $this->send(array('result' => 'success', 'message' => $this->lang->solution->notices->success, 'data' => $solution, 'locate' => $this->inLink('progress', "id={$solution->id}&install=true")));
        }

        $category = helper::arrayColumn($components->category, 'name');
        $category = array_filter($category, function($cate){return $cate !== 'pms';});

        $this->view->title         = $this->lang->solution->install;
        $this->view->cloudSolution = $cloudSolution;
        $this->view->components    = $components;
        $this->view->category      = $category;

        $this->display();
    }

    /**
     * Show installation progress of solution.
     * 应用安装进度。
     *
     * @param  int    $id
     * @param  bool   $install
     * @access public
     * @return void
     */
    public function progress($id, $install = false)
    {
        $solution = $this->loadModel('solution')->getByID($id);

        $this->view->title    = $this->lang->solution->progress;
        $this->view->install  = $install;
        $this->view->solution = $solution;

        $this->app->loadConfig('message');
        $this->config->message->browser->turnon = 0;
        $this->display();
    }

    /**
     * Get installing progress of solution by ajax.
     * 获取安装进度。
     *
     * @param  int    $id
     * @access public
     * @return void
     */
    public function ajaxProgress($id)
    {
        $this->loadModel('common');
        $solution = $this->loadModel('solution')->getByID($id);
        $logs     = array();
        if(in_array($solution->status, array('installing', 'installed')))
        {
            $result  = 'success';
            $message = '';

            if($solution->status == 'installing')
            {
                if((time() - strtotime($solution->updatedDate)) > 60 * 20)
                {
                    $this->solution->saveStatus($id, 'timeout');
                    $result  = 'fail';
                    $message = $this->lang->solution->errors->timeout;
                }

                if($result == 'success')
                {
                    $components = json_decode($solution->components);
                    foreach($components as $categorty => $componentApp)
                    {
                        if($componentApp->status == 'installing')
                        {
                            $instance = $this->loadModel('instance')->instanceOfSolution($solution, $componentApp->chart);
                            if($instance)
                            {
                                $chartLogs = $this->loadModel('cne')->getAppLogs($instance);
                                $logs[$componentApp->chart] = !empty($chartLogs->data) ? $chartLogs->data : array();
                            }
                        }
                    }
                }
            }
        }
        else
        {
            $result  = 'fail';
            $message = zget($this->lang->solution->installationErrors, $solution->status, $this->lang->solution->errors->hasInstallationError);
        }

        $this->send(array('result' => $result, 'message' => $message, 'data' => json_decode($solution->components), 'logs' => $logs));
    }

    /**
     * Start install by ajax.
     * 安装应用。
     *
     * @param  int    $solutionID
     * @access public
     * @return void
     */
    public function ajaxInstall($solutionID)
    {
        $this->loadModel('solution')->install($solutionID);
        if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

        $this->send(array('result' => 'success', 'message' => '', 'locate' => $this->inLink('step6')));
    }

    /**
     * Uninstall app by ajax.
     * 取消安装时卸载应用。
     *
     * @param  int    $solutionID
     * @access public
     * @return void
     */
    public function ajaxUninstall($solutionID)
    {
        $this->loadModel('common');
        $this->loadModel('solution')->uninstall($solutionID);
        if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

        $this->send(array('result' => 'success', 'message' => '', 'load' => $this->inLink('index')));
    }

    /**
     * Check memory and cpu.
     * 检查内存与CPU是否满足安装所需。
     *
     * @param  int    $solutionID
     * @access public
     * @return void
     */
    public function ajaxCheck()
    {
        $this->loadModel('common');

        $apps = (array)$this->post->apps;
        foreach($apps as $index => $app)
        {
            if($app == $this->lang->install->solution->skipInstall) unset($apps[$index]);
        }

        $appMap    = $this->loadModel('store')->getAppMapByNames($apps);
        $resources = array();

        foreach($apps as $app) $resources[] = array('cpu' => $appMap->$app->cpu, 'memory' => $appMap->$app->memory);
        $result = $this->loadModel('cne')->tryAllocate($resources);

        $this->send(array('result' => 'success', 'message' => '', 'code' => $result->code));
    }

}
