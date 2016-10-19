<?php

class Utility {


    /**
     * 元URL　 http://localhost:63342/iosAdhocUploader/iosAdhocUploader/public_html/uploader.php
     * 取得URL http://localhost:63342/iosAdhocUploader/iosAdhocUploader/public_html
     *
     * @param $SERVER には、$_SERVER　の値を指定してください
     * @return string
     */
    public static function getUrlDirectoryName($SERVER) {

        # うまくいかない模様
//        $url = (empty($SERVER["HTTPS"]) ? "http://" : "https://") . $SERVER["HTTP_HOST"] . $SERVER["REQUEST_URI"];
//        $urlDirectoryName = pathinfo($url)['dirname'];


        // HTTP : NULL
        // HTTPS : on (string)
        // IISの場合は  HTTP : off (string)
        $url = (empty($SERVER["HTTPS"]) || strtolower($SERVER["HTTPS"]) === "off" ? "http://" : "https://") . $SERVER["HTTP_HOST"];


        $urlSplitArry = explode("/", $SERVER["SCRIPT_NAME"]);
        $urlUnion = "";
        for($i = 0; $i < (count($urlSplitArry) - 1); $i++) {

            if ($i > 0) {
                $urlUnion .= '/';
            }

            $urlUnion .= $urlSplitArry[$i];
        }
        $url .= $urlUnion;

        return $url;
    }



    // 完全に失敗無視しているんだが・・・
    // あとで失敗処理を追加する
    /**
     * Recursively delete a directory
     *
     * @param string $dir Directory name
     * @param boolean $deleteRootToo Delete specified top-level directory as well
     */
    public static function unlinkRecursive($dir, $deleteRootToo)
    {
        if(!$dh = @opendir($dir))
        {
            return;
        }
        while (false !== ($obj = readdir($dh)))
        {
            if($obj == '.' || $obj == '..')
            {
                continue;
            }

            if (!@unlink($dir . '/' . $obj))
            {
                unlinkRecursive($dir.'/'.$obj, true);
            }
        }

        closedir($dh);

        if ($deleteRootToo)
        {
            @rmdir($dir);
        }

        return;
    }


    /**
     * UserAgentから端末情報を取得する
     * @param $userAgent UserAgent を指定する
     * @return array 端末情報が返る
     */
    public static function getDeviceInfoFromUserAgent($userAgent) {

        $result = Array(
            'ios' => '',
            'model' => ''
        );

        $userAgentSplit = explode(' ', $userAgent);
        foreach ($userAgentSplit as $val) {
            if (preg_match('/^iOS\//', $val) === 1) {
                $result['ios'] = preg_replace('/^iOS\//', 'iOS : ', $val);
            }
            if (preg_match('/^model\//', $val) === 1) {
                $device = preg_replace('/^model\//', '', $val);
                $result['model'] = Utility::getDeviceNameFromModel($device);
            }
        }

        return $result;
    }

    /**
     * モデル番号から端末名を取得する
     * @param $model UserAgent に含まれる モデル番号を指定
     * @return string 該当する端末名が返る
     */
    public static function getDeviceNameFromModel($model) {

        // http://www.enterpriseios.com/wiki/iOS_Devices
        // 上記のサイトをスクレイピングして、JSONでデータを返すようにしたAPIが下記
        // https://study.toarupc.com/iosinfo.php

        $result = '';
        if (version_compare(PHP_VERSION, '5.2.0') < 0) {
            // json_decode が対応していないので、データなしとして返す
            return $result;
        }

        $obj = Array();
        try {
            $jsonFile = __DIR__ . "/../data/devices.json";
            if (file_exists($jsonFile)) {
                $handle = fopen($jsonFile, 'r');
                $json = fread($handle, filesize($jsonFile));
                fclose($handle);

                $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
                $obj = json_decode($json, true);
            }
        } catch (Exception $e) {
        }

        if (!is_null($obj) && array_key_exists('devices', $obj)) {
            $devices = $obj['devices'];
            foreach ($devices as $device) {
                if ($device["identifier"] === $model) {
                    $result = $device["friendlyName"];
                    break;
                }
            }
        }

        return $result;
    }


    public static function sendSlack($url, $channel, $username, $text, $icon_url, $icon_emoji) {

        if (version_compare(PHP_VERSION, '5.2.0') < 0) {
            // json_decode が対応していないので、処理終了
            return;
        }

        try {

            $payload = array();

            if (
                !is_null($channel) && $channel !== "" &&
                !is_null($username) && $username !== "" &&
                !is_null($text) && $text !== ""
            ) {
                $payload += array(
                    'channel' => $channel,
                    'username' => $username,
                    'text' => $text,
                );
            }
            else {
                return;
            }


            if (!is_null($icon_url) && $icon_url !== "") {
                $payload += array('icon_url' => $icon_url);
            }

            if (!is_null($icon_emoji) && $icon_emoji !== "") {
                $payload += array('icon_emoji' => $icon_emoji);
            }

            $content = json_encode($payload);
            $content = str_replace("\\\\n", "\\n", $content);
            $options = array(
                'http' => array(
                    'method' => 'POST',
                    'content' => $content,
                    'header' => 'Content-type: application/json',
                )
            );

            file_get_contents($url, false, stream_context_create($options));

        } catch (Exception $e) {

        }
    }
} 