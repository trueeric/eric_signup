<?php

use XoopsModules\Eric_signup\Eric_signup_actions;

// 可報名活動一覽
function action_list()
{
    $block = Eric_signup_actions::get_all(true);

    // 樣板接收時一律用block
    return $block;
}

// 可報名活動一覽𦄒輯函式
function action_list_edit()
{

}
