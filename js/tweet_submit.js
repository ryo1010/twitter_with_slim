$(document).ready(function() {
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

  $('#output').on("click", "#tweet_submit", function() {
    try {
      var fd = new FormData();
      if ($('#upfile')[0].files[0]!== null) {
        fd.append( "file", $('#upfile')[0].files[0]);
      }
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