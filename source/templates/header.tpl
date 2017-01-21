<div class="navbar navbar-default">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        {if $isMobileTablet|default:false === true}
            <a class="navbar-brand" href="./index.php">ios AdHoc List (SP専用)　{$title|default:""}　v1.1.0</a>
        {elseif $isPC|default:false === true}
            <a class="navbar-brand" href="./index.php">ios AdHoc List (PC専用)　{$title|default:""}　v1.1.0</a>
        {elseif $isAdmin|default:false === true}
            <a class="navbar-brand" href="./index.php">管理者 ios AdHoc List　{$title|default:""}　v1.1.0</a>
        {/if}
    </div>
    <div class="navbar-collapse collapse navbar-responsive-collapse">
        <ul class="nav navbar-nav">
            {if $isAdmin|default:false === true}
                <li><a href="./downloadhistory.php">ダウンロード履歴</a></li>
                <li><a href="./setting.php">アプリ設定</a></li>
            {/if}
        </ul>
    </div>
</div>