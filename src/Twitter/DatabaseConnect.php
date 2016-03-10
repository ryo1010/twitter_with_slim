<?

namespace Twitter;

class DatabaseConnect
{
    protected $db_con_info;

    public function __construct()
    {
        $this->db_connect();
    }

    public function set_db_con_info($db_con_info){
        self::$db_con_info = $db_con_info;
    }

    public function db_connect(){
        $dsn = 'mysql:dbname=slim_twitter;host=192.168.56.123';
        $user = 'akahira';
        $password = 'akahira';
        try{
            $db_con = new \PDO($dsn,$user,$password);
        }catch (PDOException $e){
            print('Error:'.$e->getMessage());
            die();
        }
        return $db_con;
    }
}