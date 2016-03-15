# twitter_with_slim

## 仕様
* メールアドレス、パスワードでログインできる
* ツイートの一覧が表示できる
* ツイートの投稿ができる
* ツイートの編集ができる
* ツイートの削除ができる
* 削除済みの投稿も履歴として残すこと

* ユーザ登録・仮登録
* お気に入り機能
* リツイート機能
* フォロー機能

users
PK QK     QK     QK       登録状態　QK
id usr_id usr_pw usr_mail status 　follows_id

CREATE TABLE users (
    user_id INT PRIMARY KEY auto_increment,
    user_name varchar(30) NOT NULL UNIQUE,
    user_password varchar(30) NOT NULL UNIQUE,
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

インサート文tweets
insert into tweets (tweet_id,user_id,user_name,content) values(null,1,'user_name','test');

SELECT tweets.tweet_id,tweets.user_id,tweets.content,tweets.created_at,
        users.user_name,
        retweets.tweet_id,retweets.user_id FROM tweets
LEFT JOIN users ON tweets.user_id = users.user_id
LEFT JOIN retweets ON retweets.tweet_id = tweets.tweet_id
ORDER BY tweets.created_at DESC;