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

  <h1><?= $user_name ?>の情報</h1>
  <a href="/user/follow/<?= $user_id ?>">フォローする</a><br />
  <a href="/user/following/<?= $user_name ?>">フォロー[]</a>
  <a href="/user/follower/<?= $user_name ?>">フォロワー[]</a>
  <div class="main">
  </div>
</body>
</html>