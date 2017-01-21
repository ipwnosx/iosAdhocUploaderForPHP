<?php


require_once(__DIR__ . '/../library/CFPropertyList/CFPropertyList.php');

class IpaAnalysis
{

    private $ipaFilePath;
    private $infoPlistData;
    private $mobileprovisionPlistData;

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

        $this->ipaUnzip();
    }

    public function __destruct()
    {
    }


    /**
     * ipa ファイルの zip内のデータを取得します。
     *
     * Info.plist
     * embedded.mobileprovision
     *
     * 一度に上記２つのplistのデータを展開します
     */
    private function ipaUnzip()
    {
        // ipa ファイルはzipなので、zipファイルを open する
        $zip = new ZipArchive();
        if ($zip->open($this->ipaFilePath) === TRUE) {

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);

                // Payload/アプリ名.app/Info.plist
                if (count(explode('/', $filename)) === 3 && preg_match('/Info\.plist$/', $filename) === 1) {
                    // ファイル名から Info.plist の内容を取り出す
                    $this->infoPlistData = $zip->getFromName($filename);
                }

                // Payload/アプリ名.app/embedded.mobileprovision
                if (count(explode('/', $filename)) === 3 && preg_match('/embedded\.mobileprovision/', $filename) === 1) {
                    // ファイル名から embedded.mobileprovision の内容を取り出す
                    $mobileprovision = $zip->getFromName($filename);
                    preg_match('/(<\?xml version="1.0" encoding="UTF-8"\?>.*<\/plist>)/s', $mobileprovision, $matches);
                    $this->mobileprovisionPlistData = $matches[1];
                }
            }

            // $zip を close する
            $zip->close();

        }
    }

    /**
     * ipa ファイルに含まれている Info.plist の内容を配列で返す
     *
     * 取得に失敗したら nil が返る
     */
    public function getInfoPlistArrayAndXml()
    {
        $plist = new CFPropertyList\CFPropertyList;
        $plist->parse($this->infoPlistData);
        $xml = Array(
            'array' => $plist->toArray(),
            'xml' => $plist->toXML(true)
        );

        return $xml;
    }

    public function getMobileprovisionArrayAndXml()
    {
        $plist = new CFPropertyList\CFPropertyList;
        $plist->parse($this->mobileprovisionPlistData);
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
    static public function getArrayFromPlistString($xml)
    {

        $plist = new CFPropertyList\CFPropertyList;
        $plist->parse($xml);
        $array = $plist->toArray();

        return $array;
    }

}