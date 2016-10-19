<?php

require_once(__DIR__ . "/../common/MySmarty.php");
require_once(__DIR__ . "/../common/DatabaseManager.php");
require_once(__DIR__ . "/../common/Utility.php");
require_once(__DIR__ . "/../common/Setting.php");
require_once(__DIR__ . "/../library/Mobile_Detect/Mobile_Detect.php");

$smarty = new MySmarty();
$db = DatabaseManager::sharedInstance();

$data = $db->adhocSelectAll();
$url = Utility::getUrlDirectoryName($_SERVER);

$smarty->assign("url", $url);
$smarty->assign("data", $data);

$detect = new Mobile_Detect;

if ($detect->isMobile() || $detect->isTablet()) {
    $smarty->assign("isMobileTablet", true);
    $smarty->assign("isPC", false);
    $smarty->assign("isAdmin", false);
} else {
    $smarty->assign("isMobileTablet", false);
    $smarty->assign("isPC", true);
    $smarty->assign("isAdmin", false);
}
$smarty->assign("headerTitle", Setting::getTitle());

$smarty->assign("tags", array("a"));
$smarty->display("index.tpl");
