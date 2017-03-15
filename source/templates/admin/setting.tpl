<!DOCTYPE html>
<html>
<head lang="jp">
    <meta charset="UTF-8">
    <title>アプリの設定</title>

    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="../css/baseEdit.css">

    <script type="text/javascript" src="../js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="../js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../js/setting.js"></script>

</head>
<body>

{include file='../header.tpl' isMobileTablet=false isPC=false isAdmin=true title=$headerTitle|default:""}

<div class="registForm well bs-component">
    <form enctype="multipart/form-data" id="form" class="form-horizontal">

        <fieldset>
            <legend>アプリ設定</legend>

            <div class="form-group">
                <label for="inputTitle" class="col-lg-3 control-label">タイトル名</label>

                <div class="col-lg-9">
                    <input type="text" class="form-control" id="inputTitle" name="inputTitle" value="{$headerTitle|default:''}">
                </div>
            </div>

            <div class="form-group">
                <label for="inputNotice" class="col-lg-3 control-label">お知らせ</label>

                <div class="col-lg-9">
                    <textarea id="inputNotice" name="inputNotice" class="form-control" rows="5">{$notice|default:''}</textarea>
                </div>
            </div>

            <div class="form-group">
                <label for="slack" class="col-lg-3 control-label">Slack情報</label>
            </div>

            <div class="form-group">
                <label for="inputSlackApiUrl" class="col-lg-3 control-label">SlackAPI URL（任意）</label>

                <div class="col-lg-9">
                    <input type="text" class="form-control" id="inputSlackApiUrl" name="inputSlackApiUrl" value="{$slack.url|default:''}">
                </div>
            </div>

            <div class="form-group">
                <label for="inputChannel" class="col-lg-3 control-label">チャンネル名（任意）</label>

                <div class="col-lg-9">
                    <input type="text" class="form-control" id="inputChannel" name="inputChannel" value="{$slack.channel|default:''}">
                </div>
            </div>

            <div class="form-group">
                <label for="inputSlackTitle" class="col-lg-3 control-label">Slack投稿時タイトル（任意）</label>

                <div class="col-lg-9">
                    <input type="text" class="form-control" id="inputSlackTitle" name="inputSlackTitle" value="{$slack.username|default:''}">
                </div>
            </div>

            <div class="form-group">
                <label for="inputIconImage" class="col-lg-3 control-label">アイコン画像（任意）</label>

                <div class="col-lg-9">
                    <input type="text" class="form-control" id="inputIconImage" name="inputIconImage" value="{$slack.icon_url|default:''}">
                    ※「アイコン絵文字」を指定する場合には指定できません
                </div>
            </div>

            <div class="form-group">
                <label for="input" class="col-lg-3 control-label">アイコン絵文字（任意）</label>

                <div class="col-lg-9">
                    <input type="text" class="form-control" id="inputIconEmoji" name="inputIconEmoji" value="{$slack.icon_emoji|default:''}">
                    ※「アイコン画像」を指定する場合には指定できません
                </div>
            </div>

            <div class="form-group">
                <div class="col-lg-9 col-lg-offset-3">
                    <button type="button" id="btnSend" class="btn btn-primary">設定する</button>
                </div>
            </div>
        </fieldset>

    </form>
</div>


</body>
</html>