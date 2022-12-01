<?php
/**
 * 此文件包括ZenTaoPHP框架的三个类：baseRouter, config, lang。
 * The router, config and lang class file of ZenTaoPHP framework.
 *
 * @package framework
 *
 * The author disclaims copyright to this source code. In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
class baseRouter
{
    /**
     * ZenTaoPHP的基础目录，一般是程序的根目录。
     * The base path of the ZenTaoPHP framework.
     *
     * @var string
     * @access public
     */
    public $basePath;

    /**
     * 框架的根目录。
     * The root directory of the framwork($this->basePath/framework)
     *
     * @var string
     * @access public
     */
    public $frameRoot;

    /**
     * 类库的根目录。{$this->basePath/lib}
     * The root directory of the library($this->basePath/lib).
     *
     * @var string
     * @access public
     */
    public $coreLibRoot;

    /**
     * 应用名称
     * The appName.
     *
     * @var string
     * @access public
     */
    public $appName = '';

    /**
     * 应用程序的根目录。
     * The root directory of the app.
     *
     * @var string
     * @access public
     */
    public $appRoot;

    /**
     * 临时文件的根目录。
     * The root directory of temp.
     *
     * @var string
     * @access public
     */
    public $tmpRoot;

    /**
     * 缓存的根目录。
     * The root directory of cache.
     *
     * @var string
     * @access public
     */
    public $cacheRoot;

    /**
     * WWW目录。
     * The root directory of www.
     *
     * @var string
     * @access public
     */
    public $wwwRoot;

    /**
     * 附件存放目录。
     * The root directory of data.
     *
     * @var string
     * @access public
     */
    public $dataRoot;

    /**
     * 日志文件的根目录。
     * The root directory of log.
     *
     * @var string
     * @access public
     */
    public $logRoot;

    /**
     * 配置文件的根目录。
     * The root directory of config.
     *
     * @var string
     * @access public
     */
    public $configRoot;

    /**
     * 模块的根目录。
     * The root directory of module.
     *
     * @var string
     * @access public
     */
    public $moduleRoot;

    /**
     * 主题的根目录。
     * The root directory of theme.
     *
     * @var string
     * @access public
     */
    public $themeRoot;

    /**
     * 用户使用的语言。
     * The lang of the client user.
     *
     * @var string
     * @access public
     */
    public $clientLang;

    /**
     * 请求的原始模块名。
     * The requestd module name parsed from a URL.
     *
     * @var string
     * @access public
     */
    public $rawModule;

    /**
     * 请求的原始方法名。
     * The requested method name parsed from a URL.
     *
     * @var string
     * @access public
     */
    public $rawMethod;

    /**
     * 当前页面所在的应用，用于左侧菜单栏判断。
     * The current app code(url: '#app=?'), highlight left menu.
     *
     * @var string
     * @access public
     */
    public $tab;

    /**
     * 用户使用的主题。
     * The theme of the client user.
     *
     * @var string
     * @access public
     */
    public $clientTheme;

    /**
     * 客户端设备类型。
     * The device type of client.
     *
     * @var string
     * @access public
     */
    public $clientDevice;

    /**
     * 当前模块的control对象。
     * The control object of current module.
     *
     * @var object
     * @access public
     */
    public $control;

    /**
     * 模块名。
     * The module name
     *
     * @var string
     * @access public
     */
    public $moduleName;

    /**
     * 当前访问模块的control文件。
     * The control file of the module current visiting.
     *
     * @var string
     * @access public
     */
    public $controlFile;

    /**
     * 当前访问的方法名。
     * The name of the method current visiting.
     *
     * @var string
     * @access public
     */
    public $methodName;

    /**
     * 当前方法的扩展文件。
     * The action extension file of current method.
     *
     * @var string
     * @access public
     */
    public $extActionFile;

    /**
     * 访问的URI。
     * The URI.
     *
     * @var string
     * @access public
     */
    public $URI;

    /**
     * url地址传递的参数。
     * The params passed in through url.
     *
     * @var array
     * @access public
     */
    public $params;

    /**
     * 视图类型。
     * The view type.
     *
     * @var string
     * @access public
     */
    public $viewType;

    /**
     * 全局$config对象。
     * The global $config object.
     *
     * @var object
     * @access public
     */
    public $config;

    /**
     * 全局$lang对象。
     * The global $lang object.
     *
     * @var object
     * @access public
     */
    public $lang;

    /**
     * 全局$dbh对象，数据库连接句柄。
     * The global $dbh object, the database connection handler.
     *
     * @var object
     * @access public
     */
    public $dbh;

    /**
     * 从数据库的句柄。
     * The slave database handler.
     *
     * @var object
     * @access public
     */
    public $slaveDBH;

    /**
     * $post对象，用于访问$_POST变量。
     * The $post object, used to access the $_POST var.
     *
     * @var object
     * @access public
     */
    public $post;

    /**
     * $get对象，用于访问$_GET变量。
     * The $get object, used to access the $_GET var.
     *
     * @var object
     * @access public
     */
    public $get;

    /**
     * $session对象，用于访问$_SESSION变量。
     * The $session object, used to access the $_SESSION var.
     *
     * @var object
     * @access public
     */
    public $session;

    /**
     * $server对象，用于访问$_SERVER变量。
     * The $server object, used to access the $_SERVER var.
     *
     * @var object
     * @access public
     */
    public $server;

    /**
     * $cookie对象，用于访问$_COOKIE变量。
     * The $cookie object, used to access the $_COOKIE var.
     *
     * @var object
     * @access public
     */
    public $cookie;

    /**
     * 原始SESSIONID
     * SESSIONID
     *
     * @var int
     * @access public
     */
    public $sessionID;

    /**
     * 网站代号。
     * The code of current site.
     *
     * @var string
     * @access public
     */
    public $siteCode;

    /**
     * 构造方法, 设置路径，类，超级变量等。注意：
     * 1.应该使用createApp()方法实例化router类；
     * 2.如果$appRoot为空，框架会根据$appName计算应用路径。
     *
     * The construct function.
     * Prepare all the paths, classes, super objects and so on.
     * Notice:
     * 1. You should use the createApp() method to get an instance of the router.
     * 2. If the $appRoot is empty, the framework will compute the appRoot according the $appName
     *
     * @param string $appName   the name of the app
     * @param string $appRoot   the root path of the app
     * @access public
     * @return void
     */
    public function __construct($appName = 'demo', $appRoot = '')
    {
        $this->setPathFix();
        $this->setBasePath();
        $this->setFrameRoot();
        $this->setCoreLibRoot();
        $this->setAppRoot($appName, $appRoot);
        $this->setTmpRoot();
        $this->setCacheRoot();
        $this->setLogRoot();
        $this->setConfigRoot();
        $this->setModuleRoot();
        $this->setWwwRoot();
        $this->setThemeRoot();
        $this->setDataRoot();
        $this->loadMainConfig();

        $this->loadClass('front',  $static = true);
        $this->loadClass('filter', $static = true);
        $this->loadClass('dao',    $static = true);
        $this->loadClass('mobile', $static = true);

        $this->setCookieSecure();
        $this->setOpenApp();
        $this->setSuperVars();
        $this->setDebug();
        $this->setErrorHandler();
        $this->setTimezone();
        $this->startSession();

        if($this->config->framework->multiSite)     $this->setSiteCode() && $this->loadExtraConfig();
        if($this->config->framework->autoConnectDB) $this->connectDB();
        if($this->config->framework->multiLanguage) $this->setClientLang();

        $this->setEdition();
        $this->setVision();

        $needDetectDevice   = zget($this->config->framework->detectDevice, $this->clientLang, false);
        $this->clientDevice = $needDetectDevice ? $this->setClientDevice() : 'desktop';

        if($this->config->framework->multiLanguage) $this->loadLang('common');
        if($this->config->framework->multiTheme)    $this->setClientTheme();
    }

    /**
     * 创建一个应用。
     * Create an application.
     *
     * @param string $appName   应用名称。  The name of the app.
     * @param string $appRoot   应用根路径。The root path of the app.
     * @param string $className 应用类名，如果对router类做了扩展，需要指定类名。When extends router class, you should pass in the child router class name.
     * @static
     * @access public
     * @return static   the app object
     */
    public static function createApp($appName = 'demo', $appRoot = '', $className = '')
    {
        if(empty($className)) $className = __CLASS__;
        return new $className($appName, $appRoot);
    }

    //-------------------- 路径相关方法(Path related methods)--------------------//

    /**
     * 设置应用名称。
     * Set app name.
     *
     * @param  string    $appName
     * @access public
     * @return void
     */
    public function setAppName($appName)
    {
        $this->appName = $appName;
    }

    /**
     * 设置目录分隔符。
     * Set the path directory separator.
     *
     * @access public
     * @return void
     */
    public function setPathFix()
    {
        define('DS', DIRECTORY_SEPARATOR);
    }

    /**
     * 设置基础目录。
     * Set the base path.
     *
     * @access public
     * @return void
     */
    public function setBasePath()
    {
        $this->basePath = realpath(dirname(dirname(dirname(__FILE__)))) . DS;
    }

    /**
     * 设置框架根目录。
     * Set the frame root.
     *
     * @access public
     * @return void
     */
    public function setFrameRoot()
    {
        $this->frameRoot = $this->basePath . 'framework' . DS;
    }

    /**
     * 设置类库的根目录。
     * Set the app lib root.
     *
     * @access public
     * @return void
     */
    public function setCoreLibRoot()
    {
        $this->coreLibRoot = $this->basePath . 'lib' . DS;
    }

    /**
     * 设置应用的根目录。
     * Set the app root.
     *
     * @param string $appName
     * @param string $appRoot
     * @access public
     * @return void
     */
    public function setAppRoot($appName = 'demo', $appRoot = '')
    {
        if(empty($appRoot))  $this->appRoot = $this->basePath . 'app' . DS . $appName . DS;
        if(!empty($appRoot)) $this->appRoot = realpath($appRoot) . DS;
        if(!is_dir($this->appRoot)) $this->triggerError("The app you call not found in {$this->appRoot}", __FILE__, __LINE__, $exit = true);
    }

    /**
     * 设置临时文件的根目录。
     * Set the tmp root.
     *
     * @access public
     * @return void
     */
    public function setTmpRoot()
    {
        $this->tmpRoot = $this->basePath . 'tmp' . DS;
    }

    /**
     * 设置缓存的根目录。
     * Set the cache root.
     *
     * @access public
     * @return void
     */
    public function setCacheRoot()
    {
        $this->cacheRoot = $this->tmpRoot . 'cache' . DS;
    }

    /**
     * 设置log的根目录。
     * Set the log root.
     *
     * @access public
     * @return void
     */
    public function setLogRoot()
    {
        $this->logRoot = $this->tmpRoot . 'log' . DS;
    }

    /**
     * 设置config配置文件的根目录。
     * Set the config root.
     *
     * @access public
     * @return void
     */
    public function setConfigRoot()
    {
        $this->configRoot = $this->basePath . 'config' . DS;
    }

    /**
     * 设置模块的根目录。
     * Set the module root.
     *
     * @access public
     * @return void
     */
    public function setModuleRoot()
    {
        $this->moduleRoot = $this->basePath . 'module' . DS;
    }

    /**
     * 设置www的根目录。
     * Set the www root.
     *
     * @access public
     * @return void
     */
    public function setWwwRoot()
    {
        $this->wwwRoot = rtrim(dirname($_SERVER['SCRIPT_FILENAME']), DS) . DS;
    }

    /**
     * 设置主题根目录。
     * Set the theme root.
     *
     * @access public
     * @return void
     */
    public function setThemeRoot()
    {
        $this->themeRoot = $this->wwwRoot . 'theme' . DS;
    }

   /**
     * 设置data根目录。
     * Set the data root.
     *
     * @access public
     * @return void
     */
    public function setDataRoot()
    {
        $this->dataRoot = $this->wwwRoot . 'data' . DS;
    }

    /**
     * 设置超级变量。
     * Set the super vars.
     *
     * @access public
     * @return void
     */
    public function setSuperVars()
    {
        if(isset($_SERVER['REQUEST_URI']))
        {
            $URI = $_SERVER['REQUEST_URI'];
            if(strpos($URI, '?') !== false)
            {
                $parsedURL = parse_url($URI);
                if(isset($parsedURL['query']))
                {
                    parse_str($parsedURL['query'], $parsedQuery);
                    foreach($parsedQuery as $key => $value)
                    {
                        if(!isset($_GET[$key])) $_GET[$key] = $value;
                    }
                }
            }
        }

        $this->post    = new super('post');
        $this->get     = new super('get');
        $this->server  = new super('server');
        $this->cookie  = new super('cookie');
        $this->session = new super('session', $this->tab);

        unset($_REQUEST);

        /* Change for CSRF. */
        if($this->config->framework->filterCSRF)
        {
            $httpType = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on') ? 'https' : 'http';
            if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https') $httpType = 'https';
            if(isset($_SERVER['REQUEST_SCHEME']) and strtolower($_SERVER['REQUEST_SCHEME']) == 'https') $httpType = 'https';

            $httpHost = zget($_SERVER, 'HTTP_HOST', '');
            $apiMode  = (defined('RUN_MODE') && RUN_MODE == 'api') || isset($_GET[$this->config->sessionVar]);
            if(!$apiMode && (empty($httpHost) or strpos($this->server->http_referer, "$httpType://$httpHost") !== 0)) $_FILES = $_POST = array();
        }

        $_FILES  = validater::filterFiles();
        $_POST   = validater::filterSuper($_POST);
        $_GET    = validater::filterSuper($_GET);
        $_COOKIE = validater::filterSuper($_COOKIE);

        /* Filter common get and cookie vars. */
        if($this->config->framework->filterParam == 2)
        {
            global $filter;
            foreach($filter->default->get as $key => $rules)
            {
                if(isset($_GET[$key]) and !validater::checkByRule($_GET[$key], $rules)) unset($_GET[$key]);
            }
            foreach($filter->default->cookie as $key => $rules)
            {
                if(isset($_COOKIE[$key]) and !validater::checkByRule($_COOKIE[$key], $rules)) unset($_COOKIE[$key]);
            }
        }
    }

    /**
     * Set cookieSecure config.
     *
     * @access public
     * @return void
     */
    public function setCookieSecure()
    {
        $this->config->cookieSecure = false;
        if($this->config->framework->setCookieSecure and isHttps()) $this->config->cookieSecure = true;
    }

    /**
     * 设置Debug模式。
     * set Debug.
     *
     * @access public
     * @return void
     */
    public function setDebug()
    {
        if(!empty($this->config->debug)) error_reporting(E_ALL & ~ E_STRICT);
    }

    /**
     * 设置版本。
     * Set edition.
     *
     * @access public
     * @return void
     */
    public function setEdition()
    {
        if(isset($this->config->edition)) return $this->config->edition;

        $edition = substr($this->config->version, 0, 3);
        if(in_array($edition, array('pro', 'biz', 'max'))) return $this->config->edition = $edition;
        $this->config->edition = 'open';
    }

    /**
     * 设置vision。
     * set Debug.
     *
     * @access public
     * @return void
     */
    public function setVision()
    {
        $account = isset($_SESSION['user']) ? $_SESSION['user']->account : '';
        if(empty($account) and isset($_POST['account'])) $account = $_POST['account'];
        if(empty($account) and isset($_GET['account']))  $account = $_GET['account'];

        $vision = '';
        if($this->config->installed and validater::checkAccount($account))
        {
            $sql     = new sql();
            $account = $sql->quote($account);
            $vision  = $this->dbh->query("SELECT * FROM " . TABLE_CONFIG . " WHERE owner = $account AND `key` = 'vision' LIMIT 1")->fetch();
            if($vision) $vision = $vision->value;
            if(empty($vision))
            {
                $user = $this->dbh->query("SELECT * FROM " . TABLE_USER . " WHERE account = $account AND deleted = '0' LIMIT 1")->fetch();
                if(!empty($user->visions)) list($vision) = explode(',', $user->visions);
            }
        }

        list($defaultVision) = explode(',', trim($this->config->visions, ','));
        if($vision and strpos($this->config->visions, ",{$vision},") === false) $vision = $defaultVision;

        $this->config->vision = $vision ? $vision : $defaultVision;
    }

    /**
     * Get installed version.
     *
     * @access public
     * @return string
     */
    public function getInstalledVersion()
    {
        $version = $this->dbh->query("SELECT value FROM " . TABLE_CONFIG . " WHERE `owner` = 'system' AND `key` = 'version' AND `module` = 'common' AND `section` = 'global' LIMIT 1")->fetch();
        $version = $version ? $version->value : '0.3.beta';                  // No version, set as 0.3.beta.
        if($version == '3.0.stable') $version = '3.0';    // convert 3.0.stable to 3.0.
        return $version;
    }

    /**
     * 设置错误处理句柄。
     * Set the error handler.
     *
     * @access public
     * @return void
     */
    public function setErrorHandler()
    {
        set_error_handler(array($this, 'saveError'));
        register_shutdown_function(array($this, 'shutdown'));
    }

    /**
     * 获取应用名称
     * Get app name
     *
     * @access public
     * @return string
     */
    public function getAppName()
    {
        return $this->appName;
    }

    /**
     * 获取$basePath，即基础路径。
     * Get the $basePath var.
     *
     * @access public
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * 获取$frameRoot，即框架根目录。
     * Get the $frameRoot var.
     *
     * @access public
     * @return string
     */
    public function getFrameRoot()
    {
        return $this->frameRoot;
    }

    /**
     * 获取$appRoot变量，即应用的根目录。
     * Get the $appRoot var.
     *
     * @access public
     * @return string
     */
    public function getAppRoot()
    {
        return $this->appRoot;
    }

    /**
     * 获取$wwwRoot变量。
     * Get the $wwwRoot var
     *
     * @access public
     * @return string
     */
    public function getWwwRoot()
    {
        return $this->wwwRoot;
    }

    /**
     * 获取$coreLibRoot变量，即应用类库的根目录。
     * Get the $coreLibRoot var.
     *
     * @access public
     * @return string
     */
    public function getCoreLibRoot()
    {
        return $this->coreLibRoot;
    }

    /**
     * 获取$tmpRoot变量，即临时文件的根目录。
     * Get the $tmpRoot var.
     *
     * @access public
     * @return string
     */
    public function getTmpRoot()
    {
        return $this->tmpRoot;
    }

    /**
     * 获取$cacheRoot变量，即缓存文件的根目录。
     * Get the $cacheRoot var.
     *
     * @access public
     * @return string
     */
    public function getCacheRoot()
    {
        return $this->cacheRoot;
    }

    /**
     * 获取$logRoot变量，即日志文件的根目录。
     * Get the $logRoot var.
     *
     * @access public
     * @return string
     */
    public function getLogRoot()
    {
        return $this->logRoot;
    }

    /**
     * 获取$configRoot变量，即配置文件的根目录。
     * Get the $configRoot var.
     *
     * @access public
     * @return string
     */
    public function getConfigRoot()
    {
        return $this->configRoot;
    }

    /**
     * 获取$moduleRoot变量，即应用模块的根目录。
     * Get the $moduleRoot var.
     *
     * @param  string $appName
     * @access public
     * @return string
     */
    public function getModuleRoot($appName = '')
    {
        if($appName == '') return $this->moduleRoot;
        return dirname($this->moduleRoot) . DS . $appName . DS;
    }

    /**
     * 获取扩展根目录。
     * Get the root of extension.
     *
     * @access public
     * @return string
     */
    public function getExtensionRoot()
    {
        return $this->basePath . 'extension' . DS;
    }

    /**
     * 获取$webRoot，即应用的路径。
     * Get the $webRoot var.
     *
     * @access public
     * @return string
     */
    public function getWebRoot()
    {
        return $this->config->webRoot;
    }

    /**
     * 获取$themeRoot变量，即主题的根目录。
     * Get the $themeRoot var.
     *
     * @access public
     * @return string
     */
    public function getThemeRoot()
    {
        return $this->themeRoot;
    }

    /**
     * 获取$dataRoot目录
     * Get the $dataRoot var
     *
     * @access public
     * @return string
     */
    public function getDataRoot()
    {
        return $this->dataRoot;
    }

   //------ 客户端环境有关的函数(Client environment related functions) ------//

    /**
     * 根据配置设置当前时区。
     * Set the time zone according to the config.
     *
     * @access public
     * @return void
     */
    public function setTimezone()
    {
        if(isset($this->config->timezone)) date_default_timezone_set($this->config->timezone);
    }

    /**
     * 开启 session
     * Start the session.
     *
     * @access public
     * @return void
     */
    public function startSession()
    {
        if(defined('SESSION_STARTED')) return;

        if(ini_get('session.save_handler') == 'files' and isset($_GET['tid']))
        {
            $savePath = ini_get('session.save_path');
            $writable = is_writable($savePath);
            if(!$writable)
            {
                $savePath = $this->getTmpRoot() . 'session';
                if(!is_dir($savePath)) mkdir($savePath, 0777, true);
                $writable = is_writable($savePath);
                if($writable) session_save_path($this->getTmpRoot() . 'session');
            }

            if($writable)
            {
                $ztSessionHandler = new ztSessionHandler($_GET['tid']);
                session_set_save_handler(
                    array($ztSessionHandler, "open"),
                    array($ztSessionHandler, "close"),
                    array($ztSessionHandler, "read"),
                    array($ztSessionHandler, "write"),
                    array($ztSessionHandler, "destroy"),
                    array($ztSessionHandler, "gc")
                );
            }
        }

        $sessionName = $this->config->sessionVar;
        session_name($sessionName);
        session_set_cookie_params(0, $this->config->webRoot, '', $this->config->cookieSecure, true);
        if($this->config->customSession) session_save_path($this->getTmpRoot() . 'session');
        if(!session_id()) session_start();

        $this->sessionID = isset($ztSessionHandler) ? $ztSessionHandler->getSessionID() : session_id();

        if(isset($_GET[$this->config->sessionVar]))
        {
            helper::restartSession($_GET[$this->config->sessionVar]);
        }
        else if(isset($_SERVER['HTTP_TOKEN'])) // If request header has token, use it as session for authentication.
        {
            helper::restartSession($_SERVER['HTTP_TOKEN']);
            $this->sessionID = isset($ztSessionHandler) ? $ztSessionHandler->getSessionID() : session_id();
        }

        define('SESSION_STARTED', true);
    }

    /**
     * 从cookie中获取当前的group, 即URL锚链接'#tab=?'。
     * Get current group from cookie, original source is url '#tab=?'.
     *
     * @access public
     * @return void
     */
    public function setOpenApp()
    {
        $module    = $this->rawModule;
        $this->tab = 'my';
        if(isset($this->lang->navGroup)) $this->tab = zget($this->lang->navGroup, $module, 'my');
        if(isset($_COOKIE['tab']) and $_COOKIE['tab'] and preg_match('/^\w+$/', $_COOKIE['tab'])) $this->tab = $_COOKIE['tab'];
    }

    /**
     * 根据用户浏览器的语言设置和服务器配置，选择显示的语言。
     * 优先级：$lang参数 > session > cookie > 浏览器 > 配置文件。
     *
     * Set the language.
     * Using the order of method $lang param, session, cookie, browser and the default lang.
     *
     * @param   string $lang  zh-cn|zh-tw|zh-hk|en
     * @access  public
     * @return  void
     */
    public function setClientLang($lang = '')
    {
        if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) $this->clientLang = $this->parseHttpAcceptLang();
        if(isset($_COOKIE['lang']))                 $this->clientLang = $_COOKIE['lang'];
        if(isset($_SESSION['lang']))                $this->clientLang = $_SESSION['lang'];
        if(!empty($lang))                           $this->clientLang = $lang;

        if(!empty($this->clientLang))
        {
            $this->clientLang = strtolower($this->clientLang);
            if(!isset($this->config->langs[$this->clientLang])) $this->clientLang = $this->config->default->lang;
        }
        else
        {
            $this->clientLang = $this->config->default->lang;
        }

        setcookie('lang', $this->clientLang, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, false);
        if(!isset($_COOKIE['lang'])) $_COOKIE['lang'] = $this->clientLang;

        return true;
    }

    /**
     * 从HTTP_ACCEPT_LANGUAGE中提出去支持的语言。
     * Parse the lang str from HTTP_ACCEPT_LANGUAGE header.
     *
     * @access public
     * @return string
     */
    public function parseHttpAcceptLang()
    {
        if(empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) return '';

        $raw  = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $pos  = strpos($raw, ',');
        $lang = $pos === false ? $raw : substr($raw, 0, $pos);

        /* Fix clientLang for ie >= 10. https://www.drupal.org/node/365615. */
        if(stripos($lang, 'hans')) $lang = 'zh-cn';
        if(stripos($lang, 'hant')) $lang = 'zh-tw';
        return $lang;
    }

    /**
     * 设置客户端使用的主题，判断逻辑与客户端的语言相同。
     * 主题的css和图片文件应该存放在www/theme/$themeName路径。
     *
     * Set the theme the client user using. The logic is same as the clientLang.
     * The css and images files of an theme should saved at www/theme/$themeName
     *
     * @param   string $theme
     * @access  public
     * @return  void
     */
    public function setClientTheme($theme = '')
    {
        if(isset($this->config->client->theme)) $this->clientTheme = $this->config->client->theme;
        if(isset($_COOKIE['theme']))            $this->clientTheme = $_COOKIE['theme'];
        if(!empty($theme))                      $this->clientTheme = $theme;

        if(!empty($this->clientTheme))
        {
            $this->clientTheme = strtolower($this->clientTheme);
            if(!isset($this->lang->themes[$this->clientTheme])) $this->clientTheme = $this->config->default->theme;
        }
        else
        {
            $this->clientTheme = $this->config->default->theme;
        }

        setcookie('theme', $this->clientTheme, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, false);
        if(!isset($_COOKIE['theme'])) $_COOKIE['theme'] = $this->clientTheme;

        return true;
    }

   /**
     * 设置客户端的设备类型。
     * Set client device.
     *
     * @access public
     * @return void
     */
    public function setClientDevice()
    {
        $this->clientDevice = 'desktop';

        if($this->cookie->device == 'mobile')  $this->clientDevice = 'mobile';
        if($this->cookie->device == 'desktop') $this->clientDevice = 'desktop';

        if(empty($this->cookie->device) || strpos('mobile,desktop', $this->cookie->device) === false)
        {
            $mobile = new mobile();
            $this->clientDevice = ($mobile->isMobile() and !$mobile->isTablet()) ? 'mobile' : 'desktop';
        }

        setcookie('device', $this->clientDevice, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, true);
        if(!isset($_COOKIE['device'])) $_COOKIE['device'] = $this->clientDevice;

        return $this->clientDevice;
    }

    /**
     * 设置站点代号，可以针对不同的站点来加载不同的扩展。
     * Set the code of current site, thus can load different extension of different site.
     *
     * @access public
     * @return void
     */
    public function setSiteCode()
    {
        return $this->siteCode = helper::parseSiteCode($this->server->http_host);
    }

    /**
     * 获取$clientLang变量，即客户端的语言。
     * Get the $clientLang var.
     *
     * @access public
     * @return string
     */
    public function getClientLang()
    {
        return $this->clientLang;
    }

    /**
     * 获取$clientTheme变量。
     * Get the $clientTheme var.
     *
     * @access public
     * @return string
     */
    public function getClientTheme()
    {
        return $this->config->webRoot . 'theme/' . $this->clientTheme . '/';
    }

    /**
     * 获得客户端的终端设备。
     * Get the client device.
     *
     * @access public
     * @return void
     */
    public function getClientDevice()
    {
        return $this->clientDevice;
    }

    //-------------------- 请求相关的方法(Request related methods) --------------------//

    /**
     * 解析本次请求的入口方法，根据请求的类型(PATH_INFO GET)，调用相应的方法。
     * The entrance of parsing request. According to the requestType, call related methods.
     *
     * @access public
     * @return void
     */
    public function parseRequest()
    {
        if($this->config->requestType == 'PATH_INFO' or $this->config->requestType == 'PATH_INFO2')
        {
            $this->parsePathInfo();
            $this->setRouteByPathInfo();
        }
        elseif($this->config->requestType == 'GET')
        {
            $this->parseGET();
            $this->setRouteByGET();
        }
        else
        {
            $this->triggerError("The request type {$this->config->requestType} not supported", __FILE__, __LINE__, $exit = true);
        }
    }

    /**
     * PATH_INFO方式解析，获取$URI和$viewType。
     * Parse PATH_INFO, get the $URI and $viewType.
     *
     * @access public
     * @return void
     */
    public function parsePathInfo()
    {
        $pathInfo = $this->getPathInfo();
        if(!empty($pathInfo))
        {
            $dotPos = strrpos($pathInfo, '.');
            if($dotPos)
            {
                $this->URI      = substr($pathInfo, 0, $dotPos);
                $this->viewType = substr($pathInfo, $dotPos + 1);
                if(strpos($this->config->views, ',' . $this->viewType . ',') === false)
                {
                    $this->viewType = $this->config->default->view;
                }
            }
            else
            {
                $this->URI      = $pathInfo;
                $this->viewType = $this->config->default->view;
            }
        }
        else
        {
            $this->viewType = $this->config->default->view;
        }
    }

    /**
     * 从$_SERVER或者$_ENV全局变量根据pathinfo变量名获取$PATH_INFO值。
     * PATH_INFO的变量名几乎都是'PATH_INFO'，但也有可能是ORIG_PATH_INFO。
     *
     * Get $PATH_INFO from $_SERVER or $_ENV by the pathinfo var name.
     * Mostly, the var name of PATH_INFO is PATH_INFO, but may be ORIG_PATH_INFO.
     *
     * @access  public
     * @return  string the PATH_INFO
     */
    public function getPathInfo()
    {
        if(isset($_SERVER['PATH_INFO']))
        {
            $value = $_SERVER['PATH_INFO'];
        }
        elseif(isset($_SERVER['ORIG_PATH_INFO']))
        {
            $value = $_SERVER['ORIG_PATH_INFO'];
        }
        elseif(isset($this->URI))
        {
            $value = $this->URI;
            $subpath = str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname($_SERVER['SCRIPT_FILENAME']));
            if($subpath != '/') $subpath = '/' . $subpath;
            if($subpath != '' and $subpath != '/' and strpos($value, $subpath) === 0) $value = substr($value, strlen($subpath));
        }
        else
        {
            $value = @getenv('PATH_INFO');
            if(empty($value)) $value = @getenv('ORIG_PATH_INFO');
        }

        if(strpos($value, $_SERVER['SCRIPT_NAME']) !== false) $value = str_replace($_SERVER['SCRIPT_NAME'], '', $value);
        if(strpos($value, '?') === false) return trim($value, '/');

        $value    = parse_url($value);
        $pathInfo = trim(zget($value, 'path', ''), '/');
        if(trim($pathInfo, '/') == trim($this->config->webRoot, '/')) $pathInfo = '';

        return $pathInfo;
    }

    /**
     * GET请求方式解析，获取$URI和$viewType。
     * Parse GET, get $URI and $viewType.
     *
     * @access public
     * @return void
     */
    public function parseGET()
    {
        if(isset($_GET[$this->config->viewVar]))
        {
            $this->viewType = $_GET[$this->config->viewVar];
            if(strpos($this->config->views, ',' . $this->viewType . ',') === false) $this->viewType = $this->config->default->view;
        }
        else
        {
            $this->viewType = $this->config->default->view;
        }
        $this->URI = $_SERVER['REQUEST_URI'];
    }

    /**
     * 获取$URL。
     * Get the $URL.
     *
     * @param  bool $full  true, the URI contains the webRoot, else only hte URI.
     * @access public
     * @return string
     */
    public function getURI($full = false)
    {
        if($full and $this->config->requestType == 'PATH_INFO')
        {
            if($this->URI) return $this->config->webRoot . $this->URI . '.' . $this->viewType;
            return $this->config->webRoot;
        }
        return $this->URI;
    }

    /**
     * 获取$viewType变量。
     * Get the $viewType var.
     *
     * @access public
     * @return string
     */
    public function getViewType()
    {
        return $this->viewType;
    }

    //-------------------- 模块及扩展设置(Module and extension) --------------------//

    /**
     * 加载common模块。
     *
     *  common模块比较特别，它会执行几乎每次请求都需要执行的操作，例如：
     *  打开session，检查权限等等。
     *  加载完$lang, $config, $dbh后，需要在入口文件(www/index.php)中手动调用该方法。
     *
     * Load the common module
     *
     *  The common module is a special module, which can be used to do some common things. For example:
     *  start session, check privilege and so on.
     *  This method should called manually in the router file(www/index.php) after the $lang, $config, $dbh loaded.
     *
     * @access public
     * @return object|bool  the common model object or false if not exits.
     */
    public function loadCommon()
    {
        $this->setModuleName('common');
        $commonModelFile = $this->setModelFile('common');
        if(!file_exists($commonModelFile)) return false;

        helper::import($commonModelFile);

        if($this->config->framework->extensionLevel == 0 and class_exists('commonModel'))    return new commonModel();
        if($this->config->framework->extensionLevel > 0  and class_exists('extCommonModel')) return new extCommonModel();

        if(class_exists('commonModel')) return new commonModel();
        return false;
    }

    /**
     * 设置要被调用的模块名。
     * Set the name of the module to be called.
     *
     * @param   string $moduleName  the module name
     * @access  public
     * @return  void
     */
    public function setModuleName($moduleName = '')
    {
        if($this->checkModuleName($moduleName)) $this->moduleName = strtolower($moduleName);
    }

    /**
     * 设置要被调用的控制器文件。
     * Set the control file of the module to be called.
     *
     * @param   bool    $exitIfNone     没有找到该控制器文件的情况：如果该参数为true，则终止程序；如果为false，则打印错误日志
     *                                  The control file was not found: if the parameter is true, the program is terminated;
     *                                  if false, the error log is printed.
     * @access  public
     * @return  bool
     */
    public function setControlFile($exitIfNone = true)
    {
        $this->controlFile = $this->getModulePath() . 'control.php';
        if(file_exists($this->controlFile)) return true;
        $this->triggerError("the control file $this->controlFile not found.", __FILE__, __LINE__, $exitIfNone);
    }

    /**
     * 设置要被调用的方法名。
     * Set the name of the method calling.
     *
     * @param string $methodName
     * @access public
     * @return void
     */
    public function setMethodName($methodName = '')
    {
        if($this->checkMethodName($methodName)) $this->methodName = strtolower($methodName);
    }

    /**
     * 设置要被调用方法的参数。
     * Set the params of method calling.
     *
     * @access public
     * @return void
     */
    public function setParams()
    {
        try
        {
            $appName    = $this->appName;
            $moduleName = $this->moduleName;
            $methodName = $this->methodName;

            /*
             * 引入该模块的control文件。
             * Include the control file of the module.
             **/
            $isExt = $this->setActionExtFile();
            if($isExt)
            {
                $controlFile = $this->controlFile;
                spl_autoload_register(function($class) use ($moduleName, $controlFile)
                {
                    if($class == $moduleName) include $controlFile;
                });
            }

            $file2Included = $isExt ? $this->extActionFile : $this->controlFile;
            chdir(dirname($file2Included));
            helper::import($file2Included);

            /* Check file is encode by ioncube. */
            $isEncrypted = false;
            if(strpos($file2Included, 'extension' . DS . $this->config->edition . DS) !== false)
            {
                $fp = fopen($file2Included, 'r');
                $line1 = fgets($fp);
                $line2 = fgets($fp);
                fclose($fp);
                if(strpos($line1, '<?php //') === 0 and strpos($line2, "if(!extension_loaded('ionCube Loader'))") === 0) $isEncrypted = true;
            }

            /*
             * 设置control的类名。
             * Set the class name of the control.
             **/
            $className = class_exists("my$moduleName") ? "my$moduleName" : $moduleName;
            if(!class_exists($className)) $this->triggerError("the control $className not found", __FILE__, __LINE__, $exit = true);

            /*
             * 创建control类的实例。
             * Create a instance of the control.
             **/
            $module = new $className();
            if(!method_exists($module, $methodName)) $this->triggerError("the module $moduleName has no $methodName method", __FILE__, __LINE__, $exit = true);
            $this->control = $module;

            /* include default value for module*/
            $defaultValueFiles = glob($this->getTmpRoot() . "defaultvalue/*.php");
            if($defaultValueFiles) foreach($defaultValueFiles as $file) include $file;

            /*
             * 使用反射机制获取函数参数的默认值。
             * Get the default settings of the method to be called using the reflecting.
             *
             * */
            $defaultParams = array();
            $methodReflect = new reflectionMethod($className, $methodName);
            foreach($methodReflect->getParameters() as $param)
            {
                $name = $param->getName();

                $default = '_NOT_SET';
                if(isset($paramDefaultValue[$appName][$className][$methodName][$name]))
                {
                    $default = $paramDefaultValue[$appName][$className][$methodName][$name];
                }
                elseif(isset($paramDefaultValue[$className][$methodName][$name]))
                {
                    $default = $paramDefaultValue[$className][$methodName][$name];
                }
                elseif(!$isEncrypted and $param->isDefaultValueAvailable())
                {
                    $default = $param->getDefaultValue();
                }

                $defaultParams[$name] = $default;
            }

            /**
             * 根据PATH_INFO或者GET方式设置请求的参数。
             * Set params according PATH_INFO or GET.
             */
            if($this->config->requestType != 'GET')
            {
                $this->setParamsByPathInfo($defaultParams);
            }
            else
            {
                $this->setParamsByGET($defaultParams);
            }

            if ('cli' === PHP_SAPI)
            {
                if ($this->params)
                {
                    $this->params = array_merge($defaultParams, $this->params);
                }
                else
                {
                    $this->params = $defaultParams;
                }
            }
            else
            {
                if($this->config->framework->filterParam == 2)
                {
                    $_GET    = validater::filterParam($_GET, 'get');
                    $_COOKIE = validater::filterParam($_COOKIE, 'cookie');
                }
            }
            return true;
        }
        catch(EndResponseException $endResponseException)
        {
            echo $endResponseException->getContent();
            return false;
        }
    }

    /**
     * 获取一个模块的路径。
     * Get the path of one module.
     *
     * @param  string $appName    the app name
     * @param  string $moduleName    the module name
     * @access public
     * @return string the module path
     */
    public function getModulePath($appName = '', $moduleName = '')
    {
        if($moduleName == '') $moduleName = $this->moduleName;
        $moduleName = strtolower($moduleName);

        if($this->checkModuleName($moduleName))
        {
            $modulePath = $this->getExtensionRoot() . 'saas' . DS . $moduleName . DS;
            if(is_dir($modulePath) and (file_exists($modulePath . 'control.php') or file_exists($modulePath . 'model.php'))) return $modulePath;

            /* 1. 最后尝试在定制开发中寻找。 Finally, try to find the module in the custom dir. */
            $modulePath = $this->getExtensionRoot() . 'custom' . DS . $moduleName . DS;
            if(is_dir($modulePath) and (file_exists($modulePath . 'control.php') or file_exists($modulePath . 'model.php'))) return $modulePath;

            /* 2. 如果设置过vision，尝试在vision中查找。 If vision is set, try to find the module in the vision. */
            if($this->config->vision != 'rnd')
            {
                $modulePath = $this->getExtensionRoot() . $this->config->vision . DS . $moduleName . DS;
                if(is_dir($modulePath) and (file_exists($modulePath . 'control.php') or file_exists($modulePath . 'model.php'))) return $modulePath;
            }

            /* 3. 尝试查找商业版本是否有此模块。 Try to find the module in other editon. */
            if($this->config->edition != 'open')
            {
                $modulePath = $this->getExtensionRoot() . $this->config->edition . DS . $moduleName . DS;
                if(is_dir($modulePath) and (file_exists($modulePath . 'control.php') or file_exists($modulePath . 'model.php'))) return $modulePath;
            }

            /* 4. 尝试查找喧喧是否有此模块。 Try to find the module in xuan. */
            $modulePath = $this->getExtensionRoot() . 'xuan' . DS . $moduleName . DS;
            if(is_dir($modulePath) and (file_exists($modulePath . 'control.php') or file_exists($modulePath . 'model.php'))) return $modulePath;

            /* 5. 如果通用版本里有此模块，优先使用。 If module is in the open edition, use it. */
            return $this->getModuleRoot($appName) . $moduleName . DS;
        }
    }

    /**
     * 获取一个模块的扩展路径。 Get extension path of one module.
     *
     * If the extensionLevel == 0, return empty array.
     * If the extensionLevel == 1, return the common extension directory.
     * If the extensionLevel == 2, return the common and site extension directories.
     *
     * @param   string $appName        the app name
     * @param   string $moduleName     the module name
     * @param   string $ext            the extension type, can be control|model|view|lang|config|zen|tao
     * @access  public
     * @return  string the extension path.
     */
    public function getModuleExtPath($appName, $moduleName, $ext)
    {
        $saasExtPath = $this->getExtensionRoot() . 'saas' . DS . $moduleName . DS . 'ext' . DS . $ext . DS;

        /* 检查失败或者extensionLevel为0，直接返回空。If check failed or extensionLevel == 0, return empty array. */
        if(!$this->checkModuleName($moduleName) or $this->config->framework->extensionLevel == 0) return array('saas' => $saasExtPath);

        $paths = array();

        /* When extensionLevel == 1. */
        $paths['common'] = $this->config->edition != 'open' ? $this->getExtensionRoot() . $this->config->edition . DS . $moduleName . DS . 'ext' . DS . $ext . DS : '';
        $paths['xuan']   = $this->getExtensionRoot() . 'xuan' . DS . $moduleName . DS . 'ext' . DS . $ext . DS;
        $paths['vision'] = $this->config->vision == 'rnd' ? '' : $this->basePath . 'extension' . DS . $this->config->vision . DS . $moduleName . DS . 'ext' . DS . $ext . DS;
        $paths['custom'] = $this->getExtensionRoot() . 'custom' . DS . $moduleName . DS . 'ext' . DS . $ext . DS;
        if($this->config->framework->extensionLevel == 1)
        {
            $paths['saas']   = $saasExtPath;
            return $paths;
        }

        /* When extensionLevel == 2. */
        $paths['site'] = empty($this->siteCode) ? '' : $this->getExtensionRoot() . $this->config->edition . DS . $moduleName . DS . 'ext' . DS . '_' . $this->siteCode . DS . $ext . DS;
        $paths['saas'] = $saasExtPath;

        return $paths;
    }

    /**
     * 检查模块中某一个变量必须为英文字母和数字组合。Check module a variable must be ascii.
     *
     * @param  string    $var
     * @param  bool      $exit
     * @access public
     * @return bool
     */
    public function checkModuleName($var, $exit = true)
    {
        global $filter;
        static $checkedModule = array();
        if(!isset($checkedModule[$var]))
        {
            $rule   = $filter->default->moduleName;
            $result = validater::checkByRule($var, $rule);
            $checkedModule[$var] = $result;
        }
        if($checkedModule[$var]) return true;
        if(!$exit) return false;
        $this->triggerError("'$var' illegal. ", __FILE__, __LINE__, $exit = true);
    }

    /**
     * 检查方法中某一个变量必须为英文字母和数字组合。Check method a variable must be ascii.
     *
     * @param  string    $var
     * @param  bool      $exit
     * @access public
     * @return bool
     */
    public function checkMethodName($var, $exit = true)
    {
        global $filter;
        $rule = $filter->default->methodName;
        if($this->config->framework->filterParam == 2 and isset($filter->{$this->moduleName}->methodName)) $rule = $filter->{$this->moduleName}->methodName;

        if(validater::checkByRule($var, $rule)) return true;
        if(!$exit) return false;
        $this->triggerError("'$var' illegal. ", __FILE__, __LINE__, $exit = true);
    }

    /**
     * 设置Action的扩展文件。 Set the action extension file.
     *
     * @access  public
     * @return  bool
     */
    public function setActionExtFile()
    {
        $moduleExtPaths = $this->getModuleExtPath('', $this->moduleName, 'control');

        /* 如果扩展目录为空，不包含任何扩展文件。If there's no ext paths return false.*/
        if(empty($moduleExtPaths)) return false;

        if(!empty($moduleExtPaths['saas']))
        {
            $this->extActionFile = $moduleExtPaths['saas'] . $this->methodName . '.php';
            if(file_exists($this->extActionFile)) return true;
        }

        /* 1. 如果extensionLevel == 2，且扩展文件存在，返回该站点扩展文件。 If extensionLevel == 2 and site extensionFile exists, return it. */
        if($this->config->framework->extensionLevel == 2 and !empty( $moduleExtPaths['site']))
        {
            $this->extActionFile = $moduleExtPaths['site'] . $this->methodName . '.php';
            if(file_exists($this->extActionFile)) return true;
        }

        /* 2. 尝试在定制开发目录寻找扩展文件。Then try to find the custom extension file. */
        $this->extActionFile = $moduleExtPaths['custom'] . $this->methodName . '.php';
        if(file_exists($this->extActionFile)) return true;;

        /* 3. 如果设置过vision，尝试在vision中查找扩展文件。If vision is set, try to find the vision extension file. */
        if($moduleExtPaths['vision']) $this->extActionFile = $moduleExtPaths['vision'] . $this->methodName . '.php';
        if(file_exists($this->extActionFile)) return true;;

        /* 4. 在喧喧目录中查找扩展文件。Then try to find the xuan extension file. */
        if($moduleExtPaths['xuan']) $this->extActionFile = $moduleExtPaths['xuan'] . $this->methodName . '.php';
        if(file_exists($this->extActionFile)) return true;;

        /* 5. 最后尝试寻找公共扩展文件。Finally, try to find the common extension file. */
        $this->extActionFile = $moduleExtPaths['common'] . $this->methodName . '.php';
        if(empty($moduleExtPaths['common'])) return false;
        return file_exists($this->extActionFile);
    }

    /**
     * Check for API extend.
     *
     * @access public
     * @return bool
     */
    public function checkAPIFile()
    {
        $moduleExtPaths = $this->getModuleExtPath('', $this->moduleName, 'control');

        /* 如果扩展目录为空，不包含任何扩展文件。If there's no ext paths return false.*/
        if(empty($moduleExtPaths)) return false;

        /* 如果extensionLevel == 2，且扩展文件存在，返回该站点扩展文件。If extensionLevel == 2 and site extensionFile exists, return it. */
        if($this->config->framework->extensionLevel == 2 and !empty( $moduleExtPaths['site']))
        {
            $locateFile  = $moduleExtPaths['site'] . $this->methodName . '.302';
            if(file_exists($locateFile)) $this->sendAPI($locateFile);
            $requestFile = $moduleExtPaths['site'] . $this->methodName . '.api';
            if(file_exists($requestFile)) $this->sendAPI($requestFile);
        }

        /* 然后再尝试寻找公共扩展文件。Then try to find the common extension file. */
        $locateFile  = $moduleExtPaths['common'] . $this->methodName . '.302';
        if(file_exists($locateFile)) $this->sendAPI($locateFile);
        $requestFile = $moduleExtPaths['common'] . $this->methodName . '.api';
        if(file_exists($requestFile)) $this->sendAPI($requestFile);

        return false;
    }

    /**
     * 设置一个模块的model文件，如果存在model扩展，一起合并。
     * Set the model file of one module. If there's an extension file, merge it with the main model file.
     *
     * @param  string $moduleName 模块名，如果为空，使用当前模块。The module name, if empty, use current module's name.
     * @param  string $appName    应用名，如果为空，使用当前应用。The app name, if empty, use current app's name.
     * @access public
     * @return string the model file
     */
    public function setModelFile($moduleName, $appName = '')
    {
        return $this->setTargetFile($moduleName, $appName);
    }

    /**
     * 设置一个模块的target文件，如果存在target扩展，一起合并。
     * Set the target file of one module. If there's an extension file, merge it with the main target file.
     *
     * @param  string $moduleName 模块名，如果为空，使用当前模块。The module name, if empty, use current module's name.
     * @param  string $appName    应用名，如果为空，使用当前应用。The app name, if empty, use current app's name.
     * @param  string $class       对象的类型，可选值 target、zen、tao，默认为 target。The type of the object, optional values target, zen, tao, the default is target.
     * @access public
     * @return string the target file
     */
    public function setTargetFile($moduleName, $appName = '', $class = 'model')
    {
        if($appName == '') $appName = $this->getAppName();

        /* 设置主target文件。 Set the main target file. */
        $mainTargetFile = $this->getModulePath($appName, $moduleName) . "{$class}.php";
        if($this->config->framework->extensionLevel == 0) return $mainTargetFile;

        /* 计算扩展的文件和hook文件。Compute the extension files and hook files. */
        $hookFiles     = array();
        $extFiles      = array();
        $apiFiles      = array();
        $siteExtended  = false;

        $targetExtPaths = $this->getModuleExtPath($appName, $moduleName, $class);
        foreach($targetExtPaths as $extType => $targetExtPath)
        {
            if(empty($targetExtPath)) continue;

            $tmpHookFiles = helper::ls($targetExtPath . 'hook/', '.php');
            $tmpExtFiles  = helper::ls($targetExtPath, '.php');
            $tmpAPIFiles  = helper::ls($targetExtPath, '.api');
            $hookFiles    = array_merge($hookFiles, $tmpHookFiles);
            $extFiles     = array_merge($extFiles,  $tmpExtFiles);
            $apiFiles     = array_merge($apiFiles,  $tmpAPIFiles);

            if($extType == 'site' and (!empty($tmpHookFiles) or !empty($tmpExtFiles) or !empty($tmpAPIFiles))) $siteExtended = true;
        }

        /* 如果没有扩展文件，返回主文件。 If no extension or hook files, return the main file directly. */
        if(empty($extFiles) and empty($hookFiles) and empty($apiFiles)) return $mainTargetFile;

        /* 计算合并之后的targetFile路径。Compute the merged target file path. */
        $extTargetPrefix = $this->config->edition . DS . $this->config->vision . DS;
        if($siteExtended and !empty($this->siteCode)) $extTargetPrefix .= $this->siteCode[0] . DS . $this->siteCode;

        $mergedTargetDir  = $this->getTmpRoot() . $class . DS . $extTargetPrefix;
        $mergedTargetFile = $mergedTargetDir . $moduleName . '.php';
        if(!is_dir($mergedTargetDir)) mkdir($mergedTargetDir, 0755, true);

        /* 判断生成的缓存文件是否需要更新。 Judge whether the merged target file needed update or not. */
        if(!$this->needTargetFileUpdate($mergedTargetFile, $extFiles, $hookFiles, $apiFiles, $targetExtPaths, $mainTargetFile)) return $mergedTargetFile;

        /* 合并扩展和hook文件。Merge the extension and hook files. */
        $targetLines = $this->mergeTargetExtFiles($moduleName, $extFiles, $mergedTargetDir, $class);
        $this->mergeTargetHookFiles($moduleName, $mainTargetFile, $targetLines, $hookFiles, $mergedTargetDir, $mergedTargetFile, $apiFiles, $class);

        return $mergedTargetFile;
    }

    /**
     * 检查合并之后的target文件是否需要更新。Check whether the merged target file need update or not.
     *
     * @param  string    $mergedTargetFile
     * @param  array     $extFiles
     * @param  array     $hookFiles
     * @param  array     $apiFiles
     * @param  string    $targetExtPaths
     * @param  string    $mainTargetFile
     * @access public
     * @return bool
     */
    public function needTargetFileUpdate($mergedTargetFile, $extFiles, $hookFiles, $apiFiles, $targetExtPaths, $mainTargetFile)
    {
        $lastTime = file_exists($mergedTargetFile) ? filemtime($mergedTargetFile) : 0;

        foreach($extFiles  as $extFile)  if(filemtime($extFile)  > $lastTime) return true;
        foreach($hookFiles as $hookFile) if(filemtime($hookFile) > $lastTime) return true;
        foreach($apiFiles  as $apiFile)  if(filemtime($apiFile)  > $lastTime) return true;

        $targetExtPath  = $targetExtPaths['common'];
        $targetHookPath = $targetExtPaths['common'] . 'hook/';
        if(is_dir($targetExtPath ) and filemtime($targetExtPath)  > $lastTime) return true;
        if(is_dir($targetHookPath) and filemtime($targetHookPath) > $lastTime) return true;

        if(!empty($targetExtPaths['site']))
        {
            $targetExtPath  = $targetExtPaths['site'];
            $targetHookPath = $targetExtPaths['site'] . 'hook/';
            if(is_dir($targetExtPath ) and filemtime($targetExtPath)  > $lastTime) return true;
            if(is_dir($targetHookPath) and filemtime($targetHookPath) > $lastTime) return true;
        }

        if(filemtime($mainTargetFile) > $lastTime) return true;

        return false;
    }

    /**
     * 将target的扩展文件合并在一起。Merge target ext files.
     *
     * @param  string    $moduleName
     * @param  array     $extFiles
     * @param  string    $mergedTargetDir
     * @param  string    $class             model | zen | tao
     * @access public
     * @return void
     */
    public function mergeTargetExtFiles($moduleName, $extFiles, $mergedTargetDir, $class = 'model')
    {
        /* 设置类名。Set the class names. */
        $targetClass    = $moduleName . ucfirst($class);
        $tmpTargetClass = "tmpExt$targetClass";

        /* 开始拼装代码。Prepare the codes. */
        $targetLines  = "<?php\n";
        $targetLines .= "global \$app;\n";
        $targetLines .= "helper::import(\$app->getModulePath('', '$moduleName') . " . "'$class.php');\n";
        $targetLines .= "class $tmpTargetClass extends $targetClass \n{\n";

        /* 将扩展文件的代码合并到代码中。Cycle all the extension files and merge them into target lines. */
        $extTargets = array();
        foreach($extFiles  as $extFile)  $extTargets[basename($extFile)] = $extFile;
        foreach($extTargets as $extTarget) $targetLines .= self::removePHPTAG($extTarget);

        /* 做个标记，方便后面替换代码使用。Make a mark for replacing codes. */
        $replaceMark = '//**//';
        $targetLines .= "\n$replaceMark\n}";

        /* 生成一个临时的target扩展文件，并加载，用于后续的hook文件加载使用。Create a tmp merged target file and import it for merge hook codes using. */
        $tmpTargetFile = $mergedTargetDir . "tmp$moduleName.php";
        if(@file_put_contents($tmpTargetFile, $targetLines))
        {
            if(!class_exists($tmpTargetClass)) include $tmpTargetFile;
            return $targetLines;
        }

        $this->triggerError("ERROR: $tmpTargetFile not writable.", __FILE__, __LINE__, true);
    }

    /**
     * 合并target的hook脚本。Merge hook files for a target.
     *
     * @param  string $moduleName
     * @param  string $mainTargetFile
     * @param  string $targetLines
     * @param  array  $hookFiles
     * @param  string $mergedTargetDir
     * @param  string $mergedTargetFile
     * @param  array  $apiFiles
     * @param  string $class            model | zen | tao
     * @access public
     * @return void
     */
    public function mergeTargetHookFiles($moduleName, $mainTargetFile, $targetLines, $hookFiles, $mergedTargetDir, $mergedTargetFile, $apiFiles, $class = 'model')
    {
        /* 定义相关变量。Init vars. */
        $targetClass    = $moduleName . ucfirst($class);
        $extTargetClass = 'ext' . $targetClass;
        $tmpTargetClass = 'tmpExt' . $targetClass;
        $tmpTargetFile  = $mergedTargetDir . "tmp$moduleName.php";
        $replaceMark   = '//**//';

        /* 读取hook文件。Get hook codes need to merge. */
        $hookCodes = array();
        foreach($apiFiles as $apiFile)
        {
            /* 通过文件名获得其对应的方法名。Get methods according it's filename. */
            $fileName = baseName($apiFile);
            list($method) = explode('.', $fileName);

            $url = self::extractAPIURL($apiFile);
            if($url) $hookCodes[$method][] = "return helper::requestAPI('$url');";
        }
        foreach($hookFiles as $hookFile)
        {
            /* 通过文件名获得其对应的方法名。Get methods according it's filename. */
            $fileName = baseName($hookFile);
            list($method) = explode('.', $fileName);
            $hookCodes[$method][] = self::removePHPTAG($hookFile);
        }

        /* 合并Hook文件。Cycle the hook methods and merge hook codes. */
        $hookedMethods    = array_keys($hookCodes);
        $mainTargetCodes   = file($mainTargetFile);
        $mergedTargetCodes = file($tmpTargetFile);
        foreach($hookedMethods as $method)
        {
            /* 通过反射获得hook脚本对应的方法所在的文件和起止行数。Reflection the hooked method to get it's defined position. */
            if(!method_exists($tmpTargetClass, $method)) continue;
            $methodRelfection = new reflectionMethod($tmpTargetClass, $method);
            $definedFile = $methodRelfection->getFileName();
            $startLine   = $methodRelfection->getStartLine();
            $endLine     = $methodRelfection->getEndLine();

            /* 将Hook脚本和老的代码合并在一起，并替换原来的定义。Merge hook codes with old codes and replace back. */
            $oldCodes  = $definedFile == $tmpTargetFile ? $mergedTargetCodes : $mainTargetCodes;
            $oldCodes  = join("", array_slice($oldCodes, $startLine - 1, $endLine - $startLine + 1));
            $openBrace = strpos($oldCodes, '{');
            $newCodes  = substr($oldCodes, 0, $openBrace + 1) . "\n" . join("\n", $hookCodes[$method]) . substr($oldCodes, $openBrace + 1);

            if($definedFile == $tmpTargetFile) $targetLines = str_replace($oldCodes, $newCodes, $targetLines);
            if($definedFile != $tmpTargetFile) $targetLines = str_replace($replaceMark, $newCodes . "\n$replaceMark", $targetLines);
        }

        /* 保存最终的Target文件。Save the last merged target file. */
        $targetLines = str_replace($tmpTargetClass, $extTargetClass, $targetLines);
        file_put_contents($mergedTargetFile, $targetLines);
        unlink($tmpTargetFile);
    }

    /**
     * Remove tags of PHP
     *
     * @param  string    $fileName
     * @static
     * @access public
     * @return string
     */
    static public function removePHPTAG($fileName)
    {
        $code = trim(file_get_contents($fileName));
        if(strpos($code, '<?php') === 0)     $code = ltrim($code, '<?php');
        if(strrpos($code, '?' . '>')   !== false) $code = rtrim($code, '?' . '>');
        return trim($code);
    }

    /**
     * Extract API url from api file.
     *
     * @param  string    $fileName
     * @static
     * @access public
     * @return string
     */
    static public function extractAPIURL($fileName)
    {
        global $config;

        $url   = '';
        $lines = file($fileName);
        foreach($lines as $line)
        {
            $line = trim($line);

            if(empty($line)) continue;
            if(preg_match('/^https?\:\/\//', $line))
            {
                $url = $line;
                break;
            }
        }
        if(empty($url)) return false;
        return $url;
    }

    /**
     * Send API.
     *
     * @param  string    $apiFile
     * @access public
     * @return void
     */
    public function sendAPI($apiFile)
    {
        $extension = substr($apiFile, strrpos($apiFile, '.') + 1);
        if($extension != '302' and $extension != 'api') return false;

        $lines = file($apiFile);
        $url   = '';
        foreach($lines as $line)
        {
            $line = trim($line);

            if(empty($line)) continue;
            if(preg_match('/^https?\:\/\//', $line))
            {
                $url = $line;
                break;
            }
        }
        if(empty($url)) return false;

        $url .= (strpos($url, '?') !== false ? '&' : '?') . $this->config->sessionVar . '=' . session_id() . '&account=' . $_SESSION['user']->account;
        if($extension == '302')
        {
            header("location: $url");
            exit;
        }

        if($extension == 'api')
        {
            $response = common::http($url);
            $headFile = $this->moduleRoot . 'common/view/header.html.php';
            $footFile = $this->moduleRoot . 'common/view/footer.html.php';

            $obLevel = ob_get_level();
            for($i = 0; $i < $obLevel; $i++) ob_end_clean();

            $viewFiles = $this->control->setViewFile($this->moduleName, $this->methodName);

            if($css) $this->control->view->pageCSS = $css;
            if($js)  $this->control->view->pageJS  = $js;

            $output  = '';
            $output .= $this->control->printViewFile($headFile);
            $output .= $response;
            if(isset($viewFiles['hookFiles'])) foreach($viewFiles['hookFiles'] as $hookFile) $output .= $this->control->printViewFile($hookFile);
            $output .= $this->control->printViewFile($footFile);
            die($output);
        }
    }

    //-------------------- 路由相关方法(Routing related methods) --------------------//

    /**
     * 设置路由(PATH_INFO 方式)：
     * 1.设置模块名；
     * 2.设置方法名；
     * 3.设置控制器文件。
     *
     * Set the route according to PATH_INFO.
     * 1. set the module name.
     * 2. set the method name.
     * 3. set the control file.
     *
     * @access public
     * @return void
     */
    public function setRouteByPathInfo()
    {
        if(!empty($this->URI))
        {
            /*
             * 根据$requestFix分割符，分割网址。
             * There's the request separator, split the URI by it.
             **/
            if(strpos($this->URI, $this->config->requestFix) !== false)
            {
                $items = explode($this->config->requestFix, $this->URI);
                $this->setModuleName($items[0]);
                $this->setMethodName($items[1]);
            }
            /*
             * 如果网址中没有分隔符，使用默认的方法。
             * No request separator, use the default method name.
             **/
            else
            {
                $this->setModuleName($this->URI);
                $this->setMethodName($this->config->default->method);
            }
        }
        else
        {
            $this->setModuleName($this->config->default->module);   // 使用默认模块 use the default module.
            $this->setMethodName($this->config->default->method);   // 使用默认方法 use the default method.
        }
        $this->setControlFile();
    }

    /**
     * 设置路由(GET 方式)：
     * 1.设置模块名；
     * 2.设置方法名；
     * 3.设置控制器文件。
     *
     * Set the route according to GET.
     * 1. set the module name.
     * 2. set the method name.
     * 3. set the control file.
     *
     * @access public
     * @return void
     */
    public function setRouteByGET()
    {
        $moduleName = isset($_GET[$this->config->moduleVar]) ? strtolower($_GET[$this->config->moduleVar]) : $this->config->default->module;
        $methodName = isset($_GET[$this->config->methodVar]) ? strtolower($_GET[$this->config->methodVar]) : $this->config->default->method;
        $this->setModuleName($moduleName);
        $this->setMethodName($methodName);
        $this->setControlFile();
    }

    /**
     * 加载一个模块：
     * 1. 引入控制器文件或扩展的方法文件；
     * 2. 创建control对象；
     * 3. 解析url，得到请求的参数；
     * 4. 使用call_user_function_array调用相应的方法。
     *
     * Load a module.
     * 1. include the control file or the extension action file.
     * 2. create the control object.
     * 3. set the params passed in through url.
     * 4. call the method by call_user_function_array
     *
     * @access public
     * @return bool|object  if the module object of die.
     */
    public function loadModule()
    {
        try {
            if(is_null($this->params) and !$this->setParams()) return false;

            /* 调用该方法   Call the method. */
            $module = $this->control;

            call_user_func_array(array($module, $this->methodName), $this->params);
            $this->checkAPIFile();
            return $module;
        } catch (EndResponseException $endResponseException) {
            echo $endResponseException->getContent();
        }

        return isset($module) ? $module : false;
    }

    /**
     * 加载指定模块下的某种对象。
     * Load the target object of one module.
     *
     * @param  string $moduleName 模块名，如果为空，使用当前模块。The module name, if empty, use current module's name.
     * @param  string $appName    应用名，如果为空，使用当前应用。The app name, if empty, use current app's name.
     * @param  string $class      对象的类型，可选值 model、zen、tao，默认为 model。The type of the object, optional values model, zen, tao, the default is model.
     * @access public
     * @return object|bool 如果没有model文件，返回false，否则返回model对象。If no model file, return false, else return the model object.
     */
    public function loadTarget($moduleName = '', $appName = '', $class = 'model')
    {
        if(empty($moduleName)) $moduleName = $this->moduleName;
        if(empty($appName)) $appName = $this->appName;

        global $loadedTargets;
        if(isset($loadedTargets[$class][$appName][$moduleName])) return $loadedTargets[$class][$appName][$moduleName];

        $targetFile = $this->setTargetFile($moduleName, $appName, $class);

        /**
         * 如果没有target文件，返回false。
         * If no target file, return false.
         */
        if(!helper::import($targetFile)) return false;

        /**
         * 如果没有扩展文件，target类名是$moduleName + $class，如果有扩展，还需要增加ext前缀。
         * If no extension file, target class name is $moduleName + $class, else with 'ext' as the prefix.
         */
        $targetClass = class_exists('ext' . $appName . $moduleName. $class) ? 'ext' . $appName . $moduleName . $class : $appName . $moduleName . $class;
        if(!class_exists($targetClass))
        {
            $targetClass = class_exists('ext' . $moduleName. $class) ? 'ext' . $moduleName . $class : $moduleName . $class;
            if(!class_exists($targetClass)) $this->triggerError(" The $class $targetClass not found", __FILE__, __LINE__, $exit = true);
        }

        /**
         * 因为zen继承自control，tao继承自model，构造函数里会调用loadTarget方法，赋默认值值防止递归调用。
         */
        if($class == 'zen' || $class == 'tao') $loadedTargets[$class][$appName][$moduleName] = false;

        /**
         * 初始化target 对象并返回。
         * Init the target object and return it.
         */
        $target = new $targetClass($appName);
        $loadedTargets[$class][$appName][$moduleName] = $target;
        return $target;
    }

    /**
     * 设置请求的参数(PATH_INFO 方式)。
     * Set the params by PATH_INFO.
     *
     * @param   array  $defaultParams the default settings of the params.
     * @param   string $type
     * @access  public
     * @return  void
     */
    public function setParamsByPathInfo($defaultParams = array(), $type = '')
    {
        $params = array();
        if($type != 'fetch')
        {
            /* 分割URI。 Spit the URI. */
            $items     = explode($this->config->requestFix, (string)$this->URI);
            $itemCount = count($items);

            /**
             * 前两项为模块名和方法名，参数从下标2开始。
             * The first two item is moduleName and methodName. So the params should begin at 2.
             **/
            for($i = 2; $i < $itemCount; $i ++)
            {
                $key = key($defaultParams);     // Get key from the $defaultParams.
                if(empty($key)) continue;

                $params[$key] = $items[$i];
                next($defaultParams);
            }
        }

        $this->params = $this->mergeParams($defaultParams, $params);
    }

    /**
     * 设置请求的参数(GET 方式)。
     * Set the params by GET.
     *
     * @param   array  $defaultParams the default settings of the params.
     * @param   string $type
     * @access  public
     * @return  void
     */
    public function setParamsByGET($defaultParams, $type = '')
    {
        $params = array();
        if($type != 'fetch')
        {
            /* Unset moduleVar, methodVar, viewVar and session 变量， 剩下的作为参数。 */
            /* Unset the moduleVar, methodVar, viewVar and session var, all the left are the params. */
            unset($_GET[$this->config->moduleVar]);
            unset($_GET[$this->config->methodVar]);
            unset($_GET[$this->config->viewVar]);
            unset($_GET[$this->config->sessionVar]);
            $params = $_GET;
        }

        /* Fix bug #3267. Param 'words' is not validated when searching. */
        if($this->rawModule == 'search' and $this->rawMethod == 'index') unset($params['words']);

        $this->params = $this->mergeParams($defaultParams, $params);
    }

    /**
     * 合并请求的参数和默认参数，这样就可以省略已经有默认值的参数了。
     * Merge the params passed in and the default params. Thus the params which have default values needn't pass value, just like a function.
     *
     * @param   array $defaultParams     the default params defined by the method.
     * @param   array $passedParams      the params passed in through url.
     * @access  public
     * @return  array the merged params.
     */
    public function mergeParams($defaultParams, $passedParams)
    {
        global $filter;

        /* Remove these three params. */
        unset($passedParams['onlybody']);
        unset($passedParams['tid']);
        unset($passedParams['HTTP_X_REQUESTED_WITH']);

        /* Check params from URL. */
        $nameRule = isset($filter->{$this->moduleName}->{$this->methodName}->paramName)  ? $filter->{$this->moduleName}->{$this->methodName}->paramName  : $filter->default->paramName;
        foreach($passedParams as $param => $value)
        {
            if(!validater::checkByRule($param, $nameRule)) die('Bad Request!');
            $valueRule = $filter->default->paramValue;
            if(isset($filter->{$this->moduleName}->{$this->methodName}->paramValue[$param]))
            {
                $valueRule = $filter->{$this->moduleName}->{$this->methodName}->paramValue[$param];
            }

            if($value and !validater::checkByRule($value, $valueRule)) die('Bad Request!');
        }

        $passedParams = array_values($passedParams);
        $i = 0;
        foreach($defaultParams as $key => $defaultValue)
        {
            if(isset($passedParams[$i]))
            {
                $defaultParams[$key] = strip_tags($passedParams[$i]);
            }
            else
            {
                if($defaultValue === '_NOT_SET') $this->triggerError("The param '$key' should pass value. ", __FILE__, __LINE__, $exit = true);
            }
            $i ++;
        }

        return $defaultParams;
    }

    /**
     * 获取$moduleName变量。
     * Get the $moduleName var.
     *
     * @access public
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * 获取$controlFile变量。
     * Get the $controlFile var.
     *
     * @access public
     * @return string
     */
    public function getControlFile()
    {
        return $this->controlFile;
    }

    /**
     * 获取$methodName变量。
     * Get the $methodName var.
     *
     * @access public
     * @return string
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * 获取$param变量。
     * Get the $param var.
     *
     * @access public
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }

    //-------------------- 常用的工具方法(Tool methods) ------------------//

    /**
     * 从类库中加载一个类文件。
     *
     * Load a class file.
     *
     * @param   string $className  the class name
     * @param   bool   $static     statis class or not
     * @access  public
     * @return  object|bool the instance of the class or just true.
     */
    public function loadClass($className, $static = false)
    {
        $className = strtolower($className);

        /* 搜索$coreLibRoot(Search in $coreLibRoot) */
        $classFile = $this->coreLibRoot . $className;
        if(is_dir($classFile)) $classFile .= DS . $className;
        $classFile .= '.class.php';
        if(!helper::import($classFile)) $this->triggerError("class file $classFile not found", __FILE__, __LINE__, $exit = true);

        /* 如果是静态调用，则返回(If static, return) */
        if($static) return true;

        /* 实例化该类(Instance it) */
        global $$className;
        if(!class_exists($className)) $this->triggerError("the class $className not found in $classFile", __FILE__, __LINE__, $exit = true);
        if(!is_object($$className)) $$className = new $className();
        return $$className;
    }

    /**
     * 加载整个应用公共的配置文件。
     * Load the common config files for the app.
     *
     * @access public
     * @return void
     */
    public function loadMainConfig()
    {
        /* 初始化$config对象。Init the $config object. */
        global $config, $filter;
        if(!is_object($config)) $config = new config();
        $this->config = $config;

        /* 加载主配置文件。 Load the main config file. */
        $mainConfigFile = $this->configRoot . 'config.php';
        if(!file_exists($mainConfigFile)) $this->triggerError("The main config file $mainConfigFile not found", __FILE__, __LINE__, $exit = true);
        include $mainConfigFile;
    }

    /**
     * 当multiSite功能打开的时候，加载额外的配置文件。
     * When multiSite feature enabled, load extra config file.
     *
     * @access public
     * @return void
     */
    public function loadExtraConfig()
    {
        global $config;
        $multiConfigFile = $this->configRoot . 'multi.php';
        if(file_exists($multiConfigFile)) include $multiConfigFile;

        $siteConfigFile = $this->configRoot . "sites/{$this->siteCode}.php";
        if(file_exists($siteConfigFile))  include $siteConfigFile;
    }

    /**
     * 加载模块的config文件，返回全局$config对象。
     * 如果该模块是common，加载$configRoot的配置文件，其他模块则加载其模块的配置文件。
     *
     * Load config and return it as the global config object.
     * If the module is common, search in $configRoot, else in $modulePath.
     *
     * @param   string $moduleName     module name
     * @param   string $appName        app name
     * @param   bool   $exitIfNone     exit or not
     * @access  public
     * @return  object|bool the config object or false.
     */
    public function loadModuleConfig($moduleName, $appName = '')
    {
        global $config;

        if($config and (!isset($config->$moduleName) or !is_object($config->$moduleName))) $config->$moduleName = new stdclass();

        /* 初始化数组。Init the variables. */
        $extConfigFiles       = array();
        $commonExtConfigFiles = array();
        $siteExtConfigFiles   = array();

        /* 先获得模块的主配置文件。Get the main config file for current module first. */
        $mainConfigFile = $this->getModulePath($appName, $moduleName) . 'config.php';

        /* 查找扩展配置文件。Get extension config files. */
        if($config->framework->extensionLevel > 0) $extConfigPath = $this->getModuleExtPath($appName, $moduleName, 'config');
        if($config->framework->extensionLevel >= 1)
        {
            if(!empty($extConfigPath['common'])) $commonExtConfigFiles = helper::ls($extConfigPath['common'], '.php');
            if(!empty($extConfigPath['xuan']))   $commonExtConfigFiles = array_merge($commonExtConfigFiles, helper::ls($extConfigPath['xuan'], '.php'));
            if(!empty($extConfigPath['vision'])) $commonExtConfigFiles = array_merge($commonExtConfigFiles, helper::ls($extConfigPath['vision'], '.php'));
            if(!empty($extConfigPath['saas']))   $commonExtConfigFiles = array_merge($commonExtConfigFiles, helper::ls($extConfigPath['saas'], '.php'));
            if(!empty($extConfigPath['custom'])) $commonExtConfigFiles = array_merge($commonExtConfigFiles, helper::ls($extConfigPath['custom'], '.php'));
        }
        if($config->framework->extensionLevel == 2 and !empty($extConfigPath['site']))   $siteExtConfigFiles   = helper::ls($extConfigPath['site'], '.php');
        $extConfigFiles = array_merge($commonExtConfigFiles, $siteExtConfigFiles);

        /* 将主配置文件和扩展配置文件合并在一起。Put the main config file and extension config files together. */
        $configFiles = array_merge(array($mainConfigFile), $extConfigFiles);

        /* 加载每一个配置文件。Load every config file. */
        static $loadedConfigs = array();
        foreach($configFiles as $configFile)
        {
            if(in_array($configFile, $loadedConfigs)) continue;
            if(file_exists($configFile)) include $configFile;
            $loadedConfigs[] = $configFile;
        }

        /* 加载数据库中与本模块相关的配置项。Merge from the db configs. */
        if($moduleName != 'common')
        {
            if(isset($config->system->$moduleName))   $this->mergeConfig($config->system->$moduleName, $moduleName);
            if(isset($config->personal->$moduleName)) $this->mergeConfig($config->personal->$moduleName, $moduleName);
        }
    }

    /**
     * Merge db config.
     *
     * @param  array  $dbConfig
     * @param  string $moduleName
     * @access public
     * @return void
     */
    public  function mergeConfig($dbConfig, $moduleName = 'common')
    {
        global $config;

        /* 如果没有设置本模块配置，则首先进行初始化。Init the $config->$moduleName if not set.*/
        if($moduleName != 'common' and !isset($config->$moduleName)) $config->$moduleName = new stdclass();

        $config2Merge = $config;
        if($moduleName != 'common') $config2Merge = $config->$moduleName;

        foreach($dbConfig as $item)
        {
            if($item->section)
            {
                if(!isset($config2Merge->{$item->section})) $config2Merge->{$item->section} = new stdclass();
                if(is_object($config2Merge->{$item->section}))
                {
                    $config2Merge->{$item->section}->{$item->key} = $item->value;
                }
            }
            else
            {
                $config2Merge->{$item->key} = $item->value;
            }
        }
    }

    /**
     * 向客户端输出配置参数，客户端可以根据这些参数实现和调整请求的逻辑。
     * Export the config params to the client, thus the client can adjust it's logic according the config.
     *
     * @access public
     * @return void
     */
    public function exportConfig()
    {
        $view = new stdclass();
        $view->version       = $this->config->version;
        $view->requestType   = $this->config->requestType;
        $view->requestFix    = $this->config->requestFix;
        $view->moduleVar     = $this->config->moduleVar;
        $view->methodVar     = $this->config->methodVar;
        $view->viewVar       = $this->config->viewVar;
        $view->sessionVar    = $this->config->sessionVar;
        $view->systemMode    = $this->config->systemMode;
        $view->sprintConcept = zget($this->config->custom, 'sprintConcept', '0');
        $view->URAndSR       = zget($this->config->custom, 'URAndSR', '0');
        $view->maxUploadSize = strtoupper(ini_get('upload_max_filesize'));

        $this->session->set('random', mt_rand(0, 10000));
        $view->sessionName = session_name();
        $view->sessionID   = session_id();
        $view->random      = $this->session->random;
        $view->expiredTime = ini_get('session.gc_maxlifetime');
        $view->serverTime  = time();

        echo json_encode($view);
    }

    /**
     * 获取主语言文件。
     * Get main lang file.
     *
     * @param   string $moduleName     the module name
     * @param   string $appName     the app name
     * @access  public
     * @return  string.
     */
    private function getMainLangFile($moduleName, $appName = '')
    {
        $path = $moduleName . DS . 'lang' . DS . $this->clientLang . '.php';

        $modulePath = $this->getExtensionRoot() . 'saas' . DS;
        if(file_exists($modulePath . $path)) return $modulePath . $path;

        /* 1. 最后尝试在定制开发中寻找。 Finally, try to find the module in the custom dir. */
        $modulePath = $this->getExtensionRoot() . 'custom' . DS;
        if(file_exists($modulePath . $path)) return $modulePath . $path;

        /* 2. 如果设置过vision，尝试在vision中查找。 If vision is set, try to find the module in the vision. */
        if($this->config->vision != 'rnd')
        {
            $modulePath = $this->getExtensionRoot() . $this->config->vision . DS;
            if(file_exists($modulePath . $path)) return $modulePath . $path;
        }

        /* 3. 尝试查找商业版本是否有此模块。 Try to find the module in other editon. */
        if($this->config->edition != 'open')
        {
            $modulePath = $this->getExtensionRoot() . $this->config->edition . DS;
            if(file_exists($modulePath . $path)) return $modulePath . $path;
        }

        /* 4. 尝试查找喧喧是否有此模块。 Try to find the module in other editon. */
        $modulePath = $this->getExtensionRoot() . 'xuan' . DS;
        if(file_exists($modulePath . $path)) return $modulePath . $path;

        /* 5. 如果通用版本里有此模块，优先使用。 If module is in the open edition, use it. */
        $modulePath = $this->getModuleRoot($appName);
        if(file_exists($modulePath . $path)) return $modulePath . $path;

        return '';
    }

    /**
     * 加载语言文件，返回全局$lang对象。
     * Load lang and return it as the global lang object.
     *
     * @param   string $moduleName     the module name
     * @param   string $appName     the app name
     * @access  public
     * @return  bool|object the lang object or false.
     */
    public function loadLang($moduleName, $appName = '')
    {
        /* 初始化变量。Init vars. */
        $modulePath      = $this->getModulePath($appName, $moduleName);
        $extLangFiles    = array();
        $langFilesToLoad = array();

        /* 判断主语言文件是否存在。Whether the main lang file exists or not. */
        $mainLangFile = $this->getMainLangFile($moduleName, $appName);
        if($mainLangFile) $langFilesToLoad[] = $mainLangFile;

        /* 获取扩展语言文件。If extensionLevel > 0, get extension lang files. */
        if($this->config->framework->extensionLevel > 0)
        {
            $commonExtLangFiles = array();
            $siteExtLangFiles   = array();

            $extLangPath = $this->getModuleExtPath($appName, $moduleName, 'lang');
            if($this->config->framework->extensionLevel >= 1)
            {
                if(!empty($extLangPath['common'])) $commonExtLangFiles = helper::ls($extLangPath['common'] . $this->clientLang, '.php');
                if(!empty($extLangPath['xuan'])) $commonExtLangFiles = array_merge($commonExtLangFiles, helper::ls($extLangPath['xuan'] . $this->clientLang, '.php'));
                if(!empty($extLangPath['vision'])) $commonExtLangFiles = array_merge($commonExtLangFiles, helper::ls($extLangPath['vision'] . $this->clientLang, '.php'));
                if(!empty($extLangPath['custom'])) $commonExtLangFiles = array_merge($commonExtLangFiles, helper::ls($extLangPath['custom'] . $this->clientLang, '.php'));
                if(!empty($extLangPath['saas']))   $commonExtLangFiles = array_merge($commonExtLangFiles, helper::ls($extLangPath['saas'] . $this->clientLang, '.php'));
            }
            if($this->config->framework->extensionLevel == 2 and !empty($extLangPath['site']))   $siteExtLangFiles   = helper::ls($extLangPath['site'] . $this->clientLang, '.php');
            $extLangFiles  = array_merge($commonExtLangFiles, $siteExtLangFiles);
        }

        /* 计算最终要加载的语言文件。 Get the lang files to be loaded. */
        $langFilesToLoad = array_merge($langFilesToLoad, $extLangFiles);
        if(empty($langFilesToLoad)) return false;

        /* 加载语言文件。Load lang files. */
        global $lang;
        if(!is_object($lang)) $lang = new language();
        if(!isset($lang->$moduleName)) $lang->$moduleName = new stdclass();

        static $loadedLangs = array();
        foreach($langFilesToLoad as $langFile)
        {
            if(in_array($langFile, $loadedLangs)) continue;
            include $langFile;
            $loadedLangs[] = $langFile;
        }

        $this->lang = $lang;
        return $lang;
    }

    /**
     * 连接数据库。
     * Connect to database.
     *
     * @access public
     * @return void
     */
    public function connectDB()
    {
        global $config, $dbh, $slaveDBH;
        if(!isset($config->installed) or !$config->installed) return;

        if(isset($config->db->host))      $this->dbh      = $dbh      = $this->connectByPDO($config->db);
        if(isset($config->slaveDB->host)) $this->slaveDBH = $slaveDBH = $this->connectByPDO($config->slaveDB);
    }

    /**
     * 使用PDO连接数据库。
     * Connect database by PDO.
     *
     * @param  object    $params    the database params.
     * @access public
     * @return object|bool
     */
    public function connectByPDO($params)
    {
        if(!isset($params->driver)) self::triggerError('no pdo driver defined, it should be mysql or sqlite', __FILE__, __LINE__, $exit = true);
        if(!isset($params->user)) return false;
        if($params->driver == 'mysql')
        {
            $dsn = "mysql:host={$params->host}; port={$params->port}; dbname={$params->name}";
        }
        try
        {
            $dbPassword = helper::decryptPassword($params->password);

            $dbh = new PDO($dsn, $params->user, $dbPassword, array(PDO::ATTR_PERSISTENT => $params->persistant));
            $dbh->exec("SET NAMES {$params->encoding}");

            /*
             * 如果系统是Linux，开启仿真预处理和缓冲查询。
             * If run on linux, set emulatePrepare and bufferQuery to true.
             **/
            if(!isset($params->emulatePrepare) and PHP_OS == 'Linux') $params->emulatePrepare = true;
            if(!isset($params->bufferQuery) and PHP_OS == 'Linux')    $params->bufferQuery = true;

            if(defined('RUN_MODE') and RUN_MODE == 'api') $params->emulatePrepare = false;

            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if(isset($params->strictMode) and $params->strictMode == false) $dbh->exec("SET @@sql_mode= ''");
            if(isset($params->emulatePrepare)) $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, $params->emulatePrepare);
            if(isset($params->bufferQuery))    $dbh->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, $params->bufferQuery);

            return $dbh;
        }
        catch (PDOException $exception)
        {
            $message = $exception->getMessage();
            if(empty($message))
            {
                /* Try to repair table. */
                header("location: {$this->config->webRoot}checktable.php");
                exit;
            }
            self::triggerError($message, __FILE__, __LINE__, $exit = true);
        }
    }

    //-------------------- 错误处理方法(Error methods) ------------------//

    /**
     * 程序停止时执行的函数。
     * The shutdown handler.
     *
     * @access public
     * @return void
     */
    public function shutdown()
    {
        /* 如果debug模式开启，保存sql语句(If debug on, save sql queries) */
        if(!empty($this->config->debug)) $this->saveSQL();

        /*
         * 发现错误，保存到日志中。
         * If any error occurs, save it.
         * */
        if(!function_exists('error_get_last')) return;
        $error = error_get_last();
        if($error) $this->saveError($error['type'], $error['message'], $error['file'], $error['line']);
    }

    /**
     * 触发一个错误。
     * Trigger an error.
     *
     * @param string    $message    错误信息      error message
     * @param string    $file       所在文件      the file error occurs
     * @param int       $line       错误行        the line error occurs
     * @param bool      $exit       是否停止程序  exit the program or not
     * @access public
     * @return void
     */
    public function triggerError($message, $file, $line, $exit = false)
    {
        /* 设置错误信息(Set the error info) */
        $message = htmlSpecialString($message);
        if(preg_match('/[^\x00-\x80]/', $message)) $message = helper::convertEncoding($message, 'gbk');

        /* Only show error when debug is open. */
        if(!$this->config->debug) die();

        $log = "ERROR: $message in $file on line $line";
        if(isset($_SERVER['SCRIPT_URI'])) $log .= ", request: $_SERVER[SCRIPT_URI]";;
        $trace = debug_backtrace();
        extract($trace[0]);
        extract($trace[1]);
        $log .= ", last called by $file on line $line through function $function.\n";

        /* Change absolute path to relative path. */
        $log = str_replace($this->basePath, '', $log);

        /* 触发错误(Trigger the error) */
        trigger_error($log, $exit ? E_USER_ERROR : E_USER_WARNING);
    }

    /**
     * 保存错误信息。
     * Save error info.
     *
     * @param  int    $level
     * @param  string $message
     * @param  string $file
     * @param  int    $line
     * @access public
     * @return void
     */
    public function saveError($level, $message, $file, $line)
    {
        if(empty($this->config->debug))  return true;
        if(!is_dir($this->logRoot))      return true;
        if(!is_writable($this->logRoot)) return true;

        /*
         * 删除设定时间之前的日志。
         * Delete the log before the set time.
         **/
        if(mt_rand(0, 10) == 1)
        {
            $logDays = isset($this->config->framework->logDays) ? $this->config->framework->logDays : 14;
            $dayTime = time() - $logDays * 24 * 3600;
            foreach(glob($this->getLogRoot() . '*') as $logFile)
            {
                if(filemtime($logFile) <= $dayTime) unlink($logFile);
            }
        }

        /*
         * 忽略该错误：Redefining already defined constructor。
         * Skip the error: Redefining already defined constructor.
         **/
        if(strpos($message, 'Redefining') !== false) return true;

        /*
         * 设置错误信息。
         * Set the error info.
         **/
        if(preg_match('/[^\x00-\x80]/', $message)) $message = helper::convertEncoding($message, 'gbk');
        $errorLog  = "\n" . date('H:i:s') . " $message in <strong>$file</strong> on line <strong>$line</strong> ";

        $URI = $this->getURI();
        $errorLog .= "when visiting <strong>" . (empty($URI) ? '' : htmlspecialchars($URI)) . "</strong>\n";

        /*
         * 为了安全起见，对公网环境隐藏脚本路径。
         * If the ip is pulic, hidden the full path of scripts.
         */
        $remoteIP = helper::getRemoteIp(true);
        if(!defined('IN_SHELL') and !($remoteIP == '127.0.0.1' or filter_var($remoteIP, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) === false))
        {
            $errorLog  = str_replace($this->getBasePath(), '', $errorLog);
        }

        /* 保存到日志文件(Save to log file) */
        $errorFile = $this->logRoot . 'php.' . date('Ymd') . '.log.php';
        if(!is_file($errorFile)) file_put_contents($errorFile, "<?php\n die();\n?" . ">\n");

        $fh = fopen($errorFile, 'a');
        if($fh) fwrite($fh, strip_tags($errorLog)) and fclose($fh);

        /*
         * 如果debug > 1，显示warning, notice级别的错误。
         * If the debug > 1, show warning, notice error.
         **/
        if($level == E_NOTICE or $level == E_WARNING or $level == E_STRICT or $level == 8192) // 8192: E_DEPRECATED
        {
            if(!empty($this->config->debug) and $this->config->debug > 1)
            {
                $cmd  = "vim +$line $file";
                $size = strlen($cmd);
                echo "<pre class='alert alert-danger'>$message: ";
                echo "<input type='text' value='$cmd' size='$size' style='border:none; background:none;' onclick='this.select();' /></pre>";
            }
        }

        /*
         * 如果是严重错误，停止程序。
         * If error level is serious, die.
         * */
        if($level == E_ERROR or $level == E_PARSE or $level == E_CORE_ERROR or $level == E_COMPILE_ERROR or $level == E_USER_ERROR)
        {
            if(empty($this->config->debug)) die();
            if(PHP_SAPI == 'cli') die($errorLog);

            $htmlError  = "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /></head>";
            $htmlError .= "<body>" . nl2br($errorLog) . "</body></html>";
            die($htmlError);
        }
    }

    /**
     * 保存sql语句。
     * Save the sql.
     *
     * @access public
     * @return void
     */
    public function saveSQL()
    {
        if(!$this->config->debug) return true;
        if(!class_exists('dao')) return;

        $sqlLog = $this->getLogRoot() . 'sql.' . date('Ymd') . '.log.php';
        if(!is_file($sqlLog)) file_put_contents($sqlLog, "<?php\n die();\n?" . ">\n");

        $fh = @fopen($sqlLog, 'a');
        if(!$fh) return false;
        fwrite($fh, date('Ymd H:i:s') . ": " . $this->getURI() . "\n");
        foreach(dao::$querys as $query) fwrite($fh, "  $query\n");
        fwrite($fh, "\n");
        fclose($fh);
    }

    /**
     * Check app if it is run in a container.
     *
     * @access public
     * @return bool
     */
    public function isContainer()
    {
        return strtolower(getenv('IS_CONTAINER')) == 'true';
    }
}

