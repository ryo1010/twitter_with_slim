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
    id int not null primary key auto_increment,
    usr_id varchar(30),
    usr_pw varchar(30),
    usr_mail varchar(50),
    status int(1),
    follows_id int UNIQUE
);

------------------------

tweet
PK                                 retweetDBのID  favoriteDBのID
id usr_id tweettime content status retweet_id    favorites_id;

CREATE TABLE tweet (
    id int not null primary key auto_increment,
    usr_id varchar(30),
    tweettime datetime,
    content varchar(255),
    retweet_id int ,
    favorite_id int ,
    stutas int(1) default 0
)ENGINE=InnoDB;

alter table tweet add foreign key(retweet_id) references retweets(retweet_id);
------------------------

retweets
PK  重複する   　重複する
id  retweet_id user_id;

CREATE TABLE retweets (
    id int not null primary key auto_increment,
    user_id varchar(30),
    retweet_id int
)ENGINE=InnoDB;

------------------------

favorites
PK  重複する　    重複する
id favorite_id user_id;

CREATE TABLE fovorites (
    id int not null primary key auto_increment,
    user_id varchar(30),
    fovorites_id int
);

------------------------

user_follows
PK QK
id usr_id follows_id

CREATE TABLE user_follows (
    id int not null primary key auto_increment,
    user_id int,
    follows_id varchar(30)
);

