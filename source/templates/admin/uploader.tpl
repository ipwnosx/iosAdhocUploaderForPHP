<!DOCTYPE html>
<html>
<head lang="jp">
    <meta charset="UTF-8">
    <title>アプリのアップロード</title>

    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="../css/baseEdit.css">

    <script type="text/javascript" src="../js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="../js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../js/uploader.js"></script>

</head>
<body>

{include file='../header.tpl' isMobileTablet=false isPC=false isAdmin=true title=$headerTitle|default:""}

<div class="registForm well bs-component">
    <form enctype="multipart/form-data" id="form" class="form-horizontal">

        <fieldset>
            <legend>アプリ情報記述</legend>

            <div class="form-group">
                <label for="inputTitle" class="col-lg-3 control-label">タイトル名</label>

                <div class="col-lg-9">
                    <input type="text" class="form-control" id="inputTitle" name="inputTitle" value="">
                </div>
            </div>

            <div class="form-group">
                <label for="inputNotes" class="col-lg-3 control-label">備考</label>

                <div class="col-lg-9">
                    <textarea id="inputNotes" name="inputNotes" class="form-control" rows="5"></textarea>
                    ※HTMLタグは aタグ に限り利用できます
                    {if isset($slackArray)}
                        <br>※Slack通知時には aタグ はSlack用リンクに自動変換されてます
                    {/if}

                </div>
            </div>

            <div class="form-group">
                <label for="inputDeveloperNotes" class="col-lg-3 control-label">開発者備考</label>

                <div class="col-lg-9">
                    <textarea id="inputDeveloperNotes" name="inputDeveloperNotes" class="form-control" rows="5"></textarea>
                </div>
            </div>

            <div class="form-group">
                <label for="inputFileIpa" class="col-lg-3 control-label">ipa ファイル</label>

                <div class="col-lg-9">
                    <input type="file" name="file[]" id="inputFileIpa" class="form-control" onchange="uploader.changeIpaFile()">
                </div>
            </div>

            {if isset($slackArray)}
            <div class="form-group">
                <label for="checkboxSlackNotificationFlag" class="col-lg-3 control-label">Slack通知</label>

                <div class="col-lg-9">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="checkboxSlackNotificationFlag" name="checkboxSlackNotificationFlag" checked="checked"> Slackへ通知を行う
                        </label>
                    </div>
                    {foreach $slackArray as $s}
                        <div class="checkbox">
                            {*<label>チーム名 : {$s.url|replace:'https://':''|regex_replace:"/.slack.com\/.*/":""}</label><br>*}
                            <label>チャンネル : {$s.channel}</label><br>
                            <label>タイトル : {$s.username}</label>
                        </div>
                    {/foreach}
                </div>
            </div>
            {/if}

            <div class="form-group">
                <div class="col-lg-9 col-lg-offset-3">
                    <button type="button" id="btnSend" class="btn btn-primary">アップロード</button>
                </div>
            </div>
        </fieldset>

    </form>
</div>


</body>
</html>