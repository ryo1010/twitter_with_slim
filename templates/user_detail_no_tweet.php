    <div class='username_div'>ユーザ名<?= $user_name ?></div>
    <a href="/tweet">つぶやく</a>
    <a href="/tweet/history">削除履歴</a>
    <a href="/tweet/favorites">お気に入り履歴</a>
    <a href="/tweet/retweets">リツイート履歴</a>
    <form action="user/logout" method="POST">
        <input type="submit" value="ログアウト">
    </form>

    <h1><?= $user_name ?>の情報</h1>
    <a href="/user/follow/<?= $user_name ?>">フォローする</a><br />
    <a href="/user/following/<?= $user_name ?>">フォロー[]</a>
    <a href="/user/follower/<?= $user_name ?>">フォロワー[]</a>
    <div class="main">
    </div>
</body>
</html>