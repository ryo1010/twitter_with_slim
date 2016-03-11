<?
namespace Twitter;

$app = new \Slim\Slim;

class LoginAuth extends DatabaseConnect
{
    public function login_auth($mail_address,$password)
    {
        $link = $this->db_connect();
        $stmt = $link->prepare("SELECT * FROM users WHERE user_mail = :usr_mail AND user_password = :user_password");
        $stmt->bindParam(':usr_mail', $mail_address, \PDO::PARAM_STR, 12);
        $stmt->bindParam(':user_password', $password, \PDO::PARAM_STR, 12);
        $stmt->execute();
        if ($stmt->rowCount() == 1) {
            $usr_data = $stmt->fetch(\PDO::FETCH_ASSOC);
            $_SESSION['user_id'] = $usr_data['user_id'];
            $_SESSION['user_name'] = $usr_data['user_name'];
            header('Location: /');
            exit();
        } else {
            echo "メールアドレスまたはパスワードが正しくありません";
        }
    }
}