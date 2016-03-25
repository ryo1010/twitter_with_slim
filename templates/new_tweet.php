  <? foreach ($rows as $row) { ?>
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
    <?= nl2br($row['content']) ?>
  </div>
  <div class="image">
    <? if($row['images_url'] !== null) : ?>
    <a href="/images/<?=$row['images_url']?>" target="_new">
      <img src="/images/<?=$row['images_url']?>" width="400">
    </a>
    <? endif;?>
  </div>
  <div class='tweet_edit'>
    <? if ($row['user_id'] == $_SESSION['user_id']) { ?>
    <a href='/tweet/edit/<?= $row['tweet_id'] ?>'>編集</a>
    <? } else { ?>
     <a href='/favorite/<?= $row['tweet_id'] ?>'>★</a>
     <a href='/retweet/<?= $row['tweet_id'] ?>'>↻</a>
    <? }?>
  </div>
  <? } ?>