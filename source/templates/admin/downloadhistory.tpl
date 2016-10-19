<!DOCTYPE html>
<html>
<head lang="jp">
    <meta charset="UTF-8">
    <title>iOS AdHoc List Download History</title>

    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="../css/downloadhistory.css">

    <script type="text/javascript" src="../js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="../js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../js/downloadhistory.js"></script>


</head>
<body>

{include file='../header.tpl' isMobileTablet=false isPC=false isAdmin=true title=$headerTitle|default:""}

<div class="viewForm">
    <div class="well bs-component">
        <form id="form" class="form-horizontal">

            <fieldset>
                <legend>絞り込み条件</legend>

                <div class="form-group">
                    <label for="selectUserAgent" class="col-lg-1 control-label">User Agent</label>

                    <div class="col-lg-11">
                        <select class="form-control" id="selectUserAgent" name="selectUserAgent">
                            <option value="">全件表示</option>
                            {if isset($condition)}
                                {foreach from=$condition key=k item=v}
                                    <option value="{$k}">{$v}</option>
                                {/foreach}
                            {/if}
                        </select>
                    </div>
                </div>


                <div class="form-group">
                    <div class="col-lg-11 col-lg-offset-1">
                        <button type="button" id="conditionSearchButton" class="btn btn-primary">絞り込み検索</button>
                        　　　
                    </div>
                </div>


            </fieldset>

        </form>
    </div>


    <table class="tablestyle">
        <thead>
        <tr>
            <th class="text-center" style="width: 50px;">ID</th>
            <th class="text-center">アプリタイトル</th>
            <th class="text-center" style="display: none">Bundle Identifier</th>
            <th class="text-center" style="width: 150px;">Version</th>
            <th class="text-center" style="width: 150px;">Build</th>
            <th class="text-center" style="display: none">User Agent</th>
            <th class="text-center" style="width: 150px;">端末名</th>
            <th class="text-center" style="width: 160px;">ダウンロード種別</th>
            <th class="text-center" style="width: 130px;">IP ADDRESS</th>
            <th class="text-center" style="width: 155px;">ダウンロード日時 (JST)</th>
        </tr>
        </thead>
        <tbody>

        {if isset($data)}
            {foreach $data as $d}
                {if $d.isDelete === 1}
                    <tr class="r">
                        <td class="r" style="text-align: center;">{$d.id}</td>
                        <td class="r">{$d.title}</td>
                        <td class="r" style="display: none">{$d.ipaBundleIdentifier}</td>
                        <td class="r">{$d.ipaVersion}</td>
                        <td class="r">{$d.ipaBuild}</td>
                        <td class="r" style="display: none">{$d.userAgent}</td>
                        <td class="r" style="text-align: center;">{$d.deviceName}</td>
                        {if $d.downloadType === 1}
                            <td class="r" style="text-align: center;">PCから直接ダウンロード</td>
                        {elseif $d.downloadType === 0}
                            <td class="r" style="text-align: center;">AdHocダウンロード</td>
                        {/if}
                        <td class="r" style="text-align: center;">{$d.ipAddress}</td>
                        <td class="r" style="text-align: center;">{$d.createDate}</td>
                    </tr>
                {else}
                    <tr>
                        <td style="text-align: center;">{$d.id}</td>
                        <td>{$d.title}</td>
                        <td style="display: none">{$d.ipaBundleIdentifier}</td>
                        <td>{$d.ipaVersion}</td>
                        <td>{$d.ipaBuild}</td>
                        <td style="display: none">{$d.userAgent}</td>
                        <td style="text-align: center;">{$d.deviceName}</td>
                        {if $d.downloadType === 1}
                            <td style="text-align: center;">PCから直接ダウンロード</td>
                        {elseif $d.downloadType === 0}
                            <td style="text-align: center;">AdHocダウンロード</td>
                        {/if}
                        <td style="text-align: center;">{$d.ipAddress}</td>
                        <td style="text-align: center;">{$d.createDate}</td>
                    </tr>
                {/if}
            {/foreach}
        {/if}

        </tbody>
    </table>

</div>

</body>
</html>