<?php
use Xmf\Request;
use XoopsModules\Eric_signup\Eric_signup_api;

require_once dirname(dirname(__DIR__)) . '/mainfile.php';

/*-----------執行動作判斷區----------*/
$op    = Request::getString('op');
$token = Request::getString('token');

$api = new Eric_signup_api($token);

switch ($op) {
    // 取得所有活動
    case 'eric_signup_actions_index':
        echo $api->eric_signup_actions_index($xoopsModuleConfig['only_enable']);
        break;

    default:
        echo $api->user();
        break;
}
