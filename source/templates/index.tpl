<!DOCTYPE html>
<html>
<head lang="jp">
    <meta charset="UTF-8">
    <title>iOS AdHoc List</title>

    {if $isAdmin|default:false === true}
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="../css/baseEdit.css">
        <script type="text/javascript" src="../js/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" src="../js/bootstrap.min.js"></script>
        <script type="text/javascript" src="../js/index.js"></script>
    {else}
        <link rel="stylesheet" href="./css/bootstrap.min.css">
        <link rel="stylesheet" href="./css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="./css/baseEdit.css">
        <script type="text/javascript" src="./js/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" src="./js/bootstrap.min.js"></script>
    {/if}


</head>
<body>

{include file='./header.tpl' isMobileTablet=$isMobileTablet|default:false isPC=$isPC|default:false isAdmin=$isAdmin|default:false title=$headerTitle|default:""}


<div class="viewForm">

    {if $isAdmin|default:false === true}
        <div>※注意 : このページは管理者用です。一般利用者には、下記のURLを連絡してください</div>
        <div><a href="../index.php">iOS AdHoc List</a></div>
    {/if}


    {if !$isAdmin|default:true === true}

        <div>最初に下記をダウンロードして、認証局を信頼リストに追加してください</div>
        <div>
            <button type="button" class="btn btn-sm btn-default" onclick="location.href='./cacert.der'">自己証明の証明書をダウンロード
            </button>
        </div>
        <div>　</div>
    {/if}

    {if $isAdmin|default:false === true}
        <div>　</div>
        <div>
            <button type="button" class="btn btn-sm btn-default" onclick="location.href='./uploader.php'">アプリを追加する
            </button>
        </div>
        <div>　</div>
    {/if}


    <table class="table table-striped table-hover text-center">
        <thead>
        <tr>
            <th class="text-center">ID</th>
            <th class="text-center">アプリタイトル</th>
            <th class="text-center">備考</th>
            {if $isAdmin|default:false === true}
                <th class="text-center">開発者備考</th>
                <th class="text-center">アプリ情報</th>
            {/if}
            <th class="text-center">Version</th>
            {if $isAdmin|default:false === true}
                <th class="text-center">Build</th>
            {/if}
            <th class="text-center">インストール期限 (JST)</th>
            <th class="text-center">登録日時 (JST)</th>
            {if $isAdmin|default:false === true}
                <th class="text-center">背景無効色</th>
                <th class="text-center">非表示</th>
                <th class="text-center">並び優先度</th>
                <th class="text-center">編集</th>
                <th class="text-center">削除</th>
            {/if}
        </tr>
        </thead>
        <tbody>

        {if isset($data)}
            {foreach $data as $d}
                {assign var="hasInstall" value={{'Y-m-d H:i:s'|date}|strtotime} > {$d.expirationDate|strtotime} nocache}
                <tr style="{if $isAdmin|default:false === false && $d.isHide === 1}display: none;{/if}">
                    <td style="{if $d.isInvalidBackground === 1 && !$hasInstall}background-color: dimgray;{elseif $hasInstall}background-color: darkred;{/if}">{$d.id}</td>
                    {if $isMobileTablet|default:false === true}
                        <td style="{if $d.isInvalidBackground === 1 && !$hasInstall}background-color: dimgray;{elseif $hasInstall}background-color: darkred;{/if}">
                            <a href="itms-services://?action=download-manifest&url={$url}/dl.php/plist/{$d.directoryName}/0">{$d.title|escape:"html"}</a>
                        </td>
                        {*<td><a href="itms-services://?action=download-manifest&url={$url}/dl.php/plist/{$d.directoryName}/0">{$d.title|escape:"html"}</a></td>*}
                    {elseif $isPC|default:false === true}
                        <td style="{if $d.isInvalidBackground === 1 && !$hasInstall}background-color: dimgray;{elseif $hasInstall}background-color: darkred;{/if}"><a href="{$url}/dl.php/ipa/{$d.directoryName}/1">{$d.title|escape:"html"}</a>
                        </td>
                        {*<td><a href="{$url}/dl.php/ipa/{$d.directoryName}/1">{$d.title|escape:"html"}</a></td>*}
                    {elseif $isAdmin|default:false === true}
                        <td style="{if $d.isInvalidBackground === 1 && !$hasInstall}background-color: dimgray;{elseif $hasInstall}background-color: darkred;{/if}">{$d.title|escape:"html"}</td>
                    {/if}
                    {*<td class="text-left">{$d.notes|escape:"html"|replace:"\r\n":"<br>"|replace:"\r":"<br>"|replace:"\n":"<br>"}</td>*}
                    <td class="text-left" style="{if $d.isInvalidBackground === 1 && !$hasInstall}background-color: dimgray;{elseif $hasInstall}background-color: darkred;{/if}">{$d.notes|htmlescape:$tags|replace:"\r\n":"<br>"|replace:"\r":"<br>"|replace:"\n":"<br>"}</td>
                    {if $isAdmin|default:false === true}
                        <td class="text-left" style="{if $d.isInvalidBackground === 1 && !$hasInstall}background-color: dimgray;{elseif $hasInstall}background-color: darkred;{/if}">{$d.developerNotes|escape:"html"|replace:"\r\n":"<br>"|replace:"\r":"<br>"|replace:"\n":"<br>"}</td>
                        <td style="{if $d.isInvalidBackground === 1 && !$hasInstall}background-color: dimgray;{elseif $hasInstall}background-color: darkred;{/if}">
                            <a href="javascript:void(window.open('../ipainfo.php?i={$d.directoryName}{$d.ipaTmpHash}', 'ipa情報', 'width=600, height=650, menubar=no, toolbar=no, scrollbars=yes'));">表示</a>
                        </td>
                    {/if}
                    <td style="{if $d.isInvalidBackground === 1 && !$hasInstall}background-color: dimgray;{elseif $hasInstall}background-color: darkred;{/if}">{$d.ipaVersion}</td>
                    {if $isAdmin|default:false === true}
                        <td style="{if $d.isInvalidBackground === 1 && !$hasInstall}background-color: dimgray;{elseif $hasInstall}background-color: darkred;{/if}">{$d.ipaBuild}</td>
                    {/if}
                    <td style="{if $d.isInvalidBackground === 1 && !$hasInstall}background-color: dimgray;{elseif $hasInstall}background-color: darkred;{/if}">{$d.expirationDate}</td>
                    <td style="{if $d.isInvalidBackground === 1 && !$hasInstall}background-color: dimgray;{elseif $hasInstall}background-color: darkred;{/if}">{$d.createDate}</td>
                    {if $isAdmin|default:false === true}
                        <td style="{if $d.isInvalidBackground === 1 && !$hasInstall}background-color: dimgray; color: red;{elseif $hasInstall}background-color: darkred;{/if}">{if $d.isInvalidBackground === 1}無効{else}有効{/if}</td>
                        <td style="{if $d.isHide === 1}color: red;{/if}{if $d.isInvalidBackground === 1 && !$hasInstall}background-color: dimgray;{elseif $hasInstall}background-color: darkred;{/if}">{if $d.isHide === 1}非表示{else}表示{/if}</td>
                        <td style="{if $d.isInvalidBackground === 1 && !$hasInstall}background-color: dimgray;{elseif $hasInstall}background-color: darkred;{/if}">{$d.sortOrder}</td>
                        <td style="{if $d.isInvalidBackground === 1 && !$hasInstall}background-color: dimgray;{elseif $hasInstall}background-color: darkred;{/if}">
                            <button type="button" class="btn btn-sm btn-warning" id="editButton" name="editButton"
                                    value="{$d.directoryName}{$d.ipaTmpHash}">編集
                            </button>
                        </td>
                        <td style="{if $d.isInvalidBackground === 1 && !$hasInstall}background-color: dimgray;{elseif $hasInstall}background-color: darkred;{/if}">
                            <button type="button" class="btn btn-sm btn-danger" id="deleteButton" name="deleteButton"
                                    value="{$d.directoryName}">削除
                            </button>
                        </td>
                    {/if}
                </tr>
            {/foreach}
        {/if}

        </tbody>
    </table>

</div>

</body>
</html>