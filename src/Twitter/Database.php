<?

namespace Twitter;

class Database
{
    public $db_con;

    public function __construct(){
        $dsn = 'mysql:dbname=slim_twitter;host=192.168.56.123';
        $user = 'akahira';
        $password = 'akahira';
        try{
            $db_con = new \PDO($dsn,$user,$password);
            $this->db_con = $db_con;
        }catch (PDOException $e){
            return $e->getMessage();
        }
        $this->db_con = $db_con;
    }

}