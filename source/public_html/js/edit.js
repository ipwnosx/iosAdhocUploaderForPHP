var uploader = {};

(function ($) {
    $(function () {

        // 確定ボタンを押したとき
        $('#btnSend').click(
            function(){

                var result = true;

                // 入力された値をチェック
                if ($("#inputTitle").val() === "") {
                    result = false;
                }

                if ($("#inputSortOrder").val() === "") {
                    result = false;
                }


                if (result === false) {
                    alert("すべての項目を入力してください");
                    return false;
                }

                $('#form').attr('action', './editresult.php');
                $('#form').attr('method', 'post');
                $('#form').submit();

                return undefined;
            }
        );

    });

})(jQuery);