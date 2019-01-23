<?php
class article_class extends FARM_MODEL
{
    public function get_new_article($size = 20)
    {
        $where = array('status = 1', 'is_business = 0');
        
        $list =  $this->fetch_page('article', implode(' AND ', $where), 'id DESC', 1, $size);

        empty($list) && $list = array();

        foreach ($list as $k => $v) {
            if (empty($v['url'])) {
                $list[$k]['url'] = G_DEMAIN.'/article/'.$v['id'].'.html';
            }
        }

        // 每天发布5条
        $time = date("Y-m-d", time()).' 00:00:00';
        $count =  $this->fetch_page('article', '`status` = 1 AND `create_time` > '.strtotime($time), 'id DESC', 1, 5);
        if (count($count) < 5 && date('H') > 8) {
            $update_list =  $this->fetch_page('article', '`status` = 2', 'id DESC', 1, 5);
            empty($update_list) && $update_list = array();
            foreach($update_list as $update) {
                $this -> update('article', array(
                    'create_time' => time(),
                    'status' => 1
                ), 'id = '.intval($update['id']));
            }
        }

        return $list;
    }
    
    public function get_article_by_id($id)
    {
        $data = $this -> fetch_row('article', "id = '".intval($id)."'");

        $data['url'] = G_DEMAIN.'/article/'.$data['id'].'.html';
        
        return $data;
    }
    
    public function publish($id, $farm_id, $city_id, $title, $content, $keywords, $from = 'shuoly', $summary = '', $status = 1, $user_id = 0)
    {
        if (!$title || !$content) {
            return false;
        }

        $user_info = $this -> fetch_row('user', "id = '".intval($user_id)."'");
        if (time() - intval($user_info['reg_time']) < (3600*6)) {
            $status = 2;
        }

        $preview = '';
        
        if (empty($summary)) {
            $summary = mb_substr(trim(strip_tags($content)), 0, 100, 'utf-8');
            $summary = str_replace("&nbsp;", ' ', $summary);
        }
        
        if (empty($id)) {
            $article_id = $this -> insert('article', array(
                    'farm_id'    => $farm_id,
                    'city_id'     => $city_id,
                    'title'       => $title,
                    'content'     => $content,
                    'summary'     => $summary,
                    'keywords'    => $keywords,
                    'user_id'     => $user_id,
                    'url'         => G_DEMAIN.'/article/',
                    'preview'     => $preview,
                    'create_time' => time(),
                    'from'        => $from,
                    'read'        => 1,
                    'sort'        => 1,
                    'status'      => intval($status)
            ));
        } else {
           $this -> update('article', array(
                    'farm_id'    => $farm_id,
                    'city_id'     => $city_id,
                    'title'       => $title,
                    'content'     => $content,
                    'summary'     => $summary,
                    'keywords'    => $keywords,
                    'url'         => G_DEMAIN.'/article/',
                    'preview'     => $preview,
                    'from'        => $from,
                    'sort'        => 1,
                    'status'      => intval($status)
            ), 'id = '.intval($id));
           
           $article_id = intval($id);
        }
        
        $url = '';
        if ($article_id) {
            $url = G_DEMAIN.'/article/'.$article_id.'.html';
            $this -> update('article', array('url' => $url), 'id = '.$article_id);
        }
        
        return $url;
    }
    
    public function get_city_article($city_id, $order_by = 'sort asc,city_id desc', $page = 1, $per_page = 20)
    {
        $where = array();
        
        if ($city_id)
        {
            $where[] = 'city_id = ' . intval($city_id) . ' OR city_id = 0';
        }
        
        $data = $this->fetch_page('article', implode(' AND ', $where), $order_by, $page, $per_page);
        
        foreach($data as $k => $v)
        {
            $data[$k]['url'] = G_DEMAIN.'/article/'.$v['id'].'.html';
        }
        
        return $data;
    }
    
    public function get_related_article($farm_id = 0, $city_id = 0, $order_by = 'id desc', $page = 1, $per_page = 20)
    {
        $where = array('status = 1');
        
        if ($farm_id)
        {
            $where[] = 'farm_id = ' . intval($farm_id);
        }
        
        if ($city_id)
        {
            $where[] = 'city_id = ' . intval($city_id);
        }

        $data = $this->fetch_page('article', implode(' AND ', $where), $order_by, $page, $per_page);

        foreach($data as $k => $v)
        {
            $data[$k]['url'] = G_DEMAIN.'/article/'.$v['id'].'.html';
        }
        
        return $data;
    }

    /**
     * 更新浏览数
     * @param  int $id
     * @param  int $view
     * @param  int $create_time
     * @return boolean
     */
    public function update_view($id, $view, $create_time)
    {
        $view = intval($view) + 1;

        if ($view < 168 && (time() - $create_time > 3600)) {
            $view = rand(188, 3000);
        }

        return $this -> update('article', array('read' => $view), 'id = '.intval($id));
    }
}
