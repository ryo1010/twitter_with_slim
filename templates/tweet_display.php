<form id="upload-form" method="post" enctype="multipart/form-data">
<table>
  <tr>
  <td>
    <textarea class="tweet" id="tweet_content" name="tweet_content" cols="40" rows="5"></textarea>
  </td>
  </tr>
  <tr>
  <td>
    <input type="file" name="upfile" id="upfile" size="30" multiple="multiple"/>
  </td>
  </tr>
  <tr>
  <td>
     <input type="submit" id="tweet_submit" value="ツイート">
  </td>
  </tr>
</table>
  <div id="output"></div>
</form>
<div id="image_drug_and_drop">
  <div id="drug_image">
  </div>
</div>

<div class='username_div'>ユーザ名<?= $_SESSION['user_name'] ?></div>
<a href="/tweet/favorites">お気に入り履歴</a>
<form action="user/logout" method="POST">
  <input type="submit" value="ログアウト">
</form>
<h1>ツイート一覧</h1>
<input type="hidden" id="last_tweet" value="<?= $rows[0]['tweet_id'] ?>">
<div class="main">
  <div class="new_tweet"></div>
  <? foreach ($rows as $row) { ?>
  <div class="tweet_div">
    <? if (!empty($row['retweet_id']) AND $row['user_id'] !== $_SESSION['user_id']) : ?>
    -----↻リツイート-----
    <? endif; ?>
    <div class='datetime_div'>
      <?= $row['created_at'] ?>
    </div>
    <div class='usr_id_div'>
      <a href="/user/<?= $row['user_id'] ?>"><?= $row['user_name'] ?></a>
    </div>
    <div class='content_div'>
      <a href="tweet/select/<?= $row['tweet_id'] ?>">
        <?= nl2br($row['content']) ?>
      </a>
    </div>
    <div class="image">
      <? if(isset($row['images_array'])) : ?>
        <?foreach ($row['images_array'] as $image) { ?>
          <a href="/images/<?=$image?>" target="_new">
            <img src="/images/<?=$image?>" width="400">
          </a>
        <? } ?>
      <? elseif(isset($row['images_url'])) : ?>
          <a href="/images/<?= $row['images_url'] ?>" target="_new">
            <img src="/images/<?= $row['images_url'] ?>" width="400">
          </a>
      <? endif; ?>
    </div>
    <div class='tweet_edit'>
      <? if ($row['user_id'] == $_SESSION['user_id']) { ?>
      <a href='/tweet/edit/<?= $row['tweet_id'] ?>'>編集</a>
      <? } else { ?>
       <a href='/favorite/<?= $row['tweet_id'] ?>'>★</a>
       <a href='/retweet/<?= $row['tweet_id'] ?>'>↻</a>
      <? }?>
    </div>
  </div>
  <? } ?>
</div>
<input type="hidden" name="display_number" id="display_number" value="0">
<input type="hidden" name="display_limit" id="display_limit" value="10">
</body>
</html>
