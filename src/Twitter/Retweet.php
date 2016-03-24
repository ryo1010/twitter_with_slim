<?

namespace Twitter;

class Retweet extends Tweet
{

    public function tweetRetweet()
    {
        try {
            $link = $this->db_con;
            if ( $stmt = $link->prepare(
                "INSERT INTO retweets (user_id,tweet_id,created_at)
                VALUES(?,?,now())"
            )) {
                $stmt->execute(
                [
                    $this->user_id,
                    $this->tweet_id
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

    public function tweetRetweetDelete()
    {
        try {
            $link = $this->db_con;
            if ( $stmt = $link->prepare(
                "DELETE FROM retweets where
                tweet_id = ? AND user_id = ?"
            )) {
                $stmt->execute(
                [
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
}