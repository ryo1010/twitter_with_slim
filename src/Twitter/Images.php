<?

namespace Twitter;

class Images extends Tweet
{
    public function imageDecode($image)
    {
<<<<<<< HEAD
        if (isset($_FILES["file"]) && is_uploaded_file($_FILES["file"]["tmp_name"])) {
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
            if (move_uploaded_file($_FILES["file"]["tmp_name"], "/root/images/images/" . $file_name)) {
                $is_insert = $this->imageInsert($file_name);
                if ($is_insert == true) {
                    return true;
                }
            } else {
                return "can_not_upload";
            }
        } else {
            return "not_file";
=======
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
>>>>>>> Image_Drag_and_Drop
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