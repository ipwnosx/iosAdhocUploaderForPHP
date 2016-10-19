<?php

require_once(__DIR__ . "/../common/DatabaseManager.php");
require_once(__DIR__ . "/../common/Utility.php");
require_once(__DIR__ . "/../common/PlistFile.php");
$db = DatabaseManager::sharedInstance();


$requestUri = explode("/", $_SERVER['REQUEST_URI']);
$scriptName = explode("/", $_SERVER['SCRIPT_NAME']);
$url = Utility::getUrlDirectoryName($_SERVER);

// 共通する要素(=routerまでのpath)を取り除いた配列を作る。
foreach ($scriptName as $key => $value) {
    if ($value == $requestUri[$key]) {
        unset($requestUri[$key]);
    }
}
$requestArgs = array_values($requestUri);


$result = count($requestArgs) == 3;
if ($result) {
    $result |= $requestArgs[0] === "plist";
    $result |= $requestArgs[0] === "ipa";
} else {
    exit;
}

$directory = "";
if (preg_match('/[0-9A-Fa-f]{64}/', $requestArgs[1]) === 1) {
    $directory = substr($requestArgs[1], 0, 64);
} else {
    exit;
}

$pc =  $requestArgs[2];

if ($result) {

    if ($requestArgs[0] === "plist") {
        $data = $db->adhocSelect($directory);
        if ($data !== False) {
            $bundleIdentifier = $data["ipaBundleIdentifier"];
            $bundleVersion = $data["ipaVersion"];
            $plistString = PlistFile::createPlistString($url . '/dl.php/ipa/' . $directory . '/0', $bundleIdentifier, $bundleVersion);
            header('Content-Description: File Transfer');
            header('Content-Type: application/x-plist');
            header('Content-Disposition: attachment; filename=plist.plist');

            echo $plistString;
        }

    } else if ($requestArgs[0] === "ipa") {

        $data = $db->adhocSelect($directory);
        $file = "";
        $fileName = "";
        if ($data !== False) {
            $file = $data["ipaTmpHash"];
            $fileName = $data["ipa"];
        }

        if ($pc === "1" && $fileName !== '') {
            // タイトルの最後に .ipa があれば付けない
            if (preg_match('/\.ipa$/', $fileName) !== 1) {
                $fileName .= '.ipa';
            }
            downloadFileNameHeader($fileName);
        }

        $filePath = __DIR__ . '/../data/app/' . $directory . '/' . $file;
        if (file_exists($filePath)) {

            header('Content-Description: File Transfer');
            header('Content-Type: application/zip');

            readfile($filePath);

            // HEAD で情報を確認しに来た後で、GET が来るので、
            // GET のみ記録
            if ($_SERVER["REQUEST_METHOD"] == "GET") {

                // DB の　ダウンロードテーブルに書き込む
                $userAgent = $_SERVER['HTTP_USER_AGENT'];
                $ipAddress = $_SERVER['REMOTE_ADDR'];

                // 0 : AdHocダウンロード
                // 1 : 直接ダウンロード
                $deviceName = '';
                $downloadType = 1;
                if (preg_match('/itunesstored/', $userAgent) === 1) {
                    $downloadType = 0;
                }


                $db->query("BEGIN;");
                $result = $db->downloadInsert($directory, $file, $userAgent, $downloadType, $ipAddress);

                if ($result === false) {
                    $db->query("ROLLBACK;");
                } else {
                    $db->query("COMMIT;");
                }

            }
        }

    }

}

exit;


/**
 * PC版でブラウザからファイルをDLする時に、ブラウザ毎にファイル名の文字化け対策を行う
 * @param $fileName ファイル名
 */
function downloadFileNameHeader($fileName)
{

    if ($fileName !== '') {
        $output_filename = $fileName;

        // http://qiita.com/mpyw/items/3838819d4af75c84b564
        $pattern = '/Chrome|Firefox|(Opera)|(Edge|MSIE|IEMobile|Trident)|(Safari)/';

        $output_filename = mb_convert_encoding(
            $output_filename,
            'UTF-8',
            'ASCII,JIS,UTF-8,CP51932,SJIS-win'
        );
        switch (true) {
            case !isset($_SERVER['HTTP_USER_AGENT']):
            case !preg_match($pattern, $_SERVER['HTTP_USER_AGENT'], $matches):
            case !isset($matches[1]):
                $enc = '=?utf-8?B?' . base64_encode($output_filename) . '?=';
                header('Content-Disposition: attachment; filename="' . $enc . '"');
                break;
            case !isset($matches[2]):
                $enc = "utf-8'ja'" . urlencode($output_filename);
                header('Content-Disposition: attachment; filename*=' . $enc);
                break;
            case !isset($matches[3]):
                $enc = urlencode($output_filename);
                header('Content-Disposition: attachment; filename="' . $enc . '"');
                break;
            default:
                header('Content-Disposition: attachment; filename="' . $output_filename . '"');
        }
    }

}
