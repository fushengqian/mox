<?php
$config[] = array(
        'title'    => MOX_APP::lang()->_t('概述'),
        'cname'    => 'home',
        'url'      => '/backend/',
        'children' => array()
);

$config[] = array(
    'title'    => MOX_APP::lang()->_t('内容管理'),
    'cname'    => 'topic',
    'children' => array(
        array(
            'id' => 101,
            'title' => MOX_APP::lang()->_t('广告管理'),
            'url' => '/backend/ad/list/'
        ),
        array(
            'id' => 102,
            'title' => MOX_APP::lang()->_t('文章管理'),
            'url' => '/backend/article/list/'
        ),
        array(
            'id' => 103,
            'title' => MOX_APP::lang()->_t('短信管理'),
            'url' => '/backend/sms/list/'
        )
    )
);

$config[] = array(
    'title'    => MOX_APP::lang()->_t('社区管理'),
    'cname'    => 'activity',
    'children' => array(
        array(
            'id' => 201,
            'title' => MOX_APP::lang()->_t('社区设置'),
            'url' => '/backend/main/settings/category-sns'
        ),
        array(
            'id' => 202,
            'title' => MOX_APP::lang()->_t('话题管理'),
            'url' => '/backend/topic/index/'
        ),
        array(
            'id' => 203,
            'title' => MOX_APP::lang()->_t('动态管理'),
            'url' => '/backend/feed/index/'
        )
    )
);

$config[] = array(
    'title'    => MOX_APP::lang()->_t('商城管理'),
    'cname'    => 'cart',
    'children' => array(
        array(
            'id' => 'SETTINGS_MALL',
            'title' => MOX_APP::lang()->_t('商城设置'),
            'url' => '/backend/main/settings/category-mall'
        ),
        array(
            'id' => 302,
            'title' => MOX_APP::lang()->_t('分类管理'),
            'url' => '/backend/category/index/'
        ),
        array(
            'id' => 303,
            'title' => MOX_APP::lang()->_t('商品管理'),
            'url' => '/backend/goods/index/'
        ),
        array(
            'id' => 304,
            'title' => MOX_APP::lang()->_t('促销管理'),
            'url' => '/backend/promotion/index/'
        ),
        array(
            'id' => 303,
            'title' => MOX_APP::lang()->_t('分销管理'),
            'url' => '/backend/goods/index/'
        ),
    )
);

$config[] = array(
    'title'    => MOX_APP::lang()->_t('用户管理'),
    'cname'    => 'user',
    'children' => array(
        array(
            'id' => 401,
            'title' => MOX_APP::lang()->_t('用户列表'),
            'url' => '/backend/user/list/'
        ),
        array(
            'id' => 402,
            'title' => MOX_APP::lang()->_t('用户分组'),
            'url' => '/backend/user/group/'
        ),
        array(
            'id' => 403,
            'title' => MOX_APP::lang()->_t('用户组管理'),
            'url' => '/backend/user/group/'
        ),
    )
);

$config[] = array(
    'title'    => MOX_APP::lang()->_t('评论管理'),
    'cname'    => 'comment',
    'children' => array(
        array(
            'id' => 501,
            'title' => MOX_APP::lang()->_t('评论列表'),
            'url' => '/backend/comment/list/'
        )
    )
);

$config[] = array(
    'title'    => MOX_APP::lang()->_t('系统设置'),
    'cname'    => 'setting',
    'children' => array(
        array(
            'id' => 'SETTINGS_SITE',
            'title' => MOX_APP::lang()->_t('站点信息'),
            'url' => '/backend/main/settings/'
        ),
        array(
            'id' => 'SETTINGS_FUNCTIONS',
            'title' => MOX_APP::lang()->_t('站点功能'),
            'url' => '/backend/main/settings/category-functions'
        ),
        array(
            'id' => 'SETTINGS_NAV',
            'title' => MOX_APP::lang()->_t('导航菜单'),
            'url' => '/backend/main/settings/category-nav'
        ),
        array(
            'id' => 601,
            'title' => MOX_APP::lang()->_t('清除缓存'),
            'url' => '/backend/cache/clear/'
        ),
        array(
            'id' => 602,
            'title' => MOX_APP::lang()->_t('后台日志'),
            'url' => '/backend/logs/index/'
        ),
        array(
            'id' => 603,
            'title' => MOX_APP::lang()->_t('SEO设置'),
            'url' => '/backend/seo/list/'
        )
    )
);
