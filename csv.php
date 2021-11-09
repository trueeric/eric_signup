<?php
// global $xoopsUser;

use Xmf\Request;
use XoopsModules\Eric_signup\Eric_signup_actions;
use XoopsModules\Eric_signup\Eric_signup_data;
use XoopsModules\Tadtools\Utility;

require_once __DIR__ . '/header.php';
// 限制下載權限
if (!$_SESSION['can_add']) {
    redirect_header($_SERVER['PHP_SELF'], 3, "您沒有權限使用此功能!");
}

$id     = Request::getInt('id');
$type   = Request::getString('type');
$action = Eric_signup_actions::get($id);

// 限制本人或管理者使用
if ($action['uid'] != $xoopsUser->uid() && $action['uid'] != $_SESSION['eric_signup_adm']) {
    redirect_header($_SERVER['PHP_SELF'], 3, "您沒有權限使用此功能!");
}

$csv = [];

$head = Eric_signup_data::get_head($action);

$csv[] = implode(',', $head);

if ($type == 'signup') {
    $signup = Eric_signup_data::get_all($action['id']);
    // Utility::dd($signup);
    foreach ($signup as $signup_data) {
        $item = [];
        foreach ($signup_data['tdc'] as $user_data) {
            $item[] = implode('|', $user_data);
        }

        // 為免後續匯入者困擾，csv只產生tdc的欄位就好
        // if ($signup_data['accept'] === '1') {
        //     $item[] = '錄取';
        // } elseif ($signup_data['accept'] === '0') {
        //     $item[] = '未錄取';
        // } else {
        //     $item[] = '尚未設定';
        // }
        // $item[] = $signup_data['signup_date'];
        // $item[] = $signup_data['signup_tag'];
        $csv[] = implode(',', $item);
    }

}

$content = implode("\n", $csv);
$content = mb_convert_encoding($content, 'Big5');

// 匯出csv
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename={$action['title']}報名名單.csv");

echo $content;

exit;
