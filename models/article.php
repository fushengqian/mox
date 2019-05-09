<?php
class article_class extends MOX_MODEL
{
    /**
     * 获取banner
     * @param int $size
     * @return array
     */
    public function get_banner($size = 5)
    {
        $where = array('status = 1', 'is_banner = 1');
        $list = $this->fetch_page('article', implode(' AND ', $where), 'update_time DESC', 1, $size);

        foreach ($list as $key => $value) {
            if (preg_match_all('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', $value['content'], $matches)) {
                foreach ($matches[2] as $s) {
                    if (!stripos($s, 'ttp:/')) {
                        $list[$key]['img'] = G_DEMAIN . $s;
                    } else {
                        $list[$key]['img'] = $s;
                    }
                    break;
                }
            }
        }

        $arr = array();
        foreach ($list as $k => $v) {
            $arr[] = array(
                    'name' => $v['title'],
                    'detail' => $v['summary'],
                    'img' =>  $v['img'] ?  $v['img'] :'http://www.moxquan.com/static/upload/01/14-1.png',
                    'href' => G_DEMAIN.'/article/'.$v['id'].'.html',
                    'pubDate' => date("Y-m-d H:i:s", $v['create_time']),
                    'type' => 6,
                    'id' => $v['id']);
        }

        return $arr;
    }

    public function get_article_list($cate = 'focus', $page = 1, $size = 20, $article_id = 0)
    {
        $where = array('status = 1', 'is_banner = 0');

        if ($cate) {
            $where[] = 'cate = "'.trim($cate).'"';
        }

        if ($article_id) {
            $where[] = 'id <> "'.trim($article_id).'"';
        }
        
        $list =  $this->fetch_page('article', implode(' AND ', $where), 'id DESC', $page, $size);

        empty($list) && $list = array();

        foreach ($list as $k => $v) {
            if (empty($v['url'])) {
                $list[$k]['url'] = G_DEMAIN.'/article/'.$v['id'].'.html';
            }
            $list[$k]['comment_num'] = rand(10, 30);
        }

        return $list;
    }
    
    public function get_article_by_id($id)
    {
        $data = $this -> fetch_row('article', "id = '".intval($id)."'");

        $data['url'] = G_DEMAIN.'/article/'.$data['id'].'.html';

        $images = array();
        if (preg_match_all('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', $data['content'], $matches)) {
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

        $image_arr = array();
        foreach ($images as $img) {
            $image_arr[] =  array('h' => 200,
                                  'w' => 200,
                                  'href' => $img,
                                  'name' => '',
                                  'thumb' => $img,
                                  'type' => 'jpg');

        }

        $data['images'] = $image_arr;
        
        return $data;
    }
    
    public function publish($id, $cate, $title, $content, $keywords, $from = 'shuoly', $summary = '', $status = 1, $user_id = 0)
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
                    'title'       => $title,
                    'cate'        => $cate,
                    'content'     => $content,
                    'summary'     => $summary,
                    'keywords'    => $keywords,
                    'user_id'     => $user_id,
                    'preview'     => $preview,
                    'create_time' => time(),
                    'update_time' => time(),
                    'from'        => $from,
                    'read'        => 1,
                    'sort'        => 1,
                    'status'      => intval($status)
            ));
        } else {
           $this -> update('article', array(
                    'title'       => $title,
                    'cate'        => $cate,
                    'content'     => $content,
                    'summary'     => $summary,
                    'keywords'    => $keywords,
                    'preview'     => $preview,
                    'from'        => $from,
                    'update_time' => time(),
                    'sort'        => 1,
                    'status'      => intval($status)
            ), 'id = '.intval($id));
           
           $article_id = intval($id);
        }

        return $article_id;
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
