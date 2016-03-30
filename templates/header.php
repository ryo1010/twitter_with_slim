<!DOCTYPE html>
<html>
<head>
  <title><?= $title ?></title>
  <link rel="stylesheet" type="text/css" href="/css/style.css">
  <script src="/js/jquery-2.2.2.min.js"></script>
  <script src="/js/tweet_load_top_page.js"></script>
  <script src="/js/tweet_submit.js"></script>
  <script src="/js/image_upload.js"></script>
</head>
<body>
<form action="/tweet/search" method="GET">
  <input type="text" name="tweet_search">
  <input type="submit" value="検索">
</form>
