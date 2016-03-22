    <div class='username_div'>
    <? if (isset($_SESSION['user_name'])) { ?>
        <?= "ユーザ名".$_SESSION['user_name'] ?>
    <? } else { ?>
        <?= "ログインしてください" ?>
    <? } ?>
    </div>
    <a href="/tweet">つぶやく</a>
    <a href="/tweet/favorites">お気に入り履歴</a>
        <? if (isset($_SESSION['user_name'])) { ?>
        <form action="/user/logout" method="POST">
            <input type="submit" value="ログアウト">
        </form>
    <? } else { ?>
        <a href="/login">ログイン</a>
    <? } ?>

    <h1><?= $tweet_rows[0]['user_name'] ?>の情報</h1>

    <? if (isset($_SESSION['user_id'])) { ?>
        <? if ($tweet_rows[0]['user_id'] !== $_SESSION['user_id']) : ?>
            <? if ($follow_status == true ) : ?>
                <a href="/user/refollow/<?= $tweet_rows[0]['user_id'] ?>">フォローをはずす</a><br />
            <? else : ?>
                <a href="/user/follow/<?= $tweet_rows[0]['user_id'] ?>">フォローする</a><br />
            <? endif ; ?>
        <? endif; ?>
    <? } ?>

    <a href="/user/following/<?= $tweet_rows[0]['user_id'] ?>">フォロー[]</a>
    <a href="/user/follower/<?= $tweet_rows[0]['user_id'] ?>">フォロワー[]</a>
    <div class="main">
        <? foreach ($tweet_rows as $row) { ?>
            <div class='datetime_div'>
                <?= $row['created_at'] ?>
            </div>
            <div class='usr_id_div'>
                <a href="/user/<?= $row['user_id'] ?>"><?= $row['user_name'] ?></a>
            </div>
            <div class='content_div'>
                <?= $row['content'] ?>
            </div>
            <div class='tweet_edit'>
                <a href='/tweet/edit/<?= $row['tweet_id'] ?>'>編集</a>
                <a href='/favorite/<?= $row['tweet_id'] ?>'>お気に入り</a>
                <a href='/retweet/<?= $row['tweet_id'] ?>'>リツイート</a>
            </div>
        <? } ?>
    </div>
</body>
</html>