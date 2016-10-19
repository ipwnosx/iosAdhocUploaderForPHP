<?php

session_start();
require_once("../../common/MySmarty.php");
require_once(__DIR__ . "/../../common/Setting.php");
require_once("../../common/DatabaseManager.php");
$smarty = new MySmarty();
$db = DatabaseManager::sharedInstance();


if (empty($_SESSION['editRefreshToken'])) {
    $_SESSION['editRefreshToken'] = "true";
}

$data = null;

if (isset($_GET['d'])) {

    $hexString = $_GET['d'];

    if (preg_match('/[0-9A-Fa-f]{104}/', $hexString) === 1) {

        $directory = substr($hexString, 0 , 64);
        $file = substr($hexString, 64, 40);

        if ($directory !== '' && $file !== '') {
            $data = $db->adhocSelect($directory, $file);
        }
    }
}

if ( !is_null($data) ) {
    $smarty->assign("data", $data);
}
$smarty->assign("headerTitle", Setting::getTitle());
$smarty->display("admin/edit.tpl");