<?php
/**
 * 图片model
 * */
class images_class extends FARM_MODEL
{
    /**
     * @desc   获取地点图片列表
     * @param  string  $where
     * @param  int     $page
     * @param  int     $per_page
     * @param  string  $order_by
     * @param  boolean $preview  用封面
     * @return array
     * */
    public function get_farm_images($farm_id, $page = 1, $per_page = 10, $order_by = 'id asc', $preview = true, $is_cate = false)
    {
        $data = $this->fetch_page('farm_images', 'farm_id = '.intval($farm_id).' AND `status` = 1', $order_by, $page, $per_page);
        
        $farm_info = $this -> fetch_row('farm', "id = '".intval($farm_id)."'", null, array('id', 'name', 'city_id', 'preview'));
        
        $city_info = $this -> fetch_row('city', "id = '".intval($farm_info['city_id'])."'", null, array('id', 'name'));
        
        //取出封面
        if ($preview && $farm_info['preview'])
        {
            array_unshift($data, array('id' => 0, 'preview' => 1, 'brief' => '标志图', 'url' => $farm_info['preview']));
        }
        
        foreach($data as $k => $v)
        {
            if ($is_cate) {
                $data[$k]['src'] = G_OSS.$data[$k]['url'];
            } else {
                $data[$k]['src'] = G_STATIC.$data[$k]['url'];
            }
            $data[$k]['alt'] = $data[$k]['brief'] ? $city_info['name'].$farm_info['name'].$data[$k]['brief'] : $city_info['name'].$farm_info['name'].'图片'.($k+1);
        }
        
        return $data;
    }
    
    /**
     * 批量获取评论图片
     * @param  int   $farm_id 
     * @param  array $ids     评论id
     * @return array
     * */
    public function get_images_by_commentids($farm_id, $ids)
    {
        $info = $this->fetch_all('farm_images', "farm_id = ".intval($farm_id)." AND `comment_id` IN(" . implode(',', $ids) . ")");
        
        $result = array();
        foreach($info as $key => $value)
        {
            foreach($ids as $id)
            {
                if ($value['comment_id'] == $id)
                {
                    $result[$id][] = $value;
                }
            }
        }
        
        return $result;
    }
}