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

class points_class extends MOX_MODEL
{
    /**
     * 积分获取标准
     */
    public function getPoints($type = '') {
        $arr = array(
            'like' => array(5, '点赞'),
            'comment' => array(10, '评论'),
            'create_feed' => array(100, '发布动态'),
            'create_topic' => array(100, '发布话题'),
            'login' => array(100, '每天登录'),
            'register' => array(500, '注册Mox'),
            'visit_home' => array(5, '访问社区主页'),
            'upload_avatar' => array(200, '上传头像'),
            'update_user_info' => array(100, '完善个人信息'),
            'visit_feed_detail' => array(10, '访问动态详情'),
            'visit_user_index' => array(10, '访问他人主页'));

        if ($type) {
            return $arr[$type] ? $arr[$type] : array();
        }

        return $arr;
    }

    /**
     * 获取列表
     */
    public function get_data_list($where, $page = 1, $per_page = 10, $order_by = 'create_time desc')
    {
        if (is_array($where)) {
            $where = implode(' AND ', $where);
        }

        $list = $this->fetch_page('point', $where, $order_by, $page, $per_page);

        $user_ids = array(0);
        foreach($list as $k => $v) {
            $user_ids[] = $v['user_id'];
        }

        $user_arr = MOX_APP::model('user')->get_user_by_ids($user_ids);
        foreach($list as $key => $value) {
            $list[$key]['user_info'] = $user_arr[$value['user_id']];
        }

        return $list;
    }

    /**
     * 根据积分获取用户荣誉称号
     * @param int $points
     * @param string
     */
    public function getHonor($point = 0) {
         if ($point < 1000) {
             return '铁匠';
         } else if ($point < 2000) {
             return '铜匠';
         } else if ($point < 5000) {
             return '银匠';
         } else if ($point < 10000) {
             return '金匠';
         } else if ($point < 20000) {
             return '匠师';
         } else if ($point < 50000) {
             return '大师';
         } else {
             return '殿堂';
         }
    }

    /**
     * 发放积分
     * @param int $user_id
     * @param string $type
     * @return boolean
     */
    public function send($user_id, $type) {
        $point = $this -> getPoints($type);
        if (!$point || !$user_id) {
            return false;
        }

        self::insert('point',
            array(
            'type' => $type,
            'user_id' => $user_id,
            'point' => $point[0],
            'create_time' => time(),
            'marks' => $point[1],
            'status' => 1));

        // 更新用户积分
        self::exec('update `mox_user` set point = point + '.intval($point[0]). " where `id` = '".$user_id."'");

        return true;
    }
}
