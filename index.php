<?
session_start();
require 'vendor/autoload.php';

$today = new DateTime('now', new DateTimeZone("Asia/Tokyo"));
$logWriter = new \Slim\logWriter(
    fopen('./logs/' . $today->format('Y-m-d'), 'a'));

$config = array(
    'log.level' => \Slim\Log::ERROR,
    'log.writer' => $logWriter
    );

$app = new \Slim\Slim($config);



$app->post('/login', function () use ($app){
    $login_auth = new \Twitter\User();
    $escape = new \Twitter\Tweet();
    $mail_address = $escape
        ->htmlEscape($app->request->post('mail_address'));
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

$app->get('/', function () use ($app) {
    if ((new \Twitter\User)->isLoginEnabled()) {
        $app->redirect('/login');
    }
    if (isset($_GET['display_number']) && isset($_GET['display_limit'])) {
        $display_number = $_GET['display_number'];
        $display_limit = $_GET['display_limit'];
    } else {
        $display_number = 0;
        $display_limit = 10;
    }
    $tweet_display = new \Twitter\Tweet();
    $tweet_rows = $tweet_display
                    ->setUserId($_SESSION['user_id'])
                    ->setDisplayNumber($display_number)
                    ->setDisplayLimit($display_limit)
                    ->tweetDisplay();
    if ($tweet_rows == "not_found" ) {
        $app->render(
            'error.php',
            ['error_info' => \Twitter\Info::ERRORINFO['not_found']]
        );
    } else {
        $tweet_time_diff = new \Twitter\TweetTimeDiff();
        $tweet_rows = $tweet_time_diff -> tweetTimeChenge($tweet_rows);
        $app->render(
            'header.php',
            ['title' => \Twitter\Info::PAGETITLE['top_page']]
        );
        $app->render(
            'tweet_display.php',
            ['rows' => $tweet_rows]
        );
    }
});

$app->post('/', function () use ($app) {
    if ((new \Twitter\User)->isLoginEnabled()) {
        $app->redirect('/login');
    }
    $display_number = $_POST['display_number'];
    $display_limit = $_POST['display_limit'];
    $tweet_display = new \Twitter\Tweet();
    $tweet_rows = $tweet_display
                    ->setUserId($_SESSION['user_id'])
                    ->setDisplayNumber($display_number)
                    ->setDisplayLimit($display_limit)
                    ->tweetDisplay();
    if ($tweet_rows !== "not_found" ) {
        $tweet_time_diff = new \Twitter\TweetTimeDiff();
        $tweet_rows = $tweet_time_diff -> tweetTimeChenge($tweet_rows);
        $app->render(
            'new_tweet.php',
            ['rows' => $tweet_rows]
        );
    }
});

$app->post('/tweet/images', function () use ($app){
    $tweet_submit = new \Twitter\Images();
    $image_uplode_message = $tweet_submit -> imageUpload();
});

$app->post('/tweet/submit', function () use ($app){
    $tweet_submit = new \Twitter\Images();
    $tweet_content = $tweet_submit
        ->htmlEscape($app->request->post('tweet_content'));
    $tweet_insert = $tweet_submit
            ->setUserId($_SESSION['user_id'])
            ->setContent($tweet_content);
    $tweet_insert = $tweet_submit->tweetInsert();
    $data = $app->request->post('images');
    $im = imagecreatefromstring(base64_decode($data));
    if ($im !== false) {
        header('Content-Type: image/png');
        imagepng($im);
        imagedestroy($im);
    }
    //$image_uplode_message = $tweet_submit -> imageUpload();
    /*
    switch ($image_uplode_message) {
        case true OR false:
            $app->redirect('/');
            break;
        case 'can_not_upload':
            $app->render(
               'error.php',
                ['error_info' => $error_info['can_not_uplode']]
            );
            break;
        case 'file_type_Fraud':
            $app->render(
               'error.php',
                ['error_info' => $error_info['file_type_Fraud']]
            );
            break;
        default:
            break;
    }
    */
});

$app->post('/tweet/submit/after', function () use ($app) {
    $tweet = new \Twitter\Tweet();
    $tweet
        ->setUserId($_SESSION['user_id'])
        ->setTweetId($app->request->post('tweet_id'));
    $tweet_rows = $tweet->newTweetDisplay();
    $tweet_time_diff = new \Twitter\TweetTimeDiff();
    $tweet_rows = $tweet_time_diff -> tweetTimeChenge($tweet_rows);
    $app->render(
           'new_tweet.php',
            ['rows' => $tweet_rows]
        );
});

$app->get('/tweet/edit/:number', function ($number) use ($app) {
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
            ['title' => \Twitter\Info::PAGETITLE['tweet_edit']]
        );
        $app->render(
            'tweet_edit.php',
            ['rows' => $select_tweet]
        );
    } else {
        $app->render(
           'error.php',
            ['error_info' => \Twitter\Info::ERRORINFO['not_fount_tweet']]
        );
    }
});

