<?php

use XoopsModules\Eric_signup\Update;
use XoopsModules\Tadtools\Utility;

if (!class_exists('XoopsModules\Tadtools\Utility')) {
    require XOOPS_ROOT_PATH . '/modules/tadtools/preloads/autoloader.php';
}

if (!class_exists('XoopsModules\Eric_signup\Update')) {
    require dirname(__DIR__) . '/preloads/autoloader.php';
}

// 更新前
function xoops_module_pre_update_eric_signup(XoopsModule $module, $old_version)
{
    // 有上傳功能才需要
    Utility::mk_dir(XOOPS_ROOT_PATH . "/uploads/eric_signup");

    // 若有用到CKEditor編輯器才需要
    Utility::mk_dir(XOOPS_ROOT_PATH . "/uploads/eric_signup/file");
    Utility::mk_dir(XOOPS_ROOT_PATH . "/uploads/eric_signup/image");
    Utility::mk_dir(XOOPS_ROOT_PATH . "/uploads/eric_signup/image/.thumbs");

    $gperm_handler = xoops_getHandler('groupperm');
    $groupid       = Update::mk_group("活動報名管理");

    // 如果該群組權限已設定過，就不再設定，但重覆設定群組權限，老師說也沒關係
    if (!$gperm_handler->checkRight($module->dirname(), 1, $groupid, $module->mid())) {
        $perm = $perm_handler->create();
        $perm->setVar('gperm_groupid', $groupid);
        $perm->setVar('gperm_itemid', 1);
        $perm->setVar('gperm_name', $module->dirname()); //一般為模組目錄名稱
        $perm->setVar('gperm_modid', $module->mid());
        $perm_handler->insert($perm);
    }
    return true;

}

// 更新後
function xoops_module_update_eric_signup(XoopsModule $module, $old_version)
{
    global $xoopsDB;

    if (Update::chk_candidate()) {
        Update::go_candidate();
    }

    return true;
}
