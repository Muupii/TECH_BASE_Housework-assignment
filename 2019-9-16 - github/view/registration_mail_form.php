<?php
session_start();
 
header("Content-type: text/html; charset=utf-8");
 
//クロスサイトリクエストフォージェリ（CSRF）対策
$_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
$token = $_SESSION['token'];
 
//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');
 
?>
 
<!DOCTYPE html>
<html lang="ja">
<head>
    <title>家事分担まるわかり</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <div class="container">

        <a href="index.php" class="title">家事分担まるわかり</a>

        <p>最初にメール認証を行います</p>

        <form action="registration_mail_check.php" method="post">
        
            <p>メールアドレス：<input type="text" name="mail" size="50"></p>
            
            <input type="hidden" name="token" value="<?=$token?>">
            <input type="submit" value="登録する">
        
        </form>

    </div>


 
</body>
</html>