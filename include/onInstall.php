<?php
// 如「eric_signup」= signup，則「首字大寫eric_signup」= Signup
// 如「資料表名」= actions，則「模組物件」= Actions

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

    return true;
}
