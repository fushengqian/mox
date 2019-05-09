<?php
class feed_class extends MOX_MODEL
{

    /**
     * 获取详情
     */
    public function get_detail($feed_id)
    {
        $feed = $this->model('feed')->fetch_row('feed', 'id = "'.$feed_id.'"');

        $feed['user_info'] = MOX_APP::model('user')->get_user_info_by_id($feed['user_id']);
        $feed['pics'] = json_decode($feed['pics'], true);
        $feed['like_num'] = MOX_APP::model('like')->count('like', 'target_id = "'.$feed['id'].'" and `type`="feed"');
        $feed['comment_num'] = MOX_APP::model('comment')->count('comment', 'target_id = "'.$feed['id'].'" and `type`="feed"');

        $comment_list = MOX_APP::model('comment')->get_comment_by_targetids(array($feed['id']), 'feed');
        $feed['comment_list'] = $comment_list[$feed['id']];

        // 给移动端用的图片格式
        $images = array();
        foreach ($feed['pics'] as $img) {
            $images[] =  array('h' => 100,
                               'w' => 200,
                               'href' => $img,
                               'name' => md5($img),
                               'thumb' => $img,
                               'type' => 1);

        }
        $feed['images'] = $images;

        return $feed;
    }

    /**
     * 获取列表
     */
    public function get_data_list($where, $page = 1, $per_page = 10, $order_by = 'update_time desc')
    {
        if (is_array($where)) {
            $where = implode(' AND ', $where);
        }
        
        $list = $this->fetch_page('feed', $where, $order_by, $page, $per_page);

        $user_ids = array(0);
        foreach($list as $k => $v) {
            $image_arr = json_decode($v['pics'], true);
            $list[$k]['pics'] = $image_arr;

            // 给移动端用的图片格式
            $images = array();
            foreach ($image_arr as $img) {
                $images[] =  array('h' => 200,
                                   'w' => 200,
                                   'href' => $img,
                                   'name' => md5($img),
                                   'thumb' => $img,
                                   'type' => 1);
            }
            $list[$k]['images'] = $images;

            $user_ids[] = $v['user_id'];
            $feed_ids[] = $v['id'];

            // 列表页取缩略图
            foreach ($list[$k]['pics'] as $pk => $pic) {
                $path_s = APP_PATH.str_replace(G_DEMAIN.'/', '', $pic);
                $path_info = path_info($path_s);
                $preview = $path_info['dirname'].md5($pic).'.'.$path_info['extension'];
                if (file_exists($preview)) {
                    $list[$k]['pics'][$pk] = str_replace($path_info['filename'], md5($pic), $pic);
                }
            }
        }

        $comment_list = MOX_APP::model('comment')->get_comment_by_targetids($feed_ids, 'feed');

        $user_arr = MOX_APP::model('user')->get_user_by_ids($user_ids);
        foreach($list as $key => $value) {
            $list[$key]['user_info'] = $user_arr[$value['user_id']];
            $list[$key]['like_num'] = MOX_APP::model('like')->count('like', 'target_id = "'.$value['id'].'" and `type`="feed"');
            $list[$key]['comment_num'] = MOX_APP::model('comment')->count('comment', 'target_id = "'.$value['id'].'" and `type`="feed"');
            $list[$key]['comment_list'] = $comment_list[$value['id']];
        }

        return $list;
    }

    /**
     * 发布动态
     * @param string $content
     * @param array  $pics
     * @param int    $user_id
     * @param int    $topic_id
     * @return boolean
     */
    public function create($content, $pics, $user_id, $topic_id = '')
    {
        // 提取图片
        $pic_pattern = '/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i';
        preg_match_all($pic_pattern, $content, $pic_arrs);
        $pic_arr = !empty($pic_arrs[0]) ? $pic_arrs[0] : array();
        $content = preg_replace($pic_pattern, '$img999$', $content);

        // 提取话题
        $topic_pattern = "/#(.*?)#/sim";
        $topic_title = '';
        preg_match_all($topic_pattern, $content, $topic_arrs);
        if (!empty($topic_arrs[1][0])) {
            $topic_title = strip_tags($topic_arrs[1][0]);
        }

        // 去除html标签
        $content = strip_tags($content);
        $content = str_replace('#'.$topic_title.'#', '', $content);

        // 替换回图片
        $arr = explode('$img999$', $content);
        $new_content = '';
        if (count($arr) > 1) {
            foreach ($arr as $k => $v) {
                if ($k > 0) {
                    $new_content .= $v.$pic_arr[$k-1];
                } else {
                    $new_content .= $v;
                }
            }
            $content = $new_content;
        }

        // 创建话题
        if (!empty($topic_title) && empty($topic_id)) {
            // 不重复创建
            $topic = $this -> fetch_row('topic', 'title = "'.$topic_title.'"');
            if (empty($topic)) {
                $topic_id = setId();
                $preview = !empty($pics[0]) ? str_replace(G_DEMAIN, '', $pics[0]) : '/static/avatar/default.jpg';
                $this->insert('topic', array('id'    => $topic_id,
                                                    'title' => $topic_title,
                                                    'words' => $topic_title,
                                                    'user_id' => $user_id,
                                                    'preview' => $preview,
                                                    'create_time' => time(),
                                                    'update_time' => time(),
                                                    'status' => 1));

                MOX_APP::model('points')->send($user_id, 'create_topic');
            } else {
                $topic_id = $topic['id'];
            }
        }

        $feed_id = $this -> insert('feed', array(
            'id'          => setId(),
            'topic_id'    => $topic_id,
            'content'     => $content,
            'user_id'     => $user_id,
            'pics'        => $pics ? json_encode($pics) : '',
            'is_home'     => 0,
            'create_time' => time(),
            'update_time' => time(),
            'status'      => 1
        ));

        MOX_APP::model('points')->send($user_id, 'create_feed');

        return $feed_id;
    }
}