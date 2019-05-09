<?php
/**
 * 推荐位数据
 * */
class recommend_class extends MOX_MODEL
{
    /**
     * @desc   获取某个推荐位数据
     * @param  string $position 推荐位 如：1-1
     * @param  int    $size
     * @return array
     * */
    public function get_position($position, $size = 10)
    {
        $data = $this->fetch_page('recommend', 'position = "'.$position.'"', 'create_time desc', 1, $size);
        
        if (is_mobile())
        {
            empty($data) && $data = array();
            
            foreach($data as $k => $v)
            {
                $data[$k]['url'] = str_replace('//www.', '//m.', $v['url']);
            }
        }
        
        return $data;
    }
}