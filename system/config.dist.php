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

// 定义 Cookies 作用域
define('G_COOKIE_DOMAIN','');

// 定义 Cookies 前缀
define('G_COOKIE_PREFIX','{G_COOKIE_PREFIX}');

// 定义应用加密 KEY
define('G_SECUKEY','{G_SECUKEY}');
define('G_COOKIE_HASH_KEY', '{G_COOKIE_HASH_KEY}');

define('G_INDEX_SCRIPT', '?/');

define('X_UA_COMPATIBLE', 'IE=edge,Chrome=1');

// GZIP 压缩输出页面
define('G_GZIP_COMPRESS', FALSE);

// Session 存储类型 (db, file)
define('G_SESSION_SAVE', 'db');

// Session 文件存储路径
define('G_SESSION_SAVE_PATH', '');

define('G_BASE_DEMAIN', '.moxquan.cn');

$base_url = explode('.', $_SERVER['SERVER_NAME']);
if ($base_url[0] == 'm') {
    define('G_DEMAIN', 'http://m.moxquan.cn');
} else {
    define('G_DEMAIN', 'http://www.moxquan.cn');
}

define('SITE_NAME', '模型圈');

define('G_STATIC', G_DEMAIN.'/static');