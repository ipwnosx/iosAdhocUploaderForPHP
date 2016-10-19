<?php

require_once("../../common/MySmarty.php");
require_once(__DIR__ . "/../../common/Setting.php");
require_once("../../common/DatabaseManager.php");
$smarty = new MySmarty();
$db = DatabaseManager::sharedInstance();

// この画面のキャッシュを無効化する
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


//echo "<pre>";
//print_r($_POST);
//echo "</pre>";

$id = -1;
if (isset($_POST['inputId'])) {
    $id = intval($_POST['inputId']);
    // 変換失敗で0の場合は、DBのidにヒットしないように -1 を入れる
    if ($id === 0 && strcmp($_POST['inputId'], '0') !== 0) {
        $id = -1;
    }
    // オブジェクトの場合は、DBのidにヒットしないように -1 を入れる
    if ($id === 1 && strcmp($_POST['inputId'], '1') !== 0) {
        $id = -1;
    }
}
$title = "";
if (isset($_POST['inputTitle'])) {
    $title = $_POST['inputTitle'];
}
$notes = "";
if (isset($_POST['inputNotes'])) {
    $notes = $_POST['inputNotes'];
}
$developerNotes = "";
if (isset($_POST['inputDeveloperNotes'])) {
    $developerNotes = $_POST['inputDeveloperNotes'];
}
$checkboxInvalidBackground = false;
if (isset($_POST['checkboxInvalidBackground']) && $_POST['checkboxInvalidBackground'] === "on") {
    $checkboxInvalidBackground = true;
}
$checkboxHide = false;
if (isset($_POST['checkboxHide']) && $_POST['checkboxHide'] === "on") {
    $checkboxHide = true;
}
$sortOrder = 0;
if (isset($_POST['inputSortOrder'])) {
    $sortOrder = intval($_POST['inputSortOrder']);
    // オブジェクトの場合は、DBのidにヒットしないように -1 を入れる
    if ($sortOrder === 1 && strcmp($_POST['inputId'], '1') !== 0) {
        $sortOrder = -1;
    }
}


$exception = "";
$isSuccess = true;

try {

    if ($title == "") {
        throw new RuntimeException('タイトルが入力されていません');
    }

    $db->query("BEGIN;");

    $dbUpdateResult = $db->adhocUpdate($id, $title, $notes, $developerNotes, $checkboxInvalidBackground ? 1 : 0, $checkboxHide ? 1 : 0, $sortOrder);

    if ($dbUpdateResult === False) {
        $db->query("ROLLBACK;");
        $isSuccess = false;
    } else {
        $db->query("COMMIT;");
    }


} catch (RuntimeException $e) {
    $exception = $e->getMessage();
}

if ($isSuccess) {
    $smarty->assign('result', "更新に成功しました");
} else {
    $smarty->assign('result', "更新に失敗しました<br>" . $exception);
}
$smarty->assign("headerTitle", Setting::getTitle());
$smarty->display("admin/result.tpl");

// 更新した時には更新無効の命令を出す
$_SESSION = array();