<?php
session_start();

header("Content-type: text/html; charset=utf-8");

//クロスサイトリクエストフォージェリ（CSRF）対策
$_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
$token = $_SESSION['token'];
 
//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

if (isset($_SESSION['user'])) {
    header("location: index.php");
}

require_once("../config/properties.php");
require_once("../class/usersTableAction.php");
$usersAction = new usersTableAction();

//エラーメッセージの初期化
$errors = array();

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>家事分担まるわかり君</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
        <a href="index.php" class="title">家事分担まるわかり</a>
        <p>Loginページ！</p>
        <form action="login.php" method="post">
            <table>
                <tr>
                    <td>あなたのお名前</td>
                    <td><input type="text" name="name"></td>
                </tr>
                <tr>
                    <td>頑丈なパスワード</td>
                    <td><input type="password" name="password"></td>
                </tr>
                <tr>
                    <td>あなたのメールアドレス</td>
                    <td><input type="text" name="mail"></td>
                </tr>
            </table>
            <input type="hidden" name="token" value="<?=$token?>">
            <input type="submit" value="ログイン">
        </form>
    </div>
</body>
</html>