<?
session_start();
require 'vendor/autoload.php';
require 'vendor/slim/slim/Slim/Slim.php';
require 'src/Twitter/DatabaseInfo.php';

$app = new \Slim\Slim;



$app->get('/', function () use ($app) {
    $db_connect = new \Twitter\TweetDisplay();
    $tweet_rows = $db_connect->tweet_display();
    $app->render('tweet_display.php', array('rows' => $tweet_rows));
});

$app->get('/:path', function ($path) use ($app) {
    try{
        $app->render($path.'.php');
    }catch (Exception $e){
        echo "そのファイルはありません。";
    }
});

$app->post('/login', function () use ($app){
    $login_auth = new \Twitter\LoginAuth();
    $usr_mail = $app->request->post('mail_address');
    $usr_pw = $app->request->post('password');
    $login_auth->login_auth($usr_mail,$usr_pw);
});

$app->post('/tweet_submit', function () use ($app){
    //echo $content;
    //echo $content;
});

$app->run();
