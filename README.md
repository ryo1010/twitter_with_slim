# twitter_with_slim

## 仕様
○* メールアドレス、パスワードでログインできる
○* ツイートの一覧が表示できる
○* ツイートの投稿ができる
○* ツイートの編集ができる
○* ツイートの削除ができる
○* 削除済みの投稿も履歴として残すこと

△* ユーザ登録・仮登録
○* お気に入り機能　登録・削除
△* リツイート機能　登録・削除
○* フォロー機能　フォローフォロワーを見れるようにしたい

users
PK QK     QK     QK       登録状態　QK
id usr_id usr_pw usr_mail status 　follows_id

CREATE TABLE users (
    user_id INT PRIMARY KEY auto_increment,
    user_name varchar(30) NOT NULL ,
    user_password varchar(30) NOT NULL ,
    user_mail varchar(50) NOT NULL UNIQUE
)ENGINE=InnoDB;



------------------------
tweet
PK                                 retweetDBのID  favoriteDBのID
id usr_id tweettime content status retweet_id    favorites_id;

CREATE TABLE tweets (
    tweet_id int NOT NULL primary key auto_increment,
    user_id INT NOT NULL,
    user_name VARCHAR(30) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    content VARCHAR(255) NOT NULL,
    stutas TINYINT(1) default 0 COMMENT 'display 0 delete 1',
    FOREIGN KEY (user_id)
    REFERENCES users(user_id)
)ENGINE=InnoDB;
------------------------
retweets
PK  重複する   　重複する
id  retweet_id user_id;

CREATE TABLE retweets (
    user_id INT NOT NULL,
    tweet_id INT NOT NULL,
    FOREIGN KEY (user_id)
    REFERENCES users(user_id),
    FOREIGN KEY (tweet_id)
    REFERENCES tweets(tweet_id),
    UNIQUE(user_id,tweet_id)
)ENGINE=InnoDB;

------------------------

favorites
PK  重複する　    重複する
id favorite_id user_id;


CREATE TABLE favorites (
    user_id INT NOT NULL,
    tweet_id INT NOT NULL,
    FOREIGN KEY (user_id)
    REFERENCES users(user_id),
    FOREIGN KEY (tweet_id)
    REFERENCES tweets(tweet_id),
    UNIQUE(user_id,tweet_id)
)ENGINE=InnoDB;

------------------------

user_follows
PK QK
id usr_id follows_id

CREATE TABLE user_follows (
    user_id INT NOT NULL KEY,
    followed_user_id INT NOT NULL,
    FOREIGN KEY (user_id)
    REFERENCES users(user_id),
    FOREIGN KEY (followed_user_id)
    REFERENCES users(user_id),
    UNIQUE(user_id,followed_user_id)
);

images_urlあとで
CREATE TABLE `images` (
  `tweet_id` INT NOT NULL,
  `images_url` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`tweet_id`,`images_url`),
  KEY `tweet_id_idx` (`tweet_id`),
  CONSTRAINT `images_ibfk_1` FOREIGN KEY (`tweet_id`) REFERENCES `tweets` (`tweet_id`)
) ENGINE=InnoDB;

インサート文tweets
insert into tweets (tweet_id,user_id,user_name,content) values(null,1,'user_name','test');

よくわからないもの
SELECT tweets.tweet_id,tweets.user_id,
tweets.content,tweets.created_at,
users.user_name,retweets.tweet_id,
retweets.user_id FROM tweets
LEFT JOIN users ON tweets.user_id = users.user_id
LEFT JOIN retweets ON retweets.tweet_id = tweets.tweet_id
ORDER BY tweets.created_at DESC;

------------------------------------
フォロワーしか見れない
SELECT retweets.tweet_id,tweets.user_id,tweets.content,
retweets.user_id,user_follows.followed_user_id
FROM tweets
LEFT JOIN user_follows ON user_follows.user_id = tweets.user_id
LEFT JOIN retweets ON retweets.tweet_id = tweets.tweet_id
LEFT JOIN users ON users.user_id = tweets.user_id
WHERE retweets.tweet_id = tweets.tweet_id
AND user_follows.followed_user_id = $this->user_id;

フォロワーとリツイートしたもの　その１
SELECT DISTINCT  tweets.tweet_id, tweets.user_id,
tweets.content, tweets.created_at,
users.user_name, users.user_id,
retweets.tweet_id AS retweet_id,
retweets.user_id AS retweet_user_id
FROM tweets
LEFT JOIN user_follows ON user_follows.followed_user_id = tweets.user_id
    OR user_follows.user_id = tweets.user_id
