<?

namespace Twitter;

class User extends DatabaseConnect
{

    private $user_id;
    private $user_name;
    private $user_password;
    private $user_mail;
    private $user_number;
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

    public function setUserNumber($user_number)
    {
        $this->user_number = $user_number;
        return $this;
    }

    public function isLoginEnabled()
    {
        if (!isset($_SESSION['user_name'])
            && !isset($_SESSION['user_id'])) {
            return true;
        }else{
            return false;
        }
    }

    public function isFollowingEnabled()
    {
        try {
            $link = $this->db_connect();
            $stmt = $link->prepare(
                "SELECT * FROM user_follows
                WHERE user_id = ? AND followed_user_id = ?"
            );
            $stmt->execute(
                array(
                    $this->follow_user_id,
                    $this->user_id
                )
            );
            if ($stmt->rowCount() == 1) {
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

    public function mailConfirmation()
    {
        try {
            $link = $this->db_connect();
            $stmt = $link->prepare(
                "SELECT * FROM users
                WHERE user_mail = ?"
            );
            $stmt->execute(
                array(
                   $this->user_mail,
                )
            );
            if ($stmt->rowCount() == 1) {
                return false;
            } else {
                return true;
            }

        } catch (Exception $e) {
            return false;
        } finally {
            $stmt = null;
            $link =null;
        }
    }

    public function mailCheck()
    {
        $match = "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/";
        if (preg_match($match, $this->user_mail)) {
            return true;
        } else {
            return false;
        }
    }

    public function loginAuth()
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

    public function userReFollow()
    {
        try {
            $link = $this->db_connect();
            $stmt = $link->prepare(
                "DELETE FROM user_follows
                 WHERE user_id = ? AND followed_user_id = ?"
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

    public function createUserSendMail()
    {
        $transport = \Swift_SmtpTransport::newInstance('localhost', 25);
        $mailer = \Swift_Mailer::newInstance($transport);

        $message = \Swift_Message::newInstance()
            ->setSubject('ツイッターモドキ・仮登録ありがとうございます。')
            ->setTo($this->user_mail)
            ->setFrom(['ryo@slim-twitter.jp' => 'ツイッターモドキ管理人'])
            ->setBody(
                '下記のアドレスにアクセスしてユーザー登録をしてください'
                .'slim-twitter.jp/user/create/info/'.$this->user_number
                );

        $result = $mailer->send($message);
    }

}