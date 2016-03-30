<?

namespace Twitter;

class Images extends Tweet
{
    public function imageDecode($image)
    {
        $image = preg_replace("/data:[^,]+,/i","",$image);
        //デコード
        $image = base64_decode($image);
        //image作成
        $image = imagecreatefromstring($image);
        $timestamp = microtime(true);
        $file_name = md5($timestamp).".png";
        if ($image !== false) {
            //image保存
            imagepng($image, '/root/images/images/'.$file_name);
            return $file_name;
        }
        else {
            return false;
        }
    }

    public function imageInsert($file_name)
    {
        try {
            $db = new \Twitter\Database();
            $link = $db->db_con;
            $stmt = $link->prepare(
                "INSERT INTO images(tweet_id,images_url,created_at)
                 VALUES(?,?,now())"
            );
            $stmt->execute(
                [
                    $this->tweet_id,
                    $file_name
                ]
            );
            return true;

        } catch (PDOException $e) {

        } finally {
            $link = null;
            $stmt = null;
        }
    }
}