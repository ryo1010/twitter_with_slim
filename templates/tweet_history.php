<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>ツイート削除履歴</title>
</head>
<body>
<a href="/">もどる</a>

<div class="main">
    <? foreach ($rows as $row) { ?>
        <div class='datetime_div'>
            <?= $row['created_at'] ?>
        </div>
        <div class='usr_id_div'>
            <?= $row['user_name'] ?>
        </div>
        <div class='content_div'>
            <?= $row['content'] ?>
        </div>
    <? } ?>
</div>
</body>
</html>