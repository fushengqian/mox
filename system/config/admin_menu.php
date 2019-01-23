<?php
$config[] = array(
        'title'    => FARM_APP::lang()->_t('概述'),
        'cname'    => 'home',
        'url'      => '/admin/',
        'children' => array()
);

$config[] = array(
    'title'    => FARM_APP::lang()->_t('社区管理'),
    'cname'    => 'activity',
    'children' => array(
        array(
            'id' => 804,
            'title' => FARM_APP::lang()->_t('话题管理'),
            'url' => '/admin/topic/index/'
        ),
        array(
            'id' => 803,
            'title' => FARM_APP::lang()->_t('动态管理'),
            'url' => '/admin/feed/index/'
        ),
        array(
            'id' => 802,
            'title' => FARM_APP::lang()->_t('文章管理'),
            'url' => '/admin/article/list/'
        ),
        array(
            'id' => 801,
            'title' => FARM_APP::lang()->_t('短信管理'),
            'url' => '/admin/sms/list/'
        ),
    )
);

$config[] = array(
    'title'    => FARM_APP::lang()->_t('模型管理'),
    'cname'    => 'plus',
    'children' => array(
        array(
            'id' => 100,
            'title' => FARM_APP::lang()->_t('品牌管理'),
            'url' => '/admin/brand/index/'
        ),
        array(
            'id' => 101,
            'title' => FARM_APP::lang()->_t('模型管理'),
            'url' => '/admin/model/index/'
        ),
    )
);

$config[] = array(
    'title'    => FARM_APP::lang()->_t('运营管理'),
    'cname'    => 'edit',
    'children' => array(
        array(
            'id' => 902,
            'title' => FARM_APP::lang()->_t('日志监控'),
            'url' => '/admin/logs/index/'
        ),
    )
);

$config[] = array(
    'title'    => FARM_APP::lang()->_t('用户管理'),
    'cname'    => 'user',
    'children' => array(
        array(
            'id' => 700,
            'title' => FARM_APP::lang()->_t('用户列表'),
            'url' => '/admin/user/list/'
        ),
    )
);

$config[] = array(
    'title'    => FARM_APP::lang()->_t('城市管理'),
    'cname'    => 'draft',
    'children' => array(
        array(
            'id' => 400,
            'title' => FARM_APP::lang()->_t('城市列表'),
            'url' => '/admin/city/index/'
        ),
        array(
            'id' => 401,
            'title' => FARM_APP::lang()->_t('地区列表'),
            'url' => '/admin/area/index/'
        ),
    )
);

$config[] = array(
    'title'    => FARM_APP::lang()->_t('系统管理'),
    'cname'    => 'inviteask',
    'children' => array(
        array(
            'id' => 600,
            'title' => FARM_APP::lang()->_t('清除搜索缓存'),
            'url' => '/admin/search/clear/'
        ),
    )
);
