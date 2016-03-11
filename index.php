<?
session_start();
require 'vendor/autoload.php';
require 'vendor/slim/slim/Slim/Slim.php';
require 'src/Twitter/DatabaseInfo.php';

$app = new \Slim\Slim;

$app->post('/login', function () use ($app){
    $login_auth = new \Twitter\LoginAuth();
    $usr_mail = $app->request->post('mail_address');
    $usr_pw = $app->request->post('password');
    $login_auth->login_auth($usr_mail,$usr_pw);
});

$app->get('/', function () use ($app) {
    $tweet_display = new \Twitter\TweetFunction();
    $tweet_time = new \Twitter\TweetTimeDiff();
    $tweet_rows = $tweet_display->tweet_display();
    $tweet_rows = $tweet_time->tweet_time_chenge($tweet_rows);
    $app->render('tweet_display.php', array('rows' => $tweet_rows));
});

$app->post('/tweet_submit', function () use ($app){
    $db_connect = new \Twitter\TweetFunction();
    $content = $app->request->post('tweet_content');
    $user_id = $_SESSION['user_id'];
    $db_connect->tweet_submit($user_id,$content);
    if ($db_connect == true) {
        header('Location: /');
    }else{
        echo "エラー";
    }
});

$app->get('/tweet_edit/:number', function ($number) use ($app) {
    $tweet_edit = new \Twitter\TweetFunction();
    $select_tweet = $tweet_edit->
    tweet_edit($number,$_SESSION['user_id']);
    $app->render('tweet_edit.php', array('rows' => $select_tweet));
});

$app->post('/tweet_edit',function () use ($app){
    $tweet_edit_submit = new \Twitter\TweetFunction();
    $content = $app->request->post('tweet_content');
    $tweet_id = $app->request->post('tweet_id');
    $tweet_edit_submit->tweet_edit_submit($tweet_id,$_SESSION['user_id'],$content);
    if ($tweet_edit_submit == true) {
        header('Location: /');
    }else{
        echo "エラー";
    }
});

$app->get('/:path', function ($path) use ($app) {
    try{
        $app->render($path.'.php');
    }catch (Exception $e){
        header('Location: /');
    }
});

$app->run();
