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
require_once("../class/houseworksToDoTableAction.php");

$houseworksToDoAction = new houseworksToDoTableAction();
$usersAction = new usersTableAction();
$loginUser = $usersAction->getUser($_SESSION['user']);

$year = $_GET['year'];
$month = $_GET['month'];
$day = $_GET['day'];

if (mb_strlen($month) == 1) {
    $month = "0" . $month;
}
if (mb_strlen($day) == 1) {
    $day = "0" . $day;
}

if ($houseworksToDoAction->count($_SESSION['user'], $year, $month, $day) != null) {
    $count = $houseworksToDoAction->count($_SESSION['user'], $year, $month, $day);
} else {
    $count = 0;
}

//合計ポイント計算用の変数定義
$sum_point = 0;

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>家事分担まるわかり君</title>
    <link rel="stylesheet" href="styles.css?<?= date("Y/m/d/H:i:s") ?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script type="text/javascript" src="houseworks_todo.js"></script>
</head>
<body>

        <a href="index.php" class="title">家事分担まるわかり</a>
        <h2><?= $loginUser['name'] ?>さんの<?= $month ?>月<?= $day ?>日に行う予定の家事についてのページです</h2>
        <div class="wrapper">
            <h2>家事登録フォーム</h2>
            <h3><?= $loginUser['name'] ?>さんの<?= $month ?>月<?= $day ?>日に行う予定の家事を一つずつ入力してください。</h3>

            
            
            <form action="housework_todo.php" id="hausework_todo" method="post">

                <div class="housework_form">
                    <h3><?= $count + 1 ?>つめの家事について</h3>
                    名もなき家事ですか？
                    <input type="radio" id="flag" name="flag" value="yes" checked="checked">はい
                    <input type="radio" id="flag" name="flag" value="no">いいえ
                
                </div>

                <div class="housework_form" id="housework_noname_list">
                    <a id="housework_noname_list_trigger">名もなき家事の具体例一覧　クリックすると一覧の表示・非表示を切り替えられます</a>
                    <ul id="housework_noname_list_ul">
                        <li>溜まったゴミを捨てる</li>
                        <li>アイロン掛けをする</li>
                        <li>食事の献立を考える</li>
                        <li>ベッドや布団を整える</li>
                        <li>飲みっぱなしのグラスを片付ける</li>
                        <li>調味料を補充・交換する</li>
                        <li>ごみを分類する</li>
                        <li>食べ残しの食品を冷蔵庫にしまう</li>
                        <li>食事の前に食卓を拭く</li>
                        <li>トイレットペーパーがなくなったときに、買いに行く</li>
                        <li>手洗い場のタオルを取り換える</li>
                        <li>新聞・雑誌などをまとめて捨てる</li>
                        <li>脱ぎっぱなしの服をクローゼットやタンスにしまう</li>
                        <li>クリーニングに出す、取りに行く</li>
                        <li>玄関の靴をそろえる</li>
                        <li>靴を磨く</li>
                        <li>町内やマンションの会合に出席する</li>
                        <li>郵便物をチェックする</li>
                        <li>子供の食事を手伝う</li>
                        <li>子供の送迎</li>
                        <li>子供の学校準備、勉強を見る</li>
                        <li>ペット、植物の世話</li>
                        <li>使い切ったティッシュの交換</li>
                        <li>古くなった照明の交換</li>
                        <li>ポストに入っていた不要なチラシの処分</li>
                        <li>朝カーテンを開け、夜カーテンを閉める</li>
                        <li>子供と会話する</li>
                        <li>家電製品の選定・購入・設置する</li>
                        <li>朝刊、夕刊を取りに行く</li>
                        <li>使った道具を、元の位置にきちんと片付ける</li>
                        <p>出典：</p>
                        <a href="https://www.daiwahouse.co.jp/column/lifestyle/dual_income/">共働き夫婦の「家事」に関する意識調査　第1回 家事への意識の違い編｜間取りと暮らし方｜TRY家コラム（トライエコラム）｜大和ハウス</a>
                    </ul>
                </div>

                    
                <div class="housework_form">
                
                    行う予定の家事は何ですか？
                    <input type="text" name="housework" id="housework" placeholder="例：ポストに入っていた不要なチラシの処分">
                
                </div>

                <div id="housework_time" class="housework_form">
                
                    その家事は何時から何時までする予定ですか？
                    <input type="time" name="start_time" pattern="[0-9]{2}:[0-9]{2}">～<input type="time" name="finish_time" pattern="[0-9]{2}:[0-9]{2}">
                
                </div>

                <input type="hidden" name="year" value="<?=$year?>">
                <input type="hidden" name="month" value="<?=$month?>">
                <input type="hidden" name="day" value="<?=$day?>">
                <input type="hidden" name="token" value="<?=$token?>">

                <input type="submit" value="家事登録！">

            </form>
        </div>

        
                
        <?php 
            if ($count > 0): 
                $houseworks = $houseworksToDoAction->selectAll(); ?>
                <div class="wrapper">
                    <h2>行う予定の家事一覧</h2>
                    <?php foreach ($houseworks as $housework):
                            

                            if ($_SESSION['user'] == $housework['user_id'] && mb_substr($housework['start_date'], 0, 10) == $year . "-" . $month . "-" . $day): 
                                if ($housework['is_noname'] == 0): 
                                    $sum_point++; ?>
                        <p><?php echo "名もなき家事：" . $housework['housework'] . "　"; ?><a href="delete_housework_todo.php?id=<?=$housework['id']?>&month=<?= $month ?>&day=<?=$day?>&year=<?=$year?>" class="btn">削除</a></p>

                    <?php else: ?>
                        
                        <!-- ポイント計算 -->
                        <?php $start_date = date_create(mb_substr($housework['start_date'], 0, 16));
                                $finish_date = date_create(mb_substr($housework['finish_date'], 0, 16));
                                $interval = date_diff($start_date, $finish_date);
                                $point = ($interval->h * 60 + $interval->i) / 30;
                                $sum_point += $point;
                        ?>

                        <p><?= "家事：" . $housework['housework'] . "　開始時間：" . mb_substr($housework['start_date'], 11, 5) . "　終了時間：" . mb_substr($housework['finish_date'], 11, 5) . "　取得ポイント：" . "　" ?> <a href="delete_housework_todo.php?id=<?=$housework['id']?>&month=<?= $month ?>&day=<?=$day?>&year=<?=$year?>" class="btn">削除</a></p>    
            
        <?php           
                        endif;
                    endif;
                endforeach; ?>
                <br>
                <a href="all_family_houseworks_todo.php?month=<?= $month ?>&day=<?=$day?>&year=<?=$year?>" class="btn"">他の家族も含めた<?= $month ?>月<?= $day ?>日に行う予定の家事一覧</a>
                </div>
        <?php endif;    ?>
            
    
</body>
</html>