<?php

if (is_mobile())
{
    $config = array(
            'first_link' => '首页',
            'next_link' => '下一页',
            'prev_link' => '上一页',
            'last_link' => '尾页',
            'uri_segment' => 1,
            'suffix' => '.html',
            'full_tag_open' => '<div class="page-info"><ul class="pagination pull-right">',
            'full_tag_close' => '</ul></div>',
            'first_tag_open' => '<li>',
            'first_tag_close' => '</li>',
            'last_tag_open' => '<li>',
            'last_tag_close' => '</li>',
            'first_url' => '', // Alternative URL for the First Page.
            'cur_tag_open' => '<li class="active"><a href="javascript:;">',
            'cur_tag_close' => '</a></li>',
            'next_tag_open' => '<li>',
            'next_tag_close' => '</li>',
            'prev_tag_open' => '<li>',
            'prev_tag_close' => '</li>',
            'num_tag_open' => '<li>',
            'num_tag_close' => '</li>',
            'display_pages' => TRUE,
            'anchor_class' => '',
            'query_string_segment' => 'p',
            'num_links' => 1
    );
}
else
{
    //后台的分页和前台不同
    if (stripos($_SERVER['REQUEST_URI'], 'admin'))
    {
        $suffix = '/';
        $query_string_segment = 'aid';
    }
    else
    {
        $suffix = '.html';
        $query_string_segment = 'p';
    }
    
    $config = array(
        'first_link' => '首页',
        'next_link' => '下一页',
        'prev_link' => '上一页',
        'last_link' => '尾页',
        'uri_segment' => 6,
        'suffix' => $suffix,
        'full_tag_open' => '<div class="page-info"><ul class="pagination pull-right">',
        'full_tag_close' => '</ul></div>',
        'first_tag_open' => '<li>',
        'first_tag_close' => '</li>',
        'last_tag_open' => '<li>',
        'last_tag_close' => '</li>',
        'first_url' => '', // Alternative URL for the First Page.
        'cur_tag_open' => '<li class="active"><a href="javascript:;">',
        'cur_tag_close' => '</a></li>',
        'next_tag_open' => '<li>',
        'next_tag_close' => '</li>',
        'prev_tag_open' => '<li>',
        'prev_tag_close' => '</li>',
        'num_tag_open' => '<li>',
        'num_tag_close' => '</li>',
        'display_pages' => TRUE,
        'anchor_class' => '',
        'query_string_segment' => $query_string_segment,
        'num_links' => 6
    );
}