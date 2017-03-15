<!DOCTYPE html>
<html>
<head lang="jp">
    <meta charset="UTF-8">
    <title>iOS AdHoc List</title>

    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="./css/baseEdit.css">

    <script type="text/javascript" src="./js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="./js/bootstrap.min.js"></script>
</head>
<body>


<div class="viewForm">

    <div>アプリ概要</div>
    <table class="table table-striped table-bordered table-hover text-center">
        <tbody>
        <tr><td>id</td><td>{$database.id}</td></tr>
        <tr><td style="vertical-align: middle;">タイトル</td><td style="vertical-align: middle;">{$database.title}</td></tr>
        <tr><td style="vertical-align: middle;">ipaファイル名</td><td style="vertical-align: middle;">{$database.ipa}</td></tr>
        <tr><td style="vertical-align: middle;">背景無効色</td><td style="vertical-align: middle;">{if $d.isInvalidBackground === 1}無効{else}有効{/if}</td></tr>
        <tr><td style="vertical-align: middle;">登録日時 (JST)</td><td style="vertical-align: middle;">{$database.createDate}</td></tr>
        </tbody>
    </table>

    <div>アプリ情報</div>
    <table class="table table-striped table-bordered table-hover text-center">
        <tbody>
        <tr><td>Bundle Identifier</td><td>{$data.CFBundleIdentifier}</td></tr>
        <tr><td>Version</td><td>{$data.CFBundleShortVersionString}</td></tr>
        <tr><td>Build</td><td>{$data.CFBundleVersion}</td></tr>
        <tr><td>デフォルト言語</td><td>{$data.CFBundleDevelopmentRegion|default:'不明'}</td></tr>
        <tr><td>最低実行 iOSバージョン</td><td>{$data.MinimumOSVersion|default:'不明'}</td></tr>
        <tr>
            <td>ビルド時のXcodeバージョン</td>
            <td>
                {if isset($data.DTXcode)}
                    {$data.DTXcode|string_format:"%d"/100|string_format:"%.2f"}
                {else}
                    不明
                {/if}
            </td>
        </tr>
        <tr>
            <td>対応デバイス</td>
            <td>
                {foreach $data.UIDeviceFamily as $c}
                    {$c|default:'不明'|replace:"1":"iPhone と iPad で実行可能"|replace:"2":"iPadのみ実行可能"}<br>
                {/foreach}
            </td>
        </tr>
        <tr>
            <td style="vertical-align: middle;">対応アーキテクチャ<br>使用機能</td>
            <td style="vertical-align: middle;">
                {foreach $data.UIRequiredDeviceCapabilities as $c}
                    {$c|default:'不明'}<br>
                {/foreach}
            </td>
        </tr>
        <tr>
            <td style="vertical-align: middle;">iPhone 画面回転方向</td>
            <td style="vertical-align: middle;">
                {if isset($data.UISupportedInterfaceOrientations)}
                    {foreach $data.UISupportedInterfaceOrientations as $c}
                        {$c|default:'不明'|replace:"UIInterfaceOrientationPortrait":"縦画面 : ホームボタン下"|replace:"UIInterfaceOrientationPortraitUpsideDown":"縦画面 : ホームボタン上"|replace:"UIInterfaceOrientationLandscapeLeft":"横画面 : ホームボタン左"|replace:"UIInterfaceOrientationLandscapeRight":"横画面 : ホームボタン右"}<br>
                    {/foreach}
                {else}
                    iPhoneの画面回転は定義されていません
                {/if}
            </td>
        </tr>
        <tr>
            <td style="vertical-align: middle;">iPad 画面回転方向</td>
            <td style="vertical-align: middle;">
                {if isset($data['UISupportedInterfaceOrientations~ipad'])}
                    {foreach $data['UISupportedInterfaceOrientations~ipad'] as $c}
                        {$c|default:'不明'|replace:"UIInterfaceOrientationPortrait":"縦画面 : ホームボタン下"|replace:"UIInterfaceOrientationPortraitUpsideDown":"縦画面 : ホームボタン上"|replace:"UIInterfaceOrientationLandscapeLeft":"横画面 : ホームボタン左"|replace:"UIInterfaceOrientationLandscapeRight":"横画面 : ホームボタン右"}<br>
                    {/foreach}
                {else}
                    iPadの画面回転は定義されていません
                {/if}
            </td>
        </tr>
        </tbody>
    </table>

    <div>Provisioning Profile情報</div>
    <table class="table table-striped table-bordered table-hover text-center">
        <tbody>
        <tr><td>Apple ID</td><td>{$mobileprovisionArray.AppIDName}</td></tr>
        <tr><td>インストール期限</td><td>{'Y-m-d H:i:s T'|date:$mobileprovisionArray.ExpirationDate}</td></tr>
        <tr><td>名前</td><td>{$mobileprovisionArray.Name}</td></tr>
        <tr>
            <td>インストール可能デバイス(UDID)</td>
            <td>
                {foreach $mobileprovisionArray['ProvisionedDevices'] as $m}
                    {$m}<br>
                {/foreach}
            </td>
        </tr>
        </tbody>
    </table>

</div>

</body>
</html>