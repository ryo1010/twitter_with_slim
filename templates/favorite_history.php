<a href="/">もどる</a>

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
</body>
</html>