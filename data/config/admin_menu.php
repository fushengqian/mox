<?php
$config[] = array(
        'title'    => MOX_APP::lang()->_t('概述'),
        'cname'    => 'home',
        'url'      => '/backend/',
        'children' => array()
);

$config[] = array(
    'title'    => MOX_APP::lang()->_t('内容管理'),
    'cname'    => 'activity',
    'children' => array(
        array(
            'id' => 804,
            'title' => MOX_APP::lang()->_t('话题管理'),
            'url' => '/backend/topic/index/'
        ),
        array(
            'id' => 803,
            'title' => MOX_APP::lang()->_t('动态管理'),
            'url' => '/backend/feed/index/'
        ),
        array(
            'id' => 802,
            'title' => MOX_APP::lang()->_t('文章管理'),
            'url' => '/backend/article/list/'
        ),
        array(
            'id' => 801,
            'title' => MOX_APP::lang()->_t('短信管理'),
            'url' => '/backend/sms/list/'
        ),
    )
);

$config[] = array(
    'title'    => MOX_APP::lang()->_t('模型管理'),
    'cname'    => 'plus',
    'children' => array(
        array(
            'id' => 100,
            'title' => MOX_APP::lang()->_t('品牌管理'),
            'url' => '/backend/brand/index/'
        ),
        array(
            'id' => 101,
            'title' => MOX_APP::lang()->_t('模型管理'),
            'url' => '/backend/model/index/'
        ),
    )
);

$config[] = array(
    'title'    => MOX_APP::lang()->_t('用户管理'),
    'cname'    => 'user',
    'children' => array(
        array(
            'id' => 700,
            'title' => MOX_APP::lang()->_t('用户列表'),
            'url' => '/backend/user/list/'
        ),
        array(
            'id' => 701,
            'title' => MOX_APP::lang()->_t('用户行为'),
            'url' => '/backend/action/index/'
        ),
    )
);

$config[] = array(
    'title'    => MOX_APP::lang()->_t('运营管理'),
    'cname'    => 'edit',
    'children' => array(
        array(
            'id' => 902,
            'title' => MOX_APP::lang()->_t('日志监控'),
            'url' => '/backend/logs/index/'
        ),
    )
);

$config[] = array(
    'title'    => MOX_APP::lang()->_t('城市管理'),
    'cname'    => 'draft',
    'children' => array(
        array(
            'id' => 400,
            'title' => MOX_APP::lang()->_t('城市列表'),
            'url' => '/backend/city/index/'
        ),
        array(
            'id' => 401,
            'title' => MOX_APP::lang()->_t('地区列表'),
            'url' => '/backend/area/index/'
        ),
    )
);

$config[] = array(
    'title'    => MOX_APP::lang()->_t('系统管理'),
    'cname'    => 'inviteask',
    'children' => array(
        array(
            'id' => 600,
            'title' => MOX_APP::lang()->_t('清除搜索缓存'),
            'url' => '/backend/search/clear/'
        ),
    )
);