<?

namespace Twitter;

class TweetDisplay extends DatabaseConnect
{
    public function __construct(){
        $this->login_auth();
    }

    public function login_auth()
    {
        if (!isset($_SESSION['username'])) {
            header('Location: /login');
            exit();
        }
    }

    public function tweet_display(){
        $link = $this->db_connect();
        $INSERTED = 0;
        $EDIT = 2;
        $stmt = $link->query("SELECT * FROM tweet WHERE status = $INSERTED OR
        status = $EDIT ORDER BY tweettime DESC");
        $stmt->execute();
        $tweet_rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $tweet_rows;
    }
}