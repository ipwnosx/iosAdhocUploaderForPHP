<?php

session_start();
require_once("../../common/MySmarty.php");
require_once(__DIR__ . "/../../common/Setting.php");
$smarty = new MySmarty();

$slackArray = Setting::getSlackSetting();
if (count($slackArray) > 0) {
    $smarty->assign("slackArray", $slackArray);
}

if (empty($_SESSION['refreshToken'])) {
    $_SESSION['refreshToken'] = "true";
}

$smarty->assign("headerTitle", Setting::getTitle());
$smarty->display("admin/uploader.tpl");