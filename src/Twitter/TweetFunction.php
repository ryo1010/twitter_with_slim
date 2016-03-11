<?

namespace Twitter;

class TweetFunction extends DatabaseConnect
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

    public function tweet_display(){
        try {
            $link = $this->db_connect();
            $INSERTED = 0;
            $stmt = $link->query("SELECT tweets.tweet_id,tweets.user_id,tweets.content,tweets.created_at,users.user_name FROM tweets left join users  ON tweets.user_id = users.user_id ORDER BY tweets.created_at DESC");
            $stmt->execute();
            $tweet_rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $tweet_rows;
        } catch (PDOException $e) {
            echo $e->Message();
        } finally {
            $link = null;
            $stmt = null;
        }
    }

    public function tweet_submit($user_id,$content)
    {
        try {
            $link = $this->db_connect();
            $stmt = $link->prepare(
                "INSERT INTO tweets(user_id,content,created_at)
                 VALUES(?,?,now())"
            );
            $stmt->execute(array($user_id,$content));
            return true;

        } catch (PDOException $e) {
            echo $e->getMessage();
        } finally {
            $link = null;
            $stmt = null;
        }
    }

    public function tweet_edit($tweet_id,$user_id){
        try {
            $link = $this->db_connect();
            $stmt = $link->prepare(
                "SELECT tweets.tweet_id, tweets.user_id,
                tweets.content, tweets.created_at, users.user_name
                FROM tweets
                left join users ON
                tweets.user_id = users.user_id
                WHERE tweets.tweet_id = ? AND tweets.user_id = ?
                ORDER BY tweets.created_at DESC"
            );
            $stmt->execute(array($tweet_id,$user_id));
            $tweet_rows = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $tweet_rows;
        } catch(PDOException $e) {
            echo $e->getMessage();
        } finally {
            $link = null;
            $stmt = null;
        }
    }

    public function tweet_edit_submit($tweet_id,$user_id,$content){
        try {
            echo $tweet_id."/".$user_id."/".$content;
            $link = $this->db_connect();
            $stmt = $link->prepare(
                "UPDATE tweets SET content = ?
                 WHERE tweet_id = ? AND user_id = ?"
            );
            $stmt->execute(array($content,$tweet_id,$user_id));
            return true;
        } catch (PDOException $e) {

        } finally {
        }
    }

}