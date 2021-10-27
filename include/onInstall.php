<?php
// 如「eric_signup」= signup，則「首字大寫eric_signup」= Signup
// 如「資料表名」= actions，則「模組物件」= Actions

use XoopsModules\Eric_signup\Update;
use XoopsModules\Tadtools\Utility;

if (!class_exists('XoopsModules\Tadtools\Utility')) {
    require XOOPS_ROOT_PATH . '/modules/tadtools/preloads/autoloader.php';
}

// 安裝前 pre 很少用
function xoops_module_pre_install_eric_signup(XoopsModule $module)
{

}

// 安裝後
function xoops_module_install_eric_signup(XoopsModule $module)
{

    // 有上傳功能才需要
    Utility::mk_dir(XOOPS_ROOT_PATH . "/uploads/eric_signup");

    // 若有用到CKEditor編輯器才需要
    Utility::mk_dir(XOOPS_ROOT_PATH . "/uploads/eric_signup/file");
    Utility::mk_dir(XOOPS_ROOT_PATH . "/uploads/eric_signup/image");
    Utility::mk_dir(XOOPS_ROOT_PATH . "/uploads/eric_signup/image/.thumbs");

    $groupid      = Update::mk_group("活動報名管理");
    $perm_handler = xoops_getHandler('groupperm');
    $perm         = $perm_handler->create();
    $perm->setVar('gperm_groupid', $groupid);
    $perm->setVar('gperm_itemid', 1);
    $perm->setVar('gperm_name', $module->dirname()); //一般為模組目錄名稱
    $perm->setVar('gperm_modid', $module->mid());
    $perm_handler->insert($perm);
    return true;
}