/**
 * config类。
 * The config class.
 *
 * @package framework
 */
class config
{
    /**
     * 设置成员变量，成员可以是'db.user'类似的格式。
     * Set the value of a member. the member can be the format like db.user.
     *
     * <code>
     * <?php
     * $config->set('db.user', 'wwccss');
     * ?>
     * </code>
     * @param   string  $key    the key of the member
     * @param   mixed   $value  the value
     * @access  public
     * @return  void
     */
    public function set($key, $value)
    {
        helper::setMember('config', $key, $value);
    }
}

/**
 * lang类。
 * The lang class.
 *
 * @package framework
 */
class language
{
    /**
     * 设置成员变量，成员可以是'db.user'类似的格式。
     * Set the value of a member. the member can be the foramt like db.user.
     *
     * <code>
     * <?php
     * $lang->set('version', '1.0);
     * ?>
     * </code>
     * @param   string  $key    成员的键名，可以是father.child的形式。
     *                          the key of the member, can be father.child
     * @param   mixed   $value  the value
     * @access  public
     * @return  void
     */
    public function set($key, $value)
    {
        helper::setMember('lang', $key, $value);
    }

    /**
     * 显示一个成员的值。
     * Show a member.
     *
     * @param   object $obj    the object
     * @param   string $key    the key
     * @access  public
     * @return  void
     */
    public function show($obj, $key)
    {
        $obj = (array)$obj;
        echo isset($obj[$key]) ? $obj[$key] : '';
    }
}

