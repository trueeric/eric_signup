<?php
use Xmf\Request;
use XoopsModules\Eric_signup\Eric_signup_actions;
use XoopsModules\Tadtools\Utility;

/*-----------引入檔案區--------------*/
require_once __DIR__ . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/tadtools/tcpdf/tcpdf.php';
$pdf = new TCPDF("P", "mm", "A4", true, 'UTF-8', false);
$pdf->setPrintHeader(false); //不要頁首
$pdf->setPrintFooter(false); //不要頁尾
$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM); //設定自動分頁
$pdf->setFontSubsetting(true); //產生字型子集（有用到的字才放到文件中）
$pdf->SetFont('droidsansfallback', '', 12, '', true); //設定字型
$pdf->SetMargins(15, 15); //設定頁面邊界，
$pdf->AddPage(); //新增頁面，一定要有，否則內容出不來

if (!$_SESSION['can_add']) {
    redirect_header($_SERVER['PHP_SELF'], 3, "您沒有權限使用此功能");
}

$id   = Request::getInt('id');
$type = Request::getString('type');

$action = Eric_signup_actions::get($id);

if ($action['uid'] != $xoopsUser->uid() && $action['uid'] != $_SESSION['eric_signup_adm']) {
    redirect_header($_SERVER['PHP_SELF'], 3, "您沒有權限使用此功能");
}
// Utility::dd($action['title']);
$file_name_conv = iconv('UTF-8', 'Big5', $action['title']);
$pdf->Output("{$file_name_conv}.pdf", 'D');
// $pdf->Output("王12小.pdf", 'D');
