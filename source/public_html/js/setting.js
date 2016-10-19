var uploader = {};

(function ($) {
    $(function () {


        // ボタンを押したとき
        $('#btnSend').click(
            function(){
                $('#form').attr('action', './settingresult.php');
                $('#form').attr('method', 'post');
                $('#form').submit();
            }
        );


    });

})(jQuery);