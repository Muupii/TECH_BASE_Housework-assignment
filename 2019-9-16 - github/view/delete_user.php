<?php
session_start();

header("Content-type: text/html; charset=utf-8");

if (!isset($_SESSION['user']) | !isset($_GET)) {
    header("location: index.php");
}

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

//データベース接続
require_once("../config/properties.php");
require_once("../class/pre_memberTableAction.php");
require_once("../class/usersTableAction.php");
require_once("../class/houseworksDoneTableAction.php");
require_once("../class/houseworksToDoTableAction.php");
$pre_memberAction = new pre_memberTableAction();
$userAction = new usersTableAction();
$houseworkDoneAction = new houseworksDoneTableAction();
$houseworkToDoAction = new houseworksToDoTableAction();

$user_id = $_SESSION['user'];
$mail = $userAction->getUser($user_id)['mail'];

// 削除実行
$userAction->delete($user_id);
$pre_memberAction->delete($mail);
$houseworkDoneAction->delete($user_id);
$houseworkToDoAction->delete($user_id);

// セッションの破棄
$_SESSION = array();
session_destroy();

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>家事分担まるわかり君</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <a href="index.php" class="title">家事分担まるわかり</a>
    <div>

        <a href="index.php" class="btn">TOPページに戻る</a>

        
    
    </div>

</body>
</html>