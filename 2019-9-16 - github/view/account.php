<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("location: index.php");
}
require_once("../config/properties.php");
require_once("../class/usersTableAction.php");

$usersAction = new usersTableAction();
$user = $usersAction->getUser($_SESSION['user']);

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

        <p>あなたのお名前：<?php echo $user['name']; ?></p>

        <p>あなたの家族の名前：<?php echo $user['family']; ?></p>

        <p>あなたのメールアドレス：<?php echo $user['mail']; ?></p>

        <a href="edit_user_form.php" class="btn">編集</a>

        <a href="delete_user_confirm.php" class="btn">削除</a>
    
    </div>

</body>
</html>