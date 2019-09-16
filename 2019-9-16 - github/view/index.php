<?php
session_start();
require_once("../config/properties.php");
require_once("../class/usersTableAction.php");
date_default_timezone_set('Asia/Tokyo');

$usersAction = new usersTableAction();

if (isset($_SESSION['user'])) {
    $users = $usersAction->selectAll();
    $loginUser = $usersAction->getUser($_SESSION['user']);
}

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
$day = date('d');
 
// 月末日を取得
$last_day = date('j', mktime(0, 0, 0, $month + 1, 0, $year));
 
$calendar = array();
$j = 0;
 
// 月末日までループ
for ($i = 1; $i < $last_day + 1; $i++) {
 
    // 曜日を取得
    $week = date('w', mktime(0, 0, 0, $month, $i, $year));
 
    // 1日の場合
    if ($i == 1) {
 
        // 1日目の曜日までをループ
        for ($s = 1; $s <= $week; $s++) {
 
            // 前半に空文字をセット
            $calendar[$j]['day'] = '';
            $j++;
 
        }
 
    }
 
    // 配列に日付をセット
    $calendar[$j]['day'] = $i;
    $j++;
 
    // 月末日の場合
    if ($i == $last_day) {
 
        // 月末日から残りをループ
        for ($e = 1; $e <= 6 - $week; $e++) {
 
            // 後半に空文字をセット
            $calendar[$j]['day'] = '';
            $j++;
 
        }
 
    }
 
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>家事分担まるわかり君</title>
    <link rel="stylesheet" href="styles.css?<?= date("Y/m/d/H:i:s") ?>">
</head>
<body>
    <div>

        <a href="index.php" class="title">家事分担まるわかり</a>

        <div>
            <?php if (!isset($_SESSION['user'])) :?>

                <a href="./registration_mail_form.php" class="btn">新規会員登録</a>

                <a href="./login_form.php" class="btn">ログイン画面</a>

            <?php endif; ?>     

            

            <?php if (isset($_SESSION['user'])) :?>

                <div class="wrapper">

                    <p>こんにちは、<?php echo $loginUser['name']; ?>さん！</p>

                    <a href="./logout.php" class="btn">ログアウト</a>

                    <a href="./account.php" class="btn">アカウント情報</a>

                    <a href="./ranking.php" class="btn">ランキング</a>
                
                </div>

                

            <div class="family_list wrapper">
                <h2><?=$loginUser['family'] ?>一覧</h2>
                <?php foreach ($users as $user) :
                        if ($loginUser['family'] == $user['family']): ?>
                
                        <p><?php echo "名前：" . $user['name'] . "　メールアドレス：" . $user['mail']; ?></p>
                    
                
                <?php   endif;
                    endforeach; ?>
            </div>

            <div class="calender_wrap">
    
    <h2>カレンダーの日付けを押すと、行った家事の記録と、家事の予定をたてる、ことができます！</h2>

    <table class="calender_table">
        <thead>
            <tr>
            <th><a href="index.php?shift=previous">&laquo;</a> </th>
            <th colspan="5"> <?php echo $year; ?>年<?php echo $month; ?>月</th>
            <th><a href="index.php?shift=next">&raquo;</a> </th>
            </tr>
        </thead>

        <tr>
            <th>日</th>
            <th>月</th>
            <th>火</th>
            <th>水</th>
            <th>木</th>
            <th>金</th>
            <th>土</th>
        </tr>
    
        <tr>
        <?php $cnt = 0; ?>
        <?php foreach ($calendar as $key => $value): ?>
        <?php 
            if (mb_strlen($month) == 1) {
                $month_for_date = "0" . $month;
            } else {
                $month_for_date = $month;
            }
            if (mb_strlen($value['day']) == 1) {
                $day_for_date = "0" . $value['day'];
            } else {
                $day_for_date = $value['day'];
            }
            $date = $year . $month_for_date . $day_for_date
        ?>
            <?php if ( (int) $date == date("Ymd")): ?>
                <td class="calender_today">
                    <?php $cnt++; ?>
                    <a href="past_day_show.php?month=<?= $month ?>&day=<?= $value['day'] ?>&year=<?= $year ?>"><?php echo $value['day']; ?></a>
                </td>
            <?php elseif ((int) $date < date("Ymd")): ?>
                <td>
                    <?php $cnt++; ?>
                    <a href="past_day_show.php?month=<?= $month ?>&day=<?= $value['day'] ?>&year=<?= $year ?>"><?php echo $value['day']; ?></a>
                </td>
            <?php elseif ((int) $date > date("Ymd")): ?>
                <td>
                    <?php $cnt++; ?>
                    <a href="future_day_show.php?month=<?= $month ?>&day=<?= $value['day'] ?>&year=<?= $year ?>"><?php echo $value['day']; ?></a>
                </td>
            <?php endif; ?>
            
    
        <?php if ($cnt == 7): ?>
        </tr>
        <tr>
        <?php $cnt = 0; ?>
        <?php endif; ?>
    
        <?php endforeach; ?>
        </tr>
        <tfoot>
            <th colspan="7"><a href="index.php">Today</a> </th>
        </tfoot>
    </table>

</div>
                

            <?php endif; ?>

        </div>

    </div>

   
</body>
</html>