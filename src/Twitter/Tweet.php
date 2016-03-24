<?

namespace Twitter;

class Tweet extends DatabaseConnect
{

    private $tweet_id;
    private $user_id;
    private $user_name;
    private $content;
    private $tweet_search;
    private $INSERTED = 0;
    private $sql;
    private $sql_words;

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

    public function setTweetSearch($tweet_search)
    {
        $this->tweet_search = $tweet_search;
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
            $link = null;
            $stmt = null;
        }
    }

    public function retweetTweetDisplay($rows)
    {
        $content_1 = "";
        $content_2 = "";
        $count = 0;
        foreach ($rows as $row) {
            $content_1 = $row['retweet_id'];
            if ($content_1 == $content_2 AND $content_1 !== NULL) {
                $tweet_user = $row['user_name'];
                $row['retweet_user'] = $tweet_user;
            }
            $content_2 = $content_1;
        $count++;
        }
    }

    public function imageUpload()
    {
        if (is_uploaded_file($_FILES["file"]["tmp_name"])) {
            if (!$check = array_search(
                mime_content_type($_FILES['file']['tmp_name']),
                array(
                    'gif' => 'image/gif',
                    'jpg' => 'image/jpeg',
                    'png' => 'image/png',
                ),
                true
                )) {
                return 'file_type_Fraud';
            }
            $file_name = uniqid("slim_twitter")."_".$_FILES["file"]["name"];
            if (move_uploaded_file($_FILES["file"]["tmp_name"], "/mnt/akahira/twitter_with_slim/images/" . $file_name)) {
                $is_insert = $this->imageInsert($file_name);
                if ($is_insert == true) {
                    return true;
                }
            } else {
                return "can_not_upload";
            }
        } else {
            return "not_file";
        }
    }

    public function imageInsert($file_name)
    {
        try {
            $link = $this->db_connect();
            $stmt = $link->prepare(
                "INSERT INTO images(tweet_id,images_url,created_at)
                 VALUES(?,?,now())"
            );
            $stmt->execute(
                array(
                    $this->tweet_id,
                    $file_name
                )
            );
            return true;

        } catch (PDOException $e) {

        } finally {
            $link = null;
            $stmt = null;
        }
    }

    public function htmlEscape($escape_string)
    {
        $escape_string = htmlspecialchars(
            $escape_string, ENT_QUOTES
        );
        return $escape_string;
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

    public function searchWord()
    {
        $search_words = str_replace("　", " ", $this->tweet_search);
        $word_array = explode(" ",$search_words);
        $sql = "";
        $count = 0;
        $word_count = count($word_array);
        foreach ($word_array as $word) {
            $words[] = '%'.$word.'%';
            $sql .= "tweets.content LIKE ? ";
            if ($count != ($word_count-1)) {
                $sql .= " OR ";
            }
           $count++;
        }
        $this->sql = $sql;
        $this->sql_words = $words;
    }

    public function tweetSearch()
    {
        try {
            $link = $this->db_connect();
            $sql_test = "SELECT tweets.tweet_id, tweets.user_id,
                tweets.content, tweets.created_at,
                users.user_name, users.user_id,
                images.images_url
                FROM tweets
                LEFT JOIN users ON
                    tweets.user_id = users.user_id
                LEFT JOIN images
                    ON images.tweet_id = tweets.tweet_id
                WHERE $this->sql
                ORDER BY tweets.created_at DESC";
            $stmt = $link->prepare(
                $sql_test
            );
                $count = 1;
                foreach ($this->sql_words as $word) {
                    $stmt->bindValue($count,$word,\PDO::PARAM_STR);
                    $count++;
                }
                $stmt->execute();

            $search_rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $search_rows;
        } catch (PDOException $e) {
            return false;
        } finally {
            $link = null;
            $stmt = null;
        }
    }

}