<?php

// plist に書いてある、URLはアップロード時にはわからないので書き換える

class PlistFile
{

    public static function changeUrl($fileName, $url)
    {
        $xml = simplexml_load_file($fileName);

        if ($xml) {

            $resultXml = PlistFile::rewriteUrl($xml, $url);

            // 変更内容で上書き
            $resultXml->asXML($fileName);

        } else {
            throw new RuntimeException('plist が読み込めませんでした<br>');
        }
    }


    public static function createPlistFile($savefileName, $url, $bundleIdentifier, $bundleVersion)
    {
        $xml = simplexml_load_string(PlistFile::templatePlist());

        if ($xml) {

            $resultXml = PlistFile::rewriteUrl($xml, $url);
            $resultXml = PlistFile::rewriteBundleIdentifier($resultXml, $bundleIdentifier);
            $resultXml = PlistFile::rewriteBundleVersion($resultXml, $bundleVersion);

            // 変更内容で上書き
            $resultXml->asXML($savefileName);

        } else {
            throw new RuntimeException('plist が読み込めませんでした<br>');
        }
    }

    public static function createPlistString($url, $bundleIdentifier, $bundleVersion)
    {
        $xml = simplexml_load_string(PlistFile::templatePlist());

        if ($xml) {

            $resultXml = PlistFile::rewriteUrl($xml, $url);
            $resultXml = PlistFile::rewriteBundleIdentifier($resultXml, $bundleIdentifier);
            $resultXml = PlistFile::rewriteBundleVersion($resultXml, $bundleVersion);
            $result = $resultXml->asXML();

        } else {
            throw new RuntimeException('plist が読み込めませんでした<br>');
        }

        return $result;
    }


    private static function rewriteUrl($xml, $url) {

        for ($i = 0, $ic = count($xml->dict->array->dict->array->dict); $i < $ic; $i++) {
            $dictElement = $xml->dict->array->dict->array->dict[$i];
            $softwarePackage = false;

            if (count($dictElement->string) >= 2 && count($dictElement->key) >= 2) {
                // software-package の文字列があればフラグを変える
                for ($j = 0, $jc = count($dictElement->string); $j < $jc; $j++) {
                    if ($dictElement->key[$j] == "kind" && $dictElement->string[$j] == "software-package") {
                        $softwarePackage = true;
                    }
                }

                // フラグが立っていれば、key == url 番目 の添え字の string を書き換える
                if ($softwarePackage) {
                    for ($j = 0, $jc = count($dictElement->string); $j < $jc; $j++) {
                        if ($dictElement->key[$j] == "url") {
                            // url の書き換え
                            $dictElement->string[$j] = $url;
                        }
                    }
                }
            }

        }

        return $xml;
    }


    private static function rewriteBundleIdentifier($xml, $bundleIdentifier) {

        for ($i = 0, $ic = count($xml->dict->array->dict->dict); $i < $ic; $i++) {
            $dictElement = $xml->dict->array->dict->dict[$i];

            if (count($dictElement->string) >= 4 && count($dictElement->key) >= 4) {

                for ($j = 0, $jc = count($dictElement->string); $j < $jc; $j++) {
                    if ($dictElement->key[$j] == "bundle-identifier" && $dictElement->string[$j] == "com.example.dummy") {
                        // bundle-identifier の書き換え
                        $dictElement->string[$j] = $bundleIdentifier;
                    }
                }

            }
        }

        return $xml;
    }


    private static function rewriteBundleVersion($xml, $bundleVersion) {

        for ($i = 0, $ic = count($xml->dict->array->dict->dict); $i < $ic; $i++) {
            $dictElement = $xml->dict->array->dict->dict[$i];

            if (count($dictElement->string) >= 4 && count($dictElement->key) >= 4) {

                for ($j = 0, $jc = count($dictElement->string); $j < $jc; $j++) {
                    if ($dictElement->key[$j] == "bundle-version" && $dictElement->string[$j] == "1.0") {
                        // bundle-version の書き換え
                        $dictElement->string[$j] = $bundleVersion;
                    }
                }

            }
        }

        return $xml;
    }


    private static function rewriteTitle($xml, $title) {

        for ($i = 0, $ic = count($xml->dict->array->dict->dict); $i < $ic; $i++) {
            $dictElement = $xml->dict->array->dict->dict[$i];

            if (count($dictElement->string) >= 4 && count($dictElement->key) >= 4) {

                for ($j = 0, $jc = count($dictElement->string); $j < $jc; $j++) {
                    if ($dictElement->key[$j] == "title" && $dictElement->string[$j] == "AdHoc アプリ") {
                        // title の書き換え
                        $dictElement->string[$j] = $title;
                    }
                }

            }
        }

        return $xml;
    }



    public static function templatePlist() {

        // Nowdoc php 5.3 以降
        $result = <<< 'EOT'
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
	<key>items</key>
	<array>
		<dict>
			<key>assets</key>
			<array>
				<dict>
					<key>kind</key>
					<string>software-package</string>
					<key>url</key>
					<string>http://example.com</string>
				</dict>
			</array>
			<key>metadata</key>
			<dict>
				<key>bundle-identifier</key>
				<string>com.example.dummy</string>
				<key>bundle-version</key>
				<string>1.0</string>
				<key>kind</key>
				<string>software</string>
				<key>title</key>
				<string>AdHoc アプリ</string>
			</dict>
		</dict>
	</array>
</dict>
</plist>
EOT;

        return $result;

    }

} 