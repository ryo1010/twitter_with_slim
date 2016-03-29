$(document).ready(function() {

    $(window).bind("scroll", function() {
        scrollHeight = $(document).height(); //全体の高さ取得
        scrollPosition = $(window).height() + $(window).scrollTop(); //画面の高さ＋スクロールの高さ
        if ((scrollHeight - scrollPosition) / scrollHeight == 0) {
            var fd = new FormData();
            var display_number = parseInt($("#display_number").val());
            var display_limit = parseInt($("#display_limit").val());
            $("#display_number").val(display_number + 10);

            fd.append("display_number", display_number+10);
            fd.append("display_limit", display_limit);

            var pageData = {
              method: "POST",
              dataType: "html",
              data: fd,
              processData: false,
              contentType: false
            };

            $.ajax(
              "/", pageData
            ).done(function( text ){
              $("div.main").append(text);
            }).fail(function(){
              alert("読み込みに失敗しました");
            });
        }
    });
});