<?php

require_once("../../common/MySmarty.php");
require_once(__DIR__ . "/../../common/Setting.php");
require_once("../../common/DatabaseManager.php");
require_once("../../common/Utility.php");

$smarty = new MySmarty();
$db = DatabaseManager::sharedInstance();

$data = $db->adhocSelectAll();
$url = Utility::getUrlDirectoryName($_SERVER);

$smarty->assign("url", $url);
$smarty->assign("data", $data);

$smarty->assign("isMobileTablet", false);
$smarty->assign("isPC", false);
$smarty->assign("isAdmin", true);
$smarty->assign("headerTitle", Setting::getTitle());

$smarty->assign("tags", array("a"));
$smarty->display("index.tpl");