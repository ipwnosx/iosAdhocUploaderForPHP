<!DOCTYPE html>
<html>
<head lang="jp">
    <meta charset="UTF-8">
    <title>アプリの編集</title>

    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="../css/baseEdit.css">

    <script type="text/javascript" src="../js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="../js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../js/edit.js"></script>

</head>
<body>

{include file='../header.tpl' isMobileTablet=false isPC=false isAdmin=true title=$headerTitle|default:""}

<div class="registForm well bs-component">
    <form id="form" class="form-horizontal">

        <fieldset>
            <legend>アプリ情報編集</legend>

            <div class="form-group">
                <label for="inputId" class="col-lg-3 control-label">id</label>

                <div class="col-lg-9">
                    <input type="hidden" id="inputId" name="inputId" value="{$data.id|default:'-1'}">
                    <span class="form-control">{$data.id|default:'-1'}</span>
                </div>
            </div>

            <div class="form-group">
                <label for="inputTitle" class="col-lg-3 control-label">タイトル名</label>

                <div class="col-lg-9">
                    <input type="text" class="form-control" id="inputTitle" name="inputTitle" value="{$data.title|default:'想定外'|escape:"html"}">
                </div>
            </div>

            <div class="form-group">
                <label for="inputNotes" class="col-lg-3 control-label">備考</label>

                <div class="col-lg-9">
                    <textarea id="inputNotes" name="inputNotes" class="form-control" rows="5">{$data.notes|default:''|escape:"html"}</textarea>
                    ※HTMLタグは aタグ に限り利用できます
                </div>
            </div>

            <div class="form-group">
                <label for="inputDeveloperNotes" class="col-lg-3 control-label">開発者備考</label>

                <div class="col-lg-9">
                    <textarea id="inputDeveloperNotes" name="inputDeveloperNotes" class="form-control" rows="5">{$data.developerNotes|default:''|escape:"html"}</textarea>
                </div>
            </div>

            <div class="form-group">
                <label for="checkboxInvalidBackground" class="col-lg-3 control-label">背景無効色</label>

                <div class="col-lg-9">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="checkboxInvalidBackground" name="checkboxInvalidBackground"{if $data.isInvalidBackground === 1} checked="checked"{/if}> 背景無効色にする
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="checkboxHide" class="col-lg-3 control-label">非表示</label>

                <div class="col-lg-9">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="checkboxHide" name="checkboxHide"{if $data.isHide === 1} checked="checked"{/if}> 非表示にする
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputSortOrder" class="col-lg-3 control-label">並び優先度</label>

                <div class="col-lg-9">
                    <input type="text" class="form-control" id="inputSortOrder" name="inputSortOrder" value="{$data.sortOrder|default:'0'}">
                </div>
            </div>

            <div class="form-group">
                <div class="col-lg-9 col-lg-offset-3">
                    <button type="button" id="btnSend" class="btn btn-primary">確定</button>
                </div>
            </div>
        </fieldset>

    </form>
</div>


</body>
</html>