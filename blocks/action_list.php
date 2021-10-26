<?php

use XoopsModules\Eric_signup\Eric_signup_actions;
use XoopsModules\Tadtools\Utility;

// 可報名活動一覽
function action_list()
{
    $block = Eric_signup_actions::get_all(true);

    // 樣板接收時一律用block
    return $block;
}

// 可報名活動一覽𦄒輯函式
function action_list_edit($options)
{
    $form = "
    <ol class='my-form'>
        <li class='my-row'>
            <lable class='my-label'>顯示活動數</lable>
            <div class='my-content'>
                <input type='text' class='my-input' name='options[0]' value='{$options[0]}' size=6>
            </div>
        </li>

        <li class='my-row'>
        <lable class='my-label'>排序依據</lable>
            <div class='my-content'>
                <select name='options[1]' class='my-input'>
                    <option value=', `action_date` desc' " . Utility::chk($options[1], ', `action_date` desc', '1', "selected") . ">活動日期從遠到近</option>
                    <option value=', `action_date`' " . Utility::chk($options[1], ', `action_date`', '', "selected") . ">活動日期從近到遠</option>
                    <option value=', `end_date` desc ' " . Utility::chk($options[1], ', `end_date` desc ', '', "selected") . ">活動日期從近到遠</option>
                    <option value=', `end_date` '" . Utility::chk($options[1], `end_date`, '', "selected") . ">活動日期從近到遠</option>
                </select>
            </div>
        </li>
    </ol>
    ";
    return $form;
}
