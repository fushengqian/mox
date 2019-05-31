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

class info_class extends MOX_MODEL
{
    public function get_new_info($size = 20)
    {
        $where = array('status = 1');
        
        return $this->fetch_page('info', implode(' AND ', $where), 'id DESC', 1, $size);
    }
    
    /**
     * @desc   获取单条信息
     * @param  int     $id
     * @param  boolean $insert_img 把图片穿插到内容里
     * @return array
     * */
    public function get_info_by_id($id, $insert_img = false)
    {
        $data = $this -> fetch_row('info', "id = '".intval($id)."'");
        
        if (!empty($data['images']))
        {
            $data['images'] = unserialize($data['images']);
        }
        
        //{@img}
        if ($insert_img)
        {
            $content_arr = explode('{@img}', $data['content']);
            
            $content = '';
            
            if (!empty($content_arr))
            {
                foreach($content_arr as $k => $v)
                {
                    if (!empty($data['images'][$k]))
                    {
                        $content .= $v.'<p><img width="569" height="352" src="'.$data['images'][$k].'"/></p>';
                    }
                    else 
                    {
                        $content .= $v;
                    }
                }
            }
            
            if (!empty($content))
            {
                $data['content'] = $content;
            }
        }
        else 
        {
            $data['content'] = str_replace('{@img}', '', $data['content']);
        }
        
        return $data;
    }
    
    public function publish($id, $mox_id, $city_id, $title, $content, $keywords, $from = 'aiqoo', $summary = '', $status = 1)
    {
        if (!$title || !$content)
        {
            return false;
        }
        
        $preview = '';
        
        if (empty($summary))
        {
            $summary = mb_substr(trim(strip_tags($content)), 0, 100, 'utf-8');
            $summary = str_replace("&nbsp;", ' ', $summary);
        }
        
        if (empty($id))
        {
            $info_id = $this -> insert('info', array(
                    'mox_id'    => $mox_id,
                    'city_id'     => $city_id,
                    'title'       => $title,
                    'content'     => $content,
                    'summary'     => $summary,
                    'keywords'    => $keywords,
                    'url'         => G_DEMAIN.'/info/',
                    'preview'     => $preview,
                    'create_time' => time(),
                    'from'        => $from,
                    'read'        => rand(100, 1000),
                    'sort'        => 1,
                    'status'      => intval($status)
            ));
        }
        else
        {
           $this -> update('info', array(
                    'mox_id'    => $mox_id,
                    'city_id'     => $city_id,
                    'title'       => $title,
                    'content'     => $content,
                    'summary'     => $summary,
                    'keywords'    => $keywords,
                    'url'         => G_DEMAIN.'/info/',
                    'preview'     => $preview,
                    'from'        => $from,
                    'sort'        => 1,
                    'status'      => intval($status)
            ), 'id = '.intval($id));
           
           $info_id = intval($id);
        }
        
        $url = '';
        if ($info_id)
        {
            $url = G_DEMAIN.'/info/'.$info_id.'.html';
            
            if ($city_id)
            {
                $city_info = get_city_detail($city_id);
                $url = 'http://'.$city_info['uname'].G_BASE_DEMAIN.'/info/'.$info_id.'.html';
            }
            
            $this -> update('info', array('url' => $url), 'id = '.$info_id);
        }
        
        return $url;
    }
    
    public function get_city_info($city_id, $order_by = 'id desc', $page = 1, $per_page = 20)
    {
        $where = array();
        
        if ($city_id)
        {
            $where[] = 'city_id = ' . intval($city_id) . ' OR city_id = 0';
        }
        
        $data = $this->fetch_page('info', implode(' AND ', $where), $order_by, $page, $per_page);
        
        foreach($data as $k => $v)
        {
            $data[$k]['url'] = G_DEMAIN.'/info/'.encode($v['id'], 666).'.html';
            
            if ($v['city_id'])
            {
                $city_info = get_city_detail($v['city_id']);
                $data[$k]['url'] = 'http://'.$city_info['uname'].G_BASE_DEMAIN.'/info/'.encode($v['id'], 666).'.html';
            }
        }
        
        return $data;
    }
    
    public function get_related_info($mox_id = 0, $city_id = 0, $order_by = 'id desc', $page = 1, $per_page = 20)
    {
        $where = array();
        
        if ($mox_id)
        {
            $where[] = 'mox_id = ' . intval($mox_id);
        }
        
        if ($city_id)
        {
            $where[] = 'city_id = ' . intval($city_id);
        }
        
        $data = $this->fetch_page('info', implode(' AND ', $where), $order_by, $page, $per_page);
        
        return $data;
    }
}
