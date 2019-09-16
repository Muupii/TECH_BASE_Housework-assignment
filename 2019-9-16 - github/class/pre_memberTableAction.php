<?php
class pre_memberTableAction {
    public $pdo;
    function __construct(){
        try {
            $this->pdo = new PDO(PDO_DSN, DB_USERNAME, DB_PASSWORD);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public function createTable() {
        $sql = 'CREATE TABLE pre_member (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            urltoken VARCHAR(128) NOT NULL,
            mail VARCHAR(50) NOT NULL,
            date DATETIME NOT NULL,
            flag TINYINT(1) NOT NULL DEFAULT 0
            )ENGINE=InnoDB DEFAULT CHARACTER SET=utf8';
        $smt = $this->pdo->query($sql);
    }

    public function dropTable() {
        $smt = $this->pdo->prepare('drop table if exists pre_member;');
        $smt->execute();
    }
	
    public function register($urltoken, $mail) {
        $smt = $this->pdo->prepare("INSERT INTO pre_member (urltoken,mail,date) VALUES (:urltoken,:mail,now())");
		$smt->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
		$smt->bindValue(':mail', $mail, PDO::PARAM_STR);
		$smt->execute();
    }

    public function delete($mail) {
        $smt = $this->pdo->prepare('delete from pre_member where mail=:mail');
        $smt->bindParam(':mail', $mail, PDO::PARAM_STR);
        $smt->execute();
    }

    public function checkMail($mail) {
        $smt = $this->pdo->prepare("SELECT COUNT(*) FROM pre_member WHERE mail=(:mail) AND flag = 1 LIMIT 1");
		$smt->bindValue(':mail', $mail, PDO::PARAM_STR);
        $smt->execute();
        $result = $smt->fetch(PDO::FETCH_ASSOC);
        return $result['COUNT(*)'];
    }

    public function checkAndGet($urltoken) {
        $smt = $this->pdo->prepare("SELECT mail FROM pre_member WHERE urltoken=(:urltoken) AND flag = 0 AND date > now() - interval 24 hour");
		$smt->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
        $smt->execute();
        $row_count = $smt->rowCount();
        $mail_array = $smt->fetch();
        $result[] = [$row_count, $mail_array];
        return $result;
    }

    public function flagOn($mail) {
        $smt = $this->pdo->prepare("UPDATE pre_member SET flag=1 WHERE mail=(:mail)");
		$smt->bindValue(':mail', $mail, PDO::PARAM_STR);
        $smt->execute();
    }

    public function select() {
        foreach($this->pdo->query('select * from pre_member') as $row) {    
            $results[] = $row;
            }
        return $results;
    }

}