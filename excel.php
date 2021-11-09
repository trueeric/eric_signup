<?php

use Xmf\Request;
use XoopsModules\Eric_signup\Eric_signup_actions;
use XoopsModules\Eric_signup\Eric_signup_data;
use XoopsModules\Tadtools\Utility;

/*-----------引入檔案區--------------*/
require_once __DIR__ . '/header.php';

// 限制下載權限
if (!$_SESSION['can_add']) {
    redirect_header($_SERVER['PHP_SELF'], 3, "您沒有權限使用此功能!");
}

$id   = Request::getInt('id');
$type = Request::getString('type');

$action = Eric_signup_actions::get($id);
// Utility::dd($type);

// 限制本人或管理者使用
if ($action['uid'] != $xoopsUser->uid() && $action['uid'] != $_SESSION['eric_signup_adm']) {
    redirect_header($_SERVER['PHP_SELF'], 3, "您沒有權限使用此功能!");
}

require_once XOOPS_ROOT_PATH . '/modules/tadtools/vendor/phpoffice/phpexcel/Classes/PHPExcel.php'; //引入 PHPExcel 物件庫
require_once XOOPS_ROOT_PATH . '/modules/tadtools/vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php'; //引入PHPExcel_IOFactory 物件庫
$objPHPExcel = new PHPExcel(); //實體化Excel

//----------內容-----------//

//設定預設工作表中一個儲存格的外觀
$head_style = [
    'font'      => [
        'bold'  => true,
        'color' => ['rgb' => '000000'],
        // 'size' => 12,
        'name'  => '新細明體',
    ],
    'alignment' => [
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
    ],
    'fill'      => [
        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => ['rgb' => 'cfcfcf'],
    ],
    'borders'   => [
        'allborders' => [
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => ['rgb' => '000000'],
        ],
    ],
];

//excel 內容樣式
$content_style = [
    'font'      => [
        'bold'  => false,
        'color' => ['rgb' => '000000'],
        // 'size' => 12,
        'name'  => '新細明體',
    ],
    'alignment' => [
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
    ],
    // 'fill' => [
    //     'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //     'color' => ['rgb' => 'ffffff'],
    // ],
    'borders'   => [
        'allborders' => [
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => ['rgb' => '000000'],
        ],
    ],
];
$title = "{$action['title']}報名名單";
// Utility::dd($title);

$objPHPExcel->setActiveSheetIndex(0); //設定預設顯示的工作表
$objActSheet = $objPHPExcel->getActiveSheet(); //指定預設工作表為 $objActSheet
$objActSheet->setTitle($title); //設定工作表名稱
$objPHPExcel->createSheet(); //建立新的工作表，上面那三行再來一次，編號要改

// 抓出標題資料
$head = Eric_signup_data::get_head($action);
$row  = 1;

// Utility::dd($head);

foreach ($head as $column => $value) {
    $objActSheet->setCellValueByColumnAndRow($column, 1, $value); //直欄從0開始，橫列從1開始
    $objActSheet->getStyleByColumnAndRow($column, $row)->applyFromArray($head_style);
    $len = strlen($value);
    if (!isset($_session['length'][$column])) {
        $_session['length'][$column] = $len;
        $objActSheet->getColumnDimensionByColumn($column)->setWidth($len);
    }

}

// 抓出內容
if ($type == 'signup') {
    $signup = Eric_signup_data::get_all($action['id']);
    // Utility::dd($signup);
    foreach ($signup as $signup_data) {
        $item = [];
        foreach ($signup_data['tdc'] as $user_data) {
            $item[] = implode('|', $user_data);
        }

        if ($signup_data['accept'] === '1') {
            $item[] = '錄取';
        } elseif ($signup_data['accept'] === '0') {
            $item[] = '未錄取';
        } else {
            $item[] = '尚未設定';
        }
        $item[] = $signup_data['signup_date'];
        $item[] = $signup_data['signup_tag'];

        $row++;
        foreach ($item as $column => $value) {
            $objActSheet->setCellValueByColumnAndRow($column, $row, $value); //直欄從0開始，橫列從1開始
            $objActSheet->getStyleByColumnAndRow($column, $row)->applyFromArray($content_style);
            $len = strlen($value);
            if (!isset($_session['length'][$column]) || $len > $_session['length'][$column]) {
                $_session['length'][$column] = $len;
                $objActSheet->getColumnDimensionByColumn($column)->setWidth($len);
            }

        }
    }
}
//針對ie10以下，下載時檔名亂碼，目前的系統可不加，_CHARSET是常數，指系統目前所用編碼，可在網頁目錄下,language/tchinese_utf8的golbal.php中找到參照值
$title = (_CHARSET === 'UTF-8') ? iconv('UTF-8', 'Big5', $title) : $title;

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename={$title}.xlsx"); // 工作表檔名有字數限制(<23字?)，太長需裁
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); //產生excel檔

// 避免excel下載錯誤訊息，先清記憶體暫存區
for ($i = 0; $i < ob_get_level(); $i++) {
    ob_end_flush();
}
ob_implicit_flush(1);
ob_clean();

// 內容如有公式先勿計算，以加快下載速度
$objWriter->setPreCalculateFormulas(false);
// 輸出及下載
$objWriter->save('php://output');
exit;

// 清掉 session的 length 紀錄
unset($_SESSION['length']);
