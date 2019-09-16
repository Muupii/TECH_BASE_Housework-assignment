<?php
session_start();

header("Content-type: text/html; charset=utf-8");

//クロスサイトリクエストフォージェリ（CSRF）対策のトークン判定
if ($_POST['token'] != $_SESSION['token']){
	echo "不正アクセスの可能性あり";
	exit();
}

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

//データベース接続
require_once("../config/properties.php");
require_once("../class/pre_memberTableAction.php");
require_once("../class/usersTableAction.php");
$pre_memberAction = new pre_memberTableAction();
$userAction = new usersTableAction();

//エラーメッセージの初期化
$errors = array();

if(empty($_POST)) {
	header("Location: registration_mail_form.php");
	exit();
}

$mail = $_SESSION['mail'];
$name = $_SESSION['name'];
$family = $_SESSION['family'];

//パスワードのハッシュ化
$password_hash =  password_hash($_SESSION['password'], PASSWORD_DEFAULT);

//ここでデータベースに登録する

$userAction->register($name, $password_hash, $mail, $family);
		
$pre_memberAction->flagOn($mail);
	
//セッション変数を全て解除
$_SESSION = array();

//セッションクッキーの削除・sessionidとの関係を探れ。つまりはじめのsesssionidを名前でやる
if (isset($_COOKIE["PHPSESSID"])) {
        setcookie("PHPSESSID", '', time() - 1800, '/');
}

//セッションを破棄する
session_destroy();


// 登録完了のメールを送信
require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require '../phpmailer/setting.php';

// PHPMailerのインスタンス生成
$PHPMailer = new PHPMailer\PHPMailer\PHPMailer();

$PHPMailer->isSMTP(); // SMTPを使うようにメーラーを設定する
$PHPMailer->SMTPAuth = true;
$PHPMailer->Host = MAIL_HOST; // メインのSMTPサーバー（メールホスト名）を指定
$PHPMailer->Username = MAIL_USERNAME; // SMTPユーザー名（メールユーザー名）
$PHPMailer->Password = MAIL_PASSWORD; // SMTPパスワード（メールパスワード）
$PHPMailer->SMTPSecure = MAIL_ENCRPT; // TLS暗号化を有効にし、「SSL」も受け入れます
$PHPMailer->Port = SMTP_PORT; // 接続するTCPポート

// メール内容設定
$PHPMailer->CharSet = "UTF-8";
$PHPMailer->Encoding = "base64";
$PHPMailer->setFrom(MAIL_FROM,MAIL_FROM_NAME);
$PHPMailer->addAddress($mail, '新規登録希望者さん'); //受信者（送信先）を追加する
$PHPMailer->addReplyTo('kazimaruwakari@gmail.com','返信先');
//$PHPMailer->addCC('xxxxxxxxxx@xxxxxxxxxx'); // CCで追加
//$PHPMailer->addBcc('xxxxxxxxxx@xxxxxxxxxx'); // BCCで追加
$PHPMailer->Subject = MAIL_SUBJECT; // メールタイトル
$PHPMailer->isHTML(true);    // HTMLフォーマットの場合はコチラを設定します
$body = "<p>登録完了いたしました。下記URLからログイン出来ます。</p><p></p>";

$PHPMailer->Body  = $body; // メール本文
// メール送信の実行
if(!$PHPMailer->send()) {
    $errors['mail_error'] = "メールの送信に失敗しました。";
    echo 'Mailer Error: ' . $PHPMailer->ErrorInfo;
} else {
    $message = "登録完了のメールをお送りしました。";
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
<a href="index.php" class="title">家事分担まるわかり</a>
<?php if (count($errors) === 0): ?>
<h2>会員登録完了画面</h2>

<p>登録完了いたしました。</p>
<p><a href="./login_form.php" class="btn">ログイン画面</a></p>

<?php elseif(count($errors) > 0): ?>

<?php
foreach($errors as $value){
	echo "<p>".$value."</p>";
}
?>

<?php endif; ?>
 
</body>
</html>