<?

namespace Twitter;

class User extends DatabaseConnect
{

    private $user_id;
    private $user_name;
    private $user_password;
    private $user_mail;
    private $INSETED = 0;

    private $follow_user_id;

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }

    public function setUserName($user_name)
    {
        $this->user_name = $user_name;
        return $this;
    }

    public function setUserPassword($user_password)
    {
        $this->user_password = $user_password;
        return $this;
    }

    public function setUserMail($user_mail)
    {
        $this->user_mail = $user_mail;
        return $this;
    }

    public function setFollowUserId($follow_user_id)
    {
        $this->follow_user_id = $follow_user_id;
        return $this;
    }


    public function login_auth()
    {
        try{
            $link = $this->db_connect();
            $stmt = $link->prepare(
                "SELECT * FROM users
                WHERE user_mail = ?
                AND user_password = ?"
            );
            $stmt->execute(
                array(
                   $this->user_mail,
                   $this->user_password
                )
            );
            if ($stmt->rowCount() == 1) {
                $usr_data = $stmt->fetch(\PDO::FETCH_ASSOC);
                $_SESSION['user_id'] = $usr_data['user_id'];
                $_SESSION['user_name'] = $usr_data['user_name'];
                return true;
            } else {
                return false;
            }

        } catch (Exception $e) {
            return false;
        } finally {
            $stmt = null;
            $link =null;
        }
    }


    public function userCreate()
    {
        try{
            $link = $this->db_connect();
            $stmt = $link->prepare(
                "INSERT INTO users(user_name,user_password,user_mail,created_at)
                VALUES(?,?,?,now())"
            );
            $stmt->execute(
                array(
                    $this->user_name,
                    $this->user_password,
                    $this->user_mail
                )
            );
        } catch (Exception $e) {

        } finally {
            $stmt = null;
            $link = null;
        }
    }

    public function userLogOut()
    {
        try {
            session_destroy();
            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    public function selectUserName(){
        try {
            $link = $this->db_connect();
            $stmt = $link->prepare(
                "SELECT user_name from users WHERE user_id = ?"
            );
            $stmt->execute(
                array(
                   $this->user_id
                )
            );
            if ($stmt->rowCount() == 1 ) {
                $user_name = $stmt->fetch(\PDO::FETCH_ASSOC);
                return $user_name['user_name'];
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        } finally {
            $stmt = null;
            $link = null;
        }
    }

    public function userFind()
    {
        try {
            $link = $this->db_connect();
            $stmt = $link->prepare(
                "SELECT * FROM users where user_id = ?"
            );
            $stmt->execute(array($this->user_id));
            if ($stmt->rowCount() == 1  ) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return false;
        } finally {
            $stmt = null;
            $link = null;
        }
    }

    public function userDetail()
    {
        try {
            $link = $this->db_connect();
            $stmt = $link->prepare(
                "SELECT users.user_name,users.user_id,tweets.tweet_id,tweets.content,tweets.created_at FROM users
                LEFT JOIN tweets ON users.user_id = tweets.user_id
                WHERE tweets.user_id = ? AND tweets.stutas = $this->INSETED
                ORDER BY tweets.created_at DESC"
            );
            $stmt->execute(
                array(
                   $this->user_id
                )
            );
            if ($stmt->rowCount() > 0 ) {
                $user_tweet_rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                echo "string";
                return $user_tweet_rows;
            } elseif ( $stmt->rowCount() == 0 ) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        } finally {
            $stmt = null;
            $link = null;
        }
    }

    public function userFollow()
    {
        try {
            $link = $this->db_connect();
            $stmt = $link->prepare(
                "INSERT INTO user_follows(user_id,followed_user_id,created_at)
                VALUES(?,?,now())"
            );
            $stmt->execute(
                array(
                   $this->user_id,
                   $this->follow_user_id
                )
            );
            if ($stmt->rowCount() > 0 ) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        } finally {
            $stmt = null;
            $link = null;
        }
    }

    public function userFollowingList()
    {
        try {
            $link = $this->db_connect();
            $stmt = $link->prepare(
                "SELECT * from user_follows
                WHERE user_id = ?"
            );
            $stmt->execute(
                array(
                   $this->user_id,
                )
            );
            if ($stmt->rowCount() > 0 ) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return $e;
        } finally {
            $stmt = null;
            $link = null;
        }
    }

    //ユーザーのツイートがなかったらの処理を書きたいけど考えるのめんどくさい
    protected function userTweetNotFound()
    {
        $not_found = array('created_at' => '', );
    }
}