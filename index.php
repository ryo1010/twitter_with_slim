<?
session_start();
require 'vendor/autoload.php';
require 'src/Twitter/title_info.php';


$app = new \Slim\Slim;

$app->post('/login', function () use ($app){
    $login_auth = new \Twitter\User();
    $mail_address = htmlspecialchars(
        $app->request->post('mail_address'), ENT_QUOTES
    );
    $login_auth
        ->setUserMail($mail_address)
        ->setUserPassword(md5($app->request->post('password')));
    if ($login_auth->login_auth() == true) {
        $app->redirect("/");
    }else{
        $app->render(
            'login.php',
            ['info' => "メールアドレス・パスワードが一致しません"]
        );
    }
});

$app->post('/user/logout', function () use ($app){
    $logout = new \Twitter\User();
    $logout_flag = $logout->userLogOut();
    if ($logout_flag == true ) {
        $app->redirect('/');
    }
});

$app->get('/', function () use ($app, $page_title) {
    $tweet_time = new \Twitter\TweetTimeDiff();
    $tweet_rows = $tweet_time->tweetDisplay();
    if ($tweet_rows == "not_found" ) {
        $app->render(
            'error.php',
            ['error_info' => 'ツイートがありません。']
        );
    } else {
        $tweet_rows = $tweet_time->tweetTimeChenge($tweet_rows);
        $app->render(
            'header.php',
            ['title' => $page_title['top_page']]
        );
        $app->render(
            'tweet_display.php',
            ['rows' => $tweet_rows]
        );
    }
});

$app->post('/tweet/submit', function () use ($app){
    $db_connect = new \Twitter\Tweet();
    $tweet_content = htmlspecialchars(
        $app->request->post('tweet_content'), ENT_QUOTES
    );
    $tweet_insert = $db_connect
            ->setUserId($_SESSION['user_id'])
            ->setContent($tweet_content)
            ->tweetInsert();
    if ($tweet_insert == true) {
        $app->redirect('/');
    }else{

    }
});

$app->get('/tweet/edit/:number', function ($number) use ($app, $page_title) {
    $tweet_edit = new \Twitter\Tweet();
    $select_tweet = $tweet_edit
        ->setUserId($_SESSION['user_id'])
        ->setTweetId($number)
        ->tweetEditSelect($number,$_SESSION['user_id']);
    $app->render(
        'header.php',
        ['title' => $page_title['tweet_edit']]
    );
    $app->render(
        'tweet_edit.php',
        ['rows' => $select_tweet]
    );
});

