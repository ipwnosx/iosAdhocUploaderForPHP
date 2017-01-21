<?php

require_once(__DIR__ . "/../../common/DatabaseManager.php");
require_once(__DIR__ . "/../../common/Utility.php");
require_once(__DIR__ . "/../../common/IpaAnalysis.php");
require_once(__DIR__ . "/../../common/PlistFile.php");
require_once(__DIR__ . "/../../common/Setting.php");

$db = DatabaseManager::sharedInstance();
$ipaInfoMessage = '';



//echo "<pre>";
//print_r($_POST);
//print_r($_FILES);
//echo "</pre>";

function reArrayFiles(&$file_post)
{
    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i = 0; $i < $file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}


$title = "";
if (isset($_POST['title'])) {
    $title = $_POST['title'];
}
$notes = "";
if (isset($_POST['notes'])) {
    $notes = $_POST['notes'];
}
$developerNotes = "";
if (isset($_POST['developerNotes'])) {
    $developerNotes = $_POST['developerNotes'];
}
$checkboxSlackNotificationFlag = false;
if (isset($_POST['slackNotificationFlag']) && $_POST['slackNotificationFlag'] === "on") {
    $checkboxSlackNotificationFlag = true;
}
$isHide = 0;
if (isset($_POST['isHide']) && $_POST['isHide'] === "1") {
    $isHide = 1;
}
$insertIdNumber = "";

$fileNameArray = array();


$exception = "";
$id = "";
$isRollBack = false;
$isTransaction = false;


//　plistに記述する絶対url の作成（自動生成するようになったので無効化）
//$url = Utility::getUrlDirectoryName($_SERVER);

try {


    if ($title == "") {
        throw new RuntimeException('タイトルが入力されていません');
    }


    if ($_FILES['file']) {
        $file_ary = reArrayFiles($_FILES['file']);

        foreach ($file_ary as $file) {

            if (!isset($file['error']) || !is_int($file['error'])) {
                throw new RuntimeException('パラメータが不正です');
            }

            switch ($file['error']) {
                case UPLOAD_ERR_OK: // OK
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException('ファイルが選択されていません');
                case UPLOAD_ERR_INI_SIZE:
                    throw new RuntimeException('php.ini定義の最大サイズ超過');
                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException('フォーム定義の最大サイズ超過');
                case UPLOAD_ERR_PARTIAL:
                    throw new RuntimeException('ファイルの一部しかアップロードされていません');
                default:
                    throw new RuntimeException('その他のエラーが発生しました');
            }

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            if (!$ext = array_search(
                $finfo->file($file['tmp_name']),
                array(
                    'ipa' => 'application/zip',
                    'plist' => 'application/xml',
                ),
                true
            )
            ) {
                throw new RuntimeException('ファイル形式が不正です');
            } else {

                $fileInfo = new SplFileInfo($file['name']);
                if ($ext === "ipa" && $ext === $fileInfo->getExtension()) {

                    // ファイルのハッシュではなく、ランダム値をファイル名にする
                    $fileNameArray['ipa'] = array("name" => $file['name'], "tmpName" => $file['tmp_name'], "hashName" => sha1((uniqid(rand(), 1))));

                } else {
                    throw new RuntimeException('ファイル形式が不正です');
                }
            }

        }

        if (!isset($fileNameArray['ipa'])) {
            throw new RuntimeException('入力ファイルが不正です');
        }


        // ライブラリが binaryPlist に対応しているので、OS関係なく実行が可能
        $ipa = new IpaAnalysis($fileNameArray['ipa']['tmpName']);
        $infoPlistArrayAndXml = $ipa->getInfoPlistArrayAndXml();
        if (array_key_exists('CFBundleIdentifier', $infoPlistArrayAndXml['array']) &&
            array_key_exists('CFBundleShortVersionString', $infoPlistArrayAndXml['array']) &&
            array_key_exists('CFBundleVersion', $infoPlistArrayAndXml['array'])
        ) {
            $bundleIdentifier = $infoPlistArrayAndXml['array']['CFBundleIdentifier'];
            $bundleVersion = $infoPlistArrayAndXml['array']['CFBundleShortVersionString'];
            $bundleBuild = $infoPlistArrayAndXml['array']['CFBundleVersion'];

            // 最後に アプリ情報を表示るすための情報を整形
            $ipaInfoMessage = '<br><br>Bundle Identifier : ' . $bundleIdentifier . '<br>Version : ' . $bundleVersion . '<br>Build : ' . $bundleBuild;
        }
        $mobileprovisionArrayAndXml = $ipa->getMobileprovisionArrayAndXml();
        if (array_key_exists('ExpirationDate', $mobileprovisionArrayAndXml['array'])) {
            $expirationDate = date('Y-m-d H:i:s', $mobileprovisionArrayAndXml['array']['ExpirationDate']);
        }

        // DB
        $db->query("BEGIN;");
        $isTransaction = true;

        $directoryHash = "";
        for ($i = 0; $i < 3; $i++) {
            $directoryHash = hash('sha256', (uniqid(rand(), 1)));
            $result = $db->adhocInsert(
                $title,
                $directoryHash,
                $fileNameArray['ipa']['name'],
                $fileNameArray['ipa']['hashName'],
                $notes,
                $developerNotes,
                $bundleIdentifier,
                $bundleVersion,
                $bundleBuild,
                $infoPlistArrayAndXml['xml'],
                0,
                $mobileprovisionArrayAndXml['xml'],
                $expirationDate);
            $insertIdNumber = $result;

            if ($result === false) {
                if ($i == 2) {
                    // 最後のループでもインサートを失敗したらもうあきらめる
                    throw new RuntimeException('DBのインサート時に問題が発生しました');
                }
            } else {
                // 成功したら、ループ処理を終了
                break;
            }
        }


        $dataDirectory = '../../data/app/' . $directoryHash . '/';

        if (!file_exists($dataDirectory)) {
            if (!mkdir($dataDirectory, 0755, TRUE)) {
                throw new RuntimeException('アプリディレクトリの作成に失敗しました');
            }
        }

        // 保存ファイル名をハッシュ化するのは、意図しない階層に保存されるのを防止するため
        // ディレクトリトラバーサル対応
        foreach ($fileNameArray as $fileName) {
            $path = $dataDirectory . $fileName['hashName'];
            $path = mb_convert_encoding($path, "SJIS", "AUTO");
            if ($fileName['tmpName'] != "") {
                if (!move_uploaded_file($fileName['tmpName'], $path)) {
                    throw new RuntimeException('ファイル保存時にエラーが発生しました');
                }
            }
            chmod($path, 0644);

        }

    }


} catch (RuntimeException $e) {

    if ($isTransaction) {
        $db->query("ROLLBACK;");
        $isRollBack = true;
        $isTransaction = false;
    }

    $exception = $e->getMessage();

}