/**
 * 超级对象类，转化超级全局变量。
 * The super object class.
 *
 * @package framework
 */
class super
{
    /**
     * 构造函数，设置超级变量名。
     * Construct, set the var scope.
     *
     * @param   string $scope  the scope, can be server, post, get, cookie, session, global
     * @access  public
     * @return  void
     */
    public function __construct($scope, $tab = '')
    {
        $this->scope = $scope;
        $this->tab   = $tab;
    }

    /**
     * 设置超级变量的成员值。
     * Set one member value.
     *
     * @param   string $key the key
     * @param   mixed  $value  the value
     * @param   string $tab
     * @access  public
     * @return  void
     */
    public function set($key, $value, $tab = '')
    {
        if($this->scope == 'post')
        {
            $_POST[$key] = $value;
        }
        elseif($this->scope == 'get')
        {
            $_GET[$key] = $value;
        }
        elseif($this->scope == 'server')
        {
            $_SERVER[$key] = $value;
        }
        elseif($this->scope == 'cookie')
        {
            $_COOKIE[$key] = $value;
        }
        elseif($this->scope == 'session')
        {
            if($tab) $_SESSION["app-$tab"][$key] = $value;
            $_SESSION[$key] = $value;
        }
        elseif($this->scope == 'env')
        {
            $_ENV[$key] = $value;
        }
        elseif($this->scope == 'global')
        {
            $GLOBALS[$key] = $value;
        }
    }