LEFT JOIN retweets ON retweets.tweet_id = tweets.tweet_id
LEFT JOIN users ON tweets.user_id = users.user_id AND users.user_id = retweets.user_id
    OR users.user_id = tweets.user_id
WHERE user_follows.user_id = 5 OR tweets.user_id = 5
OR retweets.tweet_id = tweets.tweet_id
AND user_follows.followed_user_id = 5
ORDER BY tweets.created_at DESC

フォロワーとリツイートしたもの　その２
SELECT DISTINCT  tweets.tweet_id, tweets.user_id,
tweets.content, tweets.created_at,
users.user_name, users.user_id,
retweets.tweet_id AS retweet_id,
retweets.user_id AS retweet_user_id
FROM tweets
LEFT JOIN user_follows ON user_follows.followed_user_id = tweets.user_id
    OR user_follows.user_id = tweets.user_id
LEFT JOIN retweets ON retweets.tweet_id = tweets.tweet_id
LEFT JOIN users ON users.user_id = retweets.user_id OR users.user_id = tweets.user_id
WHERE user_follows.user_id = 5 OR tweets.user_id = 5
OR retweets.tweet_id = tweets.tweet_id
AND user_follows.followed_user_id = 5
ORDER BY tweets.created_at DESC


ユーザーがリツイートした内容
SELECT retweets.tweet_id,tweets.user_id,tweets.content,
retweets.user_id,user_follows.followed_user_id
FROM tweets
LEFT JOIN user_follows ON user_follows.user_id = tweets.user_id
LEFT JOIN retweets ON retweets.tweet_id = tweets.tweet_id
LEFT JOIN users ON users.user_id = tweets.user_id
WHERE retweets.tweet_id = tweets.tweet_id AND user_follows.followed_user_id = 5;



誰リツイートしたか見れる
SELECT tweets.tweet_id,tweets.user_id,tweets.stutas,tweets.content,tweets.created_at,
users.user_name,retweets.user_id
FROM tweets
LEFT JOIN user_follows ON user_follows.followed_user_id = tweets.user_id
LEFT JOIN users ON tweets.user_id = users.user_id
LEFT JOIN retweets ON retweets.tweet_id = tweets.tweet_id
where user_follows.user_id = 5 OR tweets.user_id = 5
AND tweets.stutas = 0
ORDER BY tweets.created_at DESC;

★誰がリツイートしたかやりたい
public function retweetTweetDisplay($rows)
{
    $content_1 = "";
    $content_2 = "";
    $count = 0;
    foreach ($rows as $row) {
        $content_1 = $row['retweet_id'];
        if ($content_1 == $content_2 AND $content_1 !== NULL) {
            $tweet_user = $row['user_name'];
            unset($rows[$count]);
        }
        $content_2 = $content_1;
    $count++;
    }
    echo $tweet_user;
    print_r($rows);
}

★ユーザーがフォローしてる人を見たい
$app->get('/user/following/:user_id' , function ($user_id) use ($app, $page_title) {
    $following = new \Twitter\User();
    $follow
        ->setUserId($_SESSION['user_id']);
    if ($follow -> userFollowingList() == true) {
        $app->redirect("/user/{$user_id}");
    } else {
        $app->render(
            'error.php',
            ['error_info' => '値を取得できませんでした']
        );
    }
});
CREATE TABLE `favorites` (
  `user_id` int(11) NOT NULL,
  `tweet_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`tweet_id`),
  KEY `tweet_id_idx` (`tweet_id`),
  CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`tweet_id`) REFERENCES `tweets` (`tweet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `retweets` (
  `user_id` int(11) NOT NULL,
  `tweet_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `user_id` (`user_id`,`tweet_id`),
  KEY `tweet_id_idx` (`tweet_id`),
  CONSTRAINT `retweets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `retweets_ibfk_2` FOREIGN KEY (`tweet_id`) REFERENCES `tweets` (`tweet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tweets` (
  `tweet_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `content` varchar(255) NOT NULL,
  `stutas` tinyint(1) DEFAULT '0' COMMENT 'display 0 delete 1',
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`tweet_id`),
  KEY `user_id_idx` (`user_id`),
  CONSTRAINT `tweets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;

CREATE TABLE `user_follows` (
  `user_id` int(11) NOT NULL,
  `followed_user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`followed_user_id`),
  KEY `followed_user_id_idx` (`followed_user_id`),
  CONSTRAINT `user_follows_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `user_follows_ibfk_2` FOREIGN KEY (`followed_user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(30) NOT NULL,
  `user_password` varchar(30) NOT NULL,
  `user_mail` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_mail` (`user_mail`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

            location ~ \.(json|yml)$ {
                deny all;
            }
