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
        <script type="text/javascript" src="./js/index.js"></script>
    {/if}


</head>
<body>

{include file='./header.tpl' isMobileTablet=$isMobileTablet|default:false isPC=$isPC|default:false isAdmin=$isAdmin|default:false title=$headerTitle|default:""}


<div class="viewForm">

    {$notice|default:''}

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

    {assign var="isInvalidBackgroundColor" value="background-color: dimgray" scope="root" nocache}
    {assign var="isExpiredBackgroundColor" value="background-color: #FADBDA" scope="root" nocache}
    <div style="margin: 20px 0">
        凡例
        <div style="display: table; border: 1px solid;">
            <div style="display: table-row">
                <div style="display: table-cell; border: 1px solid; text-align: center; font-weight: bold; width: 80px;">背景色</div>
                <div style="display: table-cell; border: 1px solid; text-align: center; font-weight: bold; width: 250px;">効果</div>
            </div>
            <div style="display: table-row">
                <div style="display: table-cell; border: 1px solid; {$isInvalidBackgroundColor}"></div>
                <div style="display: table-cell; border: 1px solid;">推奨されないアプリ(詳細は開発者へ)</div>
            </div>
            <div style="display: table-row">
                <div style="display: table-cell; border: 1px solid; {$isExpiredBackgroundColor}"></div>
                <div style="display: table-cell; border: 1px solid;">インストールの有効期限切れ</div>
            </div>
        </div>
    </div>

    {if $isAdmin|default:false === false}
    <div>
        <input type="checkbox" id="canNotBeInstallCheckbox" style="margin: 10px 10px 20px; transform: scale(1.5)"><label id="canNotBeInstallLabel">インストール出来ないアプリを表示する</label>
    </div>
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
                {assign var="styleBackgroundColor" value="" scope="root" nocache}
                {assign var="classNameNotInstall" value="" scope="root" nocache}
                {if $d.isInvalidBackground === 1 && !$hasInstall}
                    {assign var="styleBackgroundColor" value=$isInvalidBackgroundColor scope="root" nocache}
                {elseif $hasInstall}
                    {assign var="styleBackgroundColor" value=$isExpiredBackgroundColor scope="root" nocache}
                    {assign var="classNameNotInstall" value="notInstall" scope="root" nocache}
                {/if}
                <tr class="{$classNameNotInstall}" style="{if $isAdmin|default:false === false && ($d.isHide === 1 || $hasInstall)}display: none;{/if}">
                    <td style="{$styleBackgroundColor};">{$d.id}</td>
                    {if $isMobileTablet|default:false === true}
                        <td style="{$styleBackgroundColor}">
                            <a href="itms-services://?action=download-manifest&url={$url}/dl.php/plist/{$d.directoryName}/0">{$d.title|escape:"html"}</a>
                        </td>
                    {elseif $isPC|default:false === true}
                        <td style="{$styleBackgroundColor}"><a href="{$url}/dl.php/ipa/{$d.directoryName}/1">{$d.title|escape:"html"}</a>
                        </td>
                    {elseif $isAdmin|default:false === true}
                        <td style="{$styleBackgroundColor}">{$d.title|escape:"html"}</td>
                    {/if}
                    <td class="text-left" style="{$styleBackgroundColor}">{$d.notes|htmlescape:$tags|replace:"\r\n":"<br>"|replace:"\r":"<br>"|replace:"\n":"<br>"}</td>
                    {if $isAdmin|default:false === true}
                        <td class="text-left" style="{$styleBackgroundColor}">{$d.developerNotes|escape:"html"|replace:"\r\n":"<br>"|replace:"\r":"<br>"|replace:"\n":"<br>"}</td>
                        <td style="{$styleBackgroundColor}">
                            <a href="javascript:void(window.open('../ipainfo.php?i={$d.directoryName}{$d.ipaTmpHash}', 'ipa情報', 'width=600, height=650, menubar=no, toolbar=no, scrollbars=yes'));">表示</a>
                        </td>
                    {/if}
                    <td style="{$styleBackgroundColor}">{$d.ipaVersion}</td>
                    {if $isAdmin|default:false === true}
                        <td style="{$styleBackgroundColor}">{$d.ipaBuild}</td>
                    {/if}
                    <td style="{$styleBackgroundColor}">{$d.expirationDate}</td>
                    <td style="{$styleBackgroundColor}">{$d.createDate}</td>
                    {if $isAdmin|default:false === true}
                        <td style="{if $d.isInvalidBackground === 1}color: red;{/if} {$styleBackgroundColor}">{if $d.isInvalidBackground === 1}無効{else}有効{/if}</td>
                        <td style="{if $d.isHide === 1}color: red;{/if} {$styleBackgroundColor}">{if $d.isHide === 1}非表示{else}表示{/if}</td>
                        <td style="{$styleBackgroundColor}">{$d.sortOrder}</td>
                        <td style="{$styleBackgroundColor}">
                            <button type="button" class="btn btn-sm btn-warning" id="editButton" name="editButton"
                                    value="{$d.directoryName}{$d.ipaTmpHash}">編集
                            </button>
                        </td>
                        <td style="{$styleBackgroundColor};">
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