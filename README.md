# Apple の Adhoc ビルドしたファイルを配布するサイト

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/npoyu/iosAdhocUploaderForPHP/badges/quality-score.png?b=master&v1)](https://scrutinizer-ci.com/g/npoyu/iosAdhocUploaderForPHP/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/npoyu/iosAdhocUploaderForPHP/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/npoyu/iosAdhocUploaderForPHP/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/npoyu/iosAdhocUploaderForPHP/badges/build.png?b=master)](https://scrutinizer-ci.com/g/npoyu/iosAdhocUploaderForPHP/build-status/master)

### Note:
クローズドな環境でテストを行うために作成

外部公開する場合は無料のサービスを利用すべき


#### PHP バージョン 5.4.0以降に対応

#### 使用ライブラリ等
* [CSS/Javascript] Bootstrap v3.1.1
* [Javascript] jQuery v2.1.1
* [php] Smarty 3.1.27
* [php] Mobile Detect 2.8.17
* [php] rodneyrehm/CFPropertyList v2.0.1


#### Mac に設定する際の手順
* php.ini のタイムゾーンを設定する
  * date.timezone = Asia/Tokyo

* デフォルトのアップロード容量は数十MBなので、数百MBアップロードできるように2GBを設定
  * memory_limit = 2G
  * post_max_size = 2G
  * upload_max_filesize = 2G

* SSLの証明書は運用するIPアドレスにすること（自己証明でも！下記参照）
  * [https://gist.github.com/ngyoi/7ee01562a2188e1e63ce9557321fbb17](https://gist.github.com/ngyoi/7ee01562a2188e1e63ce9557321fbb17)


#### アクセス権限の設定等

推奨のアクセス権は下記の通りです

````
# mac の apache の実行プロセスは _www:_www（各環境で調べる）
sudo chown -R _www:_www
sudo chmod -R 755 ./
````

* ユーザーからアクセス出来る用に apache nginx 等で、data フォルダー配下を設定する
* list.dbファイルを作成するのに、apache等の書き込み権限を設定
* app フォルダ以下に、フォルダとファイルを作成するために、apache等の書き込み権限を設定



jenkinsuploadapi.php に対してアップロードするための curl コマンドサンプル

````
curl\
 -F "title=テストタイトルです" \
 -F "notes=本文" \
 -F "developerNotes=開発者様コメント" \
 -F "slackNotificationFlag=off" \
 -F "file[]=@path.ipa" \
 -F "isHide=1" \
https://url/public_html/admin/jenkinsuploadapi.php
````


####実際のサンプルJenkins設定サンプル
````
# 成功したときだけ iOS AdHoc サイトに登録す
IPAPATH=`ls ${WORKSPACE}/build/*.ipa 2>&1`

COMMIT_USER=`git log -n 1 --pretty=format:"%an" 2>&1`
COMMIT_LOG=`git log -n 1 --pretty=format:"%s" 2>&1`
COMMIT_DATE=`git log -n 1 --date=iso --pretty=format:"%ad" 2>&1`
COMMIT_HASH=`git log -n 1 --pretty=format:"%H" 2>&1`

curl -k\
 -F "title=HOGE_" \
 -F "notes=Jenkinsからの自動登録処理です

ビルドモード : Release
ログ : 非表示" \
 -F "developerNotes=Jenkins情報です
${BUILD_DISPLAY_NAME}

ビルド情報

コミットユーザー : ${COMMIT_USER}
コミットログ : ${COMMIT_LOG}
コミット日時 : ${COMMIT_DATE}
コミットハッシュ : ${COMMIT_HASH}" \
 -F "slackNotificationFlag=off" \
 -F "file[]=@${IPAPATH}" \
 -F "isHide=1" \
https://url/admin/jenkinsuploadapi.php \
--verbose
````



MIT License
