<?

namespace Twitter;

class User extends DatabaseConnect
{

    private $user_name;
    private $user_password;
    private $user_mail;

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


    public function login_auth()
    {
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
    }


    public function userCreate()
    {
        $link = $this->db_connect();
        echo $this->user_name;
        echo $this->user_password;
        echo $this->user_mail;
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
    }
    public function userLogOut()
    {
        session_destroy();
    }

}