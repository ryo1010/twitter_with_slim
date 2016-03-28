<form action="/tweet/select" method="POST">
  <table border='0'>
  <tr>
    <td>
      <a href="/user/<?= $rows['user_id'] ?>">
        ユーザー名：<?= $rows['user_name'] ?>
      </a>
    </td>
  </tr>
  <tr>
    <td>
    ツイート時間<?= $rows['created_at']?>
    </td>
  </tr>
  <? if (isset($rows['images_url'])) : ?>
  <tr>
    <td>
      <a href="/images/<?=$rows['images_url']?>" target="_new">
        <img src="/images/<?= $rows['images_url']?>" width=300>
      </a>
    </td>
  </tr>
  <? endif; ?>
  <tr>
    <td>
    <?= $rows['content']?>
    </td>
  <tr>
    <td>
    <input type="hidden" name="tweet_id" value="<?= $rows['tweet_id']?>">
    </td>
  </tr>
  </table>
  <a href='/favorite/<?= $rows['tweet_id'] ?>'>★</a>
  <a href='/retweet/<?= $rows['tweet_id'] ?>'>↻</a>
</form>
<form action="/tweet/delete" method="POST">
  <input type="hidden" name="tweet_id" value="<?= $rows['tweet_id']?>">
</form>
</body>
</html>