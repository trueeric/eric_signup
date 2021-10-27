<?php

use XoopsModules\Eric_signup\Eric_signup_actions;
use XoopsModules\Eric_signup\Eric_signup_data;
use XoopsModules\Tadtools\Utility;

// 活動報名焦點一覽
function action_signup($options)
{
    // 傳回id，確定過濾
    $block           = Eric_signup_actions::get($options[0], true);
    $block['signup'] = Eric_signup_data::get_all($options[0], null, true);

    // 樣板接收時一律用block
    return $block;
}

// 活動報名焦點一覽𦄒輯函式
function action_signup_edit($options)
{
    $actions = Eric_signup_actions::get_all(true);
    $opt     = '';
    foreach ($actions as $action) {
        $selected = Utility::chk($options[0], $action['id'], '', "selected");
        $opt .= " <option value={$action['id']} $selected >{$action['action_date']}{$action['title']}</option>";
    }

    $form = "
    <ol class='my-form'>
        <li class='my-row'>
            <lable class='my-label'>請選擇一個活動</lable>
            <div class='my-content'>
                <select name='options[0]' class='my-input'>
                    $opt
                </select>
            </div>
        </li>

    </ol>
    ";
    return $form;
}
