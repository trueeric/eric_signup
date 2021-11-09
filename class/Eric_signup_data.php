<?php
// 如「模組目錄」= signup，則「首字大寫模組目錄」= Signup
// 如「資料表名」= actions，則「模組物件」= Actions

namespace XoopsModules\Eric_signup;

use XoopsModules\Eric_signup\Eric_signup_actions;
use XoopsModules\Tadtools\BootstrapTable;
use XoopsModules\Tadtools\FormValidator;
use XoopsModules\Tadtools\SweetAlert;
use XoopsModules\Tadtools\TadDataCenter;
use XoopsModules\Tadtools\Utility;

class Eric_signup_data
{
    //列出所有資料
    public static function index($action_id)
    {
        global $xoopsTpl;

        $all_data = self::get_all($action_id);
        $xoopsTpl->assign('all_data', $all_data);
    }

    //編輯表單
    public static function create($action_id, $id = '')
    {
        global $xoopsTpl, $xoopsUser;

        // 如果非管理員，強制抓user_id
        $uid = $_SESSION['can_add'] ? null : $xoopsUser->uid();

        //抓取預設值
        $db_values = empty($id) ? [] : self::get($id, $uid);

        foreach ($db_values as $col_name => $col_val) {
            $$col_name = $col_val;
            $xoopsTpl->assign($col_name, $col_val);
        }

        $op = empty($id) ? "eric_signup_data_store" : "eric_signup_data_update";
        $xoopsTpl->assign('next_op', $op);

        //套用formValidator驗證機制
        $formValidator = new FormValidator("#myForm", true);
        $formValidator->render();

        //加入Token安全機制
        include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');
        $token      = new \XoopsFormHiddenToken();
        $token_form = $token->render();
        $xoopsTpl->assign("token_form", $token_form);

        $action = Eric_signup_actions::get($action_id, true);
        // Utility::dd($action);
        $action['signup'] = Eric_signup_data::get_all($action_id);
        if (time > strtotime($action['end_date'])) {
            redirect_header($_SERVER['PHP_SELF'], 3, "報名日期已截止，無法進行新增報名或修改報名!");
        } elseif (!$action['enable']) {
            redirect_header($_SERVER['PHP_SELF'], 3, "該報名已關閉，，無法進行新增報名或修改報名!");
        } elseif ((count($action['signup']) >= ($action['candidate'] + $action['number'])) && $op == "eric_signup_data_store") {
            redirect_header($_SERVER['PHP_SELF'], 3, "人數已滿，無法進行新增報名!");

        }

        $xoopsTpl->assign("action", $action);

        $uid = $xoopsUser ? $xoopsUser->uid() : 0;
        $xoopsTpl->assign("uid", $uid);

        $EricDataCenter = new TadDataCenter('eric_signup');
        $EricDataCenter->set_col('id', $id);
        $signup_form = $EricDataCenter->strToForm($action['setup']);
        $xoopsTpl->assign('signup_form', $signup_form);

    }

    //新增資料
    public static function store()
    {
        global $xoopsDB;

        //XOOPS表單安全檢查
        Utility::xoops_security_check();

        $myts = \MyTextSanitizer::getInstance();

        foreach ($_POST as $var_name => $var_val) {
            $$var_name = $myts->addSlashes($var_val);
        }

        $action_id = (int) ($action_id);
        $uid       = (int) ($uid);

        $sql = "insert into `" . $xoopsDB->prefix("eric_signup_data") . "` (
        `action_id`,
        `uid`,
        `signup_date`
        ) values(
        '{$action_id}',
        '{$uid}',
        now()
        )";
        $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

        //取得最後新增資料的流水編號
        $id = $xoopsDB->getInsertId();
        // setup資料寫入
        $EricDataCenter = new TadDataCenter('eric_signup');
        $EricDataCenter->set_col('id', $id);
        $EricDataCenter->saveData();