    /**
     * 超级变量的魔术方法，比如用$post->key访问$_POST['key']。
     * The magic get method.
     *
     * @param  string $key    the key
     * @access public
     * @return mixed|bool     return the value of the key or false.
     */
    public function __get($key)
    {
        if($this->scope == 'post')
        {
            if(isset($_POST[$key])) return $_POST[$key];
            return false;
        }
        elseif($this->scope == 'get')
        {
            if(isset($_GET[$key])) return $_GET[$key];
            return false;
        }
        elseif($this->scope == 'server')
        {
            if($key == 'ajax') return isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? true : false;
            if(isset($_SERVER[$key])) return $_SERVER[$key];
            $key = strtoupper($key);
            if(isset($_SERVER[$key])) return $_SERVER[$key];
            return false;
        }
        elseif($this->scope == 'cookie')
        {
            if(isset($_COOKIE[$key])) return $_COOKIE[$key];
            return false;
        }
        elseif($this->scope == 'session')
        {
            $tab = $this->tab;
            if($tab and isset($_SESSION["app-$tab"][$key])) return $_SESSION["app-$tab"][$key];
            if(isset($_SESSION[$key])) return $_SESSION[$key];
            return false;
        }
        elseif($this->scope == 'env')
        {
            if(isset($_ENV[$key])) return $_ENV[$key];
            return false;
        }
        elseif($this->scope == 'global')
        {
            if(isset($GLOBALS[$key])) return $GLOBALS[$key];
            return false;
        }
        else
        {
            return false;
        }
    }

