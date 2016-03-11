<?

namespace Twitter;

class TweetTimeDiff
{

    public function tweet_time_chenge($tweet_rows)
    {
        foreach ($tweet_rows as $row) {
            $row['created_at'] = $this->tweet_time_diff($row['created_at']);
            $tweet_rows_after[] = $row;
        }
        return $tweet_rows_after;
    }

    protected function tweet_time_diff($tweettime)
    {
        $now_datetime = date("Y-m-d H:i");
        $tweettime_diff =
        (strtotime($now_datetime) - strtotime($tweettime));

        if ($tweettime_diff < 60) {
            $tweettime_diff = "今さっき";
        } elseif ($tweettime_diff < 3600) {
            $tweettime_diff
            = (floor($tweettime_diff / 60 )) . "分前";
        } elseif ($tweettime_diff < 86400) {
            $tweettime_diff
            = (floor($tweettime_diff / 3600 )) . "時間前";
        } elseif ($tweettime_diff > 86400) {
            $tweettime_diff
            = date("m月d日",strtotime($tweettime));
        }
        return $tweettime_diff;
    }
}