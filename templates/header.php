<!DOCTYPE html>
<html>
<head>
  <title><?= $title ?></title>
  <link rel="stylesheet" type="text/css" href="/style.css">
  <script src="/js/jquery-2.2.2.min.js"></script>
  <script src="/js/tweet_submit.js"></script>
</head>
<body>
<form action="/tweet/search" method="POST">
  <input type="text" name="tweet_search">
  <input type="submit" value="検索" name="tweet_search_button">
</form>
