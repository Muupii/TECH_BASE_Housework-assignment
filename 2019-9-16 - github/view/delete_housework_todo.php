<?php
session_start();

header("Content-type: text/html; charset=utf-8");

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

if (!isset($_SESSION['user']) | !isset($_GET)) {
    header("location: index.php");
}

require_once("../config/properties.php");
require_once("../class/houseworksToDoTableAction.php");

$houseworksToDoAction = new houseworksToDoTableAction();

$houseworkId = $_GET['id'];
$year = $_GET['year'];
$month = $_GET['month'];
$day = $_GET['day'];

$houseworksToDoAction->delete_housework($houseworkId);

header("location: future_day_show.php?year=$year&month=$month&day=$day");