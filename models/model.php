<?php
/**
+--------------------------------------------------------------------------
|   Mox 1.0.1
|   ========================================
|   by Mox Software
|   © 2018 - 2019 Mox. All Rights Reserved
|   http://www.moxquan.com
|   ========================================
|   Support: 540335306@qq.com
|   Author: FSQ
+---------------------------------------------------------------------------
*/

class model_class extends MOX_MODEL
{
    /**
     * 获取列表
     */
    public function get_data_list($where, $page = 1, $per_page = 10, $order_by = 'id asc')
    {
        if (is_array($where)) {
            $where = implode(' AND ', $where);
        }

        return $this->fetch_page('model', $where, $order_by, $page, $per_page);
    }

    public function get_model_by_id($id)
    {
        $data = $this -> fetch_row('model', "id = '".trim($id)."'");

        $brand_list = $this->model('brand')->fetch_all('brand');
        foreach ($brand_list as $k => $v) {
            if ($v['id'] == $data['brand_id']) {
                $data['brand_name'] = $v['name'];
            }
        }

        $data['url'] = G_DEMAIN.'/model/'.$data['id'].'.html';

        $images = array();
        if (preg_match_all('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', $data['intro'], $matches)) {
            foreach ($matches[2] as $s) {
                if (!stripos($s, 'ttp:/')) {
                    $images[] = G_DEMAIN . $s;
                    $fix = G_DEMAIN . $s;
                } else {
                    $images[] = $s;
                    $fix = $s;
                }
                $data['content'] = str_replace($s, $fix, $data['content']);
            }
        }

        return $data;
    }
}