$app->post('/tweet/edit', function () use ($app){
    $edit_submit = new \Twitter\Tweet();
    $tweet_content = htmlspecialchars(
        $app->request->post('tweet_content'), ENT_QUOTES
    );
    $edit_submit
        ->setUserId($_SESSION['user_id'])
        ->setTweetId($app->request->post('tweet_id'))
        ->setContent($tweet_content);
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

$app->get('/tweet/history', function () use ($app, $page_title) {
    $history = new \Twitter\Tweet();
    $history_rows = $history
        ->setUserId($_SESSION['user_id'])
        ->tweetHistory();
    $app->render(
        'header.php',
        ['title' => $page_title['tweet_delete_page']]
    );
    $app->render(
        'tweet_history.php',
        ['rows' => $history_rows]
    );
});

$app->get('/tweet', function () use ($app, $page_title) {
    $app->render(
        'header.php',
        ['title' => $page_title['tweet_page']]
    );
    $app->render('tweet.php');
});

$app->get('/login', function () use ($app, $page_title) {
    $app->render(
        'header.php',
        ['title' => $page_title['login_page']]
    );
    $app->render(
        'login.php',
        ['info' => "メールアドレス・パスワードを入力してください"]
    );
});

$app->get('/user/create/mail', function () use ($app, $page_title) {
    $app->render(
        'header.php',
        ['title' => $page_title['user_pre_mail_page']]
    );
    $app->render('user_pre_mail.php');
});

$app->post('/user/create/mail', function () use ($app) {
    $user_mail = $app->request->post('mail_address');
    $user_id = uniqid(rand(100,999));
    $_SESSION['user_number'] = $user_id;
    $url = "slim-twitter.jp/user/create/info/";
    echo $url.$user_id;
});

$app->get('/user/create/info/:number', function ($number) use ($app, $page_title) {
    if ($number == $_SESSION['user_number']) {
        $app->render(
            'header.php',
            ['title' => $page_title['user_info_page']]
        );
        $app->render('user_info.php');
     }
});

$app->post('/user/create/info', function () use ($app) {
    $user_create = new \Twitter\User();
    $user_create
        ->setUserName($app->request->post('user_name'))
        ->setUserPassword(md5($app->request->post('user_password')))
        ->setUserMail($app->request->post('mail_address'))
        ->userCreate();
});

$app->get('/favorite/:number', function ($number) use ($app) {
    $tweet_favorite = new \Twitter\Tweet();
    $tweet_favorite
        ->setUserId($_SESSION['user_id'])
        ->setTweetId($number);
    if ($tweet_favorite->tweetFavorite() == true) {
        $app->redirect('/');
    }else{
        $app->render(
            'error.php',
            ['error_info' => 'お気に入りできませんでした。']
        );
    }
});

$app->get('/retweet/:number', function ($number) use ($app) {
    $tweet_retweet = new \Twitter\Tweet();
    $tweet_retweet
        ->setUserId($_SESSION['user_id'])
        ->setTweetId($number);
    if ($tweet_retweet->tweetRetweet() == true) {
        $app->redirect('/');
    }else{
        $app->render(
            'error.php',
            ['error_info' => 'リツイートできませんでした。']
        );
    }
});

$app->get('/retweet/delete/:number', function ($number) use ($app) {
    $retweet_delete = new \Twitter\Tweet();
    $retweet_delete
        ->setUserId($_SESSION['user_id'])
        ->setTweetId($number);
    if ($retweet_delete->tweetRetweetDelete() == true) {
        $app->redirect('/');
    }else{
        $app->render(
            'error.php',
            ['error_info' => 'リツイートを取り消せませんでした。']
        );
    }
});

$app->get('/favorite/delete/:number', function ($number) use ($app) {
    $favorite_delete = new \Twitter\Tweet();
    $favorite_delete
        ->setUserId($_SESSION['user_id'])
        ->setTweetId($number);
    if ($favorite_delete->tweetFavoriteDelete() == true) {
        $app->redirect('/');
    }else{
        $app->render(
            'error.php',
            ['error_info' => 'リツイートを取り消せませんでした。']
        );
    }
});

$app->get('/tweet/favorites', function () use ($app, $page_title) {
    $favorites_history = new \Twitter\Tweet();
    $favorite_rows = $favorites_history
        ->setUserId($_SESSION['user_id'])
        ->tweetFavoriteHistory();
    $app->render(
        'header.php',
        ['title' => $page_title['favorites_history_page']]
    );
    $app->render(
        'favorite_history.php',
        ['rows'=>$favorite_rows]
    );
});

$app->get('/tweet/retweets', function () use ($app, $page_title) {
    $retweets_history = new \Twitter\Tweet();
    $retweet_rows = $retweets_history
        ->setUserId($_SESSION['user_id'])
        ->tweetRetweetHistor();
    $app->render(
        'header.php',
        ['title' => $page_title['retweets_history_page']]
    );
    $app->render(
        'retweet_history.php',
        ['rows'=>$retweet_rows]
    );
});

$app->get('/user/:user_id', function ($user_id) use ($app, $page_title) {
    $user_detail = new \Twitter\User();
    $user_detail->setUserId($user_id);
    $user_name = $user_detail->selectUserName();
    if ($user_detail->userFind()) {
        $tweet_rows = $user_detail->userDetail();
        if ( $tweet_rows !== false ) {
             $app->render(
                'header.php',
                ['title' => $user_name.$page_title['user_detail_page']]
            );
            $app->render(
                'user_detail.php',
                ['tweet_rows'=> $tweet_rows]
            );

        } else {
            $app->render(
                'header.php',
                ['title' => $user_name.$page_title['user_detail_page']]
            );
            $app->render(
                'user_detail_no_tweet.php',
                ['user_name'=>$user_name]
            );
        }

    } else {
        $app->render(
            'error.php',
            ['error_info' => 'ユーザーが存在しません']
        );
    }
});

$app->get('/user/follow/:user_id' , function ($user_id) use ($app, $page_title) {
    $follow = new \Twitter\User();
    $follow
        ->setUserId($_SESSION['user_id'])
        ->setFollowUserId($user_id);
    if ($follow -> userFollow() == true) {
        $app->redirect("/user/{$user_id}");
    } else {
        $app->render(
            'error.php',
            ['error_info' => 'フォローできませんでした']
        );
    }
});

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

$app->post('/hide/test' , function () use ($app) {
    echo $app->request->post('tweet_id_hide');
});


$app->notFound(function () use ($app) {
    $app->render(
        'error.php',
        ['error_info' => 'ページが見つかりません']
    );
});


$app->run();