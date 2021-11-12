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

$signup = Eric_signup_data::get_all($action['id'], null, true, false);
// Utility::dd(count($signup));

$templateProcessor = new TemplateProcessor("signup.docx");
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

header('Content-Type: application/vnd.ms-word');
header("Content-Disposition: attachment;filename={$action['title']}.docx");
header('Cache-Control: max-age=0');
$templateProcessor->saveAs('php://output');
