$(document).ready(function() {
  $("#drug_image").on("drop",function(event){
    event.preventDefault();
    var files = event.originalEvent.dataTransfer.files;
    for (var i = 0 ; i<files.length; i++) {
      imagesDisplay(files[i]);
      //imagesUpload(files[i]);
    }
  });


  function imagesDisplay(file) {
    var reader = new FileReader();
    reader.onload = function() {
      var img_src = $('<img>').attr('src',reader.result).attr('width', 100).attr("id", "images");
      $('#drug_image').append(img_src);
    }
    reader.readAsDataURL(file);
  }

  function imagesUpload(file) {
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


  $("#drug_image").on("dragover",function(event){
    event.preventDefault();
  });


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
      //if ($('#drug_image').files!== null) {
      //  fd.append( "file", $('#drug_image').files);
      //}
      var image_binary = $("#images").attr("src"); //バイナリデータ取得
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
          // $('text.tweet_content').val("");
          // tweetid = new FormData();
          // tweetid.append("tweet_id", $("#last_tweet").val());
          // var tweetDisplay = {
          //   method : "POST",
          //   dataType : "html",
          //   data : tweetid,
          //   processData : false,
          //   contentType : false
          // };
          // $.ajax(
          //   "/tweet/submit/after", tweetDisplay
          // ).done(function( text ){
          //   $('div.new_tweet').empty();
          //   $("div.new_tweet").prepend(text);
          // });
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

// $(function(){

//     // 何かの値をPostしたいなら
//     var hogeParam = "hoge";

//     // おまじない
//     Dropzone.autoDiscover = false;

//     Dropzone.options.myAwesomeDropzone = {
//         paramName : "file",         // input fileの名前
//         parallelUploads:1,            // 1度に何ファイルずつアップロードするか
//         acceptedFiles:'image/*',   // 画像だけアップロードしたい場合
//         maxFiles:10,                      // 1度にアップロード出来るファイルの数
//         maxFilesize:0.5,                // 1つのファイルの最大サイズ(1=1M)
//         dictFileTooBig: "ファイルが大きすぎます。 ({{filesize}}MiB). 最大サイズ: {{maxFilesize}}MiB.",
//         dictInvalidFileType: "画像ファイル以外です。",
//         dictMaxFilesExceeded: "一度にアップロード出来るのは10ファイルまでです。",
//     };
//     // urlは実際に画像をアップロードさせるURLパスを入れる
//     var myDropzone = new Dropzone("div#my-awesome-dropzone",{url:"my_api_upload_img.php"});
//     // 何か値をpostしたい場合
//     myDropzone.on("sending", function(file,xhr,formData) {
//         formData.append("hoge", hogeParam);
//     });
// });