$(function() {
  $('#send').click(function() {
      var postData = {
        method : "GET",
        processData : false,
        contentType : false
      };
      $.ajax(
        "/tweet", postData
      ).done(function( text ){
        $("#output").html(text);
      }).fail(function(){
        alert("読み込みに失敗しました");
      });
    return false;
  });

  $('#tweet_submit').click(function() {
    try {
      var fd = new FormData();
      //バイナリデータ取得
      var images = new Array();
      $("[id=images]").each(function() {
        images.push($(this).attr("src"));
      });
      var image_binary = $("#images").attr("src");
      fd.append("images_array", images);
      fd.append("images", image_binary);
      fd.append("tweet_content", $("#tweet_content").val());
        var postData = {
          method : "POST",
          dataType : "html",
          data : fd,
          processData : false,
          contentType : false
        };
        $.ajax(
          "/tweet/submit", postData
        ).done(function( text ){
          console.log(text);
          $("new_tweet").append(text);
          $('text.tweet_content').val("");
          tweetid = new FormData();
          tweetid.append("tweet_id", $("#last_tweet").val());
          var tweetDisplay = {
            method : "POST",
            dataType : "html",
            data : tweetid,
            processData : false,
            contentType : false
          };
          $.ajax(
            "/tweet/submit/after", tweetDisplay
          ).done(function( text ){
            $('div.new_tweet').empty();
            $("div.new_tweet").prepend(text);
          });
        }).fail(function(){
          alert("投稿に失敗しました");
        });
    }catch (e) {
      alert(e);
    }finally{
      return false;
    }
  });

});
/*function imagesUpload(file) {
  var fd = new FormData();
  fd.append('file',file);
  var postData = {
    method : "POST",
    data : fd,
    processData : false,
    contentType : false
  };
  $.ajax(
    "/tweet/images", postData
    ).done(function( text ) {
      console.log(text);
    }).fail(function(){
      alert("画像アップロードに失敗しました");
    });
};
*/