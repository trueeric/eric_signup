<?php
// 如「模組目錄」= signup，則「首字大寫模組目錄」= Signup
// 如「資料表名」= actions，則「模組物件」= Actions

namespace XoopsModules\Eric_signup;

use XoopsModules\Eric_signup\Eric_signup_actions;
use XoopsModules\Tadtools\FormValidator;
use XoopsModules\Tadtools\TadDataCenter;
use XoopsModules\Tadtools\Utility;

class Eric_signup_data
{
    //列出所有資料
    public static function index()
    {
        global $xoopsTpl;

        $all_data = self::get_all();
        $xoopsTpl->assign('all_data', $all_data);
    }

    //編輯表單
    public static function create($action_id, $id = '')
    {
        global $xoopsTpl, $xoopsUser;

        //抓取預設值
        $db_values = empty($id) ? [] : self::get($id);

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

        $action = Eric_signup_actions::get($action_id);
        // Utility::dd($action);

        if (time > strtotime($action['end_date'])) {
            redirec_header($_SERVER['PHP_SELF'], 3, "報名日期已截止，無法進行新增報名或修改報名!");

        }
        $myts = \MyTextSanitizer::getInstance();
        foreach ($action as $col_name => $col_val) {
            if ($col_name == 'detail') {
                $col_val = $myts->displayTarea($col_val, 0, 1, 0, 1, 1);
            } else {
                $col_val = $myts->htmlSpecialChars($col_val);
            }

            $action[$col_name] = $col_val;
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

        $id   = (int) $id;
        $data = self::get($id);

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

        $action = Eric_signup_actions::get($action_id);
        foreach ($action as $col_name => $col_val) {
            if ($col_name == 'detail') {
                $col_val = $myts->displayTarea($col_val, 0, 1, 0, 1, 1);
            } else {
                $col_val = $myts->htmlSpecialChars($col_val);
            }

            $action[$col_name] = $col_val;
        }
        $xoopsTpl->assign("action", $action);

        $now_uid = $xoopsUser ? $xoopsUser->uid() : 0;
        $xoopsTpl->assign("now_uid", $now_uid);

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
        global $xoopsDB;

        if (empty($id)) {
            return;
        }

        $sql = "delete from `" . $xoopsDB->prefix("eric_signup_data") . "`
        where `id` = '{$id}'";
        $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);
    }

    //以流水號取得某筆資料
    public static function get($id = '')
    {
        global $xoopsDB;

        if (empty($id)) {
            return;
        }

        $sql = "select * from `" . $xoopsDB->prefix("eric_signup_data") . "`
        where `id` = '{$id}'";
        $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
        $data   = $xoopsDB->fetchArray($result);
        return $data;
    }

    //取得所有資料陣列
    public static function get_all($auto_key = false)
    {
        global $xoopsDB;
        $myts = \MyTextSanitizer::getInstance();

        $sql      = "select * from `" . $xoopsDB->prefix("eric_signup_data") . "` where 1 ";
        $result   = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
        $data_arr = [];
        while ($data = $xoopsDB->fetchArray($result)) {

            // $data['文字欄'] = $myts->htmlSpecialChars($data['文字欄']);
            // $data['大量文字欄'] = $myts->displayTarea($data['大量文字欄'], 0, 1, 0, 1, 1);
            // $data['HTML文字欄'] = $myts->displayTarea($data['HTML文字欄'], 1, 0, 0, 0, 0);
            // $data['數字欄'] = (int) $data['數字欄'];

            if ($_SESSION['api_mode'] or $auto_key) {
                $data_arr[] = $data;
            } else {
                $data_arr[$data['id']] = $data;
            }
        }
        return $data_arr;
    }

}
