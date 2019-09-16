<?php
session_start();

header("Content-type: text/html; charset=utf-8");

//クロスサイトリクエストフォージェリ（CSRF）対策
$_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
$token = $_SESSION['token'];
 
//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

if (!isset($_SESSION['user']) | !isset($_GET)) {
    header("location: index.php");
}

date_default_timezone_set('Asia/Tokyo');
require_once("../config/properties.php");
require_once("../class/usersTableAction.php");
require_once("../class/houseworksDoneTableAction.php");

$houseworksDoneAction = new houseworksDoneTableAction();
$usersAction = new usersTableAction();
$loginUser = $usersAction->getUser($_SESSION['user']);
$users = $usersAction->selectAll();

$year = $_GET['year'];
$month = $_GET['month'];
$day = $_GET['day'];

if (mb_strlen($month) == 1) {
    $month = "0" . $month;
}
if (mb_strlen($day) == 1) {
    $day = "0" . $day;
}





?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>家事分担まるわかり君</title>
    <link rel="stylesheet" href="styles.css?<?= date("Y/m/d/H:i:s") ?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script type="text/javascript" src="houseworks_done.js"></script>
</head>
<body>

        <a href="index.php" class="title">家事分担まるわかり</a>
        <h2><?=$loginUser['family'] ?>の<?= $month ?>月<?= $day ?>日に行った家事一覧ページです</h2>

        <?php foreach ($users as $user) :
                if ($loginUser['family'] == $user['family']): ?>
        
                <?php 
                   
                    $sum_point = 0;

                    if ($houseworksDoneAction->count($user['id'], $year, $month, $day) != null) {
                        $count = $houseworksDoneAction->count($user['id'], $year, $month, $day);
                    } else {
                        $count = 0;
                    }

                    if ($count > 0): 
                        $houseworks = $houseworksDoneAction->selectAll(); ?>
                        <div class="wrapper">
                            <h2><?=$user['name']?>の行った家事一覧</h2>
                            <?php foreach ($houseworks as $housework):
                                    

                                    if ($user['id'] == $housework['user_id'] && mb_substr($housework['start_date'], 0, 10) == $year . "-" . $month . "-" . $day): 
                                        if ($housework['is_noname'] == 0): 
                                            $sum_point++; ?>
                                <p><?php echo "名もなき家事：" . $housework['housework'] . "　取得ポイント： 1pt　"; ?></p>

                            <?php else: ?>
                                
                                <!-- ポイント計算 -->
                                <?php   $start_date = date_create(mb_substr($housework['start_date'], 0, 16));
                                        $finish_date = date_create(mb_substr($housework['finish_date'], 0, 16));
                                        $interval = date_diff($start_date, $finish_date);
                                        $point = ($interval->h * 60 + $interval->i) / 30;
                                        $sum_point += $point;
                                ?>

                                <p><?= "家事：" . $housework['housework'] . "　開始時間：" . mb_substr($housework['start_date'], 11, 5) . "　終了時間：" . mb_substr($housework['finish_date'], 11, 5) . "　取得ポイント：" . round($point, 2) . "pt　" ?></p>    
                    
                <?php           
                                endif;
                            endif;
                        endforeach; ?>
                            <div class="wrapper_inset">
                                <h3><?=$user['name']?>の取得ポイント合計</h3>
                                <p><?= round($sum_point, 2) ?>ポイント</p>
                                
                            </div>
                        </div>
                <?php endif;    ?>

                
            
        
        <?php   endif;
            endforeach; ?>

        
            
    
</body>
</html>