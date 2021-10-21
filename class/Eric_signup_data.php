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
        $uid = $_SESSION['eric_signup_adm'] ? null : $xoopsUser->uid();

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
            redirec_header($_SERVER['PHP_SELF'], 3, "報名日期已截止，無法進行新增報名或修改報名!");
        } elseif (count($action['signup']) >= $action['number']) {
            redirec_header($_SERVER['PHP_SELF'], 3, "報名日期已截止，無法進行新增報名或修改報名!");

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
        $uid  = $_SESSION['eric_signup_adm'] ? null : $xoopsUser->uid();
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
            //deldata沒有指定，就是刪該筆所有的相關欄位
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
    public static function get_all($action_id = '', $uid = '', $auto_key = false)
    {
        global $xoopsDB, $xoopsUser;
        $myts = \MyTextSanitizer::getInstance();

        if ($action_id) {
            $sql = "select * from `" . $xoopsDB->prefix("eric_signup_data") . "` where `action_id`= '$action_id' order by `signup_date` ";
        } else {
            if (!$_SESSION['eric_signup_adm'] or !$uid) {
                $uid = $xoopsUser ? $xoopsUser->uid() : 0;
            }
            $sql = "select * from `" . $xoopsDB->prefix("eric_signup_data") . "` where `uid`= '$uid' order by `signup_date` ";
        }

        $result   = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
        $data_arr = [];

        $EricDataCenter = new TadDataCenter('eric_signup');
        while ($data = $xoopsDB->fetchArray($result)) {

            $EricDataCenter = new TadDataCenter('eric_signup');
            $EricDataCenter->set_col('id', $data['id']);
            $data['tdc']    = $EricDataCenter->getData();
            $data['action'] = Eric_signup_actions::get($data['action_id'], true);
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

        if (!$_SESSION['eric_signup_adm']) {
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
}
