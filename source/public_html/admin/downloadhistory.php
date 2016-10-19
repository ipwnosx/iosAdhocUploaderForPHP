<?php

require_once(__DIR__ . "/../../common/MySmarty.php");
require_once(__DIR__ . "/../../common/DatabaseManager.php");
require_once(__DIR__ . "/../../common/Utility.php");
require_once(__DIR__ . "/../../common/Setting.php");

$smarty = new MySmarty();
$db = DatabaseManager::sharedInstance();


$data = $db->downloadSelectAll();

// ここで、User Agentを重複なきように調整
$condition = array();
for($i = 0; $i < count($data); $i++) {
    $row = $data[$i];

    $userAgent = $row["userAgent"];
    $deviceInfo = Utility::getDeviceInfoFromUserAgent($userAgent);

    // 絞り込み用のリスト
    if (!array_key_exists($userAgent, $condition)) {
        if ($deviceInfo['model'] === "") {
            $condition[$userAgent] = $userAgent;
        } else {
            $condition[$userAgent] = $deviceInfo['model'] . " - " . $userAgent;
        }
    }

    // デバイス名を取得
    $deviceName = $deviceInfo['model'] . '<br>' . $deviceInfo['ios'];
    $data[$i]["deviceName"] = $deviceName;
}

$smarty->assign("headerTitle", Setting::getTitle());
$smarty->assign("data", $data);
$smarty->assign("condition", $condition);
$smarty->display("admin/downloadhistory.tpl");