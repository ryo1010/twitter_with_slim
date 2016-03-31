<?

namespace Twitter;

class Favorite extends Tweet
{

    public function tweetFavorite()
    {
        try {
            $db = new \Twitter\Database();
            $link = $db->db_con;
            if ( $stmt = $link->prepare(
                "INSERT INTO favorites (user_id,tweet_id,created_at)
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

    public function tweetFavoriteHistory()
    {
        try{
            $db = new \Twitter\Database();
            $link = $db->db_con;
            if ( $stmt = $link->prepare(
                "SELECT
                tweets.content,tweets.user_id,tweets.created_at,
                favorites.tweet_id,
                images.images_url,
                users.user_name
                FROM favorites
                LEFT JOIN tweets ON favorites.tweet_id = tweets.tweet_id
                LEFT JOIN users ON users.user_id = tweets.user_id
                LEFT JOIN images ON images.tweet_id = tweets.tweet_id
                WHERE favorites.user_id = ?
                ORDER BY tweets.created_at DESC"
            )) {
                $stmt->execute(
                [
                    $this->user_id,
                ]
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

    public function tweetFavoriteDelete()
    {
        try {
            $db = new \Twitter\Database();
            $link = $db->db_con;
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