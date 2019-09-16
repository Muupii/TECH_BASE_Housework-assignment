<?php
session_start();

header("Content-type: text/html; charset=utf-8");

//クロスサイトリクエストフォージェリ（CSRF）対策のトークン判定
if ($_POST['token'] != $_SESSION['token']){
	echo "正常な方法でアクセスしてください。戻るなどは使用できません。";
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
$user = $userAction->getUser($_SESSION['user']);

//前後にある半角全角スペースを削除する関数
function spaceTrim ($str) {
	// 行頭
	$str = preg_replace('/^[ 　]+/u', '', $str);
	// 末尾
	$str = preg_replace('/[ 　]+$/u', '', $str);
	return $str;
}

//エラーメッセージの初期化
$errors = array();

if(empty($_POST)) {
	header("Location: edit_user_form.php");
	exit();
}else{
	//POSTされたデータを各変数に入れる
	$name = isset($_POST['name']) ? $_POST['name'] : NULL;
	$family = isset($_POST['family']) ? $_POST['family'] : NULL;
	$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : NULL;
	$password = isset($_POST['password']) ? $_POST['password'] : NULL;
    $family = isset($_POST['family']) ? $_POST['family'] : NULL;
	$mail = isset($_POST['mail']) ? $_POST['mail'] : NULL;
	
	//前後にある半角全角スペースを削除
	$name = spaceTrim($name);
	$family = spaceTrim($family);
	$confirm_password = spaceTrim($confirm_password);
    $password = spaceTrim($password);
    $family = spaceTrim($family);
	$mail = spaceTrim($mail);
	
	//アカウント入力判定
	if ($name == ''):
		$errors['name'] = "あなたのお名前が入力されていません。";
	endif;
	
	//家族名入力判定
	if ($family == ''):
		$errors['family'] = "あなたの家族名が入力されていません。";
	endif;

	//変更前のパスワード入力判定
	if ($confirm_password == ''):
		$errors['confirm_password'] = "変更後のパスワードが入力されていません。";
	elseif(!preg_match('/^[0-9a-zA-Z]{5,30}$/', $_POST["confirm_password"])):
		$errors['confirm_password_length'] = "パスワードは半角英数字の5文字以上30文字以下で入力して下さい。";
	else:
		$confirm_password_hide = str_repeat('*', strlen($confirm_password));
	endif;

	//変更後のパスワード入力判定
	if ($password == ''):
		$errors['password'] = "変更後のパスワードが入力されていません。";
	elseif(!preg_match('/^[0-9a-zA-Z]{5,30}$/', $_POST["password"])):
		$errors['password_length'] = "パスワードは半角英数字の5文字以上30文字以下で入力して下さい。";
	else:
		$password_hide = str_repeat('*', strlen($password));
    endif;

    //メール入力判定
	if ($mail == ''){
		$errors['mail'] = "メールアドレスが入力されていません。";
	}else{
		if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mail)){
			$errors['mail_check'] = "メールアドレスの形式が正しくありません。";
		}
	}
	
}

//エラーが無ければUpdate
if(count($errors) === 0){
	//ここでUpdateする
	//パスワードのハッシュ化
	$password_hash =  password_hash($password, PASSWORD_DEFAULT);
    $updateCheck = $userAction->update($user['name'], $user['mail'], $confirm_password, $name, $family, $password_hash, $mail, $user['id']);
    if ($updateCheck != 'update') {
        $errors['update'] = $updateCheck;
    };
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
	<h2>会員情報変更完了画面</h2>

	<p>会員情報変更が正常に完了いたしました。</p>
	<p><a href="./index.php" class="btn">TOP画面</a></p>

<?php elseif(count($errors) > 0): ?>

	<?php
	foreach($errors as $value){
		echo "<p>".$value."</p>";
	}
	?>

<?php endif; ?>
 
</body>
</html>