<?php

// 可報名活動一覽

use XoopsModules\Eric_signup\Eric_signup_actions;

function action_list()
{
    $block = Eric_signup_actions::get_all(true);
    // 樣板接收時一律用block
    return $block;
}

function action_list_edit()
{

}
