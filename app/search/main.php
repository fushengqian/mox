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

class main extends MOX_CONTROLLER
{
    /**
     * 搜索结果页
     * */
    public function index_action()
    {
        fix_client();

        $q = $qq = trim($_GET['keyword']) ? trim($_GET['keyword']) : '农家乐';

        $words = analysis_keyword($q);

        $where = '1 = 1';

        if ($words) {
            foreach ($words as $w) {
                $city_info = $this->model('system')->get_city_by_name($w);
                if ($city_info) {
                    $where = 'city_id = ' . intval($city_info['id']);
                    $qq = str_replace($city_info['name'], '', $q);
                    break;
                }
            }
        }

        $list = $this->model('mox')->search($qq, $where, 'mox', 1, 100);

        if ($list) {
            $city_list = $this->model('system')->get_city_list(false, false);

            foreach ($list as $k => $v) {
                foreach ($city_list as $city) {
                    if ($city['id'] == $v['city_id']) {
                        $list[$k]['city_info'] = $city;
                    }
                }

                foreach ($words as $k1 => $v2) {
                    $v['summary'] = summary($v['summary'], 152);
                    $list[$k]['summary'] = str_replace($v2, '<strong style="color:#f00;">' . $v2 . '</strong>', $v['summary']);
                    $list[$k]['address'] = str_replace($v2, '<strong style="color:#f00;">' . $v2 . '</strong>', $v['address']);

                    foreach ($v['tags'] as $k3 => $v3) {
                        $list[$k]['tags'][$k3]['name'] = str_replace($v2, '<strong style="color:#f00;">' . $v2 . '</strong>', $v3['name']);
                    }
                }
            }
        }

        TPL::assign('list', $list);

        //最新加入
        $new_list = $this->model('mox')->get_recommend_list($city_info['id'], 10);
        TPL::assign('new_list', $new_list);

        //热门推荐
        $recommend_list = $this->model('mox')->get_data_list('city_id = ' . intval($city_info['id']), 1, 10, 'avg_point desc');
        TPL::assign('recommend_list', $recommend_list);

        $seo = array('title' => array($q),
                     'keywords' => array($q),
                     'description' => array($q));

        if ($city_info) {
            TPL::assign('city_info', $city_info);
        }

        TPL::assign('words', $words);
        TPL::assign('seo', get_seo('search', $seo));
        TPL::assign('q', $q);

        TPL::output('search/list');
    }
}
