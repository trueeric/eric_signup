<?php

require_once __DIR__ . '/header.php';

include_once $GLOBALS['xoops']->path('class/xoopsform/grouppermform.php');
//權限項目陣列（編號超級重要！設定後，以後切勿隨便亂改。）
$item_list = array(
    '1' => _MA_ERIC_SIGNUP_ACTION_CREATE,
    // '2' => "權限二",
);
$mid       = $xoopsModule->mid();
$perm_name = $xoopsModule->dirname();
$formi     = new XoopsGroupPermForm(_MA_ERIC_SIGNUP_ACTION_PERMISSION_SETTING, $mid, $perm_name, _MA_ERIC_SIGNUP_ACTION_CHECK_PERMISSIONS . '：<br>');
foreach ($item_list as $item_id => $item_name) {
    $formi->addItem($item_id, $item_name);
}
echo $formi->render();

require_once __DIR__ . '/footer.php';
