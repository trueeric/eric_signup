<?php
// 如「模組目錄」= signup，則「首字大寫模組目錄」= Signup
// 如「資料表名」= actions，則「模組物件」= Actions
use Xmf\Request;
use XoopsModules\Eric_signup\Eric_signup_actions;

/*-----------引入檔案區--------------*/
$GLOBALS['xoopsOption']['template_main'] = 'eric_signup_admin.tpl';
require_once __DIR__ . '/header.php';
require_once dirname(__DIR__) . '/function.php';
$_SESSION['Eric_signup_adm'] = true;
$_SESSION['can_add']         = true;
/*-----------變數過濾----------*/
$op = Request::getString('op');
$id = Request::getInt('id');

/*-----------執行動作判斷區----------*/
switch ($op) {

    //新增活動
    case 'eric_signup_actions_create':
        Eric_signup_actions::create();
        break;

    //新增活動資料
    case 'eric_signup_actions_store':
        $id = Eric_signup_actions::store();
        // header("location: {$_SERVER['PHP_SELF']}?id=$id");
        redirect_header($_SERVER['PHP_SELF'] . "?id=$id", 3, "成功建立活動！");
        exit;

    //修改用表單
    case 'eric_signup_actions_edit':
        Eric_signup_actions::create($id);
        $op = 'eric_signup_actions_create';
        break;

    //更新資料
    case 'eric_signup_actions_update':
        Eric_signup_actions::update($id);
        // header("location: {$_SERVER['PHP_SELF']}?id=$id");
        redirect_header($_SERVER['PHP_SELF'] . "?id=$id", 3, "成功修改活動！");
        exit;

    //刪除資料
    case 'eric_signup_actions_destroy':
        Eric_signup_actions::destroy($id);
        // header("location: {$_SERVER['PHP_SELF']}?id=$id");
        redirect_header($_SERVER['PHP_SELF'], 3, "成功刪除活動！");
        exit;

    default:

        if (empty($id)) {
            Eric_signup_actions::index(false);
            $op = 'eric_signup_actions_index';
        } else {
            Eric_signup_actions::show($id);
            $op = 'eric_signup_actions_show';
        }
        break;
}

/*-----------功能函數區----------*/

/*-----------秀出結果區--------------*/
$xoopsTpl->assign('now_op', $op);
$xoTheme->addStylesheet('/modules/tadtools/css/font-awesome/css/font-awesome.css');
$xoTheme->addStylesheet(XOOPS_URL . '/modules/tadtools/css/xoops_adm4.css');
$xoTheme->addStylesheet(XOOPS_URL . '/modules/模組目錄/css/module.css');
require_once __DIR__ . '/footer.php';
