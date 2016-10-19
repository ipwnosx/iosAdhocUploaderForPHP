<?php

require_once(__DIR__ . "/Setting.php");

class Setting
{

    private static function getSettingJsonFile()
    {
        return __DIR__ . '/../data/setting.json';
    }

    /**
     * 設定ファイルの情報を取得する
     * @return array|mixed　JSON_Decode した Object が返る
     * @throws Exception 処理中に発生した例外を投げる
     */
    private static function getSettingJsonFileDecodeObject()
    {
        if (version_compare(PHP_VERSION, '5.2.0') < 0) {
            // json_decode が対応していないので、データなしとして返す
            return Array();
        }

        $obj = Array();

        try {

            $jsonFile = Setting::getSettingJsonFile();

            if (file_exists($jsonFile)) {

                $handle = fopen($jsonFile, 'r');
                $json = fread($handle, filesize($jsonFile));
                fclose($handle);

                $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
                $obj = json_decode($json, true);
            }

        } catch (Exception $e) {
            throw $e;
        }

        return $obj;
    }


    public static function getTitle()
    {
        $result = "";

        $obj = Setting::getSettingJsonFileDecodeObject();
        if (!is_null($obj) && array_key_exists('title', $obj)) {

            $result = $obj['title'];

        }

        return $result;
    }

    public static function getSlackSetting()
    {

        $result = Array();

        $obj = Setting::getSettingJsonFileDecodeObject();
        if (!is_null($obj) && array_key_exists('slack', $obj)) {

            $slacks = $obj['slack'];
            foreach ($slacks as $slack) {

                if (array_key_exists('url', $slack) &&
                    array_key_exists('channel', $slack) &&
                    array_key_exists('username', $slack)
                ) {
                    $tmp = Array(
                        'url' => $slack['url'],
                        'channel' => $slack['channel'],
                        'username' => $slack['username'],
                    );
                } else {
                    continue;
                }

                $tmp['icon_url'] = "";
                if (array_key_exists('icon_url', $slack)) {
                    $tmp['icon_url'] = $slack['icon_url'];
                }

                $tmp['icon_emoji'] = "";
                if (array_key_exists('icon_emoji', $slack)) {
                    $tmp['icon_emoji'] = $slack['icon_emoji'];
                }

                if ($tmp['url'] != "" && $tmp['channel'] != "" && $tmp['username'] != "") {
                    $result[] = $tmp;
                }

            }

        }

        return $result;
    }

}