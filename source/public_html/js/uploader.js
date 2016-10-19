var uploader = {};

(function ($) {
    $(function () {

        uploader.changeIpaFile = function() {

            var ipaFile = $("#inputFileIpa")[0].files[0];
            if (ipaFile === undefined) {
                $("#inputTitle").val('');
            }

            if (ipaFile['name'] !== '') {
                var filePath = ipaFile['name'].split('\\');
                var fileName = filePath[filePath.length - 1];
                var result = fileName.replace(/.ipa$/g, '');

                if (result !== '') {
                    $("#inputTitle").val(result);
                } else {
                    $("#inputTitle").val('');
                }
            }
        }

        // アップロードボタンを押したとき
        $('#btnSend').click(
            function(){

                var result = true;

                // 入力された値をチェック
                if ($("#inputTitle").val() === "") {
                    result = false;
                }

                var ipaFile = $("#inputFileIpa")[0].files[0];
                if(ipaFile === undefined) {
                    result = false;
                }

                if (result === false) {
                    alert("すべての項目を入力してください");
                    return false;
                }

                $('#form').attr('action', './result.php');
                $('#form').attr('method', 'post');
                $('#form').submit();

                return undefined;
            }
        );

    });

})(jQuery);