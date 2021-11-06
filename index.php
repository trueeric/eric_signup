<?php
// 如「模組目錄」= signup，則「首字大寫模組目錄」= Signup
// 如「資料表名」= actions，則「模組物件」= Actions
use Xmf\Request;
use XoopsModules\Eric_signup\Eric_signup_actions;
use XoopsModules\Eric_signup\Eric_signup_data;
use XoopsModules\Tadtools\TadUpFiles;
use XoopsModules\Tadtools\Utility;

/*-----------引入檔案區--------------*/
require_once __DIR__ . '/header.php';
$GLOBALS['xoopsOption']['template_main'] = 'eric_signup_index.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

/*-----------變數過濾----------*/
$op        = Request::getString('op');
$id        = Request::getInt('id');
$action_id = Request::getInt('action_id');
$accept    = Request::getInt('accept');
$files_sn  = Request::getInt('files_sn');

/*-----------執行動作判斷區----------*/
switch ($op) {

    // 下載檔案的switch，一定要加
    case "tufdl":
        $EricUpFiles = new TadUpFiles("eric_signup");
        $EricUpFiles->add_file_counter($files_sn);
        exit;

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

    //新增報名表單
    case 'eric_signup_data_create':
        Eric_signup_data::create($action_id);
        break;

    //新增報名資料
    case 'eric_signup_data_store':
        $id = Eric_signup_data::store();
        // Utility::dd($id);
        Eric_signup_data::mail($id, 'store');
        // header("location: {$_SERVER['PHP_SELF']}?id=$id");
        redirect_header($_SERVER['PHP_SELF'] . "?op=eric_signup_data_show&id=$id", 3, "成功報名活動！");
        break;

    //顯示報名表
    case 'eric_signup_data_show':
        Eric_signup_data::show($id);
        break;

    //修改報名表單
    case 'eric_signup_data_edit':
        Eric_signup_data::create($action_id, $id);
        $op = 'eric_signup_data_create';
        break;

    //更新報名表單
    case 'eric_signup_data_update':
        Eric_signup_data::update($id);
        Eric_signup_data::mail($id, 'update');
        redirect_header($_SERVER['PHP_SELF'] . "?op=eric_signup_data_show&id=$id", 3, "成功修改報名資料！");
        break;

    //刪除資料
    case 'eric_signup_data_destroy':
        $uid    = $_SESSION['eric_sign_up'] ? null : $xoopsUser->uid();
        $signup = Eric_signup_data::get($id, $uid);
        Eric_signup_data::destroy($id);
        Eric_signup_data::mail($id, 'destroy', $signup);
        // header("location: {$_SERVER['PHP_SELF']}?id=$id");
        redirect_header($_SERVER['PHP_SELF'] . "?id=$action_id", 3, "成功刪除活動！");
        exit;

    //更新錄取狀能
    case 'eric_signup_data_accept':
        Eric_signup_data::accept($id, $accept);
        Eric_signup_data::mail($id, 'accept');
        // header("location: {$_SERVER['PHP_SELF']}?id=$id");
        redirect_header($_SERVER['PHP_SELF'] . "?id=$action_id", 3, "成功更新錄取狀態！");
        exit;

    // 複製活動
    case 'eric_signup_actions_copy':
        $new_id = Eric_signup_actions::copy($id);
        header("location: {$_SERVER['PHP_SELF']}?op=eric_signup_actions_edit&id=$new_id");
        exit;

    // 匯入報名名冊csv
    case 'eric_signup_data_preview_csv':
        Eric_signup_data::preview_csv($id);
        break;

    //批次匯入報名資料csv
    case 'eric_signup_data_import_csv':
        Eric_signup_data::import_csv($id);
        // header("location: {$_SERVER['PHP_SELF']}?id=$id");
        redirect_header($_SERVER['PHP_SELF'] . "?id=$id", 3, "成功匯入報名資料csv！");
        break;

    // 匯入報名名冊excel
    case 'eric_signup_data_preview_excel':
        Eric_signup_data::preview_excel($id);
        break;

    //批次匯入報名資料EXCEL
    case 'eric_signup_data_import_excel':
        Eric_signup_data::import_excel($id);
        // header("location: {$_SERVER['PHP_SELF']}?id=$id");
        redirect_header($_SERVER['PHP_SELF'] . "?id=$id", 3, "成功匯入報名資料csv！");
        break;

    default:
        if (empty($id)) {
            Eric_signup_actions::index($xoopsModuleConfig['only_enable']);
            $op = 'eric_signup_actions_index';
        } else {
            Eric_signup_actions::show($id);
            $op = 'eric_signup_actions_show';
        }
        break;
}

/*-----------function區--------------*/

/*-----------秀出結果區--------------*/
unset($_SESSION['api_mode']);
$xoopsTpl->assign('toolbar', Utility::toolbar_bootstrap($interface_menu));
$xoopsTpl->assign('now_op', $op);
$xoTheme->addStylesheet(XOOPS_URL . '/modules/eric_signup/css/module.css');
require_once XOOPS_ROOT_PATH . '/footer.php';
