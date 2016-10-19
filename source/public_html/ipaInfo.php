<?php

require_once(__DIR__ . "/../common/MySmarty.php");
require_once(__DIR__ . "/../common/DatabaseManager.php");
require_once(__DIR__ . "/../common/Utility.php");
require_once(__DIR__ . "/../common/IpaAnalysis.php");
$smarty = new MySmarty();
$db = DatabaseManager::sharedInstance();

$databaseData = null;
$unserializeData = null;

if (isset($_GET['i'])) {

    $hexString = $_GET['i'];

    if (preg_match('/[0-9A-Fa-f]{104}/', $hexString) === 1) {

        $directory = substr($hexString, 0 , 64);
        $file = substr($hexString, 64, 40);

        if ($directory !== '' && $file !== '') {
            $databaseData = $db->adhocSelect($directory, $file);
            $infoPlistXml = $databaseData['infoPlistXml'];
            $plistArray = IpaAnalysis::getInfoPlistArray($infoPlistXml);
        }
    }
}

$smarty->assign("database", $databaseData);
if ( !is_null($plistArray) ) {
    $smarty->assign("data", $plistArray);
}

//echo "<pre>";
//print_r($unserializeData);
//echo "</pre>";

$smarty->display("ipainfo.tpl");