<?php
if (! defined('FARM_PATH'))
{
    define('FARM_PATH', dirname(__FILE__) . '/');
}

require_once (FARM_PATH . 'init.php');

if (defined('G_GZIP_COMPRESS') AND G_GZIP_COMPRESS === TRUE)
{
    if (@ini_get('zlib.output_compression') == FALSE)
    {
        if (extension_loaded('zlib'))
        {
            if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) AND strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE)
            {
                ob_start('ob_gzhandler');
            }
        }
    }
}

require_once (FARM_PATH . 'farm_app.inc.php');
require_once (FARM_PATH . 'farm_controller.inc.php');
require_once (FARM_PATH . 'farm_model.inc.php');
