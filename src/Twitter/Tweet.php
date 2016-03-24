<?

namespace Twitter;

class Tweet extends Database
{

    public $INSERTED = 0;
    protected $tweeet_id;
    protected $usere_id;
    protected $usere_name;
    protected $conteent;

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

    private function isMyTweet($user_id)
    {
        if ($this->user_id == $user_id) {
            return true;
        } else {
            return false;
        }
    }

    public function tweetDisplay(){
        try {
            $link = $this->db_con;
            $stmt = $link->query(
                "SELECT DISTINCT  tweets.tweet_id, tweets.user_id,
                tweets.content, tweets.created_at,
                users.user_name, users.user_id,
                images.images_url,
                retweets.tweet_id AS retweet_id
                FROM tweets
                LEFT JOIN user_follows
                    ON user_follows.followed_user_id = tweets.user_id
                    OR user_follows.user_id = tweets.user_id
                LEFT JOIN retweets
                    ON retweets.tweet_id = tweets.tweet_id
                LEFT JOIN users
                    ON users.user_id = tweets.user_id
                LEFT JOIN images
                    ON images.tweet_id = tweets.tweet_id
                WHERE
                user_follows.followed_user_id = $this->user_id OR
                (user_follows.user_id = $this->user_id OR
                tweets.user_id = $this->user_id OR retweets.tweet_id = tweets.tweet_id)
                AND tweets.stutas = $this->INSERTED
                ORDER BY tweets.created_at DESC;"
            );
            $stmt->execute();
            if ($stmt->rowCount() >= 1) {
                $tweet_rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
               return $tweet_rows;
            }  else {
                return "not_found";
            }
        } catch (PDOException $e) {

        } finally {
            $stmt = null;
        }
    }

    public function htmlEscape($escape_string)
    {
        return htmlspecialchars($escape_string, ENT_QUOTES);
    }

    public function tweetInsert()
    {
        try {
            $link = $this->db_con;
            $stmt = $link->prepare(
                "INSERT INTO tweets(user_id,content,created_at)
                 VALUES(?,?,now())"
            );
            $stmt->execute(
                [
                    $this->user_id,
                    $this->content
                ]
            );
            $this->tweet_id = $link->lastInsertId('tweet_id');
            return true;

        } catch (PDOException $e) {

        } finally {
            $link = null;
            $stmt = null;
        }
    }

    public function tweetEditSelect(){
        try {
            $link = $this->db_con;
            $stmt = $link->prepare(
                "SELECT tweets.tweet_id, tweets.user_id,
                tweets.content, tweets.created_at, users.user_name,
                images.images_url
                FROM tweets
                left join users ON
                tweets.user_id = users.user_id
                left join images ON
                images.tweet_id = tweets.tweet_id
                WHERE tweets.tweet_id = ? AND tweets.user_id = ?
                ORDER BY tweets.created_at DESC"
            );
            $stmt->execute(
                [
                    $this->tweet_id,
                    $this->user_id
                ]
            );
            $tweet_rows = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($this->isMyTweet($tweet_rows['user_id'])) {
                return $tweet_rows;
            } else {
                return false;
            }

        } catch(PDOException $e) {

        } finally {
            $link = null;
            $stmt = null;
        }
    }

    public function tweetEditSubmit(){
        try {
            $link = $this->db_con;
            $stmt = $link->prepare(
                "UPDATE tweets SET content = ?
                 WHERE tweet_id = ? AND user_id = ?"
            );
            $stmt->execute(
                [
                $this->content,
                $this->tweet_id,
                $this->user_id
                ]
            );
            return true;

        } catch (PDOException $e) {

        } finally {
            $link = null;
            $stmt = null;
        }
    }

    public function tweetDelete()
    {
        try {
            $link = $this->db_con;
            $STATUS = 1;
            if ( $stmt = $link->prepare(
                "UPDATE tweets SET stutas = ?
                WHERE tweet_id = ? AND user_id = ?"
            )) {
                $stmt->execute(
                [
                    $STATUS,
                    $this->tweet_id,
                    $this->user_id
                ]
                );
            }
            return true;
        } catch (PDOException $e) {
            return false;
        } finally {
            $link = null;
            $stmt = null;
        }
    }

    public function tweetHistory()
    {
        $link = $this->db_con;
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
                [
                    $this->user_id
                ]
            );
            $tweet_rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        $stmt=null;
        $link=null;
        return $tweet_rows;
    }
}