$app->post('/tweet/edit', function () use ($app){
    $edit_submit = new \Twitter\Tweet();
    $tweet_content = $edit_submit
        ->htmlEscape($app->request->post('tweet_content'));
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

$app->get('/tweet', function () use ($app) {
    if ((new \Twitter\User)->isLoginEnabled()) {
        $app->redirect('/login');
    }
    $app->render('tweet.php');
});

$app->get('/login', function () use ($app) {
    $app->render(
        'header.php',
        ['title' => \Twitter\Info::PAGETITLE['login_page']]
    );
    $app->render(
        'login.php',
        ['info' => "メールアドレス・パスワードを入力してください"]
    );
});

$app->post('/user/create/mail', function () use ($app) {
    $app->render(
        'header.php',
        ['title' => \Twitter\Info::PAGETITLE['user_pre_mail_page']]
    );
    $app->render('user_pre_mail.php');
});

$app->post('/user/create/mail/add', function () use ($app) {
    $confirmation = new \Twitter\User();
    $escape = new \Twitter\tweet();
    $mail = new \Twitter\User();
    $user_mail = $escape
        ->htmlEscape($app->request->post('mail_address'));
    $confirmation->setUserMail($user_mail);
    if ($confirmation->mailCheck() == true) {
        if ($confirmation->mailConfirmation()) {
            $user_id = uniqid(rand(100,999));
            $_SESSION['user_number'] = $user_id;
            $_SESSION['user_mail'] = $user_mail;

            $mail->setUserMail($user_mail)
                 ->setUserNumber($user_id)
                 ->createUserSendMail($user_mail);
            $app->redirect("/mail/sent");
        } else {
            $app->render(
                'error.php',
                ['error_info' => \Twitter\Info::ERRORINFO['mail_already']]
            );
        }
    } else {
        $app->render(
            'error.php',
            ['error_info' => \Twitter\Info::ERRORINFO['mail_not_correct']]
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
            ['error_info' => \Twitter\Info::ERRORINFO['url_erro']]
        );
    }
});

$app->get('/user/create/info/:number', function ($number) use ($app) {
    if ($number == $_SESSION['user_number']) {
        $app->render(
            'header.php',
        ['title' => \Twitter\Info::PAGETITLE['user_info_page']]
        );
        $app->render('user_info.php');
     } else {
        $app->render(
            'error.php',
            ['error_info' => \Twitter\Info::ERRORINFO['create_user_url_erro']]
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
    $tweet_favorite = new \Twitter\Favorite();
    $tweet_favorite
        ->setUserId($_SESSION['user_id'])
        ->setTweetId($number);
    if ($tweet_favorite->tweetFavorite() == true) {
        $app->redirect('/');
    }else{
        $app->render(
            'error.php',
            ['error_info' => \Twitter\Info::ERRORINFO['can_not_favorite']]
        );
    }
});

$app->get('/retweet/:number', function ($number) use ($app) {
    if ((new \Twitter\User)->isLoginEnabled()) {
        $app->redirect('/login');
    }
    $tweet_retweet = new \Twitter\Retweet();
    $tweet_retweet
        ->setUserId($_SESSION['user_id'])
        ->setTweetId($number);
    if ($tweet_retweet->tweetRetweet() == true) {
        $app->redirect('/');
    }else{
        $app->render(
            'error.php',
            ['error_info' => \Twitter\Info::ERRORINFO['can_not_retweet']]
        );
    }
});

$app->get('/retweet/delete/:number', function ($number) use ($app) {
    if ((new \Twitter\User)->isLoginEnabled()) {
        $app->redirect('/login');
    }
    $retweet_delete = new \Twitter\Retweet();
    $retweet_delete
        ->setUserId($_SESSION['user_id'])
        ->setTweetId($number);
    if ($retweet_delete->tweetRetweetDelete() == true) {
        $app->redirect('/');
    }else{
        $app->render(
            'error.php',
            ['error_info' => \Twitter\Info::ERRORINFO['can_not_retweet_delete']]
        );
    }
});

$app->get('/favorite/delete/:number', function ($number) use ($app) {
    if ((new \Twitter\User)->isLoginEnabled()) {
        $app->redirect('/login');
    }
    $favorite_delete = new \Twitter\Favorite();
    $favorite_delete
        ->setUserId($_SESSION['user_id'])
        ->setTweetId($number);
    if ($favorite_delete->tweetFavoriteDelete() == true) {
        $app->redirect('/');
    }else{
        $app->render(
            'error.php',
            ['error_info' => \Twitter\Info::ERRORINFO['can_not_favorite_delete']]
        );
    }
});

$app->get('/tweet/favorites', function () use ($app) {
    if ((new \Twitter\User)->isLoginEnabled()) {
        $app->redirect('/login');
    }

    $favorites_history = new \Twitter\Favorite();
    $favorite_rows = $favorites_history
        ->setUserId($_SESSION['user_id'])
        ->tweetFavoriteHistory();
    $app->render(
        'header.php',
        ['title' => \Twitter\Info::PAGETITLE['favorites_history_page']]
    );

    $app->render(
        'favorite_history.php',
        ['rows'=>$favorite_rows]
    );
});

$app->get('/user/:user_id', function ($user_id) use ($app) {
    $detail = new \Twitter\User();
    $detail->setUserId($user_id);
    if ((new \Twitter\User)->isLoginEnabled()) {
        $follow_status = "";
    }else {
        $detail->setFollowUserId($_SESSION['user_id']);
        if ($detail->isFollowingEnabled()) {
            $follow_status = true;
        } else {
            $follow_status = false;
        }
    }

    $user_name = $detail->selectUserName();
    if ($detail->userFind()) {
        $tweet_rows = $detail->userDetail();
        if ( $tweet_rows !== false ) {
             $app->render(
                'header.php',
                ['title' => $user_name.\Twitter\Info::PAGETITLE['user_detail_page']]
            );
            $app->render(
                'user_detail.php',
                ['tweet_rows'=> $tweet_rows,
                'follow_status' => $follow_status]
            );
        } else {
            $app->render(
                'header.php',
                ['title' => $user_name.\Twitter\Info::PAGETITLE['user_detail_page']]
            );
            $app->render(
                'user_detail_no_tweet.php',
                ['user_name'=>$user_name]
            );
        }
    } else {
        $app->render(
            'error.php',
            ['error_info' => \Twitter\Info::ERRORINFO['not_found_user']]
        );
    }
});

$app->get('/user/follow/:user_id' , function ($user_id) use ($app) {
    $follow = new \Twitter\User();
    $follow
        ->setUserId($_SESSION['user_id'])
        ->setFollowUserId($user_id);
    if ($follow -> userFollow() == true) {
        $app->redirect("/user/{$user_id}");
    } else {
        $app->render(
            'error.php',
            ['error_info' => \Twitter\Info::ERRORINFO['can_not_follow']]
        );
    }
});

$app->get('/user/refollow/:user_id' , function ($user_id) use ($app) {
    $follow = new \Twitter\User();
    $follow
        ->setUserId($_SESSION['user_id'])
        ->setFollowUserId($user_id);
    if ($follow -> userReFollow() == true) {
        $app->redirect("/user/{$user_id}");
    } else {
        $app->render(
            'error.php',
            ['error_info' => \Twitter\Info::ERRORINFO['can_not_follow']]
        );
    }
});

$app->post('/tweet/search' , function () use ($app) {
    $tweet_search = new \Twitter\Search();
    $tweet_time_diff = new \Twitter\TweetTimeDiff();

    $search_word = $tweet_search
        ->htmlEscape($app->request->post('tweet_search'));

    $result = $tweet_search
        ->tweetSearch($search_word);

    $result = $tweet_time_diff
        -> tweetTimeChenge($result);

    $app->render(
        'header.php',
        ['title' => \Twitter\Info::PAGETITLE['tweet_search']]
    );
    $app->render('tweet_search.php',
        ['search_word' => $search_word,'rows' => $result]
    );
});


$app->get('/tweet/select/:number' , function ($number) use ($app) {
    if ((new \Twitter\User)->isLoginEnabled()) {
            $app->redirect('/login');
        }
        $tweet_select = new \Twitter\Tweet();
        $select_tweet = $tweet_select
            ->setUserId($_SESSION['user_id'])
            ->setTweetId($number)
            ->tweetEditSelect($number,$_SESSION['user_id']);
        if ($select_tweet !== false ) {
            $app->render(
                'header.php',
                ['title' => \Twitter\Info::PAGETITLE['tweet_select']]
            );
            $app->render(
                'tweet_select.php',
                ['rows' => $select_tweet]
            );
        } else {
            $app->render(
               'error.php',
                ['error_info' => \Twitter\Info::ERRORINFO['not_fount_tweet']]
            );
        }
});

// $app->post('/upload', function () use ($app) {
//         if (isset($_FILES["file"]) && is_uploaded_file($_FILES["file"]["tmp_name"])) {
//             if (!$check = array_search(
//                 mime_content_type($_FILES['file']['tmp_name']),
//                 array(
//                     'gif' => 'image/gif',
//                     'jpg' => 'image/jpeg',
//                     'png' => 'image/png',
//                 ),
//                 true
//                 )) {
//                 return 'file_type_Fraud';
//             }
//             $file_name = uniqid("slim_twitter")."_".$_FILES["file"]["name"];
//             if (move_uploaded_file($_FILES["file"]["tmp_name"], "/root/images/images/" . $file_name)) {
//                 $is_insert = $this->imageInsert($file_name);
//                 if ($is_insert == true) {
//                     return true;
//                 }
//             } else {
//                 return "can_not_upload";
//             }
//         } else {
//             return "not_file";
//         }

// });

$app->notFound(function () use ($app) {
    $app->render(
    'error.php',
    ['error_info' => \Twitter\Info::ERRORINFO['not_found_page']]
    );
});

$app->run();