<?

namespace Twitter;

class Tweet extends DatabaseConnect
{

    private $tweet_id;
    private $user_id;
    private $user_name;
    private $content;
    private $INSERTED = 0;

    public function __construct(){
        $this->login_auth();
    }

    public function login_auth()
    {
        if (!isset($_SESSION['user_name'])
            && !isset($_SESSION['user_id'])) {

            header('Location: /login');
            exit();
        }
    }


    public function setTweetId($tweet_id)
    {
        $this->tweet_id = $tweet_id;
        return $this;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }

    public function setUserName($user_name)
    {
        $this->user_name = $user_name;
        return $this;
    }

    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }


    public function tweetDisplay(){
        try {
            $link = $this->db_connect();
            $stmt = $link->query(
                "SELECT tweets.tweet_id,tweets.user_id,tweets.stutas,
                tweets.content,tweets.created_at,users.user_name
                FROM tweets left join users
                ON tweets.user_id = users.user_id
                WHERE tweets.stutas = $this->INSERTED
                ORDER BY tweets.created_at DESC"
            );
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

    public function tweetInsert()
    {
        try {
            $link = $this->db_connect();
            $stmt = $link->prepare(
                "INSERT INTO tweets(user_id,content,created_at)
                 VALUES(?,?,now())"
            );
            $stmt->execute(
                array(
                    $this->user_id,
                    $this->content
                )
            );
            return true;

        } catch (PDOException $e) {
            echo $e->getMessage();
        } finally {
            $link = null;
            $stmt = null;
        }
    }

    public function tweetEditSelect(){
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
            $stmt->execute(
                array(
                    $this->tweet_id,
                    $this->user_id
                )
            );
            $tweet_rows = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $tweet_rows;
        } catch(PDOException $e) {
            echo $e->getMessage();
        } finally {
            $link = null;
            $stmt = null;
        }
    }

    public function tweetEditSubmit(){
        try {
            $link = $this->db_connect();
            $stmt = $link->prepare(
                "UPDATE tweets SET content = ?
                 WHERE tweet_id = ? AND user_id = ?"
            );
            $stmt->execute(array($this->content,$this->tweet_id,$this->user_id));
            return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
        } finally {
            $link = null;
            $stmt = null;
        }
    }

    public function tweetDelete()
    {
        try {
            $link = $this->db_connect();
            $STATUS = 1;
            if ( $stmt = $link->prepare(
                "UPDATE tweets SET stutas = ?
                WHERE tweet_id = ? AND user_id = ?"
            )) {
                $stmt->execute(
                array(
                    $STATUS,
                    $this->tweet_id,
                    $this->user_id
                    )
                );
            }
            return true;
        } catch (Exception $e) {
            return false;
        } finally {
            $link = null;
            $stmt = null;
        }
    }

    public function tweetHistory()
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
            $stmt->execute(
                array(
                    $this->user_id
                )
            );
            $tweet_rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        $stmt=null;
        $link=null;
        return $tweet_rows;
    }

    public function tweetFavorite()
    {
        try {
            $link = $this->db_connect();
            if ( $stmt = $link->prepare(
                "INSERT INTO favorites (user_id,tweet_id,created_at)
                VALUES(?,?,now())"
            )) {
                $stmt->execute(
                array(
                    $this->user_id,
                    $this->tweet_id
                    )
                );
            }
            return true;
        } catch (Exception $e) {
            return false;
        } finally {
            $link = null;
            $stmt = null;
        }
    }
}