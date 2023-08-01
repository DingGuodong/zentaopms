<?php
declare(strict_types=1);
/**
 * The step3 view file of install module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Mengyi Liu <liumengyi@easycorp.ltd>
 * @package     install
 * @link        https://www.zentao.net
 */
namespace zin;

set::zui(true);
$configContent = <<<EOT
<?php
\$config->installed       = true;
\$config->debug           = false;
\$config->requestType     = '$config->requestType';
\$config->timezone        = '$config->timezone';
\$config->db->driver      = '{$myConfig['dbDriver']}';
\$config->db->host        = '{$myConfig['dbHost']}';
\$config->db->port        = '{$myConfig['dbPort']}';
\$config->db->name        = '{$myConfig['dbName']}';
\$config->db->user        = '{$myConfig['dbUser']}';
\$config->db->encoding    = '{$myConfig['dbEncoding']}';
\$config->db->password    = '{$myConfig['dbPassword']}';
\$config->db->prefix      = '{$myConfig['dbPrefix']}';
\$config->webRoot         = getWebRoot();
\$config->default->lang   = '{$myConfig['defaultLang']}';
EOT;
if($customSession) $configContent .= "\n\$config->customSession = true;";

$configRoot   = $this->app->getConfigRoot();
$myConfigFile = $configRoot . 'my.php';
$saveTip      = '';
if(is_writable($configRoot))
{
    if(@file_put_contents($myConfigFile, $configContent))
    {
        $saveTip = sprintf($lang->install->saved2File, $myConfigFile);
    }
    else
    {
        $saveTip = sprintf($lang->install->save2File, $myConfigFile);
    }
}
else
{
    $saveTip = sprintf($lang->install->save2File, $myConfigFile);
}

div
(
    set::id('main'),
    div
    (
        set::id('mainContent'),
        setClass('px-1 mt-2'),
        panel
        (
            set::title($lang->install->saveConfig),
            set::titleClass('pt-3'),
            textarea
            (
                set::name('config'),
                html($configContent),
            ),
            cell
            (
                setClass('text-center p-2'),
                html($saveTip),
            ),
            cell
            (
                setClass('text-center'),
                btn
                (
                    setClass('px-6'),
                    set::url(inlink('step4')),
                    set::type('primary'),
                    $lang->install->next,
                ),
            ),
        ),
    ),
);

render('pagebase');
