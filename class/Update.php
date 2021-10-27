<?php
// 如「模組目錄」= signup，則「首字大寫模組目錄」= Signup
// 如「資料表名」= actions，則「模組物件」= Actions

namespace XoopsModules\Eric_signup;

class Update
{
    // 建立群組
    public static function mk_group($name = "")
    {
        global $xoopsDB;
        $sql           = "select groupid from " . $xoopsDB->prefix("groups") . " where `name`='$name'";
        $result        = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
        list($groupid) = $xoopsDB->fetchRow($result);
        if (empty($groupid)) {
            $sql = "insert into " . $xoopsDB->prefix("groups") . " (`name`) values('{$name}')";
            $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);
            //取得最後新增資料的流水編號
            $groupid = $xoopsDB->getInsertId();
        }
        return $groupid;
    }

    // 進行有無侯補欄位檢查
    public static function chk_candidate()
    {
        global $xoopsDB;
        $sql    = 'SELECT count(`candidate`) FROM ' . $xoopsDB->prefix('eric_signup_actions') . ' ';
        $result = $xoopsDB->query($sql);
        if (empty($result)) {
            return true;
        }

        return false;
    }

    // 執行新增侯補欄位
    public static function go_candidate()
    {
        global $xoopsDB;
        $sql = 'ALTER TABLE ' . $xoopsDB->prefix('eric_signup_actions') . ' ADD `candidate` tinyint(3) unsigned NOT NULL';
        $xoopsDB->queryF($sql) or redirect_header(XOOPS_URL . '/modules/system/admin.php?fct=modulesadmin', 30, $xoopsDB->error());
    }
}