$uploadResult = "";
$uploadMessage = "";

if (!$isRollBack && $isTransaction) {
    $db->query("COMMIT;");
    $isTransaction = false;

    $uploadResult = "アップロード成功";
    $uploadMessage = $ipaInfoMessage;
} else {
    $uploadResult = "アップロード失敗";
    $uploadMessage = $exception;
}



// Slack に通知するかどうか
if ($checkboxSlackNotificationFlag) {

    // Slack 用のリンクに置換する
    preg_match_all('#<.*?a.*?href.*?=.*?"(.*?)".*?>(.*?)<.*?/.*?a.*?>#', $notes, $notedRegMatch);
    if (count($notedRegMatch) === 3 && count($notedRegMatch[0]) === count($notedRegMatch[1]) && count($notedRegMatch[0]) === count($notedRegMatch[2])) {
        for($i = 0; $i < count($notedRegMatch[0]); $i++) {
            $notes = str_replace($notedRegMatch[0][$i], "<" . $notedRegMatch[1][$i] . "|" . $notedRegMatch[2][$i] . ">", $notes);
        }
    }

    $slackString = "ID：" . $insertIdNumber . "\nタイトル：" . $title . "\n詳細：\n" . $notes;

    $slaks = Setting::getSlackSetting();
    foreach($slaks as $slack) {
        Utility::sendSlack($slack['url'], $slack['channel'], $slack['username'], $slackString, $slack['icon_url'], $slack['icon_emoji']);
    }
}


// JSONレスポンスを返す
//$entries = Array(
//    'result' => $uploadResult,
//    'message'  => $uploadMessage,
//);
//header("Content-Type: application/json; charset=utf-8");
//header('X-Content-Type-Options: nosniff');
//$json =  json_encode($entries, JSON_PRETTY_PRINT);

// plain text データを返す
header("Content-Type: text/plain; charset=utf-8");

echo "result : " . $uploadResult . "\nmessage : " . $uploadMessage;