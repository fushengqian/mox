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

class core_lang
{
    private $lang = array();

    public function __construct()
    {
        if (!defined('SYSTEM_LANG'))
        {
            return false;
        }

        if (SYSTEM_LANG == '')
        {
            return false;
        }

        $language_file = ROOT_PATH . 'language/' . SYSTEM_LANG . '.php';

        if (file_exists($language_file))
        {
            require $language_file;
        }
    }

    public function translate($string, $replace = null, $display = false)
    {
        $search = '%s';

        if (is_array($replace))
        {
            $search = array();

            for ($i=0; $i<count($replace); $i++)
            {
                $search[] = '%s' . $i;
            };
        }

        if ($translate = $this->lang[trim($string)])
        {
            if (isset($replace))
            {
                $translate = str_replace($search, $replace, $translate);
            }

            if (!$display)
            {
                return $translate;
            }

            echo $translate;
        }
        else
        {
            if (isset($replace))
            {
                $string = str_replace($search, $replace, $string);
            }

            return $string;
        }
    }

    public function _t($string, $replace = null, $display = false)
    {
        return $this->translate($string, $replace, $display);
    }
}