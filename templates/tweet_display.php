<!DOCTYPE html>
<html>
<head>
    <title>ツイート</title>
</head>
<body>
    <div class='username_div'>ユーザ名<?= $_SESSION['username'] ?></div>
    <a href="/tweet">つぶやく</a>
    <a href="/tweet_history">削除履歴</a>

    <h1>ツイート一覧</h1>
        <? foreach ($rows as $row) { ?>
            <div class='datetime_div'>
                <?= $row['tweettime'] ?>
            </div>
            <div class='usr_id_div'>
                <?= $row['usr_id'] ?>
            </div>
            <div class='content_div'>
                <?= $row['content'] ?>
            </div>
            <div class='tweet_edit'>
                <a href='tweet_edit.php?tweet_id/<?= $row['id'] ?>'>編集</a>
            </div>
        <? } ?>
</body>
</html>