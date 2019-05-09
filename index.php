<?php
header("Content-type: text/html; charset=utf-8");
include('system/system.php');

if (! defined('APP_PATH'))
{
    define('APP_PATH', dirname(__FILE__) . '/');
}

MOX_APP :: run();
