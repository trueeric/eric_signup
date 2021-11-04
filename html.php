<?php
require_once __DIR__ . '/header.php';

use Xmf\Request;
use XoopsModules\Eric_signup\Eric_signup_actions;
use XoopsModules\Tadtools\Utility;

$id     = Request::getInt('id');
$action = Eric_signup_actions::get($id);

header("Content-type: text/html");
// 是否提供下載
header("Content-Disposition: attachment; filename={$action['title']}.html");

$content = "
<h2 class='my'>
    {$action['title']}
</h2>
<div class='alert alert-info'>
{$action['detail']}
</div>
{$action['files']}
<h4 class='my'>
    <small>
        <div><i class='fa fa-calendar' aria-hidden='true'></i>活動日期 :{$action['action_date']}</div>
        <div><i class='fa fa-calendar' aria-hidden='true'></i>報名截止日期:{$action['end_date']}</div>
        <div>
            <i class='fa fa-users' aria-hidden='true'></i>報名狀況:" . count($action['signup']) . "/{$action['number']}
            <span data-toggle='tooltip' title='可侯補人數'>({$action['candidate']}) </span>
        </div>
    </small>
</h4>
<div class='text-center my-2'>
    <a href='" . XOOPS_URL . "/modules/eric_signup/index.php?op=eric_signup_data_create&action_id={$action['id']}' class='btn btn-lg btn-info'><i class='fa fa-plus' aria-hidden='true'></i> 立即報名</a>
</div>

";
$content = Utility::html5($content, false, true, 4, true, 'container', $action['title'], '<link rel="stylesheet" href="' . XOOPS_URL . 'eric_signup/css/module.css" type="text/css">');
// echo $content;

// 把靜態網頁存在主機中，存完後轉到新位置;
if (file_put_contents(XOOPS_ROOT_PATH . "/uploads/eric_signup/{$action['id']}.html", $content)) {
    header("location: " . XOOPS_URL . "/uploads/eric_signup/{$action['id']}.html");
}
exit;
