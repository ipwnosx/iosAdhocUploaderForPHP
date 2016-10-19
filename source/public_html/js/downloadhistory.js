(function ($) {
    $(function () {

        $('#conditionSearchButton').click(
            function(){

                var selectVal = $('[name=selectUserAgent]').val();

                var trs = $("table tr");
                for(var i=1,l=trs.length;i<l;i++ ){

                    var tr = trs.eq(i);
                    var cells = tr.children();

                    var tdText = cells.eq(5).text();

                    if (selectVal === "") {
                        $(tr).css("display", "");
                    } else if (tdText !== selectVal) {
                        $(tr).css("display", "none");
                    } else {
                        $(tr).css("display", "");
                    }

                }

            }
        );

    });
})(jQuery);