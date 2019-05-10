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

class search_class extends MOX_MODEL
{
    public $max_results = 200;
    
    public $found_rows = 0;
    
    public $col = 'id,name,city_id,summary,contact,tel,mobile,lng,lat,cate,tags,address,preview,view,avg_price';

    /**
     * @desc  查找问答
     * @param string  $q     关键字
     * @param string  $where 查询条件
     * @param string  $table 数据表
     * @param int     $page  当前页
     * @param int     $size  每页数
     * @param boolean $full  是否完全匹配
     * @return array
     * */
    public function get_wenda($q, $where, $table = 'wenda', $page = 1, $limit = 10, $full = false, $col = '', $max_results = 0)
    {
        $search_hash = $this->get_search_hash($table, 'match_row', $q, $where, $col);
        $result = $this->fetch_cache($search_hash);

        if (!$result)
        {
            $sql = $this->bulid_query($table, 'match_row', $q, $where, $col, $full);

            if ($max_results > 0)
            {
                $this -> max_results = $max_results;
            }

            $result = $this->query_all($sql, $this -> max_results);

            if (!$result)
            {
                return false;
            }

            $this->save_cache($search_hash, $result);
        }

        if (!$page)
        {
            $slice_offset = 0;
        }
        else
        {
            $slice_offset = (($page - 1) * $limit);
        }
        $this -> found_rows = count($result);

        $list = array_slice($result, $slice_offset, $limit);
        foreach($list as $key => &$val)
        {
            $list[$key]['content'] = $val['content'] ? $val['content'] : $val['title'];
            $list[$key]['user_info'] =  MOX_APP::model('user') -> fetch_row('user', 'id ='.intval($val['id']));
            $val['reply'] = MOX_APP::model('wenda')->get_reply($val['id']);
            foreach($val['reply'] as &$reply)
            {
                $reply['user_info'] = MOX_APP::model('user') -> fetch_row('user', 'id ='.intval($reply['user_id']));
            }
        }

        return $list;
    }

    /**
     * @param string  $q     关键字
     * @param string  $where 查询条件
     * @param string  $table 数据表
     * @param int     $page  当前页
     * @param int     $size  每页数
     * @param boolean $full  是否完全匹配
     * */
    public function get($q, $where, $table = 'mox', $page = 1, $limit = 10, $full = false, $col = '', $max_results = 0)
    {
        if ($table == 'goods' && empty($col))
        {
            $col = 'id,name,city_id,summary,cate,tags,preview';
        }
        
        $search_hash = $this->get_search_hash($table, 'match_row', $q, $where, $col);
        $result = $this->fetch_cache($search_hash);
        
        if (!$result)
        {
            $sql = $this->bulid_query($table, 'match_row', $q, $where, $col, $full);
            
            if ($max_results > 0)
            {
                $this -> max_results = $max_results;
            }
            
            $result = $this->query_all($sql, $this -> max_results);
            
            if (!$result)
            {
                return false;
            }
            
            $this->save_cache($search_hash, $result);
        }
        
        if (!$page)
        {
            $slice_offset = 0;
        }
        else
        {
            $slice_offset = (($page - 1) * $limit);
        }
        
        $this -> found_rows = count($result);
        
        $tag_list = $this->fetch_all('tag');
        
        foreach($result as $k => $v)
        {
            $result[$k]['id'] = encode($v['id']);
            $result[$k]['preview'] = G_STATIC.$result[$k]['preview'];
            $result[$k]['tags'] = explode(" ", str_replace(",", " ", $v['tags']));
            if ($table == 'goods')
            {
                $result[$k]['url'] = get_goods_url($v['city_id'], $v['id']);
            }
            else
            {
                $result[$k]['url'] = get_mox_url($v['city_id'], $v['id']);
            }
            
            $tag_arr = array();
            if ($result[$k]['tags'])
            {
                foreach($result[$k]['tags'] as $t)
                {
                    foreach($tag_list as $tag)
                    {
                        if ($tag['name'] == $t)
                        {
                            $tag_arr[] = $tag;
                        }
                    }
                }
                
                $result[$k]['tags'] = $tag_arr;
            }
        }
        
        return array_slice($result, $slice_offset, $limit);
    }
    
    public function found_rows()
    {
        return $this -> found_rows;
    }
    
    public function get_search_hash($table, $column, $q, $where = null, $col)
    {
        return md5($this->bulid_query($table, $column, $q, $where, $col));
    }
    
    public function fetch_cache($search_hash)
    {
        $search_cache = $this->fetch_row('search_cache', "`hash` = '" . $this->quote($search_hash) . "'");
        
        $data = unserialize(gzuncompress(base64_decode($search_cache['data'])));
        
        return $data;
    }
    
    public function save_cache($search_hash, $data)
    {
        if (!$data)
        {
            return false;
        }
        
        if ($this->fetch_cache($search_hash))
        {
            $this->remove_cache($search_hash);
        }
        
        return $this->insert('search_cache', array(
            'hash' => $search_hash,
            'data' => base64_encode(gzcompress(serialize($data))),
            'time' => time()
        ));
    }
    
    public function remove_cache($search_hash)
    {
        return $this->delete('search_cache', "`hash` = '" . $this->quote($search_hash) . "'");
    }
    
    public function clean_cache()
    {
        return $this->delete('search_cache', 'time < ' . (time() - 900));
    }
    
    public function bulid_query($table, $column, $q, $where = null, $col = '', $full = false)
    {
        if (is_array($q))
        {
            $q = implode(' ', $q);
        }
        
        if ($analysis_keyword = analysis_keyword($q))
        {
            $keyword = implode(' ', $analysis_keyword);
        }
        else
        {
            $keyword = $q;
        }
        
        if ($where)
        {
            $where = ' AND (' . $where . ')';
        }
        
        $code = $this -> quote(en_word($keyword));
        
        if ($full)
        {
            $arr = explode(' ', $code);
            foreach($arr as $k => $v)
            {
                if ($k == 1)
                {
                    $arr[$k] = '+'.$v;
                }
            }
            $code = implode(' ', $arr);
        }
        if ($table == 'mox')
        {
            $abc = '`cate` < 13 AND';
        }
        else
        {
            $abc = '';
        }
        $sql = trim("SELECT ".($col ? $col : $this -> col)." FROM " . $this->get_table($table) . " WHERE ".$abc." brief like '%".$q."%'" . $where . ' order by avg_point desc');
        
        return $sql;
    }
}