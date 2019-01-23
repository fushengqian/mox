<?php
// 定义 Cookies 作用域
define('G_COOKIE_DOMAIN', '.moxquan.cn');

// 定义 Cookies 前缀
define('G_COOKIE_PREFIX', 'mox_');

// 定义应用加密 KEY
define('G_SECUKEY', 'xmkbfrj2xkbg');
define('G_COOKIE_HASH_KEY', 'yjklauro1fcksol');

define('G_INDEX_SCRIPT', '/');

define('X_UA_COMPATIBLE', 'IE=edge,Chrome=1');

// GZIP 压缩输出页面
define('G_GZIP_COMPRESS', true);

// 是否缓存
define('G_CACHE', true);

define('G_ONLINE', false);

// Session 存储类型 (db, file)
define('G_SESSION_SAVE', 'db');

// Session 文件存储路径
define('G_SESSION_SAVE_PATH', '');

define('G_BASE_DEMAIN', '.moxquan.cn');

$base_url = explode('.', $_SERVER['SERVER_NAME']);
if ($base_url[0] == 'm')
{
   define('G_DEMAIN', 'http://m.moxquan.cn');
}
else
{
    define('G_DEMAIN', 'http://www.moxquan.cn');
}

define('SITE_NAME', '模型圈');

define('G_STATIC', 'http://www.moxquan.cn/static');