    /**
     * 打印变量的详细结构。
     * Print the structure.
     *
     * @access public
     * @return void
     */
    public function a()
    {
        if($this->scope == 'post')    a($_POST);
        if($this->scope == 'get')     a($_GET);
        if($this->scope == 'server')  a($_SERVER);
        if($this->scope == 'cookie')  a($_COOKIE);
        if($this->scope == 'session') a($_SESSION);
        if($this->scope == 'env')     a($_ENV);
        if($this->scope == 'global')  a($GLOBALS);
    }
}

class EndResponseException extends \Exception
{
    /**
     * 响应内容
     *
     * @var string
     */
    private $content;

    /**
     * @param string $content
     *
     * @return self
     */
    public static function create($content = '')
    {
        $exception = new self;
        $exception->content = $content;
        return $exception;
    }

    /**
     * Get 响应内容
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}

/**
 * ZenTao session handler.
 *
 * @package framework
 */
class ztSessionHandler
{
    public $sessSavePath;
    public $tagID;
    public $sessionFile;
    public $sessionID;
    public $rawID;
    public $rawFile;

    /**
     * Construct.
     *
     * @param  string $tagID
     * @access public
     * @return void
     */
    public function __construct($tagID = '')
    {
        $this->tagID = $tagID;
        ini_set('session.save_handler', 'files');
        register_shutdown_function('session_write_close');
    }

