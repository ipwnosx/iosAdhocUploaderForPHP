<?php

require_once("../../common/DatabaseManager.php");
require_once("../../common/Utility.php");
$db = DatabaseManager::sharedInstance();


$fileBasePath = __DIR__ . '/../../data/app/';

// Ajax以外からのアクセスを遮断
$request = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) : '';
if($request === 'xmlhttprequest') {


    $directory = '';
    $file = '';
    if (isset($_POST['a'])) {

        $hexString = $_POST['a'];

        if (preg_match('/[0-9A-Fa-f]{64}/', $hexString) === 1) {

            $directory = substr($hexString, 0 , 64);

            $filePath = $fileBasePath . $directory;

            if (!file_exists($filePath)) {
                $directory = '';
                $file = '';
            }

        }
    }

    $result = "";

    $db->query("BEGIN;");
    $result = $db->adhocDelete($directory);
    if ($result === FALSE) {
        $db->query("ROLLBACK;");
        $result = "削除に失敗しました";
    } else {
        $db->query("COMMIT;");
        // ディレクトリの削除に失敗しても何もしない
        Utility::unlinkRecursive($fileBasePath . $directory, true);
        $result = "削除しました";
    }

    $a = array(
        'message' => $result
    );

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($a);

} else {
    exit;
}