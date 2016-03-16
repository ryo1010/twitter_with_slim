    <div class='username_div'>ユーザ名<?= $_SESSION['user_name'] ?></div>
    <a href="/tweet">つぶやく</a>
    <a href="/tweet/history">削除履歴</a>
    <a href="/tweet/favorites">お気に入り履歴</a>
    <a href="/tweet/retweets">リツイート履歴</a>
    <form action="user/logout" method="POST">
        <input type="submit" value="ログアウト">
    </form>

    <h1>ツイート一覧</h1>
    <div class="main">
        <? foreach ($rows as $row) { ?>
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
                <a href='tweet/edit/<?= $row['tweet_id'] ?>'>編集</a>
                <a href='favorite/<?= $row['tweet_id'] ?>'>お気に入り</a>
                <a href='retweet/<?= $row['tweet_id'] ?>'>リツイート</a>
            </div>

                <input type="hidden" value="<?= $row['tweet_id'] ?>" name="tweet_id_hide">
                <input type="submit" value="TEST">
            </form>
        <? } ?>
    </div>
</body>
</html>