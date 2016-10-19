#!/bin/sh -eu

# 管理者権限を要求
sudo ls &>/dev/null

# 基本設定の確認
pushd /System/Library/OpenSSL

grep -E "^dir" ./openssl.cnf | grep -c "./demoCA"
grep "CATOP" ./misc/CA.sh | grep "./demoCA"
grep "CAKEY" ./misc/CA.sh | grep "./cakey.pem"
grep "CAREQ" ./misc/CA.sh | grep "./careq.pem"
grep "CACERT" ./misc/CA.sh | grep "./cacert.pem"

popd


# 作業用ディレクトリを作成
SSL_TMP_DIR=~/ssl_`date '+%Y%m%d%H%M%S'`
mkdir $SSL_TMP_DIR
pushd $SSL_TMP_DIR

#------------------------------------------------------------
# 認証局の作成
# CA certificate filename (or enter to create)
### Enter PEM pass phrase:
### Verifying - Enter PEM pass phrase:
# Country Name (2 letter code) [AU]:
# State or Province Name (full name) [Some-State]:
# Locality Name (eg, city) []:
# Organization Name (eg, company) [Internet Widgits Pty Ltd]:
# Organizational Unit Name (eg, section) []:
# Common Name (e.g. server FQDN or YOUR name) []:
# Email Address []:
# A challenge password []:
# An optional company name []:
### Enter pass phrase for ./demoCA/private/./cakey.pem:
#------------------------------------------------------------
echo "++++++++++++++++++++++++++++++"
echo "認証局の作成 開始"
echo "++++++++++++++++++++++++++++++"
sudo /System/Library/OpenSSL/misc/CA.sh -newca <<EOF

JP
Tokyo
Shinjuku-ku
HOGE Co.,Ltd.
HOGE Group
192.168.0.1



EOF
echo "++++++++++++++++++++++++++++++"
echo "認証局の作成 完了"
echo "++++++++++++++++++++++++++++++"


# 秘密鍵の作成
echo "++++++++++++++++++++++++++++++"
echo "秘密鍵の作成 開始"
echo "++++++++++++++++++++++++++++++"
# sudo openssl genrsa -des3 -out server.key 2048
# パスワードなしで作成
sudo openssl genrsa -out server.key 2048
echo "++++++++++++++++++++++++++++++"
echo "秘密鍵の作成 完了"
echo "++++++++++++++++++++++++++++++"
# # サーバー起動時に毎回パスフレーズを尋ねられるのでプロテクトを解除
# echo "++++++++++++++++++++++++++++++"
# echo "サーバー起動時に毎回パスフレーズを尋ねられるのでプロテクトを解除 開始"
# echo "++++++++++++++++++++++++++++++"
# sudo openssl rsa -in server.key -out server.key
# echo "++++++++++++++++++++++++++++++"
# echo "サーバー起動時に毎回パスフレーズを尋ねられるのでプロテクトを解除 完了"
# echo "++++++++++++++++++++++++++++++"


# 証明書の作成
#------------------------------------------------------------
## 証明書を作成するために署名要求ファイル (.csr) を作成
# Country Name (2 letter code) [AU]:
# State or Province Name (full name) [Some-State]:
# Locality Name (eg, city) []:
# Organization Name (eg, company) [Internet Widgits Pty Ltd]:
# Organizational Unit Name (eg, section) []:
# Common Name (e.g. server FQDN or YOUR name) []:
# Email Address []:
# A challenge password []:
# An optional company name []:
#------------------------------------------------------------
echo "++++++++++++++++++++++++++++++"
echo "証明書を作成するために署名要求ファイル (.csr) を作成 開始"
echo "++++++++++++++++++++++++++++++"
sudo openssl req -new -days 3650 -key server.key -out server.csr <<EOF
JP
Tokyo
Shinjuku-ku
HOGE Co.,Ltd.
HOGE Group
192.168.0.1



EOF
echo "++++++++++++++++++++++++++++++"
echo "証明書を作成するために署名要求ファイル (.csr) を作成 終了"
echo "++++++++++++++++++++++++++++++"
## 証明書を作成
echo "++++++++++++++++++++++++++++++"
echo "証明書を作成 開始"
echo "++++++++++++++++++++++++++++++"
set +e
sudo openssl ca -in server.csr -keyfile demoCA/private/cakey.pem -out server.crt
STATUS_CODE=$?
set -e
if [ "$STATUS_CODE" -ne 0 ]; then
    sudo rm -f demoCA/index.txt
    sudo touch demoCA/index.txt
    sudo openssl ca -in server.csr -keyfile demoCA/private/cakey.pem -out server.crt
fi
echo "++++++++++++++++++++++++++++++"
echo "証明書を作成 終了"
echo "++++++++++++++++++++++++++++++"

popd


# ios機器に証明書をインストールさせるために形式を der に変更
pushd ${SSL_TMP_DIR}/demoCA

sudo openssl x509 -in cacert.pem -outform DER -out cacert.der

popd


# 証明書の確認
echo
echo
echo
echo "++++++++++++++++++++++++++++++"
echo "証明書の確認 開始"
echo "++++++++++++++++++++++++++++++"
echo
echo "証明書ファイルの内容を確認 crt"
openssl x509 -text -noout -in ${SSL_TMP_DIR}/server.crt
echo
echo "証明書の有効期限を確認 crt"
openssl x509 -in ${SSL_TMP_DIR}/server.crt -noout -dates

echo
echo "証明書の確認 pem"
openssl x509 -in ${SSL_TMP_DIR}/demoCA/cacert.pem -text
echo
echo "証明書の有効期限を確認 crt"
openssl x509 -in ${SSL_TMP_DIR}/demoCA/cacert.pem -noout -dates

echo
echo "秘密鍵ファイルの内容を確認 key"
openssl rsa -text -noout -in ${SSL_TMP_DIR}/server.key
echo
echo "秘密鍵が正しいかどうか確認する key"
openssl rsa -in ${SSL_TMP_DIR}/server.key -check -noout

echo
echo "CSRファイルの内容を確認(証明書署名要求 ) csr"
openssl req -text -noout -in ${SSL_TMP_DIR}/server.csr
echo "++++++++++++++++++++++++++++++"
echo "証明書の確認 終了"
echo "++++++++++++++++++++++++++++++"


# 出力
echo
echo "++++++++++++++++++++++++++++++"
echo "出力情報 開始"
echo "++++++++++++++++++++++++++++++"
echo "サーバー証明書 : ${SSL_TMP_DIR}/server.crt"
echo "サーバー証明書秘密鍵 : ${SSL_TMP_DIR}/server.key"
echo "ios等にダウンロードさせる証明書 : ${SSL_TMP_DIR}/demoCA/cacert.der"
echo "++++++++++++++++++++++++++++++"
echo "出力情報 終了"
echo "++++++++++++++++++++++++++++++"