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