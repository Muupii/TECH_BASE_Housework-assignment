<?php
class usersTableAction {
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
        $sql = 'create table users (id int auto_increment PRIMARY KEY, name varchar(255) NOT NULL, password varchar(255) NOT NULL, mail varchar(255) NOT NULL, family varchar(255) NOT NULL) engine=innodb default charset=utf8';
        $smt = $this->pdo->query($sql);
    }

    public function dropTable() {
        $smt = $this->pdo->prepare('drop table if exists users;');
        $smt->execute();
    }

    public function delete($id) {
        $smt = $this->pdo->prepare('delete from users where id=:id');
        $smt->bindParam(':id', $id, PDO::PARAM_STR);
        $smt->execute();
    }

    public function register($name, $password, $mail, $family) {
        $smt = $this->pdo->prepare('insert into users (name, password, mail, family) values(:name, :password, :mail, :family)');
        $smt->bindParam(':name',$name, PDO::PARAM_STR);
        $smt->bindParam(':password',$password, PDO::PARAM_STR);
        $smt->bindParam(':mail',$mail, PDO::PARAM_STR);
        $smt->bindParam(':family',$family, PDO::PARAM_STR);
        $smt->execute();
    }

    public function update($confirm_name, $confirm_mail, $confirm_password, $name, $family, $password, $mail, $id) {
        $smt = $this->pdo->prepare('select * from users where name=:name AND mail=:mail');
        $smt->bindParam(':name',$confirm_name, PDO::PARAM_STR);
        $smt->bindParam(':mail',$confirm_mail, PDO::PARAM_STR);
        $smt->execute();
        $user = $smt->fetch(PDO::FETCH_ASSOC);
        // ユーザがいない
        if(!$user){
            return 'ユーザ名かメールアドレスが正しくありません。';
        } 
        // パスワードチェック
        elseif (!password_verify($confirm_password, $user['password'])) {
            return 'ユーザ名かパスワードが正しくありません。';
        }
        // Update
        else {
            $statement = $this->pdo->prepare('update users set name=:name, family=:family, password=:password, mail=:mail where id = :id');
            $statement->bindParam(':name', $name, PDO::PARAM_STR);
            $statement->bindParam(':family', $family, PDO::PARAM_STR);
            $statement->bindParam(':password', $password, PDO::PARAM_STR);
            $statement->bindParam(':mail', $mail, PDO::PARAM_STR);
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            return 'update';
        }
    }

    public function login($name, $password, $mail) {
        $smt = $this->pdo->prepare('select * from users where name=:name AND mail=:mail');
        $smt->bindParam(':name',$name, PDO::PARAM_STR);
        $smt->bindParam(':mail',$mail, PDO::PARAM_STR);
        $smt->execute();
        $user = $smt->fetch(PDO::FETCH_ASSOC);
        // ユーザがいない
        if(!$user){
            return 'ユーザ名かメールアドレスが正しくありません。';
        } 
        // パスワードチェック
        elseif (!password_verify($password, $user['password'])) {
            return 'ユーザ名かパスワードが正しくありません。';
        }
        // ログイン
        else {
            $_SESSION['user'] = $user['id'];
            return 'login';
        }
    }

    public function selectAll() {
        foreach($this->pdo->query('select * from users') as $row) {    
            $results[] = $row;
            }
        return $results;
    }

    public function getUser($id) {
        $smt = $this->pdo->prepare('select * from users where id=:id');
        $smt->bindParam(':id',$id, PDO::PARAM_INT);
        $smt->execute();
        $result = $smt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }


}