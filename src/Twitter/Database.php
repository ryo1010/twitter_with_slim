<?

namespace Twitter;

class Database
{
    protected $db_con;

    public function __construct(){
        if (!isset($this->db_con)) {
            //echo "string";
            $dsn = 'mysql:dbname=slim_twitter;host=192.168.56.123';
            $user = 'akahira';
            $password = 'akahira';
            try{
                $db_con = new \PDO($dsn,$user,$password,array(\PDO::ATTR_PERSISTENT => true));
            }catch (PDOException $e){
                print('Error:'.$e->getMessage());
                die();
            }
            $this->db_con = $db_con;
        }
    }

}