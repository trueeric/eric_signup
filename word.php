<?php
use Xmf\Request;
use XoopsModules\Eric_signup\Eric_signup_actions;
use XoopsModules\Eric_signup\Eric_signup_data;
use \PhpOffice\PhpWord\TemplateProcessor;

/*-----------引入檔案區--------------*/
require_once __DIR__ . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/tadtools/vendor/autoload.php';

if (!$_SESSION['can_add']) {
    redirect_header($_SERVER['PHP_SELF'], 3, "您沒有權限使用此功能");
}
$id   = Request::getInt('id');
$type = Request::getString('type');

//取得報名活動資料
$action = Eric_signup_actions::get($id);

$signup = Eric_signup_data::get_all($action['id']);
// Utility::dd(count($signup));

$templateProcessor = new TemplateProcessor("signup_1.docx");
$templateProcessor->setValue('title', $action['title']);
$templateProcessor->setValue('detail', str_replace("\n", "</w:t><w:br/><w:t>", strip_tags($action['detail'])));
$templateProcessor->setValue('action_date', $action['action_date']);
$templateProcessor->setValue('end_date', $action['end_date']);
$templateProcessor->setValue('number', $action['number']);
$templateProcessor->setValue('candidate', $action['candidate']);
$templateProcessor->setValue('signup', count($signup));
$templateProcessor->setValue('url', XOOPS_URL . "/modules/eric_signup/index.php?op=eric_signup_data_create&amp;action_id={$action['id']}");

// 會直接存，然後畫面變成空白
// $templateProcessor->saveAs("{$action['title']}.docx");

// 已報名資料表格內容

// 要複製幾筆資枓
$templateProcessor->cloneRow('id', count($signup));
// Utility::dd($signup);
$i = 1;
foreach ($signup as $id => $signup_data) {
    $item = [];
    foreach ($signup_data['tdc'] as $head => $user_data) {
        $item[] = $head . '：' . implode('、', $user_data);
    }
    // Utility::dd($item);
    // word下方已報名資料欄位$data
    $data = implode('<w:br/>', $item);

    if ($signup_data['accept'] === '1') {
        $accept = '錄取';
    } elseif ($signup_data['accept'] === '0') {
        $accept = '未錄取';
    } else {
        $accept = '尚未設定';
    }
    // Utility::dd($accept);

    $templateProcessor->setValue("id#{$i}", $id);
    $templateProcessor->setValue("accept#{$i}", $accept);
    $templateProcessor->setValue("data#{$i}", $data);

    $i++;
}

header('Content-Type: application/vnd.ms-word');
header("Content-Disposition: attachment;filename={$action['title']}報名名單.docx");
header('Cache-Control: max-age=0');
$templateProcessor->saveAs('php://output');
