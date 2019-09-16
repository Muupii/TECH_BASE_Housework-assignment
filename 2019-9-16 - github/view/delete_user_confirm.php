<?php
session_start();

header("Content-type: text/html; charset=utf-8");

 
//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

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

        <h2>本当に削除しますか？家事の記録等含めあなたのアカウント情報はすべて削除されます。</h2>

        <a href="delete_user.php" class="btn">削除する</a>

        
    
    </div>

</body>
</html>