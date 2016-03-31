<?

namespace Twitter;

class HashTag extends Tweet
{

    public function selectHashTag($word)
    {
         try {
            $db = new \Twitter\Database();
            $link = $db->db_con;
            $stmt = $link->prepare(
                "SELECT
                tweets.tweet_id, tweets.user_id, tweets.content, tweets.created_at,
                users.user_name,
                images.images_url,
                hash_tag.hash_tag, hash_tag.tweet_id
                FROM tweets
                LEFT JOIN users ON users.user_id = tweets.user_id
                LEFT JOIN images ON images.tweet_id = tweets.tweet_id
                LEFT JOIN hash_tag ON hash_tag.tweet_id = tweets.tweet_id
                where hash_tag.hash_tag = ?
                ORDER BY tweets.created_at DESC"
            );

            $stmt->execute(
                [
                $word
                ]
            );
            if ($stmt->rowCount() > 0) {
                $tweet_rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
               return $tweet_rows;

            }  else {
                return false;
            }

        } catch (PDOException $e) {
            return false;
        } finally {
            $stmt = null;
        }
    }

    public function displayHashTag($rows)
    {
        $rows_count = 0;
        foreach ($rows as $row) {
            $pattern = "/[#＃][Ａ-Ｚａ-ｚA-Za-z一-鿆0-9０-９ぁ-んァ-ヶｦ-ﾟー!-:-@≠\[-{-~]+/u";
            $pattern1 = '/[#＃]/u';
            if (preg_match_all($pattern, $row['content'], $hash_tags)) {
               foreach ($hash_tags as $hash_tag) {
                    foreach ($hash_tag as $tag) {

                        $tag_link = preg_replace('/[#＃]/u',"",$tag);
                        $text = str_replace(
                            $tag,
                            "<a href=/hashtag/$tag_link>".$tag."</a>",
                            $row['content']
                        );
                        $row['content'] = $text;
                    }
               }
            }
            $rows[$rows_count]['content'] = $row['content'];
            $rows_count++;
        }
        return $rows;
    }


    public function matchHashTag($content)
    {
        $pattern = "/[\s][#＃][Ａ-Ｚａ-ｚA-Za-z一-鿆0-9０-９ぁ-んァ-ヶｦ-ﾟー!-:-@≠\[-{-~]+/";
        $content = str_replace("　"," ",$content);
        if (preg_match_all($pattern, $content, $hash_tag)) {
            return $hash_tag;
        }
        return false;
    }

    public function hashTagInsert($hash_tag_rows)
    {
        $sql = "";
        $comma_flag = 1;
        foreach ($hash_tag_rows as $rows) {
            $hash_tags = $this->tweetHashTag($rows);
            $tag_count = count($rows);
            foreach ($hash_tags as $tag) {
                $tag_array[] = $tag;
                $sql .= "(?, ?, now())";
                if ($comma_flag !== $tag_count) $sql .= ", ";
                $comma_flag++;
            }
        }

        try {
            $db = new \Twitter\Database();
            $link = $db->db_con;
            $sql_test = "";
            $stmt = $link->prepare(
                "INSERT INTO hash_tag(hash_tag, tweet_id, created_at) VALUES $sql"
            );

            $sql_count = 1;
            foreach ($tag_array as $tag) {
                $stmt->bindValue($sql_count,$tag,\PDO::PARAM_STR);
                $stmt->bindValue($sql_count+1,$this->tweet_id,\PDO::PARAM_STR);
                $sql_count = $sql_count + 2;
            }

            $stmt->execute();
            return true;

        } catch (PDOException $e) {

        } finally {
            $link = null;
            $stmt = null;
        }
    }

    private function tweetHashTag($hash_tag)
    {
        foreach ($hash_tag as $tag) {
            $tag = preg_replace("/[#＃]/u", "", $tag);
            $tag = str_replace(" ", "", $tag);
            $hash_tags[] = $tag;
        }
        return $hash_tags;
    }
}