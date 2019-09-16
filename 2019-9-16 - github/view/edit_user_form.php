<?php
session_start();

header("Content-type: text/html; charset=utf-8");

//クロスサイトリクエストフォージェリ（CSRF）対策
$_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
$token = $_SESSION['token'];
 
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
<div class="container">
        <a href="index.php" class="title">家事分担まるわかり</a>
        <p>会員情報変更ページ</p>
        <form action="edit_user.php" method="post">
            <table>
                <tr>
                    <td>変更後のお名前</td>
                    <td><input type="text" name="name" value="<?php echo $user['name']; ?>"></td>
                </tr>
                <tr>
                    <td>変更後の家族の名前</td>
                    <td><input type="text" name="family" value="<?php echo $user['family']; ?>"></td>
                    <td>　※家族で同じ名前にしてね！</td>
                </tr>
                <tr>
                    <td>変更前のパスワード</td>
                    <td><input type="password" name="confirm_password"></td>
                </tr>
                <tr>
                    <td>変更後のパスワード</td>
                    <td><input type="password" name="password"></td>
                </tr>
                <tr>
                    <td>変更後のメールアドレス</td>
                    <td><input type="text" name="mail" value="<?php echo $user['mail']; ?>"></td>
                </tr>
            </table>
            <input type="hidden" name="token" value="<?=$token?>">
            <input type="submit" value="会員情報変更">
        </form>
    </div>
</body>
</html>