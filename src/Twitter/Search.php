<?

namespace Twitter;

class Search extends Tweet
{

    private $sql;
    private $sql_words;

    public function tweetSearch($search_word)
    {
        $this->searchWord($search_word);
        try {
            $db = new \Twitter\Database();
            $link = $db->db_con;
            $sql_test = "SELECT tweets.tweet_id, tweets.user_id,
                tweets.content, tweets.created_at,
                users.user_name, users.user_id,
                images.images_url
                FROM tweets
                LEFT JOIN users ON
                    tweets.user_id = users.user_id
                LEFT JOIN images
                    ON images.tweet_id = tweets.tweet_id
                WHERE tweets.stutas = ? AND $this->sql
                ORDER BY tweets.created_at DESC";

            $stmt = $link->prepare(
                $sql_test
            );

                $count = 2;
                $stmt->bindValue(1,parent::INSERTED,\PDO::PARAM_STR);
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

    protected function searchWord($search_word)
    {
        $search_words = str_replace("　", " ", $search_word);
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
}