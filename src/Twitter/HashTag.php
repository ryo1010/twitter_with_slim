<?

namespace Twitter;

class HashTag
{

    public function matchHashTag($rows)
    {
        foreach ($rows as $row) {
            $pattern = "/[\s][#＃][Ａ-Ｚａ-ｚA-Za-z一-鿆0-9０-９ぁ-んァ-ヶｦ-ﾟー!-:-@≠\[-{-~]+/";
            $str = str_replace("　"," ",$row['content']);
            if (preg_match_all($pattern, $str, $hash_tag)) {
                echo $this->tweetHashTag($hash_tag);
            }
        }
    }

    private function tweetHashTag($hash_tag)
    {
        foreach ($hash_tag as $tag) {
            foreach ($tag as $t) {
                $t = str_replace("#", "", $t);
                return $t;
            }
        }
    }
}