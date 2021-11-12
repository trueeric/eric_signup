<?php
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use Xmf\Request;
use XoopsModules\Eric_signup\Eric_signup_actions;
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
$textrun = $section->addTextRun($paraStyle);

//產生內容
// $filename = "中文word";
// $filename  = iconv("UTF-8", "Big5", $filename);
$objWriter = IOFactory::createWriter($phpWord, 'Word2007');
header('Content-Type: application/vnd.ms-word');
header("Content-Disposition: attachment;filename={$title}.docx");
header('Cache-Control: max-age=0');
$objWriter->save('php://output');
