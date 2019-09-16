<?php
require_once("../config/properties.php");
require_once("../class/usersTableAction.php");
require_once("../class/pre_memberTableAction.php");
require_once("../class/houseworksDoneTableAction.php");
require_once("../class/houseworksToDoTableAction.php");
require_once("../class/dbAction.php");

$usersAction = new usersTableAction();
$dbAction = new dbAction();
$pre_memberAction = new pre_memberTableAction();
$houseworksDoneAction = new houseworksDoneTableAction();
$houseworksToDoAction = new houseworksToDoTableAction();
// $houseworksDoneAction->dropTable();
// $houseworksDoneAction->createTable();

$houseworksToDoAction->createTable();

// $usersAction->dropTable();
// $usersAction->createTable();
// $pre_memberAction->dropTable();
// $pre_memberAction->createTable();

$tables = $dbAction->showTables();
foreach ($tables as $table) {
    var_dump($table);
    echo "<br>";
}

// foreach ($houseworksDoneAction->selectAll() as $housework) {
//     echo "<p>" . "user_id:" . $housework['user_id'] . "　家事：" . $housework['housework'] . "　開始時間：" . $housework['start_date'] . "　終了時間：" . $housework['finish_date'] . "</p>";
// }

// echo $houseworksDoneAction->count(2, 2019, 9, 1);

// $pre_meber_list = $pre_memberAction->select();
// foreach ($pre_meber_list as $pre_member) :

//     echo "urltoken：" . $pre_member['urltoken'] . "　mail：" . $pre_member['mail'] . "　date：" . $pre_member['date'] . "　flag：" . $pre_member['flag'] . "<br>";
    

// endforeach;

// $users = $usersAction->selectAll();
// foreach ($users as $user) :

//     echo "名前：" . $user['name'] . "　パスワード：" . $user['password'] . "　メールアドレス：" . $user['mail'] . "<br>";
    

// endforeach;