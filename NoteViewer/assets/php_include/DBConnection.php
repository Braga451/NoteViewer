<?php
    class DBConnection{
        private $server_host = "127.0.0.1";
        private $database = "noteviewer";
        private $user = "root";
        private $password = "";
        protected $class_connection;

        public function __construct(){
            try{
                $this->class_connection = new PDO("mysql:host={$this->server_host};dbname={$this->database};charset=utf8", $this->user, $this->password);
                $this->class_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch(PDOException $except){
                echo "Erro: ".$except->getMessage();
            }
        }
    }
?>
