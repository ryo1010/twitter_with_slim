<?

namespace Twitter;

class Tweet extends DatabaseConnect
{

    private $tweet_id;
    private $user_id;
    private $user_name;
    private $content;
    private $INSERTED = 0;

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
            $link = $this->db_connect();
            $stmt = $link->query(
                "SELECT DISTINCT  tweets.tweet_id, tweets.user_id,
                tweets.content, tweets.created_at,
                users.user_name, users.user_id,
                retweets.tweet_id AS retweet_id
                FROM tweets
                LEFT JOIN user_follows ON user_follows.followed_user_id = tweets.user_id
                    OR user_follows.user_id = tweets.user_id
                LEFT JOIN retweets ON retweets.tweet_id = tweets.tweet_id
                LEFT JOIN users ON tweets.user_id = users.user_id
                    OR users.user_id = tweets.user_id
                WHERE user_follows.user_id = $this->user_id OR tweets.user_id = $this->user_id
                OR retweets.tweet_id = tweets.tweet_id
                AND user_follows.followed_user_id = $this->user_id
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

            if ($this->isMyTweet($tweet_rows['user_id'])) {
                $tweet_rows['content'] = $this->br2nl($tweet_rows['content']);
                return $tweet_rows;
            } else {
                return false;
            }

        } catch(PDOException $e) {
            echo $e->getMessage();
        } finally {
            $link = null;
            $stmt = null;
        }
    }

    private function br2nl($content) {
        return preg_replace('/<br[[:space:]]*\/?[[:space:]]*>/i', "", $content);
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
        } catch (PDOException $e) {
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
        } catch (PDOException $e) {
            return false;
        } finally {
            $link = null;
            $stmt = null;
        }
    }

    public function tweetRetweet()
    {
        try {
            $link = $this->db_connect();
            if ( $stmt = $link->prepare(
                "INSERT INTO retweets (user_id,tweet_id,created_at)
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
        } catch (PDOException $e) {
            return false;
        } finally {
            $link = null;
            $stmt = null;
        }
    }

    public function tweetFavoriteHistory(){
        try{
            $link = $this->db_connect();
            if ( $stmt = $link->prepare(
                "SELECT
                tweets.content,tweets.user_id,tweets.created_at,
                favorites.tweet_id,
                users.user_name
                FROM favorites
                LEFT JOIN tweets ON favorites.tweet_id = tweets.tweet_id
                LEFT JOIN users ON users.user_id = tweets.user_id
                WHERE favorites.user_id = ?
                ORDER BY tweets.created_at DESC"
            )) {
                $stmt->execute(
                array(
                    $this->user_id,
                    )
                );
            }
            $favorite_rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $favorite_rows;
        } catch (PDOException $e) {

        } finally {
            $link = null;
            $stmt = null;
        }
    }

    public function tweetRetweetHistor(){
        try{
            $link = $this->db_connect();
            if ( $stmt = $link->prepare(
                "SELECT
                tweets.content,tweets.user_id,tweets.created_at,
                retweets.tweet_id,
                users.user_name
                FROM retweets
                LEFT JOIN tweets ON retweets.tweet_id = tweets.tweet_id
                LEFT JOIN users ON users.user_id = tweets.user_id
                WHERE retweets.user_id = ?
                ORDER BY tweets.created_at DESC"
            )) {
                $stmt->execute(
                array(
                    $this->user_id,
                    )
                );
            }
            $retweet_rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $retweet_rows;
        } catch (PDOException $e) {

        } finally {
            $link = null;
            $stmt = null;
        }
    }

    public function tweetRetweetDelete()
    {
        try {
            $link = $this->db_connect();
            if ( $stmt = $link->prepare(
                "DELETE FROM retweets where
                tweet_id = ? AND user_id = ?"
            )) {
                $stmt->execute(
                array(
                    $this->tweet_id,
                    $this->user_id
                    )
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

    public function tweetFavoriteDelete()
    {
        try {
            $link = $this->db_connect();
            if ( $stmt = $link->prepare(
                "DELETE FROM favorites where
                tweet_id = ? AND user_id = ?"
            )) {
                $stmt->execute(
                array(
                    $this->tweet_id,
                    $this->user_id
                    )
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

}