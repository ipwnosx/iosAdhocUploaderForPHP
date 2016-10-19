<!DOCTYPE html>
<html>
<head lang="jp">
    <meta charset="UTF-8">
    <title>結果</title>

    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-theme.min.css">

    <script type="text/javascript" src="../js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="../js/bootstrap.min.js"></script>

</head>
<body>

{include file='../header.tpl' isMobileTablet=false isPC=false isAdmin=true title=$headerTitle|default:""}

<div class="registForm well bs-component">
    {$result|default:''}
</div>

</body>
</html>