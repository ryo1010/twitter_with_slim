<?
namespace Twitter;

class TweetTimeDiff
{

    public function tweetTimeChenge($tweet_rows)
    {
        foreach ($tweet_rows as $row) {
            $row['created_at'] = $this->tweet_time_diff($row['created_at']);
            $tweet_rows_after[] = $row;
        }
        return $tweet_rows_after;
    }

    public function tweetImage($tweet_rows)
    {
        $tweet_id = "";
        $tweet_image_url = "";

        $comparison = "";
        $image_url = "";
        $array_count = 0;
        foreach ($tweet_rows as $row) {
            $tweet_id = $row['tweet_id'];
            $tweet_image_url = $row['images_url'];

            if ($tweet_id == $comparison) {
                $images_array[] = $row['images_url'];
                $images_array[] = $image_url;
                $images_array = array_unique($images_array);
                $row = array_merge($row,array('images_array'=>$images_array));
                $tweet_rows[$array_count] = array_merge($tweet_rows[$array_count],$row);
                unset($tweet_rows[$array_count-1]);
            } else {
                $images_array = "";
            }
            $comparison = $tweet_id;
            $image_url = $tweet_image_url;
            $array_count++;
        }
        $tweet_rows = array_values($tweet_rows);
        return $tweet_rows;
    }

    protected function tweet_time_diff($tweettime)
    {
        $tweet_date = new \DateTimeImmutable($tweettime);
        $now_date = new \DateTimeImmutable();
        $interval = $now_date->diff($tweet_date);
        if ($interval->format('%a') == 0 ) {

            if ($interval->format('%h') > 0 ) {
                return $interval->format('%h時間前');

            }elseif ($interval->format('%i') > 0 ) {
                return $interval->format('%i分前');

            }elseif ($interval->format('%s') > 0) {
                return $interval->format('%s秒前');
            }

        } elseif($interval->format('%a') > 0) {
            return $tweet_date->format('m月d日');
        }
    }
}