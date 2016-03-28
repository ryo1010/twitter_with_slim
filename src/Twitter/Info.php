<?

namespace Twitter;

class Info
{
    const PAGETITLE = array(
        'top_page' => "ツイート一覧",
        'login_page' => "ログインページ",
        'user_pre_mail_page' => "仮登録画面",
        'user_info_page' => "ユーザー情報登録画面",
        'mail_sent' => "メール送信完了",
        'tweet_page' => "つぶやく",
        'tweet_edit' => "ツイート編集",
        'tweet_select' => "ツイート選択",
        'tweet_search' => "ツイート検索",
        'tweet_delete_page' => "ツイート削除履歴",
        'favorites_history_page' => "お気に入り履歴",
        'retweets_history_page' => "リツイート履歴",
        'user_detail_page' => "さんのページ"
    );
    const ERRORINFO = array(
        'not_found_user' => "ユーザーが存在しません",
        'can_not_follow' => "フォローできませんでした",
        'can_not_favorite' => "お気に入りできませんでした",
        'can_not_retweet' => "リツイートできませんでした。",
        'can_not_retweet_delete' => "リツイートを取り消せませんでした。",
        'can_not_favorite_delete' => ",お気に入りを取り消せませんでした。",
        'url_erro' => "URLが正しくありません",
        'create_user_url_erro' =>"メールに届いたURLを正しく入力してください",
        'mail_not_correct' => "メールアドレスが正しくありません。",
        'mail_already' => "すでに登録されているメールアドレスです。",
        'not_found' => "ツイートがありません",
        'not_found_page' => "ページがありません",
        'not_fount_tweet' => "ツイートを選択できませんでした。",
        'can_not_uplode' => "画像をアップロードできませんでした",
        'file_type_Fraud' => "ファイル形式が不正です。"
    );
    function pageTitle($key)
    {
        if (isset($this->PAGETITLE[$key])) {
            return self::PAGETITLE[$key];
        }
    }

}
