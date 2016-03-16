<?
session_start();
require 'vendor/autoload.php';

$app = new \Slim\Slim;

$app->post('/login', function () use ($app){
    $login_auth = new \Twitter\User();
    $login_auth
        ->setUserMail($app->request->post('mail_address'))
        ->setUserPassword(md5($app->request->post('password')));
    if ($login_auth->login_auth() == true) {
        $app->redirect("/");
    }else{
        $app->render('login.php', ['info' => "メールアドレス・パスワードが一致しません"]);
    }
});

$app->post('/user/logout', function () use ($app){
    (new \Twitter\User)->userLogOut();
});

$app->get('/', function () use ($app) {
    $tweet_time = new \Twitter\TweetTimeDiff();
    $tweet_time->setUserName('wa')
               ->setUserId('wa');
    $tweet_rows = $tweet_time->tweetDisplay();
    $tweet_rows = $tweet_time->tweetTimeChenge($tweet_rows);
    $app->render('tweet_display.php', ['rows' => $tweet_rows]);
});

$app->post('/tweet/submit', function () use ($app){
    $db_connect = new \Twitter\Tweet();
    $db_connect
            ->setUserId($_SESSION['user_id'])
            ->setContent($app->request->post('tweet_content'))
            ->tweetInsert();
    if ($db_connect == true) {
        $app->redirect('/');
    }else{

    }
});

$app->get('/tweet/edit/:number', function ($number) use ($app) {
    $tweet_edit = new \Twitter\Tweet();
    $select_tweet = $tweet_edit
        ->setUserId($_SESSION['user_id'])
        ->setTweetId($number)
        ->tweetEditSelect($number,$_SESSION['user_id']);
    $app->render('tweet_edit.php', ['rows' => $select_tweet]);
});

$app->post('/tweet/edit',function () use ($app){
    $edit_submit = new \Twitter\Tweet();
    $edit_submit
        ->setUserId($_SESSION['user_id'])
        ->setTweetId($app->request->post('tweet_id'))
        ->setContent($app->request->post('tweet_content'));
    $edit_submit
        ->tweetEditSubmit();
    if ($edit_submit == true) {
        $app->redirect('/');
    }else{

    }
});

$app->post('/tweet/delete', function () use ($app) {
    $delete = new \Twitter\Tweet();
    $delete
        ->setTweetId($app->request->post('tweet_id'))
        ->setUserId($_SESSION['user_id']);
    $delete_flag = $delete-> tweetDelete();
    if ($delete_flag == true) {
        $app->redirect('/');
    }else{

    }
});

$app->get('/tweet/history', function () use ($app) {
    $history = new \Twitter\Tweet();
    $history_rows = $history
        ->setUserId($_SESSION['user_id'])
        ->tweetHistory();
    $app->render('tweet_history.php', ['rows' => $history_rows]);
});

$app->get('/tweet', function () use ($app) {
    $app->render('tweet.php');
});

$app->get('/login', function () use ($app) {
    $app->render('login.php',['info' => "メールアドレス・パスワードを入力してください"]);
});

$app->get('/user/create/mail', function () use ($app) {
    $app->render('user_pre_mail.php');
});

$app->post('/user/create/mail', function () use ($app) {
    $user_mail = $app->request->post('mail_address');
    $user_id = uniqid(rand(100,999));
    $_SESSION['user_number'] = $user_id;
    $url = "slim-twitter.jp/user/create/info/";
    echo $url.$user_id;
});

$app->get('/user/create/info/:number' , function ($number) use ($app) {
    if ($number == $_SESSION['user_number']) {
        $app->render('user_info.php');
     }
});

$app->post('/user/create/info' , function () use ($app) {
    $user_create = new \Twitter\User();
    $user_create
        ->setUserName($app->request->post('user_name'))
        ->setUserPassword(md5($app->request->post('user_password')))
        ->setUserMail($app->request->post('mail_address'))
        ->userCreate();
});

$app->get('/favorite/:number' , function ($number) use ($app) {
    $tweet_favorite = new \Twitter\Tweet();
    $tweet_favorite
        ->setUserId($_SESSION['user_id'])
        ->setTweetId($number);
    if ($tweet_favorite->tweetFavorite() == true) {
        $app->redirect('/');
    }else{
        $app->render('error.php',['error_info' => 'お気に入りできませんでした。']);
    }
});

$app->get('/retweet/:number' , function ($number) use ($app) {
    $tweet_retweet = new \Twitter\Tweet();
    $tweet_retweet
        ->setUserId($_SESSION['user_id'])
        ->setTweetId($number);
    if ($tweet_retweet->tweetRetweet() == true) {
        $app->redirect('/');
    }else{
        $app->render('error.php',['error_info' => 'リツイートできませんでした。']);
    }
});

$app->get('/retweet/delete/:number' , function ($number) use ($app) {
    $retweet_delete = new \Twitter\Tweet();
    $retweet_delete
        ->setUserId($_SESSION['user_id'])
        ->setTweetId($number);
    if ($retweet_delete->tweetRetweetDelete() == true) {
        $app->redirect('/');
    }else{
        $app->render('error.php',['error_info' => 'リツイートを取り消せませんでした。']);
    }
});

$app->get('/tweet/favorites' , function () use ($app) {
    $favorites_history = new \Twitter\Tweet();
    $favorite_rows = $favorites_history
        ->setUserId($_SESSION['user_id'])
        ->tweetFavoriteHistory();
    $app->render('favorite_history.php',['rows'=>$favorite_rows]);
});

$app->get('/tweet/retweets' , function () use ($app) {
    $retweets_history = new \Twitter\Tweet();
    $retweet_rows = $retweets_history
        ->setUserId($_SESSION['user_id'])
        ->tweetRetweetHistor();
    $app->render('retweet_history.php',['rows'=>$retweet_rows]);
});

$app->get('/user/:user_id' , function ($user_id) use ($app) {
    $user_detail = new \Twitter\User();
    $user_detail->setUserId($user_id);
    if ( $user_detail->userDetail() == true ) {
        $app->render('user_detail.php',['user_info'=>'wa']);
    }
});


$app->run();