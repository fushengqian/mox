<?php
/**
+--------------------------------------------------------------------------
|   Mox 1.0.1
|   ========================================
|   by Mox Software
|   © 2018 - 2019 Mox. All Rights Reserved
|   http://www.mox365.com
|   ========================================
|   Support: 540335306@qq.com
|   Author: FSQ
+---------------------------------------------------------------------------
*/

class core_plugins
{
    public $plugins = array();
    public $plugins_path;

    private $plugins_table = array();
    private $plugins_model = array();

    public function __construct()
    {
        $this->plugins_path = ROOT_PATH . 'plugins/';
        $this->load_plugins();
    }

    public function plugins_list()
    {
        $plugins_list = array();

        foreach ($this->plugins AS $key => $data)
        {
            $plugins_list[$key] = $data['title'] . ' - 版本: ' . $data['version'];
        }

        return $plugins_list;
    }

    public function installed($plugin_id)
    {
        foreach ($this->plugins AS $key => $data)
        {
            if ($key == $plugin_id)
            {
                return true;
            }
        }
    }

    public function load_plugins()
    {
        $plugins_cache = TEMP_PATH . 'plugins.php';
        $plugins_table_cache = TEMP_PATH . 'plugins_table.php';
        $plugins_model_cache = TEMP_PATH . 'plugins_model.php';
        if (file_exists($plugins_cache) AND file_exists($plugins_table_cache) AND file_exists($plugins_model_cache))
        {
            $this->plugins = unserialize(file_get_contents($plugins_cache));
            $this->plugins_table = unserialize(file_get_contents($plugins_table_cache));
            $this->plugins_model = unserialize(file_get_contents($plugins_model_cache));
            return false;
        }
        
        //echo $this->plugins_path;die;
        
        
        $dir_handle = opendir($this->plugins_path);
        while (($file = readdir($dir_handle)) !== false)
        {
               if ($file != '.' AND $file != '..' AND is_dir($this->plugins_path . $file))
               {
                   $config_file = $this->plugins_path . $file . '/config.php';

                if (file_exists($config_file))
                {
                    $mox_plugin = false;

                    require_once($config_file);

                    if (is_array($mox_plugin) AND G_VERSION_BUILD >= $mox_plugin['requirements'])
                    {
                        if ($mox_plugin['contents']['model'])
                        {
                            $this->plugins_model[$mox_plugin['contents']['model']['class_name']] = $this->plugins_path . $file . '/' . $mox_plugin['contents']['model']['include'];
                        }

                        if ($mox_plugin['contents']['setups'])
                        {
                            foreach ($mox_plugin['contents']['setups'] AS $key => $data)
                            {
                                if ($data['app'] AND $data['controller'] AND $data['include'])
                                {
                                    $this->plugins_table[$data['app']][$data['controller']]['setup'][] = array(
                                        'file' => $this->plugins_path . $file . '/' . $data['include'],
                                    );
                                }
                            }
                        }

                        if ($mox_plugin['contents']['actions'])
                        {
                            foreach ($mox_plugin['contents']['actions'] AS $key => $data)
                            {
                                if ($data['app'] AND $data['controller'] AND $data['include'])
                                {
                                    $this->plugins_table[$data['app']][$data['controller']][$data['action']][] = array(
                                        'file' => $this->plugins_path . $file . '/' . $data['include'],
                                        'template' => $data['template']
                                    );
                                }
                            }
                        }

                        $this->plugins[$file] = $mox_plugin;
                    }
                }
            }
        }

           closedir($dir_handle);

           @file_put_contents($plugins_cache, serialize($this->plugins));
           @file_put_contents($plugins_table_cache, serialize($this->plugins_table));
           @file_put_contents($plugins_model_cache, serialize($this->plugins_model));

           return true;
    }

    public function model()
    {
        return $this->plugins_model;
    }

    public function parse($app, $controller, $action, $template = NULL)
    {
        if (!$controller)
        {
            $controller = 'main';
        }

        if (!$action)
        {
            $controller = 'index';
        }

        if ($this->plugins_table[$app][$controller][$action])
        {
            foreach ($this->plugins_table[$app][$controller][$action] AS $key => $plugins_files)
            {
                if ($plugins_files['template'] AND $template)
                {
                    if ($template == $plugins_files['template'])
                    {
                        $files_list[] = $plugins_files['file'];
                    }
                }
                else
                {
                    $files_list[] = $plugins_files['file'];
                }
            }

            return $files_list;
        }
    }
}