    /**
     * Get sessionID
     *
     * @access public
     * @return string
     */
    public function getSessionID()
    {
        return $this->sessionID;
    }

    /**
     * Get session file.
     *
     * @param  string    $id
     * @access public
     * @return string
     */
    public function getSessionFile($id)
    {
        if(!empty($this->sessionFile)) return $this->sessionFile;

        $sessionID = $id;
        if($this->tagID) $sessionID = md5($id . $this->tagID);

        $fileName = "sess_$sessionID";

        $this->sessionFile = $this->sessSavePath . '/' . $fileName;
        $this->sessionID   = $sessionID;
        $this->rawID       = $id;
        $this->rawFile     = $this->sessSavePath . '/' . "sess_$id";
        return $this->sessionFile;
    }

    /**
     * Open
     *
     * @param  string    $savePath
     * @param  string    $sessionName
     * @access public
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function open($savePath, $sessionName)
    {
        $this->sessSavePath = $savePath;
        return true;
    }

    /**
     * Close
     *
     * @access public
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function close()
    {
        return true;
    }

    /**
     * Read
     *
     * @param  string    $id
     * @access public
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function read($id)
    {
        $sessFile = $this->getSessionFile($id);
        if(!file_exists($sessFile))
        {
            ($this->tagID and file_exists($this->rawFile)) ? copy($this->rawFile, $sessFile) : touch($sessFile);
        }

        return (string) file_get_contents($sessFile);
    }

    /**
     * Write
     *
     * @param  string    $id
     * @param  string    $sessData
     * @access public
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function write($id, $sessData)
    {
        $sessFile = $this->getSessionFile($id);
        touch($sessFile);
        touch($this->rawFile);
        if(md5_file($sessFile) == md5($sessData)) return true;

        if(file_put_contents($sessFile, $sessData, LOCK_EX))
        {
            if(strpos($sessData, 'user|') !== false)
            {
                $rawSessContent = (string) file_get_contents($this->rawFile, false, null, 0, 1024 * 2);
                if(strpos($rawSessContent, 'user|') === false) file_put_contents($this->rawFile, $sessData, LOCK_EX);
            }

            return true;
        }
        return false;
    }

    /**
     * Destroy
     *
     * @param  string    $id
     * @access public
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function destroy($id)
    {
        $sessFile = $this->getSessionFile($id);
        if(file_exists($sessFile)) unlink($sessFile);
        if(file_exists($this->rawFile)) unlink($this->rawFile);
        touch($sessFile);
        touch($this->rawFile);
        return true;
    }

    /**
     * GC
     *
     * @param  int    $maxlifeTime
     * @access public
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function gc($maxlifeTime)
    {
        $time = time();
        foreach(glob("$this->sessSavePath/sess_*") as $fileName)
        {
            if(filemtime($fileName) + $maxlifeTime < $time) @unlink($fileName);
        }
        return true;
    }
}
