<?php

require_once(__DIR__ . "/../../common/MySmarty.php");
require_once(__DIR__ . "/../../common/Setting.php");
$smarty = new MySmarty();


// この画面のキャッシュを無効化する
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


//echo "<pre>";
//print_r($_POST);
//echo "</pre>";

$title = "";
if (isset($_POST['inputTitle'])) {
    $title = $_POST['inputTitle'];
}
$notice = "";
if (isset($_POST['inputNotice'])) {
    $notice = $_POST['inputNotice'];
}
$slackApiUrl = "";
if (isset($_POST['inputSlackApiUrl'])) {
    $slackApiUrl = $_POST['inputSlackApiUrl'];
}
$slackChannel = "";
if (isset($_POST['inputChannel'])) {
    $slackChannel = $_POST['inputChannel'];
}
$slackTitle = "";
if (isset($_POST['inputSlackTitle'])) {
    $slackTitle = $_POST['inputSlackTitle'];
}
$slackIconImage = "";
if (isset($_POST['inputIconImage'])) {
    $slackIconImage = $_POST['inputIconImage'];
}
$slackIconEmoji = "";
if (isset($_POST['inputIconEmoji'])) {
    $slackIconEmoji = $_POST['inputIconEmoji'];
}


$exception = "";
$isSuccess = true;

try {

    $setting = Array(
        'title' => $title,
        'notice' => $notice,
        'slack' => Array(
            Array(
                'url' => $slackApiUrl,
                'channel' => $slackChannel,
                'username' => $slackTitle,
                'icon_url' => $slackIconImage,
                'icon_emoji' => $slackIconEmoji
            )
        )
    );

    $json = json_encode($setting, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if (!is_null($json)) {
        $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');

        $filename = __DIR__  . '/../../data/setting.json';

        if (!is_writable($filename) || !is_writable(dirname($filename))) {
            throw new RuntimeException('設定ファイルの書き込みに失敗しました');
        }

        if (!$handle = fopen($filename, 'w')) {
            throw new RuntimeException('設定ファイルの書き込みに失敗しました');
        }
        if (fwrite($handle, $json) === FALSE) {
            throw new RuntimeException('設定ファイルの書き込みに失敗しました');
        }
        fclose($handle);

    } else {
        throw new RuntimeException('設定ファイルの書き込みに失敗しました');
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