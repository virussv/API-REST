<?php
    namespace db;

    use \PDO;
    use \PDOException;

    class Database{
        const HOST = "localhost";
        const NAME = "myapi";
        const USER = "root";
        const SENHA = "";
        
        private $table;
        private $connection;

        public function __construct($tabela = null){   
            
            $this-> table = $tabela;
        }

        private function setConnection(){
            try {
                $this-> connection = new PDO("mysql:host=" . self::HOST . ";dbname=" . self::NAME,self::USER,self::SENHA);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);                
            } catch (PDOException $e) {
                die("ERROR: " . $e->getMessage());
            }
        }

        public function execute($query,$params = []){
            $this-> setConnection();
            try{
                $statemant = $this-> connection->prepare($query);
                $statemant->execute($params);
                return $statemant->fetchObject();
            }catch(PDOException $e){
                die("ERROR: " . $e->getMessage());
            }
        }


        public function select($WHERE = null,$fields = "*"){

            $filter_where = strlen($WHERE) ? " WHERE " . "$WHERE" : "";

            $query = "SELECT " . $fields . " FROM " . $this->table . $filter_where;
  
            return $this->execute($query);
        }

        
        public function insert(array $values)
        {
            $fields = array_keys($values);
            $binds = array_pad([],count($values),"?");

            $q = "INSERT INTO " . $this->table .  " (" . implode(",",$fields) . ") " . " VALUES " .  " (" . implode(" , ",$binds) .  ") ";
         
            return $this->execute($q,array_values($values));
        }
    }
?>