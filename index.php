<?php
/**
+--------------------------------------------------------------------------
|   Mox
|   ========================================
|   by Mox Software
|   © 2018 - 2019 Mox. All Rights Reserved
|   http://www.moxquan.com
|   ========================================
|   Support: 540335306@qq.com
|   Author: FSQ
+---------------------------------------------------------------------------
*/

header("Content-type: text/html; charset=utf-8");
include('system/system.php');

if (!file_exists(dirname(__FILE__) . '/data/config/database.php') || !file_exists(dirname(__FILE__) . '/data/install.lock'))
{
    header('Location: ./install/');
    exit;
}

if (! defined('APP_PATH'))
{
    define('APP_PATH', dirname(__FILE__) . '/');
}

MOX_APP::run();
