<?php
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use Xmf\Request;
use XoopsModules\Eric_signup\Eric_signup_actions;
use XoopsModules\Eric_signup\Eric_signup_data;
use XoopsModules\Tadtools\TadDataCenter;
use XoopsModules\Tadtools\Utility;

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

// require_once XOOPS_ROOT_PATH . '/modules/tadtools/vendor/phpoffice/phpword/Classes/PHPWord.php'; //引入 PHPExcel 物件庫
// require_once XOOPS_ROOT_PATH . '/modules/tadtools/vendor/phpoffice/phpword/Classes/PHPWord/IOFactory.php'; //引入PHPExcel_IOFactory 物件庫

// if (!$_SESSION['can_add']) {
//     redirect_header($_SERVER['PHP_SELF'], 3, "您沒有權限使用此功能");
// }

$phpWord = new PhpWord();
$phpWord->setDefaultFontName('標楷體'); //設定預設字型
$phpWord->setDefaultFontSize(12); //設定預設字型大小
// $header = $section->addHeader(); //頁首
// $footer = $section->addFooter(); //頁尾
// $footer->addPreserveText('{PAGE} / {NUMPAGES}', $fontStyle, $paraStyle);

/// 標題文字樣式設定
$Title1Style = ['color' => '000000', 'size' => 18, 'bold' => true];
$Title2Style = ['color' => '000000', 'size' => 16, 'bold' => true];
// 內文文字設定
$fontStyle = ['color' => '000000', 'size' => 14, 'bold' => false];
// 置中段落樣式設定
$paraStyle = ['align' => 'center', 'valign' => 'center'];
// 靠左段落樣式設定
$left_paraStyle = ['align' => 'left', 'valign' => 'center'];
// 靠又段落樣式設定
$right_paraStyle = ['align' => 'right', 'valign' => 'center'];
// 表格樣式設定
$tableStyle = ['borderColor' => '000000', 'borderSize' => 6, 'cellMargin' => 80];
// 橫列樣式
$rowStyle = ['cantSplit' => true, 'tblHeader' => true];
// 儲存格標題文字樣式設定
$headStyle = ['bold' => true];
// 儲存格內文段落樣式設定
$cellStyle = ['valign' => 'center'];

//設定標題N樣式(第幾層標題,標題樣式,段落樣式)
$phpWord->addTitleStyle(1, $Title1Style, $paraStyle);
$phpWord->addTitleStyle(2, $Title2Style, $paraStyle);

//產生section後再套樣式
$section      = $phpWord->addSection();
$sectionStyle = $section->getStyle();
$sectionStyle->setMarginTop(Converter::cmToTwip(2.5));
$sectionStyle->setMarginLeft(Converter::cmToTwip(2.2));
$sectionStyle->setMarginRight(Converter::cmToTwip(2.2));

// $filename  = iconv("UTF-8", "Big5", $filename);
//產生內容
$title           = "{$action['title']}簽到表";
$action_date_txt = substr($action['action_date'], 5, 11);

//新增標題，套用第一層(標題1)樣式
$section->addTitle($title, 1);
$section->addTextBreak(1); //換行，可指定換幾行
$section->addText("活動日期：{$action_date_txt}", $fontStyle, $left_paraStyle);
$section->addTextBreak(1); //換行，可指定換幾行

$EricDataCenter = new TadDataCenter('eric_signup');
$EricDataCenter->set_col('pdf_setup_id', $id);
// 第2個參數0代表只抓該筆的完整資料
$pdf_setup_col = $EricDataCenter->getData('pdf_setup_id', 0);
$col_arr       = explode(',', $pdf_setup_col);

// 表格標題欄數
$col_count = count($col_arr);
if (empty($col_count)) {
    $col_count = 1;
}
$h    = 15;
$w    = 10.6 / $col_count; //21cm-1-5-2.2-2.2
$maxh = 15;

// 產生表格
$table = $section->addTable($tableStyle);
// 產生一列,可不帶參數
$table->addRow();

// 增加儲存格,建立標題列
$table->addCell(Converter::cmToTwip(1.4), $cellStyle)->addText('編號', $fontStyle, $paraStyle);
foreach ($col_arr as $col_name) {
    $table->addCell(Converter::cmToTwip($w), $cellStyle)->addText($col_name, $fontStyle, $paraStyle);
}
$table->addCell(Converter::cmToTwip(4.6), $cellStyle)->addText('簽名', $fontStyle, $paraStyle);

// 表格內容
$signup = Eric_signup_data::get_all($action['id'], null, true, true);

$i = 1;
foreach ($signup as $signup_data) {
    $table->addRow();
    $table->addCell(Converter::cmToTwip(1.4), $cellStyle)->addText($i, $fontStyle, $paraStyle);
    foreach ($col_arr as $col_name) {
        $table->addCell(Converter::cmToTwip($w), $cellStyle)->addText(implode('、', $signup_data['tdc'][$col_name]), $fontStyle, $paraStyle);

    }
    $table->addCell(Converter::cmToTwip(4.6), $cellStyle)->addText('', $fontStyle, $paraStyle);
    $i++;
}

$objWriter = IOFactory::createWriter($phpWord, 'ODText');
header('Content-Type: application/vnd.ms-word');
header("Content-Disposition: attachment;filename={$title}.odt");
header('Cache-Control: max-age=0');
$objWriter->save('php://output');
