<?php
session_start();

header("Content-type: text/html; charset=utf-8");

//クロスサイトリクエストフォージェリ（CSRF）対策のトークン判定
if ($_POST['token'] != $_SESSION['token']){
    echo "正常な方法でアクセスしてください。戻るなどは使用できません。";
    var_dump($_POST['token']);
	exit();
}

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

if (!isset($_SESSION['user']) | !isset($_GET)) {
    header("location: index.php");
}

//データベース接続
require_once("../config/properties.php");
require_once("../class/usersTableAction.php");
require_once("../class/houseworksDoneTableAction.php");
$houseworksDoneAction = new houseworksDoneTableAction();
$usersAction = new usersTableAction();
$loginUser = $usersAction->getUser($_SESSION['user']);

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
	header("Location: day_show.php");
	exit();
}else{
	//POSTされたデータを各変数に入れる
	$flag = isset($_POST['flag']) ? $_POST['flag'] : NULL;
	$housework = isset($_POST['housework']) ? $_POST['housework'] : NULL;
    $start_time = isset($_POST['start_time']) ? $_POST['start_time'] : NULL;
    $finish_time = isset($_POST['finish_time']) ? $_POST['finish_time'] : NULL;
    $year = $_POST['year'];
    $month = $_POST['month'];
    $day = $_POST['day'];
    
    //前後にある半角全角スペースを削除
	$housework = spaceTrim($housework);
    
    //入力判定
	if ($flag == ''):
		$errors['flag'] = "名もなき家事に関する質問にお答えください。";
    endif;
    
    if ($housework == '') {
        $errors['housework'] = "行った家事を入力してください。";
    }

    if ($flag == "no") {
        if ($start_time == "" || $finish_time == "") {
            $errors['time'] = "家事を行った時間を入力してください。";
        }
    }
}

//エラーが無ければ家事登録
if(count($errors) === 0){

    //ここで家事登録する
    if ($flag == "yes") {
        $houseworksDoneAction->register_noname($_SESSION['user'], $housework, $year, $month, $day);
        header("Location: past_day_show.php?year={$year}&month={$month}&day={$day}");
    } else {
        $houseworksDoneAction->register($_SESSION['user'], $housework, $year, $month, $day, $start_time, $finish_time);
        header("Location: past_day_show.php?year={$year}&month={$month}&day={$day}");
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
<a href="index.php" class="title">家事分担まるわかり</a>
<?php if(count($errors) > 0): ?>

<?php
foreach($errors as $value){
	echo "<p>".$value."</p>";
}
?>

<?php endif; ?>
 
</body>
</html>