<?php
use Xmf\Request;
use XoopsModules\Eric_signup\Eric_signup_actions;
use XoopsModules\Eric_signup\Eric_signup_data;
use XoopsModules\Tadtools\TadDataCenter;
use XoopsModules\Tadtools\Utility;
/*-----------引入檔案區--------------*/
require_once __DIR__ . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/tadtools/tcpdf/tcpdf.php';

if (!$_SESSION['can_add']) {
    redirect_header($_SERVER['PHP_SELF'], 3, "您沒有權限使用此功能");
}

$id     = Request::getInt('id');
$action = Eric_signup_actions::get($id);

$pdf = new TCPDF("P", "mm", "A4", true, "UTF-8", false);
$pdf->setPrintHeader(false); //不要頁首
$pdf->setPrintFooter(false); //不要頁尾
$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM); //設定自動分頁
$pdf->setFontSubsetting(true); //產生字型子集（有用到的字才放到文件中）
$pdf->SetFont('droidsansfallback', '', 11, '', true); //設定字型
$pdf->SetMargins(15, 15); //設定頁面邊界，
$pdf->AddPage(); //新增頁面，一定要有，否則內容出不來

//設定標題字型
$title = "{$action['title']}簽到表";

$pdf->SetFont('droidsansfallback', '', 24, '', true);
$pdf->MultiCell(190, 0, $title, 0, "C");
//活動日期
$pdf->SetFont('droidsansfallback', '', 16, '', true);
$pdf->Cell(40, 10, '活動日期：', 0, 0, 'R');
$pdf->Cell(40, 10, substr($action['action_date'], 5, 11), 0, 1);

$EricDataCenter = new TadDataCenter('eric_signup');
$EricDataCenter->set_col('pdf_setup_id', $id);
// 第2個參數0代表只抓該筆的完整資料
$pdf_setup_col = $EricDataCenter->getData('pdf_setup_id', 0);
$col_arr       = explode(',', $pdf_setup_col);

// 表格標題
$col_count = count($col_arr);
if (empty($col_count)) {
    $col_count = 1;
}
$h    = 15;
$w    = 120 / $col_count;
$maxh = 15;
$pdf->Cell(15, $h, '編號', 1, 0, 'c');
foreach ($col_arr as $col_name) {
    $pdf->Cell($w, $h, $col_name, 1, 0, 'c');
}

$pdf->Cell(55, $h, '簽名', 1, 1, 'c');

// 表格內容
$signup = Eric_signup_data::get_all($action['id'], null, true, true);
// Utility::dd($signup);
$i = 1;
foreach ($signup as $signup_data) {
    $pdf->MultiCell(15, $h, $i, 1, 'c', false, 0, '', '', true, 0, false, true, $maxh, 'M');
    foreach ($col_arr as $col_name) {
        $pdf->MultiCell($w, $h, implode('、', $signup_data['tdc'][$col_name]), 1, 'c', false, 0, '', '', true, 0, false, true, $maxh, 'M');
    }
    $pdf->MultiCell(55, $h, '', 1, 'c', false, 1, '', '', true, 0, false, true, $maxh, 'M');
    $i++;
}

// $pdf->writeHTML($html_content);

$pdf->Output("{$title}.pdf", "D");
