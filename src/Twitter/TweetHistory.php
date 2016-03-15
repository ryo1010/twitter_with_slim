<?

namespace Twitter;

class TweetHistory extends DatabaseConnect
{

    public function __construct(){
        $this->login_auth();
    }

    public function login_auth()
    {
        if (!isset($_SESSION['user_name']) && !isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
    }

    public function tweet_history($user_id)
    {
    $link = $this->db_connect();
    $TWEET_HISTORY = 1;
    if ($stmt = $link->prepare(
        "SELECT tweets.tweet_id,tweets.user_id,tweets.stutas,
        tweets.content,tweets.created_at,users.user_name
        FROM tweets left join users
        ON tweets.user_id = users.user_id
        WHERE tweets.user_id = ? AND
        tweets.stutas = $TWEET_HISTORY ORDER BY tweets.created_at DESC"
    )) {
        $stmt->execute(array($user_id));
        $tweet_rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    $stmt=null;
    $link=null;
    return $tweet_rows;
    }
}