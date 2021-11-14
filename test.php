<?php
require_once __DIR__ . '/header.php';

use Xmf\Request;
use XoopsModules\Eric_signup\Eric_signup_actions;
use XoopsModules\Tadtools\Utility;

$id     = Request::getInt('id');
$action = Eric_signup_actions::get($id);
// Utility::dd($action);
// $action = Eric_signup_actions::get($action_id);
// 目前報名人數
$signup = Eric_signup_actions::get_all($action['id']);

Utility::dd($signup);
