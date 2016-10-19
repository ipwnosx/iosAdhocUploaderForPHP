<?php


require_once(__DIR__ . '/../library/CFPropertyList/CFPropertyList.php');

class IpaAnalysis
{

    private $ipaFilePath;

    /*
     * $ipaFilePath : ipa ファイルのファイルパスを指定する
     *
     * 上記２つのいずれかの ファイル/ディレクトリが存在しない場合は例外が発生する
     */
    public function __construct($ipaFilePath)
    {
        $this->ipaFilePath = $ipaFilePath;

        if (!file_exists($this->ipaFilePath)) {
            throw new RuntimeException('ipa ファイルが存在しません');
        }
    }

    public function __destruct()
    {
    }


    /**
     * ipa ファイルに含まれている Info.plist の内容を配列で返す
     *
     * 取得に失敗したら nil が返る
     */
    public function getInfoPlistArrayAndXml()
    {

        // Info.plist ファイルのデータ
        $infoPlistData = '';


        // ipa ファイルはzipなので、zipファイルを open する
        $zip = new ZipArchive();
        if ($zip->open($this->ipaFilePath) === TRUE) {

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);

                // Payload/アプリ名.app/Info.plist
                if (count(explode('/', $filename)) === 3 && preg_match('/Info\.plist$/', $filename) === 1) {
                    // ファイル名から Info.plist の内容を取り出す
                    $infoPlistData = $zip->getFromName($filename);
                    break;
                }
            }

            // $zip を close する
            $zip->close();

        }

        $plist = new CFPropertyList\CFPropertyList;
        $plist->parse($infoPlistData);
        $xml = Array(
            'array' => $plist->toArray(),
            'xml' => $plist->toXML(true)
        );

        return $xml;
    }


    /**
     * @param $xml plistのXML文字列
     * @return mixed　配列
     * @throws DOMException
     * @throws \CFPropertyList\IOException
     * @throws \CFPropertyList\PListException
     */
    static public function getInfoPlistArray($xml) {

        $plist = new CFPropertyList\CFPropertyList;
        $plist->parse($xml);
        $array = $plist->toArray();

        return $array;
    }

}