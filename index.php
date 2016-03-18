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
    if ($login_auth->loginAuth() == true) {
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
        $app->redirect('/login');
    }
});

$app->get('/', function () use ($app, $page_title) {
    if ((new \Twitter\User)->isLoginEnabled()) {
        $app->redirect('/login');
    }
    if ((new \Twitter\User)->isLoginEnabled()) {
        $app->redirect('/login');
    }
    $tweet_time = new \Twitter\TweetTimeDiff();
    $tweet_rows = $tweet_time
                    ->setUserId($_SESSION['user_id'])
                    ->tweetDisplay();
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
    $tweet_content = nl2br($tweet_content);
    $tweet_insert = $db_connect
            ->setUserId($_SESSION['user_id'])
            ->setContent($tweet_content)
            ->tweetInsert();
    if ($tweet_insert == true) {
        $app->redirect('/');
    }else{

    }
});

$app->get('/tweet/edit/:number', function ($number) use ($app, $page_title, $error_info) {
    if ((new \Twitter\User)->isLoginEnabled()) {
        $app->redirect('/login');
    }
    $tweet_edit = new \Twitter\Tweet();
    $select_tweet = $tweet_edit
        ->setUserId($_SESSION['user_id'])
        ->setTweetId($number)
        ->tweetEditSelect($number,$_SESSION['user_id']);
    if ($select_tweet !== false ) {
        $app->render(
            'header.php',
            ['title' => $page_title['tweet_edit']]
        );
        $app->render(
            'tweet_edit.php',
            ['rows' => $select_tweet]
        );
    } else {
        $app->render(
           'error.php',
            ['error_info' => $error_info['not_fount_tweet']]
        );
    }
});

$app->post('/tweet/edit', function () use ($app){
    $edit_submit = new \Twitter\Tweet();
    $tweet_content = htmlspecialchars(
        $app->request->post('tweet_content'), ENT_QUOTES
    );
    $tweet_content = nl2br($tweet_content);
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
    if ((new \Twitter\User)->isLoginEnabled()) {
        $app->redirect('/login');
    }
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
    if ((new \Twitter\User)->isLoginEnabled()) {
        $app->redirect('/login');
    }
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

$app->post('/user/create/mail', function () use ($app, $page_title) {
    $app->render(
        'header.php',
        ['title' => $page_title['user_pre_mail_page']]
    );
    $app->render('user_pre_mail.php');
});

$app->post('/user/create/mail/add', function () use ($app, $page_title, $error_info) {
    $confirmation = new \Twitter\User();
    $user_mail = htmlspecialchars(
        $app->request->post('mail_address'), ENT_QUOTES
    );
    $confirmation->setUserMail($user_mail);

    if ($confirmation->mailCheck() == true) {
        if ($confirmation->mailConfirmation()) {
            $user_id = uniqid(rand(100,999));
            $_SESSION['user_number'] = $user_id;
            $_SESSION['user_mail'] = $user_mail;
            $app->redirect("/mail/sent");
        } else {
            $app->render(
                'error.php',
                ['error_info' => $error_info['mail_already']]
            );
        }
    } else {
        $app->render(
            'error.php',
            ['error_info' => $error_info['mail_not_correct']]
        );

    }

});
$app->get('/mail/sent', function () use ($app) {
    if (isset($_SESSION['user_mail']) &&
                isset($_SESSION['user_number'])) {
        $app->render(
            'mail_sent.php',
            ['user_mail' => $_SESSION['user_mail'],
            'user_number' => $_SESSION['user_number']]
        );
    } else {
        $app->render(
            'error.php',
            ['error_info' => 'URLが正しくありません']
        );
    }
});

$app->get('/user/create/info/:number', function ($number) use ($app, $page_title, $error_info) {
    if ($number == $_SESSION['user_number']) {
        $app->render(
            'header.php',
            ['title' => $page_title['user_info_page']]
        );
        $app->render('user_info.php');
     } else {
        $app->render(
            'error.php',
            ['error_info' => 'メールに届いたURLを正しく入力してください']
        );
     }
});

$app->post('/user/create/info', function () use ($app) {
    $user_create = new \Twitter\User();
    $user_create
        ->setUserName($app->request->post('user_name'))
        ->setUserPassword(md5($app->request->post('user_password')))
        ->setUserMail($_SESSION['user_mail'])
        ->userCreate();
    $app->redirect('/login');
});

$app->get('/favorite/:number', function ($number) use ($app) {
    if ((new \Twitter\User)->isLoginEnabled()) {
        $app->redirect('/login');
    }
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
    if ((new \Twitter\User)->isLoginEnabled()) {
        $app->redirect('/login');
    }
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
    if ((new \Twitter\User)->isLoginEnabled()) {
        $app->redirect('/login');
    }
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
    if ((new \Twitter\User)->isLoginEnabled()) {
        $app->redirect('/login');
    }
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
    if ((new \Twitter\User)->isLoginEnabled()) {
        $app->redirect('/login');
    }
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
    if ((new \Twitter\User)->isLoginEnabled()) {
        $app->redirect('/login');
    }
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
    $detail = new \Twitter\User();
    $detail->setUserId($user_id);
    if ((new \Twitter\User)->isLoginEnabled()) {
        $follow_status = "";
    }else {
        $detail->setFollowUserId($_SESSION['user_id']);
        if ($detail->isFollowingEnabled()) {
            $follow_status = "フォローしてます";
        } else {
        $follow_status = "フォローする";
        }
    }
    $user_name = $detail->selectUserName();
    if ($detail->userFind()) {
        $tweet_rows = $detail->userDetail();
        if ( $tweet_rows !== false ) {
             $app->render(
                'header.php',
                ['title' => $user_name.$page_title['user_detail_page']]
            );
            $app->render(
                'user_detail.php',
                ['tweet_rows'=> $tweet_rows,
                'follow_status' => $follow_status]
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

$app->get('/test' , function () use ($app) {
    $app->render('imgfileuplode.php');
});

$app->post('/test' , function () use ($app) {
    $app->render('imgfileseve.php');
});

$app->notFound(function () use ($app) {
    $app->render(
        'error.php',
        ['error_info' => 'ページが見つかりません']
    );
});


$app->run();