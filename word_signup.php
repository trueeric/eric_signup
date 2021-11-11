<?php
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
/*-----------引入檔案區--------------*/
require_once __DIR__ . '/header.php';

require_once XOOPS_ROOT_PATH . '/modules/tadtools/vendor/autoload.php';

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

// 標題文字樣式設定
$TitleStyle = ['color' => '000000', 'size' => 18, 'bold' => true];
// 內文文字設定
$fontStyle = ['color' => '000000', 'size' => 14, 'bold' => false];
// 置中段落樣式設定
$paraStyle = ['align' => 'center', 'valign' => 'center'];
// 靠左段落樣式設定
$left_paraStyle = ['align' => 'left', 'valign' => 'center'];
// 靠又段落樣式設定
$right_paraStyle = ['align' => 'right', 'valign' => 'center'];

//產生內容
$filename = "中文word";
// $filename  = iconv("UTF-8", "Big5", $filename);
$objWriter = IOFactory::createWriter($phpWord, 'Word2007');
header('Content-Type: application/vnd.ms-word');
header("Content-Disposition: attachment;filename={$filename}.docx");
header('Cache-Control: max-age=0');
$objWriter->save('php://output');
