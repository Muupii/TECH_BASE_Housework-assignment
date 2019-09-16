<?php
class dbAction {
    public $pdo;
    function __construct(){
        try {
            $this->pdo = new PDO(PDO_DSN, DB_USERNAME, DB_PASSWORD);
            // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage();
            exit;
        }
    }


    public function showTables() {
        foreach($this->pdo->query('SHOW TABLES FROM tb210272db') as $row) {    
            $results[] = $row;
            }
        return $results;
    }

}