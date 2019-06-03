<?php
/*
+--------------------------------------------------------------------------
|   Mox
|   ========================================
|   by Mox Software
|   © 2018 - 2019 Mox. All Rights Reserved
|   http://www.mox365.com
|   ========================================
|   Support: Mox@qq.com
|
+---------------------------------------------------------------------------
*/

class core_autoload
{
    public static $loaded_class = array();

    private static $aliases = array(
        'TPL'               => 'class/cls_template.inc.php',
        'FORMAT'            => 'class/cls_format.inc.php',
        'HTTP'              => 'class/cls_http.inc.php',
        'H'                 => 'class/cls_helper.inc.php',
        'P'                 => 'class/cls_pinyin.inc.php',
        'IMAGE'             => 'class/cls_image.inc.php',
    );

    public function __construct()
    {
        set_include_path(MOX_PATH);

        foreach (self::$aliases AS $key => $val)
        {
            self::$aliases[$key] = MOX_PATH . $val;
        }

        spl_autoload_register(array($this, 'loader'));
    }

    private static function loader($class_name)
    {
        $require_file = MOX_PATH . preg_replace('#_+#', '/', $class_name) . '.php';

        if (file_exists($require_file))
        {
            $class_file_location = $require_file;
        }
        else
        {
            if (class_exists('MOX_APP', false))
            {
                if (MOX_APP::plugins()->model())
                {
                    self::$aliases = array_merge(self::$aliases, MOX_APP::plugins()->model());
                }
            }

            if (isset(self::$aliases[$class_name]))
            {
                $class_file_location = self::$aliases[$class_name];
            }
            // 查找 models 目录
            else if (file_exists(ROOT_PATH . 'models/' . str_replace(array('_class', '_'), array('', '/'), $class_name) . '.php'))
            {
                $class_file_location = ROOT_PATH . 'models/' . str_replace(array('_class', '_'), array('', '/'), $class_name) . '.php';
            }
            // 查找 class
            else if (file_exists(MOX_PATH . 'class/' . $class_name . '.inc.php'))
            {
                $class_file_location = MOX_PATH . 'class/' . $class_name . '.inc.php';
            }
        }

        if ($class_file_location)
        {
            require $class_file_location;

            self::$loaded_class[$class_name] = $class_file_location;

            if ($class_name == 'TPL')
            {
                TPL::initialize();
            }

            return true;
        }
    }
}