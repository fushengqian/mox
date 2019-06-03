<?php
/**
+--------------------------------------------------------------------------
|   Mox
|   ========================================
|   by Mox Software
|   © 2018 - 2019 Mox. All Rights Reserved
|   http://www.mox365.com
|   ========================================
|   Support: 540335306@qq.com
|   Author: FSQ
+---------------------------------------------------------------------------
*/

define('IN_MOX', TRUE);
define('ENVIRONMENT_PHP_VERSION', '5.4.0');

define('START_TIME', microtime(TRUE));
define('TIMESTAMP', time());

if (function_exists('memory_get_usage'))
{
    define('MEMORY_USAGE_START', memory_get_usage());
}

if (! defined('MOX_PATH'))
{
    define('MOX_PATH', dirname(__FILE__) . '/');
}

if (defined('SAE_TMP_PATH'))
{
    define('IN_SAE', true);
}

define('ROOT_PATH', dirname(dirname(__FILE__)) . '/');

if (defined('IN_SAE'))
{
    error_reporting(1);
    define('TEMP_PATH', rtrim(SAE_TMP_PATH, '/') . '/');
}
else
{
    if (version_compare(PHP_VERSION, '5.4', '>='))
    {
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_WARNING);
    }
    else
    {
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
    }
    
    define('TEMP_PATH', dirname(dirname(__FILE__)) . '/tmp/');
}

if (function_exists('get_magic_quotes_gpc'))
{
    if (@get_magic_quotes_gpc()) // GPC 进行反向处理
    {
        if (! function_exists('stripslashes_gpc'))
        {
            function stripslashes_gpc(&$value)
            {
                $value = stripslashes($value);
            }
        }
        array_walk_recursive($_GET, 'stripslashes_gpc');
        array_walk_recursive($_POST, 'stripslashes_gpc');
        array_walk_recursive($_COOKIE, 'stripslashes_gpc');
        array_walk_recursive($_REQUEST, 'stripslashes_gpc');
    }
}

require_once(ROOT_PATH . 'version.php');
require_once(MOX_PATH . 'functions.inc.php');

array_walk_recursive($_GET, 'remove_invisible_characters');
array_walk_recursive($_POST, 'remove_invisible_characters');
array_walk_recursive($_COOKIE, 'remove_invisible_characters');
array_walk_recursive($_REQUEST, 'remove_invisible_characters');

if (@ini_get('register_globals'))
{
    if ($_REQUEST)
    {
        foreach ($_REQUEST AS $name => $value)
        {
            unset($$name);
        }
    }
    
    if ($_COOKIE)
    {
        foreach ($_COOKIE AS $name => $value)
        {
            unset($$name);
        }
    }
}

if (file_exists(ROOT_PATH . 'data/config/system.php'))
{
    require_once(ROOT_PATH . 'data/config/system.php');
}

load_class('core_autoload');

date_default_timezone_set('Etc/GMT-8');
