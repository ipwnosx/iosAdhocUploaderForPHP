(function ($) {
    $(function () {

        // 編集ボタンを押したとき
        $('[id=editButton]').click(function () {

            location.href = './edit.php?d=' + $(this).val();

        });

        // 削除ボタンを押したとき
        $('[id=deleteButton]').click(function () {

            var result = confirm("削除します よろしいですか？");
            if (result === false) {
                return;
            }

            $.ajax({
                url: 'delete.php',
                type: 'POST',
                cache: false,
                dataType: 'json',
                data: {
                    a: $(this).val()
                }
            })
                .done(function (data, textStatus, jqXHR) {
                    alert(data.message);
                    location.reload();
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    alert('削除に失敗しました');
                });
                //.always(function (data, textStatus, errorThrown) {
                //    alert('always!');
                //});
        });

        // チェックボックスが変化したとき
        $('#canNotBeInstallCheckbox').change(function () {
            if ($(this).is(':checked')) {
                $('.notInstall').css('display', 'table-row');
            } else {
                $('.notInstall').css('display', 'none');
            }
        });
        // ラベル領域をタップしてもチェックボックスを変化させる
        $('#canNotBeInstallLabel').click(function () {
            if($('#canNotBeInstallCheckbox').prop('checked')) {
                $('#canNotBeInstallCheckbox').prop('checked', false).change();;
            } else {
                $('#canNotBeInstallCheckbox').prop('checked', true).change();;
            }
        });

    });
})(jQuery);