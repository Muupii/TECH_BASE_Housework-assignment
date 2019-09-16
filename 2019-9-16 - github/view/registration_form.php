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
require_once("../class/pre_memberTableAction.php");
$pre_memberAction = new pre_memberTableAction();

//エラーメッセージの初期化
$errors = array();

if(empty($_GET)) {
	header("Location: registration_mail_form.php");
	exit();
}else{
	//GETデータを変数に入れる
	$urltoken = isset($_GET['urltoken']) ? $_GET['urltoken'] : NULL;
	//メール入力判定
	if ($urltoken == ''){
		$errors['urltoken'] = "もう一度登録をやりなおして下さい。";
	}else{
		$countAndFetch = $pre_memberAction->checkAndGet($urltoken);
        $row_count = $countAndFetch[0][0];
        
        //24時間以内に仮登録され、本登録されていないトークンの場合
        if( $row_count ==1){
            $mail_array = $countAndFetch[0][1];
            $mail = $mail_array['mail'];
            $_SESSION['mail'] = $mail;
        }else{
            $errors['urltoken_timeover'] = "このURLはご利用できません。有効期限が過ぎた等の問題があります。もう一度登録をやりなおして下さい。";
        }		
	}
}
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
        <p>新規登録ページ</p>

        <?php if (count($errors) === 0): ?>

        <form action="registration_check.php" method="post">
            <table>
                <tr>
                    <td>あなたのお名前</td>
                    <td><input type="text" name="name"></td>
                </tr>
                <tr>
                    <td>あなたの家族の名前</td>
                    <td><input type="text" name="family"></td>
                    <td>　※家族で同じ名前にしてね！</td>
                </tr>
                <tr>
                    <td>頑丈なパスワード</td>
                    <td><input type="password" name="password"></td>
                </tr>
                <tr>
                    <td>あなたのメールアドレス</td>
                    <td><?=htmlspecialchars($mail, ENT_QUOTES, 'UTF-8')?></td>
                </tr>
            </table>
            <input type="hidden" name="token" value="<?=$token?>">
            <input type="submit" value="確認ページへ">
        </form>

        <?php elseif(count($errors) > 0): ?>

        <?php
        foreach($errors as $value){
            echo "<p>".$value."</p>";
        }
        ?>

        <?php endif; ?>

    </div>
</body>
</html>