<?php
session_start();

header("Content-type: text/html; charset=utf-8");
 
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

// 年月を取得
if (isset($_GET['shift'])) {
    if ($_GET['shift'] == 'previous') {
        if ($_SESSION['month'] == 1) {
            $_SESSION['year'] -= 1;
            $_SESSION['month'] = 12;
        } else {
            $_SESSION['month'] -= 1;
        }
    } elseif ($_GET['shift'] == 'next') {
        if ($_SESSION['month'] == 12) {
            $_SESSION['year'] += 1;
            $_SESSION['month'] = 1;
        } else {
            $_SESSION['month'] += 1;
        }
    }
} else {
    $_SESSION['year'] = date('Y');
    $_SESSION['month'] = date('n');
}

$year = $_SESSION['year'];
$month = $_SESSION['month'];

// 月末日を取得
$last_day = date('j', mktime(0, 0, 0, $month + 1, 0, $year));

//ranking用の連想配列を作成する
$array = array();

// 順位表示用のカウンター
$count = 0;

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
    <div>

        <a href="index.php" class="title">家事分担まるわかり</a>

        
        <p class="mini_title"><a href="ranking.php?shift=previous">&laquo;先月</a><b>　　<?= $loginUser['family'] ?>の<?= $year ?>年<?= $month ?>月ランキング</b>　　<a href="ranking.php?shift=next">&raquo;来月</a></p>
        
        

        <?php foreach ($users as $user) :
                if ($loginUser['family'] == $user['family']):

                    $sum_point = 0; 

                    // 1日から月末までループしてその月のポイントの合計を求める
                    for ($day=0; $day<=$last_day; $day++) {
                        if ($houseworksDoneAction->count($user["id"], $year, $month, $day) != null) {
                            $houseworks = $houseworksDoneAction->selectAll();
                            foreach ($houseworks as $housework):
                                
                                // 月日を二文字にする
                                if (mb_strlen($month) == 1) {
                                    $month = "0" . $month;
                                }
                                if (mb_strlen($day) == 1) {
                                    $day = "0" . $day;
                                }
                                
                                if ($user['id'] == $housework['user_id'] && mb_substr($housework['start_date'], 0, 10) == $year . "-" . $month . "-" . $day): 
                                    if ($housework['is_noname'] == 0): 
                                        
                                        // 名もなき家事は無条件1ポイント
                                        $sum_point++;
    
                                    else: 
                                        // 30分2ポイントを計算
                                        $start_date = date_create(mb_substr($housework['start_date'], 0, 16));
                                        $finish_date = date_create(mb_substr($housework['finish_date'], 0, 16));
                                        $interval = date_diff($start_date, $finish_date);
                                        $point = ($interval->h * 60 + $interval->i) / 30;
                                        $sum_point += $point;

                                    endif;
                                endif;
                                
                            endforeach; 
                        }
                        
                    } 
                    // 配列にユーザーIDをキーとして、ポイントの合計値を追加する
                    $array[$user['id']] = $sum_point; ?>


            
        <?php   endif;
            endforeach; ?>

        <?php
        // arsortで値を降順でソート
        arsort($array);
        foreach ($array as $key => $value) { 
            $count++; ?>
            <h3><b><?= $count ?>位</b>　<?= $usersAction->getUser($key)['name'] ?>さん　<?= round($value, 2) ?>ポイント</h3>

        <?php    
        }
        ?>

    </div>

</body>
</html>