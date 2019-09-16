<?php
class houseworksDoneTableAction {
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
        $sql = 'create table houseworks_done (id int auto_increment PRIMARY KEY, user_id int not null, housework varchar(255) NOT NULL, start_date datetime, finish_date datetime, is_noname tinyint(1) not null default 0) engine=innodb default charset=utf8';
        $smt = $this->pdo->query($sql); 
    }

    public function dropTable() {
        $smt = $this->pdo->prepare('drop table if exists houseworks_done;');
        $smt->execute();
    }

    public function delete($user_id) {
        $smt = $this->pdo->prepare('delete from houseworks_done where user_id=:user_id');
        $smt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $smt->execute();
    }

    public function delete_housework($id) {
        $smt = $this->pdo->prepare('delete from houseworks_done where id=:id');
        $smt->bindParam(':id', $id, PDO::PARAM_STR);
        $smt->execute();
    }

    public function register_noname($user_id, $housework, $year, $month, $day) {
        $start_date = $year . "-" . $month . "-" . $day . " 00:00:00";
        $finish_date = $year . "-" . $month . "-" . $day . " 00:00:00";
        $smt = $this->pdo->prepare('insert into houseworks_done (user_id, housework, start_date, finish_date, is_noname) values(:user_id, :housework, :start_date, :finish_date, 0)');
        $smt->bindParam(':user_id',$user_id, PDO::PARAM_INT);
        $smt->bindParam(':housework',$housework, PDO::PARAM_STR);
        $smt->bindParam(':start_date',$start_date, PDO::PARAM_STR);
        $smt->bindParam(':finish_date',$finish_date, PDO::PARAM_STR);
        $smt->execute();
    }

    public function register($user_id, $housework, $year, $month, $day, $start_time, $finish_time) {
        //YYYY-MM-DD HH:MM:SS
        $start_date = $year . "-" . $month . "-" . $day . " " . $start_time . ":00";
        $finish_date = $year . "-" . $month . "-" . $day . " " . $finish_time . ":00";
        $smt = $this->pdo->prepare('insert into houseworks_done (user_id, housework, start_date, finish_date, is_noname) values(:user_id, :housework, :start_date, :finish_date, 1)');
        $smt->bindParam(':user_id',$user_id, PDO::PARAM_INT);
        $smt->bindParam(':housework',$housework, PDO::PARAM_STR);
        $smt->bindParam(':start_date',$start_date, PDO::PARAM_STR);
        $smt->bindParam(':finish_date',$finish_date, PDO::PARAM_STR);
        $smt->execute();
    }
    
    public function selectAll() {
        foreach($this->pdo->query('select * from houseworks_done') as $row) {    
            $results[] = $row;
            }
        return $results;
    }

    public function count($user_id, $year, $month, $day) {
        
        if (mb_strlen($month) == 1) {
            $month = "0" . $month;
        }
        if (mb_strlen($day) == 1) {
            $day = "0" . $day;
        }
        $date = $year . "-" . $month . "-" . $day;
        $smt = $this->pdo->prepare('SELECT COUNT(*) FROM houseworks_done WHERE user_id=:user_id AND (DATE_FORMAT(start_date, "%Y-%m-%d") = :date)');
        $smt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $smt->bindParam(':date', $date, PDO::PARAM_STR);
        $smt->execute();
        $result = $smt->fetch(PDO::FETCH_ASSOC);
        return $result['COUNT(*)'];
    }

}