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
$pre_memberAction = new pre_memberTableAction();

//エラーメッセージの初期化
$errors = array();

if(empty($_POST)) {
	header("Location: registration_mail_form.php");
	exit();
}else{
	//POSTされたデータを変数に入れる
	$mail = isset($_POST['mail']) ? $_POST['mail'] : NULL;
	
	//メール入力判定
	if ($mail == ''){
		$errors['mail'] = "メールが入力されていません。";
	}else{
		if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mail)){
			$errors['mail_check'] = "メールアドレスの形式が正しくありません。";
		}
		
		
		// ここで本登録用のmemberテーブルにすでに登録されているmailかどうかをチェックする。
        if ($pre_memberAction->checkMail($mail) != 0) {
            $errors['member_check'] = "このメールアドレスはすでに利用されております。";
        }

	}
}

if (count($errors) === 0){
	
	$urltoken = hash('sha256',uniqid(rand(),1));
	$url = "https://tb-210272.tech-base.net/TECH-BASE/mission_6/view/registration_form.php"."?urltoken=".$urltoken;
	
	//ここでデータベースに登録する
	$pre_memberAction->register($urltoken, $mail);
    
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
    $body = "<p>24時間以内に下記のURLからご登録下さい。</p><p>$url</p>";

    $PHPMailer->Body  = $body; // メール本文
    // メール送信の実行
    if(!$PHPMailer->send()) {
        $errors['mail_error'] = "メールの送信に失敗しました。";
        echo 'Mailer Error: ' . $PHPMailer->ErrorInfo;
    } else {
        //セッション変数を全て解除
        $_SESSION = array();
    
        //クッキーの削除
        if (isset($_COOKIE["PHPSESSID"])) {
            setcookie("PHPSESSID", '', time() - 1800, '/');
        }
    
        //セッションを破棄する
        session_destroy();
    
        $message = "メールをお送りしました。24時間以内にメールに記載されたURLからご登録下さい。";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
<title>メール確認画面</title>
<meta charset="utf-8">
</head>
<body>
<h1>メール確認画面</h1>

<?php if (count($errors) === 0): ?>

<p><?=$message?></p>

<?php elseif(count($errors) > 0): ?>

<?php
foreach($errors as $value){
	echo "<p>".$value."</p>";
}
?>

<input type="button" value="戻る" onClick="history.back()">

<?php endif; ?>
 
</body>
</html>