        $action           = Eric_signup_actions::get($action_id, true);
        $action['signup'] = self::get_all($action_id);
        // Utility::dd($action);
        if (count($action['signup']) > $action['number']) {
            // 以下方法仍需資料綁定，變數名稱要換一下
            $EricDataCenter->set_col('data_id', $id);
            $EricDataCenter->saveCustomData(['tag' => ['侯補']]);
            // $data_arr = [
            //     'tag'       => [0 => '侯補'],
            //     // $data_name2 => [0 => $data_value3],
            // ];
            // $TadDataCenter->saveCustomData($data_arr = []);
        }

        return $id;
    }

    //以流水號秀出某筆資料內容
    public static function show($id = '')
    {
        global $xoopsTpl, $xoopsUser;

        if (empty($id)) {
            return;
        }

        $id = (int) $id;

        // 如果非管理員，強制抓user_id
        $uid  = $_SESSION['can_add'] ? null : $xoopsUser->uid();
        $data = self::get($id, $uid);
        if (empty($data)) {
            redirect_header($_SERVER['PHP_SELF'], 3, "查無報名資料，無法觀看!!");
        }

        $myts = \MyTextSanitizer::getInstance();
        foreach ($data as $col_name => $col_val) {
            $col_val = $myts->htmlSpecialChars($col_val);
            $xoopsTpl->assign($col_name, $col_val);
            $$col_name = $col_val;
        }

        $EricDataCenter = new TadDataCenter('eric_signup');
        $EricDataCenter->set_col('id', $id);
        $tdc = $EricDataCenter->getData();
        // Utility::dd($data);
        $xoopsTpl->assign('tdc', $tdc);

        $action = Eric_signup_actions::get($action_id, true);

        $xoopsTpl->assign("action", $action);

        $now_uid = $xoopsUser ? $xoopsUser->uid() : 0;
        $xoopsTpl->assign("now_uid", $now_uid);

        //刪除前再確認
        $SweetAlert = new SweetAlert();
        $SweetAlert->render("del_data", "index.php?op=eric_signup_data_destroy&action_id={$action_id}&id=", 'id');

    }

    //更新某一筆資料
    public static function update($id = '')
    {
        global $xoopsDB, $xoopsUser;

        //XOOPS表單安全檢查
        Utility::xoops_security_check();

        $myts = \MyTextSanitizer::getInstance();

        $action_id = (int) ($action_id);
        $uid       = (int) ($uid);
        $now_uid   = $xoopsUser ? $xoopsUser->uid() : 0;

        foreach ($_POST as $var_name => $var_val) {
            $$var_name = $myts->addSlashes($var_val);
        }

        $sql = "update `" . $xoopsDB->prefix("eric_signup_data") . "` set
        `signup_date` =now()
        where `id` = '$id' and `uid` = '$now_uid'";
        if ($xoopsDB->queryF($sql)) {
            $EricDataCenter = new TadDataCenter('eric_signup');
            $EricDataCenter->set_col('id', $id);
            $EricDataCenter->saveData();
        } else {
            Utility::web_error($sql, __FILE__, __LINE__);
        }

        return $id;
    }

    //刪除某筆資料資料
    public static function destroy($id = '')
    {
        global $xoopsDB, $xoopsUser;

        if (empty($id)) {
            return;
        }
        $uid     = (int) ($uid);
        $now_uid = $xoopsUser ? $xoopsUser->uid() : 0;

        $sql = "delete from `" . $xoopsDB->prefix("eric_signup_data") . "`
        where `id` = '{$id}' and `uid` = '$now_uid'";

        if ($xoopsDB->queryF($sql)) {

            $EricDataCenter = new TadDataCenter('eric_signup');
            $EricDataCenter->set_col('id', $id);
            // $EricDataCenter->set_col('data_id', $id);
            //deldata沒有指定，就是刪該筆所有的相關欄位
            $EricDataCenter->delData();

            // 刪額外的欄位「侯補」的資料
            $EricDataCenter->set_col('data_id', $id);
            $EricDataCenter->delData();
        } else {
            Utility::web_error($sql, __FILE__, __LINE__);
        }

    }

    //以流水號取得某筆資料
    public static function get($id = '', $uid = '')
    {
        global $xoopsDB;

        if (empty($id)) {
            return;
        }

        $and_uid = $uid ? " and `uid`='$uid'" : '';
        $sql     = "select * from `" . $xoopsDB->prefix("eric_signup_data") . "` where `id` = '{$id}' $and_uid ";
        $result  = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
        $data    = $xoopsDB->fetchArray($result);
        return $data;
    }

    //取得所有資料陣列
    public static function get_all($action_id = '', $uid = '', $auto_key = false, $only_accept = false)
    {
        global $xoopsDB, $xoopsUser;
        $myts       = \MyTextSanitizer::getInstance();
        $and_accept = $only_accept ? " and `accept`='1' " : "";

        if ($action_id) {
            $sql = "select * from `" . $xoopsDB->prefix("eric_signup_data") . "` where `action_id`= '$action_id' $and_accept order by `signup_date` ";
        } else {
            if (!$_SESSION['can_add'] or !$uid) {
                $uid = $xoopsUser ? $xoopsUser->uid() : 0;
            }
            $sql = "select * from `" . $xoopsDB->prefix("eric_signup_data") . "` where `uid`= '$uid' $and_accept order by `signup_date` ";
        }

        $result   = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
        $data_arr = [];

        $EricDataCenter = new TadDataCenter('eric_signup');
        while ($data = $xoopsDB->fetchArray($result)) {

            $EricDataCenter = new TadDataCenter('eric_signup');
            $EricDataCenter->set_col('id', $data['id']);
            $data['tdc']    = $EricDataCenter->getData();
            $data['action'] = Eric_signup_actions::get($data['action_id'], true);
            // 抓出侯補資料
            $EricDataCenter->set_col('data_id', $data['id']);
            // 每人只會有一筆tag，tag只會有第0筆資料
            $data['tag'] = $EricDataCenter->getData('tag', 0);
            // Utility::dd($data);

            if ($_SESSION['api_mode'] or $auto_key) {
                $data_arr[] = $data;
            } else {
                $data_arr[$data['id']] = $data;
            }
        }
        return $data_arr;
    }

    //查詢某人的報名紀錄
    public static function my($uid)
    {
        global $xoopsTpl, $xoopsUser;
        $my_signup = self::get_all(null, $uid);

        $xoopsTpl->assign('my_signup', $my_signup);
        BootstrapTable::render();

    }

    //更新錄取狀態
    public static function accept($id, $accept)
    {
        global $xoopsDB;

        if (!$_SESSION['can_add']) {
            redirect_header($_SERVER['PHP_SELF'], 3, "您沒有權限使用此功能!");
        }

        $id     = (int) ($id);
        $accept = (int) ($accept);

        $sql = "update `" . $xoopsDB->prefix("eric_signup_data") . "` set
        `accept` ='$accept'
        where `id` = '$id' ";
        $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    }

    // 統計 radio, checkbox, select
    public static function statistics($setup, $signup = [])
    {
        $result = [];
        // "\n"用在linux環境,"\n\r"用在winodws環境
        $setup_items = explode("\n", $setup);
        // Utility::dd($setup);

        foreach ($setup_items as $setup_item) {
            if (preg_match("/radio|checkbox|select/", $setup_item)) {
                // Utility::dd($setup_item);
                $items = explode(",", $setup_item);
                // Utility::dd($item);
                //如果stup中有設定必填，"*"去掉
                $title = str_replace("*", "", $items[0]);
                // Utility::dd($title);
                foreach ($signup as $data) {
                    // Utility::dd($data);
                    foreach ($data['tdc'][$title] as $option) {
                        // Utility::dd($data['tdc'][$title]);
                        // echo "start!";
                        // print_r($result[$title][$option] . "    TEST");
                        $result[$title][$option]++;
                        // print_r($result);
                        // echo "end!!!!";
                    }
                    // echo "\n";
                }
                // die();
                // print_r($result);
                // die();
                // Utility::dd($$result[$title][$option]);
            }

            // Utility::dd($data['tdc'][$title]);
            // Utility::dd($$result[$title][$option]);
        }
        // die();
        // print_r($result);
        // die();
        return $result;
    }

    //立即寄出
    public static function send($title = "無標題", $content = "無內容", $email = "")
    {
        global $xoopsUser;
        if (empty($email)) {
            $email = $xoopsUser->email();
        }
        $xoopsMailer                           = xoops_getMailer();
        $xoopsMailer->multimailer->ContentType = "text/html";
        $xoopsMailer->addHeaders("MIME-Version: 1.0");
        $header = '';
        return $xoopsMailer->sendMail($email, $title, $content, $header);
    }

    //產生通知信
    public static function mail($id, $type, $signup = [])
    {
        global $xoopsUser;
        $id     = (int) $id;
        $signup = $signup ? $signup : self::get($id);
        if (empty($id)) {
            redirect_header($_SERVER['PHP_SELF'], 3, "無編號!無法寄送!!");
        }
        $action = Eric_signup_actions::get($signup['action_id']);
        $now    = date("Y-m-d H:i:s");
        $name   = $name ? $name : $xoopsUser->name();

        $member_handler = xoops_getHandler('member');
        $admUser        = $member_handler->getUser($action['uid']);
        $adm_email      = $admUser->email();

        if ($type == 'destroy') {
            $title = "「{$action['title']}」取消報名通知";
            $head  = "<p>您於{$signup['signup_date']}報名了「{$action['title']}」活動，已於{$now}由{$name}取消</p>";
            $foot  = "欲重新報名，請連至" . XOOPS_URL . "/modules/eric_signup/index.php?op=eric_signup_data_create&action_id={$action['id']}";
        } elseif ($type == 'store') {
            $title = "「{$action['title']}」報名完成通知";
            $head  = "<p>您於{$signup['signup_date']}報名了「{$action['title']}」活動，已於{$now}由{$name}完成</p>";
            $foot  = "完整詳情，請連至" . XOOPS_URL . "/modules/eric_signup/index.php?id={$signup['action_id']}";
        } elseif ($type == 'update') {
            $title = "「{$action['title']}」修改報名資料通知";
            $head  = "<p>您於{$signup['signup_date']}報名了「{$action['title']}」活動，已於{$now}由{$name}修改資料如后。</p>";
            $foot  = "完整詳情，請連至" . XOOPS_URL . "/modules/eric_signup/index.php?id={$signup['action_id']}";
        } elseif ($type == 'accept') {
            $title = "「{$action['title']}」報名錄取狀況通知";
            if ($signup['accept'] == 1) {
                $head = "<p>您於{$signup['signup_date']}報名了「{$action['title']}」活動經審核，<h2 style='color:blue'>恭喜錄取!!</h2>您的報名資料如后。</p>";
            } else {
                $head = "<p>您於{$signup['signup_date']}報名了「{$action['title']}」活動，很抱歉!因人數關係，<span style='color:red'>未能錄取</span>，您的報名資料如后。</p>";
            }

            $foot = "完整詳情，請連至" . XOOPS_URL . "/modules/eric_signup/index.php?id={$signup['action_id']}";

            $signupUser = $member_handler->getUser($action['uid']);
            $email      = $signupUser->email();
        }

        $content = self::mk_content($id, $head, $foot, $action);

        if (!self::send($title, $content, $email)) {
            redirect_header($_SERVER['PHP_SELF'], 3, "通知信寄發失敗!!");
        }
        // 失敗時上一行就轉走了，所以不用寫elseif
        self::send($title, $content, $adm_email);

    }
    //產生通知信知容
    public static function mk_content($id, $head = '', $foot = '', $action = [])
    {

        if ($id) {
            $EricDataCenter = new TadDataCenter('eric_signup');
            $EricDataCenter->set_col('id', $id);
            $tdc = $EricDataCenter->getData();

            $table = '<table class="table">';

            foreach ($tdc as $title => $signup) {
                $table .= "
               <tr>
                    <th>{$title}</th>
                    <td>";

                foreach ($signup as $i => $val) {
                    $table .= "<div>{$val}</div>";
                }

                $table .= "</td>
               </tr>";
            }
            $table .= '</table>';
        }

        $content = "
        <html>
            <head>
                <style>
                    .table{
                        border:1px  solid #000;
                        border-collapse: collapse;
                        margin:10px 0px;
                    }

                    .table th, .table td{
                        border:1px solid #000;
                        padding:4px 10px;
                    }

                    .table th{
                        background:#c1e7f4;
                    }
                    .well{
                        border-radius:10px;
                        background:#fcfcfc;
                        border:2px solid #cfcfcf;
                        padding:14px 16px;
                        margin:10px 0px;
                    }
                </style>
            </head>
            <body>
            $head
            <h2>{$action['title']}</h2>
            <div>活動日期:{$action['action_date']}</div>
            <div class='well'>{$action['detail']}</div>
            $table
            $foot
            </body>
        </html>
        ";

        return $content;

    }

    // 預覽匯入CSV
    public static function preview_csv($action_id)
    {

        global $xoopsTpl;
        if (!$_SESSION['can_add']) {
            redirect_header($_SERVER['PHP_SELF'], 3, "您沒有權限使用此功能!");
        }
        $action = Eric_signup_actions::get($action_id);
        // 目前報名人數
        $action['signup_count'] = count(Eric_signup_actions::get_all($action_id));
        $xoopsTpl->assign('action', $action);

        // 製作標題
        /* 舊的寫法
        $head_row = explode("\n", $action['setup']);
        $head     = $type     = [];
        foreach ($head_row as $head_data) {
        $cols = explode(',', $head_data);
        if (strpos($cols[0], '#') === false) {
        $head[] = str_replace('*', '', trim($cols[0]));
        // 抓出第二個欄位的類型(文字、單、複選...)
        $type[] = trim($cols[1]);

        }
        }
        // 不要出現 toc以外的欄位
        // $head[] = '錄取';
        // $head[] = '報名日期';
        // $head[] = '身份';
         */
        list($head, $type, $options) = self::get_head($action, true, true);
        $xoopsTpl->assign('head', $head);
        $xoopsTpl->assign('type', $type);
        $xoopsTpl->assign('options', $options);
        // 抓取csv內容
        $preview_data = [];
        // csv與 匯入的.tpl中「input type="file" name="csv"」的name相關
        $handle = fopen($_FILES['csv']['tmp_name'], "r") or die("無法開啟");
        while (($val = fgetcsv($handle, 1000)) !== false) {
            $preview_data[] = mb_convert_encoding($val, 'UTF-8', 'Big5');
        }
        fclose($handle);
        $xoopsTpl->assign('preview_data', $preview_data);

        //加入Token安全機制
        include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');
        $token      = new \XoopsFormHiddenToken();
        $token_form = $token->render();
        $xoopsTpl->assign("token_form", $token_form);

    }

    //批次匯入csv資料
    public static function import_csv($action_id)
    {
        global $xoopsDB, $xoopsUser;

        //XOOPS表單安全檢查,配合token
        Utility::xoops_security_check();

        // 確認管理者權限
        if (!$_SESSION['can_add']) {
            redirect_header($_SERVER['PHP_SELF'], 3, "您沒有權限使用此功能!");
        }

        $myts = \MyTextSanitizer::getInstance();

        // get()已過濾，這裡丕用再重覆過濾
        // foreach ($_POST as $var_name => $var_val) {
        //     $$var_name = $myts->addSlashes($var_val);
        // }

        $action_id = (int) ($action_id);
        $uid       = $xoopsUser->uid();

        // 取得活動報名人數上限
        $action = Eric_signup_actions::get($action_id, true);

        $EricDataCenter = new TadDataCenter('eric_signup');
        foreach ($_POST['tdc'] as $tdc) {

            // 匯入即錄取，accept直接給1
            $sql = "insert into `" . $xoopsDB->prefix("eric_signup_data") . "` (
            `action_id`,
            `uid`,
            `signup_date`,
            `accept`
            ) values(
            '{$action_id}',
            '{$uid}',
            now(),
            '1'
            )";
            $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

            //取得最後新增資料的流水編號
            $id = $xoopsDB->getInsertId();

            // setup資料寫入
            $EricDataCenter->set_col('id', $id);
            // 這個只會一次存一筆要換，要換下下行的寫法
            // $EricDataCenter->saveData();
            $EricDataCenter->saveCustomData($tdc);

            $action['signup'] = self::get_all($action_id);
            // Utility::dd($action);
            if (count($action['signup']) > $action['number']) {
                // 以下方法仍需資料綁定，變數名稱要換一下
                $EricDataCenter->set_col('data_id', $id);
                $EricDataCenter->saveCustomData(['tag' => ['侯補']]);
                // $data_arr = [
                //     'tag'       => [0 => '侯補'],
                //     // $data_name2 => [0 => $data_value3],
                // ];
                // $TadDataCenter->saveCustomData($data_arr = []);
            }

        }
    }

    // 預覽匯入EXCEL
    public static function preview_excel($action_id)
    {

        global $xoopsTpl;
        if (!$_SESSION['can_add']) {
            redirect_header($_SERVER['PHP_SELF'], 3, "您沒有權限使用此功能!");
        }
        $action = Eric_signup_actions::get($action_id);
        // 目前報名人數
        $action['signup_count'] = count(Eric_signup_actions::get_all($action_id));
        $xoopsTpl->assign('action', $action);

        // 製作標題
        /* 舊的寫法
        $head_row = explode("\n", $action['setup']);
        $head     = $type     = [];
        foreach ($head_row as $head_data) {
        $cols = explode(',', $head_data);
        if (strpos($cols[0], '#') === false) {
        $head[] = str_replace('*', '', trim($cols[0]));
        // 抓出第二個欄位的類型(文字、單、複選...)
        $type[] = trim($cols[1]);

        }
        }

         */

        list($head, $type, $options) = self::get_head($action, true, true);

        $xoopsTpl->assign('head', $head);
        $xoopsTpl->assign('type', $type);
        $xoopsTpl->assign('options', $options);

        // 抓取excel內容
        $preview_data = [];

        require_once XOOPS_ROOT_PATH . '/modules/tadtools/vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';
        // PHPExcel_IOFactory前面要有斜線才會到相關的路徑下找
        $reader    = \PHPExcel_IOFactory::createReader('Excel2007');
        $PHPExcel  = $reader->load($_FILES['excel']['tmp_name']); // 檔案名稱
        $sheet     = $PHPExcel->getSheet(0); // 讀取第一個工作表(編號從 0 開始)
        $maxCell   = $PHPExcel->getActiveSheet()->getHighestRowAndColumn();
        $maxColumn = self::getIndex($maxCell['column']);

        // 一次讀一列
        for ($row = 1; $row <= $maxCell['row']; $row++) {
            // 讀出每一格
            for ($column = 0; $column <= $maxColumn; $column++) {
                $preview_data[$row][$column] = $sheet->getCellByColumnAndRow($column, $row)->getCalculatedValue();
            }
        }

        $xoopsTpl->assign('preview_data', $preview_data);

        //加入Token安全機制
        include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');
        $token      = new \XoopsFormHiddenToken();
        $token_form = $token->render();
        $xoopsTpl->assign("token_form", $token_form);

    }

    //取得報名的標題欄
    public static function get_head($action, $return_type = false, $only_tdc = false)
    {
        $EricDataCenter = new TadDataCenter('tad_signup');
        $head           = $EricDataCenter->getAllColItems($action['setup'], 'label');
        $type           = $EricDataCenter->getAllColItems($action['setup'], 'type');
        $options        = $EricDataCenter->getAllColItems($action['setup'], 'options');

        if (!$only_tdc) {
            $head[] = _MD_TAD_SIGNUP_ACCEPT;
            $head[] = _MD_TAD_SIGNUP_APPLY_DATE;
            $head[] = _MD_TAD_SIGNUP_IDENTITY;
        }
        if ($return_type) {
            return [$head, $type, $options];
        } else {
            return $head;
        }

    }
    // 將文字轉為數字
    private static function getIndex($let)
    {
        // Iterate through each letter, starting at the back to increment the value
        for ($num = 0, $i = 0; $let != ''; $let = substr($let, 0, -1), $i++) {
            $num += (ord(substr($let, -1)) - 65) * pow(26, $i);
        }

        return $num;
    }

    //批次匯入EXCEL資料
    public static function import_excel($action_id)
    {
        self::import_csv($action_id);
    }

}
